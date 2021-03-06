<?php
require_once "./myNest/includes/Objects/Post.php";
require_once './myNest/SimpleTemplateEngine/loader.php';

$env = new SimpleTemplateEngine\Environment('./myNest/includes/html', '.php');

class Page
{
	protected $env;
	protected $html="";
	function __construct(){
		$this->env = new SimpleTemplateEngine\Environment('./myNest/includes/html', '.php');
	}
	protected function setHtml($h){
		$this->html .= $h;
	}
	public function getHtml(){
		return $this->html;
	}
}

class LoginPage extends Page
{
	function __construct(){
		parent::__construct();
		$this->html = $this->env->render('login');
	}
}

class SignupPage extends Page
{

	function __construct($e){
		parent::__construct();
		$this->html = $this->env->render('sign-up' , ['errors'=>$e]);
	}
}

class HomePage extends Page
{
	function __construct(){
		parent::__construct();
		$uName = $_SESSION["username"];
		$friends = getFirends($uName);
		$friends[] = getUserId( $uName);
		$posts = getPosts($friends);
		$this->html = $this->env->render('home' , ['uName'=>$uName , "posts"=>$posts]);
	}
	public function explorePage()
	{
		$instance = new self();
		$instance->posts = getAllPost();
		$instance->html = $instance->env->render('home' , ['uName'=>$_SESSION["username"] , "posts"=>$instance->posts]);
		return $instance;
	}
}

class ProfilePage extends Page
{
	function __construct(){
		parent::__construct();
		$uName = $_SESSION["username"];
		$posts = getPosts([getUserId($uName)]);//the function only accetps arrays
		$this->html = $this->env->render('profile' , ['uName'=>$uName , "posts"=>$posts , "owner"=>"self" , "editable"=>true]);
	}
	public	static	function othersProfile($owner)
	{
		$instance = new self();
		$instance->posts = getPosts([getUserId($owner)]);
		$instance->html = $instance->env->render('profile' , ['uName'=>false , "posts"=>$instance->posts , "owner"=>$owner , "editable"=>false]);
		return $instance;
	}
}

class EditPage extends Page
{
	private $posts = null;
	function __construct(){
		parent::__construct();

		$this->html = $this->env->render('edit' , ['posts'=>$this->posts]);
	}
	public static function withPosts($posts)
	{
		$instance = new self();
		$instance->posts = $posts;
		$instance->html = $instance->env->render('edit' , ['posts'=>$instance->posts]);
		return $instance;
	}
}

class FriendsPage extends Page
{
	function __construct( $answers)
	{
		parent::__construct();
		updateFriends();
		$uName = $_SESSION["username"];
		$friends = getFirends($uName);
		$requests = getReqs($uName);
		$this->html = $this->env->render('friends' , ['uName'=>$uName , "friends"=>$friends , "requests"=>$requests , "answers"=>$answers]);
	}
}


?>

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
	function __construct($connection){
		parent::__construct();
		$uName = $_SESSION["username"];
		$friends = getFirends($connection , $uName);
		$friends[] = getUserId($connection , $uName);
		$posts = getPosts($connection , $friends);

		$this->html = $this->env->render('home' , ['uName'=>$uName , "posts"=>$posts]);
	}
}

class ProfilePage extends Page
{
	function __construct($connection){
		parent::__construct();
		$uName = $_SESSION["username"];
		$posts = getPosts($connection , [getUserId($connection , $uName)]);
		$this->html = $this->env->render('profile' , ['uName'=>$uName , "posts"=>$posts]);
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
	function __construct($connection , $answers)
	{
		parent::__construct();
		updateFriends($connection);
		$uName = $_SESSION["username"];
		$friends = getFirends($connection , $uName);
		$requests = getReqs($connection , $uName);
		$this->html = $this->env->render('friends' , ['uName'=>$uName , "friends"=>$friends , "requests"=>$requests , "answers"=>$answers , "connection"=>$connection]);
	}
}


?>

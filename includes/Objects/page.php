<?php
require_once "./myNest/includes/Objects/Post.php";
require_once './myNest/SimpleTemplateEngine/loader.php';

$env = new SimpleTemplateEngine\Environment('./myNest/includes/html', '.php');

class Page {
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

class LoginPage extends Page{
	function __construct(){
		parent::__construct();
		$this->html = $this->env->render('login');
	}
}

class SignupPage extends Page{

	function __construct(){
		parent::__construct();
		$this->html = $this->env->render('sign-up');
	}
}

class HomePage extends Page{

	function __construct($connection){
		parent::__construct();
		$uName = $_SESSION["username"];
		$friends = getFirends($connection , $uName);
		$friends[] = getUserId($connection , $uName);
		$posts = getPosts($connection , $friends);

		$this->html = $this->env->render('home' , ['uName'=>$uName , "posts"=>$posts]);
	}
}

class EditPage extends Page
{

	function __construct(){
		parent::__construct();
		$this->html = $this->env->render('edit');
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

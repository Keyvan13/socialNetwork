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


/**
 *
 */
class LoginPage extends Page{

	function __construct(){

		/*
		$body = file_get_contents("./myNest/includes/html/login.php");
		$h = $this->getHtml();
		$h = str_replace("****" , $body , $h);
		$this->html = $h;*/
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
		$posts = getPosts($connection , $friends);
		$this->html = $this->env->render('home');
	}
}

class EditPage extends Page{

	function __construct(){
		parent::__construct();
		$this->html = $this->env->render('edit');
	}
}


?>

<?php
function formErrors($errors = array()){
  $output = "";
  foreach ($errors as $key => $value) {
    $output .= "<p>{$value}</p>";
  }
  return $output;
}

function loginPage(){
  $output = "<header class=\"w3-container w3-black\">
            <h1>Welcom</h1>
            </header>\n";
  $output .= "<div class=\"w3-display-container w3-auto\" , style=\"height: 20em;\">
  <form class =\"w3-container w3-display-middle w3-half w3-card-4\" action=\"index.php\" method=\"post\">
    <label>Username:</label> <input class=\"w3-input\" type=\"text\" style=\"width:90%\" name=\"username\" value=\"\">
    <label>Password:</label> <input class=\"w3-input\" type=\"password\" style=\"width:90%\" name=\"password\" value=\"\">
    <input class=\"w3-button w3-section w3-teal w3-black\" type=\"submit\" name=\"loginSubmit\" value=\"Login\">
    ";
  $output .= "<a class=\"w3-button w3-section w3-teal w3-ripple\" href=\"index.php?mode=signup\" >Sign-up</a></form></div>";

  return $output;
}

function signupPage(){
  $output = "<header class=\"w3-container w3-black\">
            <h1>Welcom</h1>
            </header>\n
            <div class=\"w3-display-container w3-auto\" , style=\"height: 29em;\">
            <form class =\"w3-container w3-display-middle w3-half w3-card-4\" action=\"index.php\" method=\"post\">
              <p>Username:</p> <input class=\"w3-input\" type=\"text\" style=\"width:90%\" name=\"username\" value=\"\"><br>
              <p>Password:</p> <input class=\"w3-input\" type=\"password\" style=\"width:90%\" name=\"password\" value=\"\"><br>
              <p>Confirm Password:</p> <input class=\"w3-input\" type=\"password\" style=\"width:90%\" name=\"confirmPassword\" value=\"\"><br>
              <input class=\"w3-button w3-section w3-teal w3-black\" type=\"submit\" name=\"signupSumbit\" value=\"signup\">
            </form>
            </div>";

  return $output;
}

function selectPassword(){
  global $connection;
  $uName = $_POST['username'];
  //$pass = $_POST['password'];
  $uName = mysqli_real_escape_string($connection , $uName);

  $passSet = mysqli_query($connection ,
   "select hashpassword from users where username = \"{$uName}\" limit 1");
  return $passSet;
}

function redirectTo($des){

  header("Location: {$des}");
  exit;

}

function connectDatabase(){
  global $hn,$un,$pw,$db;
  $db = mysqli_connect($hn , $un , $pw , $db );

  //check database connection
  if (mysqli_connect_errno()){
    die("Database connection failed:" .
     mysqli_connect_error() .
      ' (' . mysqli_connect_errno() .
       ')'
     );
  }
  return $db;
}

function insertUser($uName , $pass){
  global $connection;
  $uName = mysqli_real_escape_string($connection , $uName);
  $pass = mysqli_real_escape_string($connection , $pass);
  $hashPass = password_hash($pass, PASSWORD_DEFAULT);
  $query =  ' insert into users ( ';
  $query .= ' username , hashpassword ) values (';
  $query .= " \"{$uName}\", \"{$hashPass}\" )";
  $result = mysqli_query($connection , $query);

  if (!$result){
    die('database query failed');
  }
}

function getNoteSet($uName){
  global $connection;
  $uName = mysqli_real_escape_string($connection , $uName);
  $query = "select body , id from notes where userid =
    (select id from users where username = \"{$uName}\" limit 1)";
  $result = mysqli_query($connection , $query);
  if ($result === null){
    die('database query failed');
  }
  return $result;

}

function getNoteById($id){
  global $connection;
  $id = mysqli_real_escape_string($connection , $id);
  $query = "select body from notes where id = {$id} limit 1";
  $result = mysqli_query($connection , $query);
  if (!$result){
    die('database query failed');
  }
  return $result;

}

function updateNote($noteId ,$text){
  global $connection;
  $noteId = mysqli_real_escape_string($connection , $noteId);
  $text = mysqli_real_escape_string($connection , $text);

  $query = "update notes set body=\"{$text}\" where id ={$noteId}";
  $result = mysqli_query($connection , $query);
  if (!$result){
    die('database query failed');
  }
  return $result;
}

function insertNote($uName , $text){
  global $connection;
  $uName = mysqli_real_escape_string($connection , $uName);
  $text = mysqli_real_escape_string($connection , $text);

  $query =  ' insert into notes ( ';
  $query .= ' userid , body ) values (';
  $query .= " (select id from users where username = \"{$uName}\"), \"{$text}\" )";
  $result = mysqli_query($connection , $query);

  if (!$result){
    die('database query failed');
  }
}

function deleteNote($noteId){
  global $connection;
  $noteId = mysqli_real_escape_string($connection , $noteId);

  $query = "delete from notes where id = {$noteId}";
  $result = mysqli_query($connection , $query);

  if (!$result){
    die('database query failed');
  }
}

function verifiyNoteAccess($noteId , $uName){
  global $connection;
  $noteId = mysqli_real_escape_string($connection , $noteId);
  $uName = mysqli_real_escape_string($connection , $uName);

  $query = "select username from users where id =(select userid from notes where id = {$noteId})";
  $result = mysqli_query($connection , $query);

  if (!$result){
    die('database query failed');
  }
  if($uName === mysqli_fetch_row($result)[0]){
    return true;
  }else {
    return false;
  }

}
 ?>

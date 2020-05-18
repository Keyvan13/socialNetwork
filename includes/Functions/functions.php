<?php
function formErrors($errors = array()){
  $output = "";
  foreach ($errors as $key => $value) {
    $output .= "<p>{$value}</p>";
  }
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
  $db = new mysqli($hn , $un , $pw , $db);

  //check database connection
  if ($db->connect_error){
    die("Database connection failed:" .
      $db->connect_error);
  }
  return $db;
}

function saveProfile($p,$f){
  switch($f['photo']['type']){
    case 'image/jpeg': $ext = 'jpg'; break;
    case 'image/gif': $ext = 'gif'; break;
    case 'image/png': $ext = 'png'; break;
    case 'image/tiff': $ext = 'tif'; break;
    default: $ext = ''; break;
  }
  if ($ext){
    $n = "./myNest/profiles/".$p["username"].".$ext";
    move_uploaded_file($f['photo']['tmp_name'], $n);
  }else {
    echo "{$f['photo']['name']} is not an accepted image file";
  }
  return $n;
}

function addUser($connection , $p , $f){
  $uName = $connection->real_escape_string($p["username"]);
  $pass = $connection->real_escape_string($p["pass"]);
  $email = $connection->real_escape_string($p["email"]);
  $gender = $connection->real_escape_string($p["gender"]);
  $path = saveProfile($p , $f);
  $profilephoto = $connection->real_escape_string($path);
  $hashPass = password_hash($pass, PASSWORD_DEFAULT);
  $query = <<<_END
    insert into users (
    username ,
    hashpassword ,
    profilephoto ,
    email ,
    gender
    ) values (
    "$uName" ,
    "$hashPass" ,
    "$profilephoto" ,
    "$email" ,
    "$gender"
    )
  _END;
  $result = $connection->query($query);
  if (!$result){
    die('database query failed');
  } else {
    return true;
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

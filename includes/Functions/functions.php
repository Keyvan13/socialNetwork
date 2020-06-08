<?php
function formErrors($errors = array()){
  $output = "";
  foreach ($errors as $key => $value) {
    $output .= "<p>{$value}</p>";
  }
  return $output;
}

function selectPassword($connection , $uName){
  $uName = $connection->real_escape_string($uName);
  $query = "select hashpassword from users where username = \"{$uName}\" limit 1";
  $result = $connection->query($query);
  return $result;
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

function savePost($connection , $p , $f)
{
  $text = $connection->real_escape_string($p["text"]);
  $path = savePostImage($p , $f);
}

/*function savePostImage($p , $f)************************post id
{
  switch($f['photo']['type']){
    case 'image/jpeg': $ext = 'jpg'; break;
    case 'image/gif': $ext = 'gif'; break;
    case 'image/png': $ext = 'png'; break;
    case 'image/tiff': $ext = 'tif'; break;
    default: $ext = ''; break;
  }
  if ($ext){
    $n = "./myNest/posts/".$p["username"].".$ext";
    move_uploaded_file($f['photo']['tmp_name'], $n);
  }else {
    echo "{$f['photo']['name']} is not an accepted image file";
  }
  return $n;
}
*/
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

function checkAuth($connection , $p){
  $passSet = selectPassword($connection , $p['username']);
  if ($passSet->num_rows == 0 ) {
    return false;
  } else {
    $row = $passSet->fetch_array(MYSQLI_ASSOC);
    if (password_verify($p['pass'] , $row["hashpassword"])) {
      return true;
    }else {
      return false;
    }
  }

}

function checkSession(){
  session_start();
  if (isset($_SESSION)){
    if($_SESSION["verified"] !== true){
      return false;
    } else {
      return true;
    }
  } else {
    return false;
  }
}

function setVerified(){
  $_SESSION["verified"] = true;
}

function logOut(){
  $_SESSION["verified"]= false;

}

function destroy_session_and_data(){
  session_start();
  $_SESSION = array();
  setcookie(session_name(), '', time() - 2592000, '/');
  session_destroy();
}

function getFirends($connection , $uName){
  $uName = $connection->real_escape_string($uName);
  $userId = getUserId($connection , $uName);
  $friends = [];
  $query = "select second from friends where first = \"$userId\"";
  $result = $connection->query($query);
  while ($result->num_rows != 0) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $friends[] = $row["second"];
  }

  $query = "select first from friends where second = \"$userId\"";
  $result = $connection->query($query);
  while ($result->num_rows != 0) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $friends[] = $row["first"];
  }

  return $friends;
}

function getPosts($connection , $friends)
{
  $posts = [];
  foreach ($friends as $f) {
    $query = "select id and text and dateCreated and imgPath from posts where 	userId = \"$f\"";
    $result = $connection->query($query);
    while ($result->num_rows != 0) {
      $row = $result->fetch_array(MYSQLI_ASSOC);
      $posts[] = new Post($row["imgPath"] , $row["text"] , $f , $row["dateCreated"]);
    }
  }
  return $posts;
}

function getUserId($connection , $uName)
{
  //An input shouldn't be escaped twice ($uName)
  $query = "select id from users where username = \"$uName\" limit 1";
  $result = $connection->query($query);
  if ($result->num_rows != 0) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    return $row["id"];
  }else {
    return false;
  }
}
 ?>

<?php

function selectPassword($uName)
{
  $connection = connectDatabase();
  $uName = $connection->real_escape_string($uName);
  $query = "select hashpassword from users where username = \"{$uName}\" limit 1";
  $result = $connection->query($query);
  if ($result->num_rows == 0) {
    $result->close();
    $connection->close();
    return false;
  }else {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $result->close();
    $connection->close();
    return $row["hashpassword"];
  }
}

function redirectTo($des)
{
  header("Location: {$des}");
  exit;
}

function connectDatabase()
{
  global $hn,$un,$pw,$dn;
  $db = new mysqli($hn , $un , $pw , $dn);
  //check database connection
  if ($db->connect_error){
    die("Database connection failed:" .
      $db->connect_error);
  }
  return $db;
}

function saveProfile($p,$f)
{
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

function addUser($connection , $p , $f)
{
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
  $path = savePostImage($f);
  $photo = $connection->real_escape_string($path);
  $uName = $connection->real_escape_string($_SESSION["username"]);
  $userId = getUserId($connection , $uName);

  $query = <<<_END
    insert into posts (
    text ,
    userId ,
    imgPath
    ) values (
    "$text" ,
    "$userId" ,
    "$photo"
    )
  _END;
  $result = $connection->query($query);
  if (!$result){
    die('database query failed');
  } else {
    return true;
  }
}

function savePostImage($f)
{
  switch($f['photo']['type']){
    case 'image/jpeg': $ext = 'jpg'; break;
    case 'image/gif': $ext = 'gif'; break;
    case 'image/png': $ext = 'png'; break;
    case 'image/tiff': $ext = 'tif'; break;
    default: $ext = ''; break;
  }
  if ($ext){
    $n = "./myNest/posts/" . $_SESSION["username"] . time() . ".$ext";
    move_uploaded_file($f['photo']['tmp_name'], $n);
  }else {
    echo "{$f['photo']['name']} is not an accepted image file";
  }
  return $n;
}

function checkAuth($connection , $p)
{
  $pass = selectPassword($p['username']);
  if ($pass == false ) {
    return false;
  } else {
    if (password_verify($p['pass'] , $pass)) {
      return true;
    }else {
      return false;
    }
  }
}

function checkSession()
{
  session_start();
  if (isset($_SESSION["username"]) && isset($_SESSION)){
    if(isset($_SESSION["verified"]) && $_SESSION["verified"] !== true || $_SESSION["username"] == "") {
      return false;
    } else {
      return true;
    }
  } else {
    return false;
  }
}

function setVerified()
{
  $_SESSION["verified"] = true;
}

function logOut()
{
  $_SESSION["verified"]= false;
}

function destroy_session_and_data()
{
  if (!$_SESSION) {
    session_start();
  }
  $_SESSION = array();
  setcookie(session_name(), '', time() - 2592000, '/');
  session_destroy();
}

function getFirends($connection , $uName)
{
  $uName = $connection->real_escape_string($uName);
  $userId = getUserId($connection , $uName);
  $friends = [];
  $query = "select second from friends where first = \"$userId\"";
  $result = $connection->query($query);
  for ($i = 0 ; $i<$result->num_rows ; ++$i) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $friends[] = $row["second"];
  }

  $query = "select first from friends where second = \"$userId\"";
  $result = $connection->query($query);
  for ($i = 0 ; $i<$result->num_rows ; ++$i) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $friends[] = $row["first"];
  }
  return $friends;
}

function getReqs($connection , $uName)
{
  $uName = $connection->real_escape_string($uName);
  $userId = getUserId($connection , $uName);
  $reqs = [];
  $query = "select id,sender,status from requests where receiver = \"$userId\"";
  $result = $connection->query($query);
  for ($i = 0 ; $i<$result->num_rows ; ++$i) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $reqs[] = new Request(getuName($connection , $row["sender"]) , $uName , $row["status"] , $row["id"]) ;
  }
  return $reqs;
}

function getPosts($connection , $friends)
{
  $posts = [];
  if (count($friends) == 0) {
    return $posts;
  }

  foreach ($friends as $f) {
    $query = "select id,text,dateCreated,imgPath from posts where 	userId = $f";
    $result = $connection->query($query);

    for ($i = 0 ; $i<$result->num_rows ; ++$i) {
      $row = $result->fetch_array(MYSQLI_ASSOC);
      $posts[] = new Post($row["imgPath"] , $row["text"] , getuName($connection ,$f) , $row["dateCreated"] , $row["id"]);
    }

  }
  return sortPosts($posts);
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

function getuName($connection , $uId)
{
  //An input shouldn't be escaped twice ($uName)
  $query = "select username from users where id = \"$uId\" limit 1";
  $result = $connection->query($query);
  if ($result->num_rows != 0) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    return $row["username"];
  }else {
    return false;
  }
}

function searchUsers($connection , $p)
{
  $answers = [];
  $target =($p["searchText"]);
  $query=<<<_END
    select
    username
    from
    users
  _END;
  $result = $connection->query($query);
  for ($i = 0 ; $i<$result->num_rows ; ++$i) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $n = preg_match("/$target/i" , $row["username"]);
    if ($n && $row["username"] != $_SESSION["username"]) {
      $answers[] = $row["username"];
    }
  }
  return $answers;
}

function isFriend($connection , $u1 , $u2)
{
  $friendship = false;
  $friends = getFirends($connection , $u1);
  $ui2 = getUserId($connection , $u2);

  foreach ($friends as $f) {
    if ($f == $ui2) {
      $friendship = true;
      break;
    }
  }
  return $friendship;
}

function performRequest($connection , $sender , $receiver)
{
  $sender = getUserId($connection , $sender);
  $receiver = getUserId($connection , $receiver);
  $query=<<<_END
    insert into
    requests (
      sender ,
      receiver ,
      status
    )
    values (
      $sender ,
      $receiver ,
      "pending"
    )
  _END;
  $result = $connection->query($query);
}

function dumpInfo($param)
{
  echo "<pre>";
  var_dump($param);
  echo "</pre>";
}

function checkAccess()
{
  if ($_SESSION["verified"] == false || $_SESSION["username"] == "") {
    redirectTo("/");
  }
}

function handleReq($connection)
{
  $req = getReqbyId($connection , $_GET["id"]);
  $req->setStatus($_GET["status"]);
  $status = $req->getStatus();
  $id = $_GET["id"];
  $query = <<<_END
    UPDATE
    requests
    SET
    status =
    "$status"
    WHERE id =
    $id
  _END;

  $result = $connection->query($query);
  if ($result) {
    updateFriends($connection);
  return true;
  }else {
    die;
  }
}

function getReqbyId($connection , $id)
{
  //An input shouldn't be escaped twice ($uName)
  $query = "select sender,receiver,status from requests where id = \"$id\"";
  $result = $connection->query($query);

  if ($result->num_rows != 0) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    return new Request($row["sender"] , $row["receiver"] , $row["status"] , $id);
  }else {
    return false;
  }
}

function updateFriends($connection)
{
  $query = "select * from requests where status = \"accept\"";
  $reqSet = $connection->query($query);
  $noReqs = $reqSet->num_rows;

  $query = "select * from friends";
  $friSet = $connection->query($query);
  $noFris = $friSet->num_rows;
  $friRows = [];
  for ($i=0; $i < $noFris; $i++) {
    $row = $friSet->fetch_array(MYSQLI_ASSOC);
    $friRows[] = new FriendshipRow($row["first"] , $row["second"]);
  }

  for ($i=0; $i < $noReqs; $i++) {
    $row = $reqSet->fetch_array(MYSQLI_ASSOC);
    $first = $row["sender"];
    $second = $row["receiver"];
    $doesExist = false;
    foreach ($friRows as $k) {
      if ($k->sender == $first && $k->receiver == $second) {
        $doesExist = true;
        break;
      }
    }
    if (!$doesExist) {
      $query=<<<_END
        insert into
        friends (
          first ,
          second
        )
        values (
          $first ,
          $second
        )
      _END;
      $result = $connection->query($query);
      if (!$result) {
        die;
      }
    }
  }

}

function sortPosts($posts){
  do {
    $swaped = false;
    for ($i=0; $i < sizeof($posts)-1; $i++) {
      if (strtotime($posts[$i]->getD()) < strtotime($posts[$i+1]->getD()) ) {
        $swaped = true;
        $temp = $posts[$i];
        $posts[$i] = $posts[$i+1];
        $posts[$i+1] = $temp;
      }
    }
  } while ($swaped);
  return $posts;
}

function getPostbyId($connection , $id)
{
  $query = "select text from posts where id = \"$id\"";
  $result = $connection->query($query);
  if ($result->num_rows != 0) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    return new Post(null , $row["text"] , null , null , $id);
  }
}

function updatePost($connection , $p)
{
  if (isset($p["saveChanges"]) && $p["saveChanges"] === "Save Changes") {
    $text = $connection->real_escape_string($p["text"]);
    $id = $p["id"];
    $query = "update posts set text=\"{$text}\" where id =$id";
    $connection->query($query);
  }elseif (isset($p["delete"]) && $p["delete"] === "Delete Post") {
    $id = $p["id"];
    $query = "delete from posts where id = $id";
    $connection->query($query);
  }
 }

 ?>

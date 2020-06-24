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

function addUser($p , $f)
{
  $connection = connectDatabase();
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
    $result->close();
    $connection->close();
    die('database query failed');
  } else {
    //$result->close();
    $connection->close();
    return true;
  }
}

function savePost($p , $f)
{
  $connection = connectDatabase();
  $text = $connection->real_escape_string($p["text"]);
  $path = savePostImage($f);
  $photo = $connection->real_escape_string($path);
  $uName = $connection->real_escape_string($_SESSION["username"]);
  $userId = getUserId($uName);

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
    $connection->close();
    die('database query failed');
  } else {
    $result->close();
    $connection->close();
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

function checkAuth($p)
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

function getFirends($uName)
{
  $connection = connectDatabase();
  $uName = $connection->real_escape_string($uName);
  $userId = getUserId($uName);
  $friends = [];
  $query = "select second from friends where first = \"$userId\"";
  $result = $connection->query($query);
  for ($i = 0 ; $i<$result->num_rows ; ++$i) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $friends[] = $row["second"];
  }
  $result->close();
  $query = "select first from friends where second = \"$userId\"";
  $result = $connection->query($query);
  for ($i = 0 ; $i<$result->num_rows ; ++$i) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $friends[] = $row["first"];
  }
  $result->close();
  $connection->close();
  return $friends;
}

function getReqs($uName)
{
  $connection = connectDatabase();
  $uName = $connection->real_escape_string($uName);
  $userId = getUserId($uName);
  $reqs = [];
  $query = "select id,sender,status from requests where receiver = \"$userId\"";
  $result = $connection->query($query);
  for ($i = 0 ; $i<$result->num_rows ; ++$i) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $reqs[] = new Request(getuName($row["sender"]) , $uName , $row["status"] , $row["id"]) ;
  }
  $result->close();
  $connection->close();
  return $reqs;
}

function getPosts($friends)
{
  $posts = [];
  if (count($friends) == 0) {
    return $posts;
  }
  $connection = connectDatabase();
  foreach ($friends as $f) {
    $query = "select id,text,dateCreated,imgPath from posts where 	userId = $f";
    $result = $connection->query($query);

    for ($i = 0 ; $i<$result->num_rows ; ++$i) {
      $row = $result->fetch_array(MYSQLI_ASSOC);
      $posts[] = new Post($row["imgPath"] , $row["text"] , getuName($f) , $row["dateCreated"] , $row["id"]);
    }
  }
  $result->close();
  $connection->close();
  return sortPosts($posts);
}

function getUserId($uName)
{
  $connection = connectDatabase();
  //An input shouldn't be escaped twice ($uName)
  $query = "select id from users where username = \"$uName\" limit 1";
  $result = $connection->query($query);

  if ($result->num_rows != 0) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $result->close();
    $connection->close();
    return $row["id"];
  }else {
    $result->close();
    $connection->close();
    return false;
  }
}

function getuName($uId)
{
  $connection = connectDatabase();
  //An input shouldn't be escaped twice ($uName)
  $query = "select username from users where id = \"$uId\" limit 1";
  $result = $connection->query($query);
  if ($result->num_rows != 0) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $result->close();
    $connection->close();
    return $row["username"];
  }else {
    $result->close();
    $connection->close();
    return false;
  }
}

function searchUsers($p)
{
  $connection = connectDatabase();
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
  $result->close();
  $connection->close();
  return $answers;
}

function isFriend($u1 , $u2)
{
  $friendship = false;
  $friends = getFirends($u1);
  $ui2 = getUserId($u2);

  foreach ($friends as $f) {
    if ($f == $ui2) {
      $friendship = true;
      break;
    }
  }
  return $friendship;
}

function performRequest($sender , $receiver)
{
  $connection = connectDatabase();
  $sender = getUserId($sender);
  $receiver = getUserId($receiver);
  if (!hasRequested($sender , $receiver)) {
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
    if (!is_bool($result)){
      $result->close();
    }
  }
  $connection->close();
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

function handleReq()
{
  $connection = connectDatabase();
  $req = getReqbyId($_GET["id"]);
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
    updateFriends();
    if (!is_bool($result)) {
      $result->close();
    }
    $connection->close();
    return true;
  }else {
    $result->close();
    $connection->close();
    die;
  }
}

function getReqbyId($id)
{
  $connection = connectDatabase();
  //An input shouldn't be escaped twice ($uName)
  $query = "select sender,receiver,status from requests where id = \"$id\"";
  $result = $connection->query($query);

  if ($result->num_rows != 0) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $result->close();
    $connection->close();
    return new Request($row["sender"] , $row["receiver"] , $row["status"] , $id);
  }else {
    $result->close();
    $connection->close();
    return false;
  }
}

function updateFriends()
{
  $connection = connectDatabase();
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
        $reqset->close();
        $friSet->close();
        $connection->close();
        die;
      }
    }
  }
  $reqSet->close();
  $friSet->close();
  $connection->close();
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

function getPostbyId($id)
{
  $connection = connectDatabase();
  $query = "select text from posts where id = \"$id\"";
  $result = $connection->query($query);
  if ($result->num_rows != 0) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $result->close();
    $connection->close();
    return new Post(null , $row["text"] , null , null , $id);
  }
}

function updatePost($p)
{
  $connection = connectDatabase();
  if (isset($p["saveChanges"]) && $p["saveChanges"] === "Save Changes") {
    $text = $connection->real_escape_string($p["text"]);
    $id = $p["id"];
    $query = "update posts set text=\"{$text}\" where id =$id";
    $result = $connection->query($query);
  }elseif (isset($p["delete"]) && $p["delete"] === "Delete Post") {
    $id = $p["id"];
    $query = "delete from posts where id = $id";
    $result = $connection->query($query);
  }
  $result->close();
  $connection->close();
}

function hasRequested($sender , $receiver)
{
  $connection = connectDatabase();
  $sender = getUserId($sender);
  $receiver = getUserId($receiver);
  $query = "select count(*) from requests WHERE sender = \"$sender\" and receiver = \"$receiver\" ";
  $result = $connection->query($query);
  $count = $result->fetch_array(MYSQLI_ASSOC);
  $result->close();
  $connection->close();

  if ($count["count(*)"] != "0") {
    return true;
  }else {
    return false;
  }
}

function getProfile($uName)
{
  $conn = connectDatabase();
  $uName = $conn->real_escape_string($uName);
  $query = "select profilephoto from users where username = '$uName' limit 1";
  $result = $conn->query($query);
  $row = $result->fetch_array(MYSQLI_ASSOC);
  $result->close();
  $conn->close();
  return $row['profilephoto'];
}

function getAllPost()
{
  $posts = [];

  $connection = connectDatabase();
  $query = "select id,text,dateCreated,imgPath,userId from posts";
  $result = $connection->query($query);
  for ($i = 0 ; $i<$result->num_rows ; ++$i) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $posts[] = new Post($row["imgPath"] , $row["text"] , getuName($row['userId']) , $row["dateCreated"] , $row["id"]);
  }
  $result->close();
  $connection->close();
  return sortPosts($posts);
}

?>

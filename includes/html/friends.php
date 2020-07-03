<?php
$this->extend('layout');

$this->block('title');
  echo $uName;
$this->endblock();

$this->block("friends");
  echo "<h1> Friends </h1>\n <ul class=\"w3-ul\">";

  if ($friends) {
    foreach ($friends as $f) {
      $fName = getuName( $f);
      echo "<li>$fName</li>";
    }
  }else {
    echo "<li>You have no friends yet.</li>";
  }
  echo "</ul>";
$this->endblock();

$this->block("requests");
   echo "<h2> Friendship requests </h2>";
   if (sizeof($requests) != 0) {
     foreach ($requests as $f) {
       if ($f->getStatus() == "pending") {
         $sender = $f->getSender();
         $reqId = $f->getId();
         echo <<<_END
         <p> $sender has requested to follow you
         <a href="/friends?res=true&id=$reqId&status=accept"> accept</a>
         <a href="/friends?res=true&id=$reqId&status=reject">reject</a></p>
         _END;
       }
     }
   }else {
     echo "You have no requests.";
   }
$this->endblock();

$this->block("answers");
  if ($answers == "nothing") {
    echo "nothing found";
  }elseif($answers == "noSearch"){
    echo "<br>";
  }else {
    echo "<ul class=\"w3-ul\">";
    foreach ($answers as $f) {
      echo "<li><a href=\"/profile?uName=$f\">$f</a> ";
      if (isFriend($uName , $f) && $f != $uName) {
        echo "friend</li>";
      }elseif (hasRequested($uName , $f) && $f != $uName) {

        echo "requested</li>";
      }else {
        //dumpInfo(hasRequested($f , $uName));
        echo "<a href=\"/searchPeople?req=true&sender=$uName&receiver=$f\"> request </a></li>";
      }
    }
  }
$this->endblock();

 ?>

<div id="mainNav" class="w3-bar w3-black">
  <img class="w3-bar-item" src="<?php echo getProfile($_SESSION['username']); ?>" width="50px" height="50px">
  <a href="/home" class="w3-bar-item w3-button w3-padding-large">Home</a>
  <a href="/explore" class="w3-bar-item w3-button w3-padding-large">Explore</a>
  <a href="/profile" class="w3-bar-item w3-button w3-padding-large">Profile</a>
  <a href="/new" class="w3-bar-item w3-button w3-padding-large">New Post</a>
  <a href="/logout" class="w3-bar-item w3-button w3-padding-large w3-right w3-red ">Logout</a>
</div>
<div class="w3-container">
<?php echo $this["friends"]; ?>
<br>
<hr class="w3-clear">
<?php echo $this["requests"]; ?>
<br>
<hr class="w3-clear">
<form method="post" action="/searchPeople">
  <input type="text" name="searchText" placeholder="search people here">
  <button type="submit" value="search" name="searchButton" required="">search</button>
</form>

<?php echo $this["answers"]; ?>
</div>

<?php
$this->extend('layout');

$this->block('title');
echo $uName;
$this->endblock();
?>
<?php
$this->block("friends");
echo "<p> Friends </p>\n <ul>";

if ($friends) {
  foreach ($friends as $f) {
    $fName = getuName($connection , $f);
    echo "<li>$fName</li>";
  }
}else {
  echo "<li>You have no friends yet.</li>";
}
echo "</ul>";

$this->endblock();
 ?>

 <?php
 $this->block("requests");
 echo "<p> Friendship requests </p>";
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
  ?>
<?php
$this->block("answers");
if ($answers == "nothing") {
  echo "nothing found";
}elseif($answers == "noSearch"){
  echo "<br>";
}else {
  foreach ($answers as $f) {
    echo "<p>$f ";
    if (isFriend($connection , $uName , $f) && $f != $uName) {
      echo "requested</p>";
    }else {
      echo "<a href=\"/searchPeople?req=true&sender=$uName&receiver=$f\"> request </a></p>";
    }
  }
}
$this->endblock();

 ?>


<div id="mainNav" class="w3-bar w3-black">
  <img class="w3-bar-item" src="../myNest/profiles/learnphp.png" width="50px" height="50px">
  <a href="/home" class="w3-bar-item w3-button w3-padding-large">Home</a>
  <a href="" class="w3-bar-item w3-button w3-padding-large">Explore</a>
  <a href="" class="w3-bar-item w3-button w3-padding-large">Profile</a>
  <a href="/new" class="w3-bar-item w3-button w3-padding-large">New Post</a>
</div>
<?php echo $this["friends"]; ?>
<br>
<?php echo $this["requests"]; ?>
<br>
<form method="post" action="/searchPeople">
  <input type="text" name="searchText" placeholder="search people here">
  <button type="submit" value="search" name="searchButton" required="">search</button>
</form>

<?php echo $this["answers"]; ?>

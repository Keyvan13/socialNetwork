<?php $this->extend('layout') ?>
<?php
$this->block('title');
 echo $uName;
$this->endblock();

$this->block('posts');
 if ($posts) {
   foreach ($posts as $p ) {
     echo $p->getPhtml();
     echo "<br>";
   }
 } else {
   echo "You have posted no content yet";
 }
$this->endblock();
?>

<div id="mainNav" class="w3-bar w3-black">
  <img class="w3-bar-item" src="./myNest/profiles/learnphp.png" width="50px" height="50px">
  <a href="" class="w3-bar-item w3-button w3-padding-large">Explore</a>
  <a href="/friends" class="w3-bar-item w3-button w3-padding-large">Friends</a>
  <a href="" class="w3-bar-item w3-button w3-padding-large">Profile</a>
</div>
<?php echo $this["posts"]; ?>

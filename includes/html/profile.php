<?php
$this->extend('layout');

$this->block('title');
 echo $uName;
$this->endblock();

$this->block('posts');
  if ($editable) {
    if ($posts) {
      foreach ($posts as $p ) {
        echo $p->getPhtml();
        echo "<br>";
      }
    } else {
      echo "You have posted no content yet";
    }
  } else {
    if ($posts) {
      foreach ($posts as $p ) {
        echo $p->gethtml();
        echo "<br>";
      }
    } else {
      echo "$owner have posted no content yet";
    }
  }
$this->endblock();
?>

<div id="mainNav" class="w3-bar w3-black">
  <img class="w3-bar-item" src="<?php echo getProfile($_SESSION['username']); ?>" width="50px" height="50px">
  <a href="/home" class="w3-bar-item w3-button w3-padding-large">Home</a>
  <a href="/expolre" class="w3-bar-item w3-button w3-padding-large">Explore</a>
  <a href="/friends" class="w3-bar-item w3-button w3-padding-large">Friends</a>
  <a href="/profile" class="w3-bar-item w3-button w3-padding-large">Profile</a>
  <a href="/logout" class="w3-bar-item w3-button w3-padding-large w3-right w3-red ">Logout</a>
</div>
<?php echo $this["posts"]; ?>

<?php $this->extend('layout');
$this->block('title');
 echo $uName;
$this->endblock();

$this->block("posts") ;
  if ($posts) {
    foreach ($posts as $p ) {
      echo $p->getHtml();
      echo "<br>";
    }
  } else {
    echo "Your friends have not share any posts yet.<br> Try exploring other people posts <a href=\"/explore\">here</a>";
  }
$this->endblock() ?>

<div id="mainNav" class="w3-bar w3-black">
  <img class="w3-bar-item" src="../myNest/profiles/learnphp.png" width="50px" height="50px">
  <a href="/explore" class="w3-bar-item w3-button w3-padding-large">Explore</a>
  <a href="/friends" class="w3-bar-item w3-button w3-padding-large">Friends</a>
  <a href="/profile" class="w3-bar-item w3-button w3-padding-large">Profile</a>
  <a href="/new" class="w3-bar-item w3-button w3-padding-large">New Post</a>
</div>

<div>
<?php echo $this["posts"]; ?>
</div>
<footer class="w3-container w3-theme-d3 w3-padding-16">
	<h5><a href="/logout">Log Out</a></h5>
</footer>
<script>
// Accordion
function myFunction(id) {
	var x = document.getElementById(id);
	if (x.className.indexOf("w3-show") == -1) {
		x.className += " w3-show";
		x.previousElementSibling.className += " w3-theme-d1";
	} else {
		x.className = x.className.replace("w3-show", "");
		x.previousElementSibling.className =
		x.previousElementSibling.className.replace(" w3-theme-d1", "");
	}
}

// Used to toggle the menu on smaller screens when clicking on the menu button
function openNav() {
	var x = document.getElementById("navDemo");
	if (x.className.indexOf("w3-show") == -1) {
		x.className += " w3-show";
	} else {
		x.className = x.className.replace(" w3-show", "");
	}
}
</script>

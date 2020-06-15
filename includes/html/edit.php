<?php $this->extend('layout') ?>
<?php $this->block('title', 'My Nest') ?>
<div id="mainNav" class="w3-bar w3-black">
  <img class="w3-bar-item" src="./myNest/profiles/learnphp.png" width="50px" height="50px">
  <a href="" class="w3-bar-item w3-button w3-padding-large">Explore</a>
  <a href="" class="w3-bar-item w3-button w3-padding-large">Friends</a>
  <a href="" class="w3-bar-item w3-button w3-padding-large">Profile</a>
</div>
<form class="w3-container " action="/post" method="post" style="resize: none;"  enctype='multipart/form-data'>
  <textarea rows="10" class="w3-margin-top w3-container w3-card w3-light-blue" style="width:100%; resize:none;" name="text" ></textarea>
  <input class="w3-input" type="file" style="width:90%" name="photo">
  <label>Choose a photo</label>
  <input class="w3-button  w3-section w3-aqua w3-ripple" type="submit" name="saveChanges" value="Save Changes" >
  <input class="w3-button  w3-section w3-deep-orange w3-ripple" type="submit" name="delete" value="Delete Note" >
</form>
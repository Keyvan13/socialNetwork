<?php $this->extend('layout') ?>
<?php $this->block('title', 'My Nest') ?>
<header class="w3-container w3-deep-purple">
  <h1>Sign Up</h1>
</header>

<div class="w3-display-container w3-auto" , style="height: 40em;">

<form class="w3-container w3-display-middle w3-half w3-card-4" action="sign-up" method="post" enctype='multipart/form-data'>

<p>
<input class="w3-input" type="text" style="width:90%" required="" name="username">
<label>Username</label></p>
<p>
<input class="w3-input" type="text" style="width:90%" required="" name="email">
<label>Email</label></p>
<p>
<input class="w3-input" type="password" style="width:90%" required="" name="pass">
<label>Password</label></p>
<p>
<input class="w3-input" type="password" style="width:90%"  name="cpass">
<label>Confirm Password</label></p>
<p>
<input class="w3-radio" type="radio" name="gender" value="male" checked="checked">
<label>Male</label></p>

<p>
<input class="w3-radio" type="radio" name="gender" value="female">
<label>Female</label></p>

<p>
<input class="w3-input" type="file" style="width:90%" name="photo">
<label>Choose a profile photo</label></p>

<p>
<button class="w3-button w3-section w3-teal w3-ripple"> Create Account </button></p>

</form>

</div>

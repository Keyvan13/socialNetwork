<?php
$this->extend('layout');
$this->block('title', 'My Nest');
?>
<header class="w3-container w3-deep-purple">
  <h1>Login</h1>
</header>

<div class="w3-display-container w3-auto" , style="height: 20em;">

<form class="w3-container w3-display-middle w3-half w3-card-4" action="login" method="post" >

<p>
<input class="w3-input" type="text" style="width:90%" required="" name="username">
<label>Username</label></p>
<p>
<input class="w3-input" type="password" style="width:90%" required="" name="pass">
<label>Password</label></p>

<p>
<button class="w3-button w3-section w3-teal w3-ripple"> Log in </button></p>

</form>
<p>
<a class="w3-display-bottomright w3-button w3-section w3-teal w3-ripple" href="sign-up"> Create Account </a></p>

</div>

<?php
	session_start();

	include_once("connection.php");

	if(isset($_SESSION['login_status']))
	{
		header("Location: home.php");
	}

?>

<html>
<head>
	<title>Login and Registration</title>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
	<link rel="stylesheet" href="css/foundation.css">

	<script type="text/javascript">
	$(document).ready(function(){
		$("#register").on("submit", function(){
				var form = $(this);
				$.post(form.attr("action"), form.serialize(), function(html){

					// console.log(html);
					$("#errors").html(html);
				}, "json");
		return false;
		});

		$("#login").on("submit", function(){
				var form = $(this);
				$.post(form.attr("action"), form.serialize(), function(html){

					// console.log(html);
					$("#errors").html(html);
				}, "json");
		return false;
		});

	});
	</script>

</head>
<div class="wrapper">
<body>

<div id="errors"></div>
	<h1>Login</h1>

<form id="login" action="process.php" method="post">
		<input type="hidden" name="action" value="login" />
		<input type="text" name="email" placeholder="Email address" />
		<input type="password" name="password" placeholder="Password" />
		<input type="submit" value="Login" />
	</form>

	<h1>Registration</h1>

	<form id="register" action="process.php" method="post">
		<input type="hidden" name="action" value="register" />
		<input type="text" name="first_name" placeholder="First Name" /><br />
		<input type="text" name="last_name" placeholder="Last Name" /><br />
		<input type="text" name="alias" placeholder="Alias/Nickname" /><br />
		<input type="text" name="email" placeholder="Email address" /><br />
		<input type="password" name="password" placeholder="Password" /><br />
		<input type="password" name="confirm_password" placeholder="Confirm Password" /><br />
		<input type="submit" value="Register" />
	</form>

</body>
</div>
</html>
<!-- File name login.php -->
<!DOCTYPE html>
<html>
<head>
<title>Quotation Service</title>
<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<?php
if(!isset ($_SESSION)){
	session_start ();
}
?>
<h2> Login</h2>
<div class = "container" >
<form action = 'controller.php' method = 'POST' >
	Username : <input type = "text" name = "LoginName"  maxlength = "25" required>
	<br>
	Password : <input type = "password" name = "LoginPass"  size = "25" maxlength = "25"required>
	<br>
	<input type = "submit"  name = "SubmitLogin" value = "Submit" size = "20">
	<br>
</form>
</div>
<?php 
	//If there's an error with the user logging in, display the error.
	if(isset($_SESSION['loginError'])){
		echo $_SESSION['loginError'];
		unset($_SESSION['loginError']);
	}

?>
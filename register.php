<!-- File name register.php -->
<!DOCTYPE html>
<html>
<head>
<title>Quotation Service</title>
<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>


<?php
if (! isset ( $_SESSION )) {
	session_start ();
}
?>
<h2> Register New Account</h2>
<div class="container">
		<form action='controller.php' method='POST'>
				Username : <input type="text" name="RegName" pattern=".{4,}"maxlength="25" required> 
				<br> 
				Password : <input type="password" name="RegPass" pattern=".{6,}" size="25" maxlength="25" required> 
				<br> 
				<input type="submit" name="SubmitReg" value="Submit" size="20"> 
				<br>
		</form>
	</div>
	
<?php
	//If there's a error in the user's attempt to register an account, display the error.
	// (If the username already exists in the database)
if (isset ( $_SESSION ['RegError'] )) {
	echo $_SESSION ['RegError'];
	unset ( $_SESSION ['RegError'] );
}

?>
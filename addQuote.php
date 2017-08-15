<!-- File name addQuote.php -->
<!DOCTYPE html>
<html>
<head>
<title>Quotation Service</title>
<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<h2> Add a Quote!</h2>
<div class = "container">

<form action = 'controller.php' method = 'POST' >
	<label>Quote </label>&nbsp &nbsp<textarea  rows = "4" cols = "20" name = "Quote"  maxlength = "1000" required></textarea>
	<br>
	<label>Author </label> <input type = "text" name = "Author" size = "25" maxlength = "45" required>
	<br>
	<input type = "submit"  name = "SubmitQuote" value = "Submit" size = "20">
</form>
</div>
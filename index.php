<!-- File name index.php -->
<!DOCTYPE html>
<html>
<head>
<title>Quotation Service</title>
<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>

<?php
//require_once 'DatabaseAdaptor.php';
require_once 'controller.php';

if(!isset($_SESSION))
{
	session_start();
} 
/*
 * Variable to mark the quotes with a unique "id" so we can up/downvote and flag them
 */
if(!isset ($_SESSION['id'])){
	$_SESSION['id']=0;
}

?>

<h1>Quotes</h1>

	<ul>
		<li><a href="register.php">Register</a></li>
		<li><a href="login.php">Login</a></li>
		<li><a href="addQuote.php">Add Quote</a></li>
	</ul>
	<div id = "quoteArea"></div>
 

  <?php
 	//Get all of the quotes from the database, sorted by "votes"
  //  $arrayOfQuotes = $theDBA->getQuotesAsArray();
  $arrayOfQuotes = getQuotes();
    //Start a form that calls on our controller file
    echo "<form action = 'controller.php' method = 'POST' >";
    //If the user is logged in, show the unflag and logout buttons
 	if(isset ($_SESSION['loggedIn'])){
 		echo "<button type = 'submit' name = 'Unflag' > Unflag All </button>";
 		echo "<button type = 'submit' name = 'Logout' > Logout </button>";
 	}
 	//Store length of array as variable (rather than call the function every iteration of loop)
	$length = count ( $arrayOfQuotes );
	
	//Use for loop to show all of our quotes on page.
	for($indx = 0; $indx < $length; $indx ++) {
		//If the quote isn't flagged by user, we'll show it.
		if ($arrayOfQuotes [$indx] ['flagged'] === "FALSE") {
			//Show quote with quotations marks, with author listed underneath
 		echo("<div class = 'quote'> <q> " . $arrayOfQuotes[$indx]['quote'] . "</q><br>"
 				. " --" . $arrayOfQuotes[$indx]['author'] . "<br>"
 				//Button to downvote the quote followed by the current value of votes on quote
 				. "<button type = 'submit' name = 'DownVote' value = '".$arrayOfQuotes[$indx]['id']."'"
 						.$arrayOfQuotes[$indx]['id'] . ")> - </button> &nbsp". $arrayOfQuotes[$indx]['votes']
 						//Button to upvote the quote
 				." &nbsp <button type = 'submit' name = 'UpVote' value = '".$arrayOfQuotes[$indx]['id']."'"
 				.$arrayOfQuotes[$indx]['id'] . ")> + </button> "
					."&nbsp &nbsp &nbsp &nbsp &nbsp"
 				//Button to flag the quote so it disappears from page
  				 . "<button type = 'submit' name = 'flag' value = '".$arrayOfQuotes[$indx]['id'] ."'"
  				 		.">flag</button>"
 				 . "</div> <br>");
		}
	}
		echo "</form>";
	?>
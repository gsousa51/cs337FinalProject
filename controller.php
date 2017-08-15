<?php
	require_once 'DatabaseAdaptor.php';

	function getQuotes(){
		//Need to create an object for this function.
		$DBA = new DatabaseAdaptor();
		return $DBA->getQuotesAsArray();
	}
	if (! isset ( $_SESSION )) {
		session_start ();
	}
	//If user clicked flag on one of the quotes	
	if (isset ( $_POST ['flag'] )) {
		//Flag the quote in the database
		$theDBA->flagQuote ( $_POST ['flag'] );
		unset ( $_POST ['flag'] );
		header ( "location: index.php" );
	} 
	//If user is submitting a quote
	else if (isset ( $_POST ['SubmitQuote'] )) {
		//Check to ensure they entered text into both fields
		if ((isset ( $_POST ['Quote'] ) && isset ( $_POST ['Author'] ))) {
			//Add it to the database
			$theDBA->addQuote ( $_POST ['Quote'], $_POST ['Author'] );
			header ( "location: index.php" );
		}
	} 
	//If user "upvoted a quote"
	else if (isset ( $_POST ['UpVote'] )) {
		$theDBA->upVote ( $_POST ['UpVote'] );
		header ( "location: index.php" );
	} 
	//If user "downvoted" a quote
	else if (isset ( $_POST ['DownVote'] )) {
		$theDBA->downVote ( $_POST ['DownVote'] );
		header ( "location: index.php" );
	}

	//User is trying to register a new account
	else if(isset ($_POST['SubmitReg'])){
		$pwd = password_hash($_POST['RegPass'], PASSWORD_DEFAULT);
		echo $_POST['RegPass'] . " " .$_POST['RegName'] . " " . $pwd;
		if($theDBA->registerUser($_POST['RegName'], $pwd)=== "true"){
			header ( "location: index.php" );		
		}
		else{
			$_SESSION['RegError'] = "Username Already In Use";
			header ( "location: register.php" );
		}
	}
	else if(isset ($_POST['SubmitLogin'])){
		if($theDBA->login($_POST['LoginName'], $_POST['LoginPass'])=== "true"){
			header ( "location: index.php" );
		}
		else{
			header ( "location: login.php" );
		}
	}

	else if (isset ($_POST['Unflag'])){
		$theDBA->unflagAll();
		header ( "location: index.php" );
	}
	else if(isset ($_POST['Logout'])){
		unset($_SESSION['loggedIn']);
		header ( "location: index.php" );
	}

	?>
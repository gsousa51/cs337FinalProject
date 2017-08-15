<?php
if (! isset ( $_SESSION )) {
	session_start ();
}

class DatabaseAdaptor {
	private $DB; // The instance variable used in every function
	
	// Connect to an existing data based named 'quotationsdump'
	public function __construct() {
		$db = 'mysql:dbname=quotationsDump;host=127.0.0.1;charset=utf8';
		$user = 'root';
		$password = ""; // an empty string
		try {
			$this->DB = new PDO ( $db, $user, $password );
			$this->DB->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		} catch ( PDOException $e ) {
			echo ('Error establishing Connection');
			exit ();
		}
		//Clear the table at the start of a new session.
		//This allows us to have unique ids for our quotations
		if (! isset ( $_SESSION ['cleared'] )) {
			$this->clearTables ();
			$_SESSION ['cleared'] = true;
		}
		//Build new tables to store the quotations and accounts
		$this->buildTables ();
	}

	//Clears the quotations and accounts tables
	private function clearTables() {
		try {
			$stmt = $this->DB->prepare ( "DROP TABLE quotations;" );
			$stmt->execute ();
			$stmt = $this->DB->prepare ( "DROP TABLE accounts;" );
			$stmt->execute ();
			return $stmt->fetchAll ( PDO::FETCH_ASSOC );
		} catch ( PDOException $p ) {
		}
	}
	
	//Builds the tables to store accounts and quotations
	private function buildTables() {
		try {
			$stmt = $this->DB->prepare ( "CREATE TABLE accounts (id varchar(25), 
			password varchar(400))" );
			$stmt->execute ();
			$stmt = $this->DB->prepare ( "CREATE TABLE quotations (quote varchar(1000), 
		author varchar(45), flagged varchar(5), votes integer, id integer)" );
			$stmt->execute ();
		} catch ( PDOException $e ) {
		}
	}
	
	//Private helper function to get all accounts whose username's match the name given
	private function getAccount($name) {
		$stmt = $this->DB->prepare ( "SELECT * FROM accounts WHERE id= '" . "$name" . "';" );
		$stmt->execute ();
		return $stmt->fetchAll ( PDO::FETCH_ASSOC );
	}
	
	//Returns all of the quotes as an array descendingly sorted by the quotes' votes value
	public function getQuotesAsArray() {
		//Get all of the quotes as an array
		$quotes = $this->getQuotes ();
		//Sort them by their votes value
		usort ( $quotes, array (
				'DatabaseAdaptor',
				'cmp' 
		) );
		//Return the sorted array
		return $quotes;
	}
	//Method compares $a and $b based on their "votes" value
	private static function cmp($a, $b) {
		if ($a ['votes'] == $b ['votes']) {
			return 0;
		}
		return ($a ['votes'] > $b ['votes']) ? - 1 : 1;
	}
	//Private helper method that returns all quotes as an array
	private function getQuotes() {
		$stmt = $this->DB->prepare ( "SELECT * FROM quotations;" );
		$stmt->execute ();
		return $stmt->fetchAll ( PDO::FETCH_ASSOC );
	}
	// Returns true if password and username match an existing account
	// Returns false if either password or username are incorrect.
	public function login($name, $password) {
		$arr = $this->getAccount($name);
		$length = count ( $arr );

		for($indx = 0; $indx < $length; $indx ++) {
			$hash = $arr[$indx]['password'];
			if ($arr [$indx] ['id'] === $name && 
				password_verify($password, $hash)===true){
				$_SESSION['loggedIn'] = true;
				return "true";
			}
		}
		$_SESSION['loginError'] = "Username and/or Password are incorrect!";
		return "false";
	}
	//Method attempts to register a new account with name and password given
	//Return values true if account is added | False if an account with that username already exists
	public function registerUser($name, $password) {
		//Get all accounts that contain the name given
		//(sql isn't case sensitive so we must check the array given)
		$arr = $this->getAccount ( $name );
		$length = count ( $arr );
		//check for a matching username
		for($indx = 0; $indx < $length; $indx ++) {
			//If an account already has the username given, we can't add another account. Return false.
			if ($arr [$indx] ['id'] === $name) {
				return false;
			}
		}
		//If we get here, no accounts exist with the username given.
		//Add the account to the accounts table
		$stmt = $this->DB->prepare ( "insert into accounts values ('" . $name . "', '" . $password . "');" );
		$stmt->execute ();
		return "true";
	}
	
	//Method is used to unflag all of the quotes in our table so they all show
	public function unflagAll() {
		$stmt = $this->DB->prepare ( "UPDATE quotations SET flagged = 'FALSE'" );
		$stmt->execute ();
	}
	
	//Parameter : Id of quote to flag
	//Purpose: Flag the quote whose id matches the id given.
	public function flagQuote($id) {
		$stmt = $this->DB->prepare ( "UPDATE quotations SET flagged = 'TRUE' WHERE
									  id = '" . $id . "';" );
		$stmt->execute ();
	}

/**
 * 
 * @param  String $quote : Quote given by user
 * @param String $author : Author of quote
 * @purpose : Add quote to our database
 */				 
	public function addQuote($quote, $author) {
		//Insert into table as ($quote,$author,Flag: FALSE , int id)
		$stmt = $this->DB->prepare ( "insert into quotations values ('" . $quote . "', '" . $author . "' 
			, 'FALSE' , 0 , " . $_SESSION ['id'] . ");" );
		$stmt->execute ();
		//Increment our id tracker variable
		$_SESSION ['id'] ++;
	}
	/**
	 *
	 * @param
	 *        	id : Id of quote to return
	 * @return array of size 1 which contains the quote desired
	 */
	private function getQuoteById($id) {
		$stmt = $this->DB->prepare ( "SELECT * FROM quotations WHERE id = '" . $id . "';" );
		$stmt->execute ();
		return $stmt->fetchAll ( PDO::FETCH_ASSOC );
	}
	/**
	 *
	 * @param
	 *        	id: The id of the quote to upvote
	 */
	public function upVote($id) {
		$quote = $this->getQuoteByID ( $id );
		$voteScore = $quote [0] ["votes"];
		$voteScore ++;
		$stmt = $this->DB->prepare ( "UPDATE quotations SET votes =" . $voteScore . " WHERE
									  id = '" . $id . "';" );
		$stmt->execute ();
	}
	
	/**
	 *
	 * @param
	 *        	id : The id of the quote to downvote
	 */
	public function downVote($id) {
		$quote = $this->getQuoteByID ( $id );
		$voteScore = $quote [0] ["votes"];
		$voteScore --;
		$stmt = $this->DB->prepare ( "UPDATE quotations SET votes =" . $voteScore . " WHERE
									  id = '" . $id . "';" );
		$stmt->execute ();
	}
}

$theDBA = new DatabaseAdaptor ();
 
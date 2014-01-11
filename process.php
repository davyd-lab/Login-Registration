<?php

include_once("connection.php");
include_once("class_lib.php");

session_start();

class Process {

	var $connection;

	public function __construct()
	{

		$this->connection = new Database();

		//see if the user wants to login
		if(isset($_POST['action']) and $_POST['action'] == "login")
		{
			$this->loginAction();
		}
		else if(isset($_POST['action']) and $_POST['action'] == "register")
		{
			$this->registerAction();
		}
		else if(isset($_POST['action']) and $_POST['action'] == "makeFriend" AND isset($_SESSION['logged_in']))
		{
			$this->makeFriend();
		}
		else if(isset($_POST['action']) and $_POST['action'] == "viewFriends")
		{
			$this->viewFriends();
		}
		else
		{
			//assume that the user wants to log off
			session_destroy();
			header("Location: index.php");
		}
	}

	private function makeFriend()
	{
		$friend = new Friend();
		$friend->makeFriend($_SESSION['user']['id'], $_POST['friend_id']);
		header("Location: home.php");
	}
	
	private function loginAction()
	{
		$errors = array();

		if(!(isset($_POST['email']) and filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)))
		{
			$errors[] = "email is not valid";
		}

		if(!(isset($_POST['password']) and strlen($_POST['password'])>=6))
		{
			$errors[] = "please double check your password (length must be greater than 6)";
		}

		//see if there are errors
		if(count($errors) > 0)
		{
			$data['login_errors'] = $errors;
			
			$html ="";
			//create an html string with errors to pass back to index page
			foreach($data['login_errors'] as $error){
					$html .= "<p>" . $error . "</p>";
				}
			echo json_encode($html);
		}
		else
		{
			//check if the email and the password is valid
			$query = "SELECT * FROM users WHERE email = '{$_POST['email']}' AND password ='".md5($_POST['password'])."'";
			$users = $this->connection->fetch_all($query);
			
			if(count($users)>0)
			{
				$_SESSION['logged_in'] = true;
				$_SESSION['user']['first_name'] = $users[0]['first_name'];
				$_SESSION['user']['last_name'] = $users[0]['last_name'];
				$_SESSION['user']['email'] = $users[0]['email'];
				$_SESSION['user']['id'] = $users[0]['id'];

				//if the login is successful pass to the homepage JS that will redirect to home page
				$html ='<script>location.href="home.php";</script>';
				echo json_encode($html);
			}
			else
			{
				$html .= "<p>Invalid login information</p>";
				echo json_encode($html);
			}
		}
	}

	private function registerAction()
	{
		$errors = array();
		//let's see if the first_name is a string
		if(!(isset($_POST['first_name']) and is_string($_POST['first_name']) and strlen($_POST['first_name'])>0))
		{
			$errors[] = "first name is not valid!";
		}

		//let's see if the last_name is a string
		if(!(isset($_POST['last_name']) and is_string($_POST['last_name']) and strlen($_POST['last_name'])>0))
		{
			$errors[] = "last name is not valid!";
		}

		if(!(isset($_POST['email']) and filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)))
		{
			$errors[] = "email is not valid";
		}

		if(!(isset($_POST['password']) and strlen($_POST['password'])>=6))
		{
			$errors[] = "please double check your password (length must be greater than 6)";
		}

		if(!(isset($_POST['confirm_password']) and isset($_POST['password']) and $_POST['password'] == $_POST['confirm_password']))
		{
			$errors[] = "please confirm your password";
		}

		if(count($errors)>0)
		{
			$data['messages'] = $errors;

			$html ="";
			
			//create an html string with errors to pass back to index page
			foreach($data['messages'] as $error){
					$html .= "<p>" . $error . "</p>";
				}
			echo json_encode($html);
		}
		else
		{
			//see if the email address already is taken
			$query = "SELECT * FROM users WHERE email = '{$_POST['email']}'";
			$users = $this->connection->fetch_all($query);	

			//see if someone already registered with that email address
			if(count($users)>0)
			{
				$errors[] = "someone already registered with this email address";
				// var_dump($errors);
				$data['messages'] = $errors;

				// header("Location: index.php");
				$html ="";
				foreach($data['messages'] as $error){
					$html .= "<p>" . $error . "</p>";
				}
				// echo json_encode($data);
				echo json_encode($html);
			}
			else
			{
				$query = "INSERT INTO users (first_name, last_name, alias, email, password, created_at) VALUES ('{$_POST['first_name']}', '{$_POST['last_name']}', '{$_POST['alias']}', '{$_POST['email']}', '".md5($_POST['password'])."', NOW())";
				mysql_query($query);

				$html = "<p>User was successfully created!</p>";
				// header("Location: index.php");
				echo json_encode($html);
			}
		}
	}

	private function viewFriends()
	{

		$friendHomeInfo ="";
		$friendHomeTable ="";

		//Get info from db about selected user

		$queryUser = "SELECT alias, email FROM users WHERE id = '{$_POST['friendSelectedInfo']}'";

		$friendInfo = $this->connection->fetch_record($queryUser);

		//Get info from db about friends of selected use

		$queryUserFriends ="SELECT first_name, last_name, email, alias from users LEFT JOIN friends on  friends.friend_id = users.id WHERE user_id ='{$_POST['friendSelectedInfo']}'";
		$userFriends = $this->connection->fetch_all($queryUserFriends);

		$friendHomeInfo = "<p><strong>A.K.A.</strong>: ".  $friendInfo['alias'] . "</p>" .
				"<p><strong>Email</strong>: " . $friendInfo['email'] . "</p>";
				
		$friendHomeTable .="<table><tr><th>Name</th><th>Email</th><th>Alias</th></tr>";

		foreach ($userFriends as $friend){
			$friendHomeTable .="<tr><td>" . $friend['first_name'] . " " . $friend['last_name'] . "</td><td>" . $friend['email'] . "</td><td>" . $friend['alias'] . "<td><tr>";
		}	
		$friendHomeTable .= "</table>";

		$friendHomeInfo .= $friendHomeTable;
		// echo $friendHomeTable;
		// echo $homeHtml;
		echo json_encode($friendHomeInfo);
	}
}

$process = new Process();

?>
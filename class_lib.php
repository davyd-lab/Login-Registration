<?php
	include_once("connection.php");

	
	class Friend extends Database{


		function getFriends($user_id)
		{
			if(is_numeric($user_id))
			{
				$query = "SELECT users.id, users.first_name, users.last_name, users.email 
							FROM friends
							LEFT JOIN users ON users.id = friends.friend_id
							WHERE user_id = " .$user_id;
				return $this->fetch_all($query);
			}
		}

		//get all Users except the one specified in $_user_id
		function getAllUsers($user_id)
		{
			$query = "SELECT * FROM users WHERE id != " . $user_id;
			return $this->fetch_all($query);
		}

		function makeFriend($user_id, $friend_id)
		{
			if(is_numeric($user_id) AND is_numeric($friend_id))
			{
				$query = "INSERT INTO friends (user_id, friend_id) VALUES ({$user_id},{$friend_id})";
				mysql_query($query);
				
				$query = "INSERT INTO friends (user_id, friend_id) VALUES ({$friend_id},{$user_id})";
				mysql_query($query);
			}

		}
	}
?>
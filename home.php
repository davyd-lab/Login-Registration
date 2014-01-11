<?php
	include_once("class_lib.php");

	session_start();

	$list_of_friends = array();

	$friend = new Friend();
	$friends = $friend->getFriends($_SESSION['user']['id']);
	$users = $friend->getAllUsers($_SESSION['user']['id']);

	foreach($friends as $friend)
	{
		$list_of_friends[$friend['id']] = "true";
	}

	if(!isset($_SESSION['logged_in']))
	{
		header("Location: index.php");
	}
?>
<html>
<head>
	<title>Red Belt Home Page</title>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
	<link rel="stylesheet" href="css/foundation.css">

	<script type="text/javascript">
	$(document).ready(function(){
		$("#listUsers").on("submit", function(){
				var form = $(this);
				$.post(form.attr("action"), form.serialize(), function(friendHomeInfo){

					// console.log(html);
					$("#userInfo").html(friendHomeInfo);
				}, "json");
		return false;
		});
	});
	</script>
</head>
<div class="wrapper">
<body>
<div class="panel">

<h1>Welcome <?= $_SESSION['user']['first_name'] ." " . $_SESSION['user']['last_name'] ?>!</h1>
<a href="process.php">Log Off</a>
</div>

<h2>List Users</h2>

<form id="listUsers" action="process.php" method="POST">
	<select name="friendSelectedInfo">
<?php
	foreach($users as $user){  ?>
		
			<option value='<?= $user['id']?>'><?= $user['first_name'] ?> <?= $user['last_name'] ?></option>
			<?php } ?>
</select>
<input type="hidden" name="action" value="viewFriends" />
<input type="submit" value="View Friends">
</form>

<div id="userInfo"></div>


<h2>List of Friends</h2>
<table>
	<thead>
		<tr>
			<th>Name</th>
			<th>Email</th>
		</tr>
	<thead>
	<tbody>
<?php
	foreach($friends as $friend){ ?>		
		<tr>
			<td><?= $friend['first_name'] . " " . $friend['last_name'] ?></td>
			<td><?= $friend['email']?></td>
		</tr>
<?php
	}	?>
	</tbody>
</table>


<h2>List of All Users</h2>
<table>
	<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Email</th>
			<th>Action</th>
		</tr>
	<thead>
	<tbody>

<?php
	foreach($users as $user) { ?>
		<tr>
			<td><?= $user['id'] ?></td>
			<td><?= $user['first_name'] ?> <?= $user['last_name'] ?></td>
			<td><?= $user['email'] ?></td>
			<td>
<?php		
		if(isset($list_of_friends[$user['id']]))
			{
				echo "Friend";
			}
			else
			{ ?>
				<form action="process.php" method="post">
					<input type="hidden" name="action" value="makeFriend" />
					<input type="hidden" name="friend_id" value="<?= $user['id'] ?>" />
					<input type="submit" value="Add as friend" />
				</form>
<?php		}	?>
			</td>
		</tr>
<?php
	}	?>
	</tbody>
</table>
</div>

</body>
</html>

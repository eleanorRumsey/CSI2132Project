<?php
	session_start();
	$conn_string = "host=web0.eecs.uottawa.ca port = 15432 dbname=group_147 user=<user> password = <password>";
	
	$_SESSION['conn_string'] = $conn_string;
	
	$dbh = pg_connect($conn_string) or die ('Connection failed.');
	
	$output = '';
	$count=0;
	$checkData = '';
	$loginMail = '';
	$hostLogin = '';
	$hostCount = 0;
	$guestLogin = '';
	$guestCount = 0;

	if (isset($_POST['login'])) {
		login();
	}

	// LOGIN USER
	function login(){
		$username = $_POST['username'];

		if (!empty($_POST['username'])) {

			$guestQuery = "SELECT guest_id FROM guest WHERE email='$username'";
			$guestResults = pg_query($guestQuery);
			
			$hostQuery = "SELECT host_id FROM host WHERE email='$username'";
			$hostResults = pg_query($hostQuery);

			if (pg_num_rows($guestResults) > 0) { // user found
				$_SESSION['user_id'] = pg_fetch_row($guestResults)[0];
				header('location: ../Guest/SearchProperties.php');		  
			}elseif(pg_num_rows($hostResults) > 0) {
				$_SESSION['user_id'] = pg_fetch_row($hostResults)[0];
				header('location: ../Host/LandingPage.php');	
			}else{
				echo'No such user. Try again or Sign up.';
			}	
		}
	}
?>
<html>
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" 
                integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="../main.css">
    </head>
    <body>
        <div class="header"> 
            <h1>Propertly.</h1>
            <button type="button" class="btn btn-light" onclick="window.location.href = 'Signup.php';">Sign Up</button>
        </div>
        <div class="page">
            <div class="main-container">
                <h3>Login</h3>
				<br/>
				<form action="" method="post">
					<input type="text" name="username" placeholder="Enter your email"/>
					<input type="submit" value="Login" name="login"/>
				</form>
        </div>
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    </body>
</html>
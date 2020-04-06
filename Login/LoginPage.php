 <?php
 session_start();

 $conn_string = "host=web0.eecs.uottawa.ca port = 15432 dbname=group_147 user=<user> password = <password>";
 $dbh = pg_connect($conn_string) or die ('Connection failed.');
 
$output = '';
$count=0;
$checkData = '';
$loginMail = '';
$hostLogin = '';
$hostCount = 0;
$guestLogin = '';
$guestCount = 0;

//$loginMail = $_POST['loginMail'];
//$loginPass = $_POST['pwd'];

if(!empty($_POST['loginMail'])){
	$loginMail = $_POST['loginMail'];
	$checkData = pg_query("SELECT email FROM guest UNION SELECT email FROM host WHERE email LIKE '%$loginMail%';") or die("could not search");
	$count = pg_num_rows($checkData);
	//if($count == 0){
		//$output = 'No such login. Try again.';
	//}else{
		//$output = 'Success.';
	//}
	
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
           
            <div class="main-container" align="center">
                <h3>Login</h3>
				<br/>
                <form action="LoginPage.php" method="post">
					<input type="text" name="username" placeholder="Enter Email to Login"/>
					<input type="submit" value=">>" name="login_btn"/>
				
				</form>
				<?php 
					if (isset($_POST['login_btn'])) {
						login();
					}

					// LOGIN USER
					function login(){
						global $db, $username, $errors;
						$username='';
						$username = $_POST['username'];
						
						
						if (!empty($_POST['username'])) {

							$guestQuery = "SELECT * FROM guest WHERE email='$username'";
							$guestResults = pg_query($guestQuery);
							
							$hostQuery = "SELECT * FROM host WHERE email='$username'";
							$hostResults = pg_query($hostQuery);

							if (pg_num_rows($guestResults) == 1) { // user found
								// check if user is admin or user
								

									$_SESSION['user'] = $logged_in_user;
									$_SESSION['success']  = "You are now logged in";
									header('location: ../Guest/SearchProperties.php');		  
								
							}elseif(pg_num_rows($hostResults) == 1) {
								$_SESSION['user'] = $logged_in_user;
								$_SESSION['success']  = "You are now logged in";
								header('location: ../Host/LandingPage.php');	
							}else{
								echo'No such user. Try again or Sign up.';
							}
								
						}
}
				
				?>
            </div>
        </div>
		
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    </body>
</html>
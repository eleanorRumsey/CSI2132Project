<?php
 session_start();

 $conn_string = "host=web0.eecs.uottawa.ca port = 15432 dbname=group_147 user=<user> password = <password>";
 $dbh = pg_connect($conn_string) or die ('Connection failed.');
 

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
            <button type="button" class="btn btn-light" onclick="window.location.href = 'LoginPage.php';">Login</button>
        </div>
        <div class="page">
           
            <div class="main-container" align="center">
                <h3>Sign Up</h3>
				<br/>
                <form action="Signup.php" method="post">
					<input type="text" name="username" placeholder="Enter Your Email"/>
					<br/>
					<br/>
					<input type="text" name="firstName" placeholder="Enter Your First Name"/>
					<br/>
					<br/>
					<input type="text" name="lastName" placeholder="Enter Your Last Name"/>
					<br/>
					<br/>
					<input type="tel" name="phone" placeholder="Enter Your Phone Number"/>
					<br/>
					<br/>
					<select id="userType" name="user">
					  <option value="guest">Guest</option>
					  <option value="host">Host</option>
					</select>
					<br/>
					<br/>
					<input type="submit" value="Create Account!" name="signup_btn"/>
				
				</form>
				<?php 
					if (isset($_POST['signup_btn'])) {
						
					$email =  $_POST['username'];
					$firstName = $_POST['firstName'];
					$lastName = $_POST['lastName'];
					$phone = $_POST['phone'];
					$userType = $_POST['user'];
					$addressID = 'NULL';
					
					register();
					}
					
					// REGISTER USER
					function register(){
					global $db, $errors, $username, $email,$firstName,$lastName,$phone,$userType,$addressID;
						
						if ($userType = 'guest') {
							$query2 = "INSERT INTO person_name (firstName, lastName) 
									  VALUES('$firstName', '$lastName')";
							pg_query($dbh,$query2);
							
							$nameID = "SELECT name_id FROM person_name WHERE first_name = $firstName AND last_name = $lastname";
							$query = "INSERT INTO guest (address_id, name_id, email, phone_number) 
									  VALUES('$addressID', '$nameID','$email', '$phone')";
							pg_query($dbh,$query);
							
							$_SESSION['success']  = "New user successfully created!!";
							header('location: ../Guest/SearchProperties.php');
						}
						elseif($userType = 'host'){
							$query3 = "INSERT INTO host (email, phone_number,address_id,active) 
									  VALUES('$email', '$phone','$addressID','N')";
							pg_query($dbh,$query3);
							$query4 = "INSERT INTO person_name (firstName, lastName) 
									  VALUES('$firstName', '$lastName')";
							pg_query($dbh,$query4);
							$_SESSION['success']  = "New user successfully created!!";
							header('location: ../Host/LandingPage.php');
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
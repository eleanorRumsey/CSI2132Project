<?php
    session_start();
    $conn_string = $_SESSION['conn_string'];
    $dbh = pg_connect($conn_string) or die ('Connection failed.');

	$guest_id = $_SESSION['user_id'];
	
	$get_info = pg_query("SELECT g.address_id, g.name_id, g.email, g.phone_number, n.first_name, n.last_name, n.middle_name, 
									a.postal_code, a.street_number, a.unit, a.street_name, a.city, a.province, a.country
									FROM guest g
										JOIN person_name n ON g.name_id = n.name_id
										JOIN address a ON g.address_id = a.address_id
									WHERE g.guest_id = $guest_id");

    $user_data = pg_fetch_assoc($get_info);
	$nameID = $user_data['name_id'];
	
	if(isset($_POST['signup_btn'])){
		$username = $_POST['username'];
		$firstName = $_POST['firstName'];
		$lastName  = $_POST['lastName'];
		$middleName = $_POST['middleName'];
		$phoneNum = $_POST['phone'];
		
		//Update DB
		$q1 = pg_query("UPDATE guest SET email='$username', phone_number = '$phoneNum' WHERE guest_id=$guest_id");
		
		$q2 = pg_query("UPDATE person_name SET first_name='$firstName', last_name='$lastName', middle_name='$middleName'
						WHERE name_id = $nameID"); 
		
		header("Location: EditProfile.php");
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
            <button type="button" class="btn btn-light" onclick="window.location.href = '../Login/Logout.php';">Log out</button>
        </div>
        <div class="page">
            <nav class="nav flex-column">
                <a class="nav-link" href="SearchProperties.php">Search Properties</a>
				<a class="nav-link" href="CurrentBookings.php">My Bookings</a>
				<a class="nav-link" href="#">Edit Profile</a>
            </nav>
            <div class="main-container">
                <div class="main-container">
                <h3>Edit Your Profile</h3>
				<br/>
			
                <form action="" method="post">
					<label for="username">Email:</label>
					<input type="text" name="username" value="<?php echo $user_data['email'] ?>"/>
					<br/>
					<br/>
					<label for="firstName">First Name:</label>
					<input type="text" name="firstName" value="<?php echo $user_data['first_name'] ?>"/>
					<br/>
					<br/>
					<label for="middleName">Middle Name:</label>
					<input type="text" name="middleName" value="<?php echo $user_data['middle_name'] ?>"/>
					<br/>
					<br/>
					<label for="lastName">Last Name:</label>
					<input type="text" name="lastName" value="<?php echo $user_data['last_name'] ?>"/>
					<br/>
					<br/>
					<label for="phone">Phone Number:</label>
					<input type="tel" name="phone" value="<?php echo $user_data['phone_number'] ?>"/>
					<br/>
					<br/>
					<input type="submit" value="Save Changes" name="signup_btn"/>
				</form>
				
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    </body>
</html>
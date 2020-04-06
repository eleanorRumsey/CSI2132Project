<?php
    session_start();

    $conn_string = "host=web0.eecs.uottawa.ca port = 15432 dbname=group_147 user=<user> password = <password>";
    $dbh = pg_connect($conn_string) or die ('Connection failed.');

	$guest_id = $_SESSION['user_id'];
	$today = date("Y-m-d");

	$curr_bookings = '';
	
	$curr_bookings_stmt = pg_query("SELECT property_id FROM rental_agreement WHERE guest_id = $guest_id AND end_date > NOW()");
	if($curr_bookings_stmt){
		$curr_bookings = pg_fetch_all($curr_bookings_stmt);
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
                <a class="nav-link" href="#">Current Bookings</a>
                <a class="nav-link" href="#">Past Bookings</a>
            </nav>
            <div class="main-container">
                <h3>Upcoming Bookings:</h3>
				<?php
					if(is_array($curr_bookings)){				
						foreach($curr_bookings as $id => $booking){
							echo '<div> Property: '. $booking['property_id'] .'</div>';
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
<?php
    $conn_string = "host=web0.eecs.uottawa.ca port = 15432 dbname=group_147 user=<user> password = <password>";
    $dbh = pg_connect($conn_string) or die ('Connection failed.');

    $host_id = 1;
	
	$output = '';
	$count=0;
	//collect
    if(!empty($_POST['citySearch'])){
		$citySearchq = $_POST['citySearch'];
		$citySearchq = preg_replace("#[^a-z]#i","",$citySearchq);
		
		$query = pg_query("SELECT * FROM property WHERE property.address_id=(SELECT address.address_id FROM address WHERE city LIKE '%$citySearchq%')") or die("could not search");
		$count = pg_num_rows($query);
		if($count == 0){
			$output = 'There were no search results. Try searching something else.';
		}else{
			while($row = pg_fetch_array($query)){
				$propertyID = $row['property_id'];
				$propertyName = $row['property_name'];
				$guestCapacity = $row['guest_capacity'];
				$numBath = $row['num_bathrooms'];
				$numBed = $row['num_bedrooms'];
				$nextAvail = $row['next_available_date'];
				$rate = $row['rate'];
				$image = $row['image'];
				$description = $row['description'];
				$propertyTypeID = $row['property_type_id'];
				$addressID = $row['address_id'];
				
				
			}
		}
		
		if($count != 0){
			$query2 = pg_query("SELECT property_type FROM property_type WHERE property_type_id = $propertyTypeID") or die("could not search.");
			while($row = pg_fetch_array($query2)){
				$propertyType = $row['property_type'];
			}
		}

		if($count != 0){
			$query3 = pg_query("SELECT * FROM address WHERE address_id = $addressID") or die("could not search.");
			while($row = pg_fetch_array($query3)){
				$streetName = $row['street_name'];
				$city = $row['city'];
				$output .= '<div class="property">
								<div class="image-desc">
									<img src = "'."../Images/".$image.'" class="property-image"/>
									<div class="property-info">
										<h2>'. $propertyName .' on '.$streetName.' in '.$city.'</h2>
										<h3>$'. $rate .'/night</h3>
										<h6>'.$description.'</h6>
										<div> Next available date: '. $nextAvail .'</div>
										<div>'.$propertyType.' with '. $numBed.' bedroom, '. $numBath .' bathroom</div>
										<div></div>
										<div> Maximum number of guests: '. $guestCapacity .'</div>
									</div>
								</div>
							</div>';
			}
		}
	}

	// $_SESSION["property-id"] = $propertyID;

	// if(isset($_POST['book-property'])){
	// 	print_r("PROPERTY: " . $_SESSION["property-id"]);
	// 	// header("Location: NewBooking.php");
	// }
     
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
            <button type="button" class="btn btn-light">Log out</button>
        </div>
        <div class="page">
            <nav class="nav flex-column">
                <a class="nav-link" href="#">Search Properties</a>
                <a class="nav-link" href="CurrentBookings.php">Current Bookings</a>
                <a class="nav-link" href="#">Past Bookings</a>
            </nav>
            <div class="main-container">
                <h3>Enter Search Criteria</h3>
                <form action="SearchProperties.php" method="post">
					<input type="text" name="citySearch" placeholder="Search by City"/>
					<input type="submit" value=">>"/>
				</form>
				
				<form method="get" action="NewBooking.php">
					<?php
					if($count != 0){
						echo '<input type="hidden" name="property-id" value="'. $propertyID.'">';
						echo $output;
						echo '<button type="submit" class="btn btn-light" name="book-property" style="position:absolute; right:10%; top:50%;background-color:#86b3a0;">Book Now!</button>';
					}else{
						echo $output;
					}
					?>
				</form>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    </body>
</html>
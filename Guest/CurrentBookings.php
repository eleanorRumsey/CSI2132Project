<?php
    session_start();

	$conn_string = $_SESSION['conn_string'];
	$dbh = pg_connect($conn_string) or die ('Connection failed.');

	$guest_id = $_SESSION['user_id'];
	$today = date("Y-m-d");

	$curr_bookings = 'There are no current bookings.';
	$past_bookings = 'There are no past bookings.';
	
	$curr_bookings_stmt = pg_query("SELECT p.property_id, p.address_id, p.property_name, ra.guest_id, ra.start_date, ra.end_date, 
									pt.property_type, rt.room_type, p.rate, p.num_bedrooms, p.num_bathrooms, p.description, p.image,
									pm.amount, pmt.payment_type, pm.status
									FROM rental_agreement ra 
									JOIN property p ON ra.property_id = p.property_id
									JOIN property_type pt ON pt.property_type_id = p.property_type_id
									JOIN room_type rt ON rt.room_type_id = p.room_type_id
									JOIN payment pm on pm.payment_id = ra.payment_id
									JOIN payment_type pmt on pmt.payment_type_id = pm.payment_type_id
									WHERE ra.guest_id = $guest_id AND end_date > NOW()");

	if($curr_bookings_stmt){
		$curr_bookings = pg_fetch_all($curr_bookings_stmt);
	}

	$past_bookings_stmt = pg_query("SELECT p.property_id, p.address_id, p.property_name, ra.guest_id, ra.start_date, ra.end_date, 
									pt.property_type, rt.room_type, p.rate, p.num_bedrooms, p.num_bathrooms, p.description, p.image,
									pm.amount, pmt.payment_type, pm.status
									FROM rental_agreement ra 
									JOIN property p ON ra.property_id = p.property_id
									JOIN property_type pt ON pt.property_type_id = p.property_type_id
									JOIN room_type rt ON rt.room_type_id = p.room_type_id
									JOIN payment pm on pm.payment_id = ra.payment_id
									JOIN payment_type pmt on pmt.payment_type_id = pm.payment_type_id
									WHERE ra.guest_id = $guest_id AND end_date < NOW()");
	if($past_bookings_stmt){
		$past_bookings = pg_fetch_all($past_bookings_stmt);
	}

	$p_address_sql = "SELECT unit, street_number, street_name, city, province, country, postal_code FROM address WHERE address_id =$1";
    $p_address_stmt = pg_prepare($dbh, "pas", $p_address_sql);

    $beds_sql = "SELECT bed_type, num_of_beds FROM bed_setup WHERE property_id = $1";
    $beds_stmt = pg_prepare($dbh, "bs", $beds_sql);

    $rules_sql = "SELECT rule_type FROM rules WHERE rule_id IN (SELECT rule_id FROM property_rules WHERE property_id = $1)";
    $rules_stmt = pg_prepare($dbh, "rs", $rules_sql);

    $amenities_sql = "SELECT amenity_type FROM amenity WHERE amenity_id IN (SELECT amenity_id FROM property_amenities WHERE property_id = $1)";
    $amenities_stmt = pg_prepare($dbh, "as", $amenities_sql);
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
                <a class="nav-link" href="#">My Bookings</a>
            </nav>
            <div class="main-container">
                <h3>Upcoming Bookings:</h3>
				<?php
					if(is_array($curr_bookings)){				
						foreach($curr_bookings as $id => $booking){
							$p_address = pg_fetch_row(pg_execute($dbh, "pas", array($booking['address_id'])));
                        	$beds = pg_fetch_all(pg_execute($dbh, "bs", array($booking['property_id'])));
                        	$rules = pg_fetch_all(pg_execute($dbh, "rs", array($booking['property_id'])));
                        	$amenities = pg_fetch_all(pg_execute($dbh, "as", array($booking['property_id'])));
			
							$bedstring = "";
							if(is_array($beds)){
								foreach($beds as $b_setup){
									$bedstring .= $b_setup['bed_type'] . " beds : " . $b_setup['num_of_beds'] . " ";
								}
							}

							$rulestring = "";
							if(is_array($rules)){
								foreach($rules as $rule){
									$rulestring .= $rule['rule_type'] . " ";
								}
							}

							$amenitystring = "";
							if(is_array($amenities)){
								foreach($amenities as $amenity){
									$amenitystring .= $amenity['amenity_type'] . " ";
								}
							}

							$img_path = "../Images/" . $booking['image'];

							echo '<div class="property">
                                <div class="image-desc">
                                    <img src = "'.$img_path.'" class="property-image"/>
                                    <div class="property-info">
                                        <h3>'. $booking['property_name'] .'</h3>
                                        <h5>'. $booking['property_type'] .', '. $booking['room_type'] .', $'. $booking['rate'] .'/nt</h5>
                                        <div>'. $booking['num_bedrooms'].' bedroom, '. $booking['num_bathrooms'] .' bathroom</div>
                                        <div>'. $booking['description'].'</div>
                                        <div>'. $bedstring .'</div>
                                        <div>'. $rulestring .'</div>
                                        <div>'. $amenitystring .'</div>
                                    </div>
                                </div>
								<div class="address-date">
									<p>Dates booked: '. $booking['start_date'] .' to '. $booking['end_date'] .'</p>
                                    <br/>
                                    <br/>
                                    <h5>'. $p_address[0] .' '. $p_address[1] .' '. $p_address[2] .'</h5>
                                    <h5>'. $p_address[3] .', '. $p_address[4] .', '. $p_address[5] .'</h5>
									<h5>'. $p_address[6] .'</h5>
									<br/>
									<p> '. $booking['status'] .' payment of $'. $booking['amount'].' by '. $booking['payment_type'] . '</p>
                                </div>
                              </div>';
						}
					} else {
						echo $past_bookings;
					}
				?>
				<br/>
				<h3>Past Bookings:</h3>
				<?php
					if(is_array($past_bookings)){				
						foreach($past_bookings as $id => $booking){
							$p_address = pg_fetch_row(pg_execute($dbh, "pas", array($booking['address_id'])));
                        	$beds = pg_fetch_all(pg_execute($dbh, "bs", array($booking['property_id'])));
                        	$rules = pg_fetch_all(pg_execute($dbh, "rs", array($booking['property_id'])));
                        	$amenities = pg_fetch_all(pg_execute($dbh, "as", array($booking['property_id'])));
			
							$bedstring = "";
							if(is_array($beds)){
								foreach($beds as $b_setup){
									$bedstring .= $b_setup['bed_type'] . " beds : " . $b_setup['num_of_beds'] . " ";
								}
							}

							$rulestring = "";
							if(is_array($rules)){
								foreach($rules as $rule){
									$rulestring .= $rule['rule_type'] . " ";
								}
							}

							$amenitystring = "";
							if(is_array($amenities)){
								foreach($amenities as $amenity){
									$amenitystring .= $amenity['amenity_type'] . " ";
								}
							}

							$img_path = "../Images/" . $booking['image'];

							echo '<div class="property">
                                <div class="image-desc">
                                    <img src = "'.$img_path.'" class="property-image"/>
                                    <div class="property-info">
                                        <h3>'. $booking['property_name'] .'</h3>
                                        <h5>'. $booking['property_type'] .', '. $booking['room_type'] .', $'. $booking['rate'] .'/nt</h5>
                                        <div>'. $booking['num_bedrooms'].' bedroom, '. $booking['num_bathrooms'] .' bathroom</div>
                                        <div>'. $booking['description'].'</div>
                                        <div>'. $bedstring .'</div>
                                        <div>'. $rulestring .'</div>
                                        <div>'. $amenitystring .'</div>
                                    </div>
                                </div>
								<div class="address-date">
									<p>Dates booked: '. $booking['start_date'] .' to '. $booking['end_date'] .'</p>
                                    <br/>
                                    <br/>
                                    <h5>'. $p_address[0] .' '. $p_address[1] .' '. $p_address[2] .'</h5>
                                    <h5>'. $p_address[3] .', '. $p_address[4] .', '. $p_address[5] .'</h5>
									<h5>'. $p_address[6] .'</h5>
									<br/>
									<p> '. $booking['status'] .' payment of $'. $booking['amount'].' by '. $booking['payment_type'] . '</p>
                                </div>
                              </div>';
						}
					} else {
						echo $past_bookings;
					}
				?>
            </div>
        </div>
		
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    </body>
</html>
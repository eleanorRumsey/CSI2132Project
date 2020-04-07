<?php
	session_start();
	$conn_string = $_SESSION['conn_string'];
	$dbh = pg_connect($conn_string) or die ('Connection failed.');

	$guest_id = $_SESSION['user_id'];

	$beds_sql = "SELECT bed_type, num_of_beds FROM bed_setup WHERE property_id = $1";
    $beds_stmt = pg_prepare($dbh, "bs", $beds_sql);

	$rules_sql = "SELECT rule_type FROM rules WHERE rule_id IN (SELECT rule_id FROM property_rules WHERE property_id = $1)";
    $rules_stmt = pg_prepare($dbh, "rs", $rules_sql);

    $amenities_sql = "SELECT amenity_type FROM amenity WHERE amenity_id IN (SELECT amenity_id FROM property_amenities WHERE property_id = $1)";
    $amenities_stmt = pg_prepare($dbh, "as", $amenities_sql);

	$output = '';
	$count=0;
	//collect
    if(isset($_POST['search-btn']) && !empty($_POST['citySearch'])){
		$citySearchq = $_POST['citySearch'];
		$citySearchq = preg_replace("#[^a-z]#i","",$citySearchq);

		$filter_properties_stmt = pg_query("SELECT p.property_id, p.property_name, p.property_type_id, p.room_type_id, p.address_id, p.guest_capacity, p.num_bathrooms, 
						p.num_bedrooms, p.next_available_date, p.description, p.rate, p.active, p.image, pt.property_type, rt.room_type, 
						ad.postal_code, ad.street_number, ad.unit, ad.street_name, ad.city, ad.province, ad.country
					FROM property p
						JOIN room_type rt ON p.room_type_id = rt.room_type_id
						JOIN property_type pt ON pt.property_type_id = p.property_type_id
						JOIN address ad ON ad.address_id = p.address_id
						JOIN address_type adt ON adt.address_type_id = ad.address_type_id
					WHERE adt.address_type = 'Rental property' AND ad.city LIKE '%$citySearchq%'");
	
		$count = pg_num_rows($filter_properties_stmt);
		if($count == 0){
			$output = 'There were no search results. Try searching something else.';
		}else{
			$filter_properties = pg_fetch_all($filter_properties_stmt);

			foreach($filter_properties as $property){
				$beds = pg_fetch_all(pg_execute($dbh, "bs", array($property['property_id'])));
				$rules = pg_fetch_all(pg_execute($dbh, "rs", array($property['property_id'])));
				$amenities = pg_fetch_all(pg_execute($dbh, "as", array($property['property_id'])));

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

				$img_path = "../Images/" . $property['image'];

				$output .= '<input type="hidden" name="property-id" value="'. $property['property_id'].'">
							<div class="property">
								<div class="image-desc">
									<img src = "'.$img_path.'" class="property-image"/>
									<div class="property-info">
										<h3>'. $property['property_name'] .'</h3>
										<h5>'. $property['property_type'] .', '. $property['room_type'] .', $'. $property['rate'] .'/nt</h5>
										<div>'. $property['num_bedrooms'].' bedroom, '. $property['num_bathrooms'] .' bathroom</div>
										<div>'. $property['description'].'</div>
										<div>'. $bedstring .'</div>
										<div>'. $rulestring .'</div>
										<div>'. $amenitystring .'</div>
									</div>
								</div>
								<div class="address-date">
									<p> Available: '. $property["next_available_date"] .'</p>
									<br/>
									<h5>'. $property['unit'] .' '. $property['street_number'] .' '. $property['street_name'] .'</h5>
									<h5>'. $property['city'] .', '. $property['province'] .', '. $property['country'] .'</h5>
									<h5>'. $property['postal_code'] .'</h5>
									<button type="submit" class="btn btn-primary" name="book-property">Book Now!</button>
								</div>
							</div>
							<br/>';
			}
		}
	} elseif(isset($_POST['search-btn']) && empty($_POST['citySearch'])) {
		$all_properties_stmt = pg_query("SELECT p.property_id, p.property_name, p.property_type_id, p.room_type_id, p.address_id, p.guest_capacity, p.num_bathrooms, 
						p.num_bedrooms, p.next_available_date, p.description, p.rate, p.active, p.image, pt.property_type, rt.room_type, 
						ad.postal_code, ad.street_number, ad.unit, ad.street_name, ad.city, ad.province, ad.country
					FROM property p
						JOIN room_type rt ON p.room_type_id = rt.room_type_id
						JOIN property_type pt ON pt.property_type_id = p.property_type_id
						JOIN address ad ON ad.address_id = p.address_id
						JOIN address_type adt ON adt.address_type_id = ad.address_type_id
					WHERE adt.address_type = 'Rental property'");
	
		$all_properties = pg_fetch_all($all_properties_stmt);

		$count = pg_num_rows($all_properties_stmt);
		if($count == 0){
			$output = 'There were no search results. Try searching something else.';
		} else {
			foreach($all_properties as $property){
				$beds = pg_fetch_all(pg_execute($dbh, "bs", array($property['property_id'])));
				$rules = pg_fetch_all(pg_execute($dbh, "rs", array($property['property_id'])));
				$amenities = pg_fetch_all(pg_execute($dbh, "as", array($property['property_id'])));

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

				$img_path = "../Images/" . $property['image'];

				$output .= '<input type="hidden" name="property-id" value="'. $property['property_id'].'">
							<div class="property">
								<div class="image-desc">
									<img src = "'.$img_path.'" class="property-image"/>
									<div class="property-info">
										<h3>'. $property['property_name'] .'</h3>
										<h5>'. $property['property_type'] .', '. $property['room_type'] .', $'. $property['rate'] .'/nt</h5>
										<div>'. $property['num_bedrooms'].' bedroom, '. $property['num_bathrooms'] .' bathroom</div>
										<div>'. $property['description'].'</div>
										<div>'. $bedstring .'</div>
										<div>'. $rulestring .'</div>
										<div>'. $amenitystring .'</div>
									</div>
								</div>
								<div class="address-date">
									<p> Available: '. $property["next_available_date"] .'</p>
									<br/>
									<h5>'. $property['unit'] .' '. $property['street_number'] .' '. $property['street_name'] .'</h5>
									<h5>'. $property['city'] .', '. $property['province'] .', '. $property['country'] .'</h5>
									<h5>'. $property['postal_code'] .'</h5>
									<button type="submit" class="btn btn-primary" name="book-property">Book Now!</button>
								</div>
							</div>
							<br/>';
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
            <button type="button" class="btn btn-light" onclick="window.location.href = '../Login/Logout.php';">Log out</button>
        </div>
        <div class="page">
            <nav class="nav flex-column">
                <a class="nav-link" href="#">Search Properties</a>
                <a class="nav-link" href="CurrentBookings.php">My Bookings</a>
            </nav>
            <div class="main-container">
                <h3>Enter Search Criteria</h3>
                <form action="SearchProperties.php" method="post">
					<input type="text" name="citySearch" placeholder="Search by City"/>
					<input type="submit" name="search-btn" value=">>"/>
				</form>
				<form method="get" action="NewBooking.php">
					<?php
						echo $output;
					?>
				</form>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    </body>
</html>
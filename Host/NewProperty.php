<?php
    session_start();

    if(isset($_POST['create'])){
        $conn_string = "host=web0.eecs.uottawa.ca port = 15432 dbname=group_147 user=erums071 password = <password>";
        $dbh = pg_connect($conn_string) or die ('Connection failed.');

        $host_id = 1;
        $p_name = $_POST['p-name'];
        $p_type = $_POST['p-type'];
        $r_type = $_POST['room-type'];
        $capacity = $_POST['capacity'];
        $bathrooms = $_POST['bathrooms'];
        $bedrooms = $_POST['bedrooms'];
        $date = $_POST['date'];
        $description = $_POST['description'];
        $rate = $_POST['rate'];
        $image = $_POST['image'];

        $unit = $_POST['unit'];
        if(empty($unit)){
            $unit = 'NULL';
        }
        $street_number = $_POST['street-number'];
        $street_name = $_POST['street-name'];
        $city = $_POST['city'];
        $province = $_POST['province'];
        $country = $_POST['country'];
        $postal_code = $_POST['postal-code'];

        $p_type_id = "SELECT property_type_id FROM property_type WHERE property_type = '$p_type'";
        $r_type_id = "SELECT room_type_id FROM room_type WHERE room_type = '$r_type'";
        $a_type_id = "SELECT address_type_id FROM address_type WHERE address_type = 'Rental property'";


        $insert_address = "INSERT INTO address(postal_code, address_type_id, street_number, unit, street_name, city, province, country) 
                            VALUES ('$postal_code', ($a_type_id), $street_number, $unit, '$street_name', '$city', '$province', '$country')";

        print_r($insert_address);

        $address_result = pg_query($dbh, $insert_address); 
        if(!$address_result){ 
            die("Error in SQL query:" .pg_last_error());
        }
        pg_free_result($address_result);

        $address_id = "SELECT address_id FROM address WHERE postal_code = '$postal_code'";        

        $insert_property = "INSERT INTO property(property_name, host_id, property_type_id, room_type_id,
                                                    address_id, guest_capacity, num_bathrooms, num_bedrooms) 
                            VALUES ('$p_name', $host_id, ($p_type_id), ($r_type_id), ($address_id), $capacity, $bathrooms, $bedrooms)";

        $property_result = pg_query($dbh, ($insert_property));
        if(!$property_result){
            die("Error in SQL query:" .pg_last_error());
        }

        $property_id = "SELECT property_id FROM property WHERE property_name = $p_name AND host_id = $host_id";

        $bed_values = array (
            "King" => $_POST['num_king'],
            "Queen" => $_POST['num_queen'],
            "Double" => $_POST['num_double'],
            "Twin" => $_POST['num_twin']
        );

        $bed_result = true;
        foreach($bed_values as $bname => $bval){
            if($bval > 0){
                $insert_bed_setup = "INSERT INTO bed_setup(property_id, bed_type, num_of_beds) VALUES (($property_id), $bname, '$bval')";
                $bed_result = pg_query($dbh, $insert_bed_setup);
                if(!$bed_result){
                    die("Error in SQL query:" .pg_last_error());
                }
            }
        }
        
        $smoke_id = "SELECT rule_id FROM rules WHERE rule_type = 'No smoking'";
        $pet_id = "SELECT rule_id FROM rules WHERE rule_type = 'No pets'";
        $party_id = "SELECT party_id FROM rules WHERE rule_type = 'No parties'";

        $rule_values = array(
            $smoke_id => isset($_POST['smoke-check']),
            $pet_id => isset($_POST['pet-check']),
            $party_id => isset($_POST['party-check'])
        );

        foreach($rule_values as $r_id => $r_checked){
            if($r_checked){
                $insert_rule = "INSERT INTO property_rules(property_id, rule_id) VALUES (($property_id), ($r_id))";
                $rule_result = pg_query($dbh, $insert_bed_setup);
                if(!$rule_result){
                    die("Error in SQL query:" .pg_last_error());
                }
            }
        }

        $laundry_id = "SELECT amenity_id FROM amenity WHERE amenity_type = 'Laundry'";
        $ac_id = "SELECT amenity_id FROM amenity WHERE amenity_type = 'A/C";
        $heat_id = "SELECT amenity_id FROM amenity WHERE amenity_type = 'Heat'";
        $wifi_id = "SELECT amenity_id FROM amenity WHERE amenity_type = 'Wifi'";
        $stove_id = "SELECT amenity_id FROM amenity WHERE amenity_type = 'Stove'";
        $dishwasher_id = "SELECT amenity_id FROM amenity WHERE amenity_type = 'Dishwasher'";
        $towels_id = "SELECT amenity_id FROM amenity WHERE amenity_type = 'Towels'";
        
        $amenity_values = array(
            $laundry_id => isset($_POST['laundry-check']),
            $ac_id => isset($_POST['ac-check']),
            $heat_id => isset($_POST['heat-check']),
            $wifi_id => isset($_POST['wifi-check']),
            $stove_id => isset($_POST['stove-check']),
            $dishwasher_id => isset($_POST['dishwasher-check']),
            $towels_id => isset($_POST['towels-check'])
        );

        foreach($amenity_values as $a_id => $a_checked){
            if($a_checked){
                $insert_amenity = "INSERT INTO property_amenities(property_id, amenity_id) 
                                    VALUES (($property_id), ($a_id))";
                $amenity_result = pg_query($dbh, $insert_amenity);
                if(!$amenity_result){
                    die("Error in SQL query:" .pg_last_error());
                }
            }
        }

        echo "Data Successfully Entered";
        pg_close($dbh);
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
            <button type="button" class="btn btn-light">Log out</button>
        </div>
        <div class="page">
            <nav class="nav flex-column">
                <a class="nav-link" href="LandingPage.php">My properties</a>
                <a class="nav-link" href="#">New property</a>
                <a class="nav-link" href="#">History</a>
            </nav>
            <div class="main-container">
                <form id="new-property-form" name="property-form" method="post" action="">
                <h3>New property</h3>
                <div>Name: <input type="text" class="form-control" name="p-name"></div>
                <div>
                    Property type:                 
                    <select class="form-control" id="select_1" name="p-type">
                        <option value="Apartment">Apartment</option>
                        <option value="House">House</option>
                        <option value="Cottage">Cottage</option>
                        <option value="Loft">Loft</option>
                        <option value="Campsite">Campsite</option>
                    </select>
                </div>
                <div>
                    Room type:
                    <select class="form-control" id="select_2" name="room-type">
                        <option value="Entire">Entire property</option>
                        <option value="Private">Private room</option>
                        <option value="Shared">Shared room</option>
                    </select>
                </div>
                <div>
                    Address: 
                    <div>Unit: <input type="text" class="form-control" name="unit"/></div>
                    <div>Street number: <input type="text" class="form-control" name="street-number"/></div>
                    <div>Street name: <input type="text" class="form-control" name="street-name"/></div>
                    <div>City: <input type="text" class="form-control" name="city"/></div>
                    <div>Province/State: 
                        <select class="form-control" id="select_3" name="province">
                            <option value="NS">NS</option>
                            <option value="NB">NB</option>
                            <option value="PE">PE</option>
                            <option value="NL">NL</option>
                            <option value="QC">QC</option>
                            <option value="ON">ON</option>
                            <option value="MB">MB</option>
                            <option value="SK">SK</option>
                            <option value="AB">AB</option>
                            <option value="BC">BC</option>
                            <option value="YK">YK</option>
                            <option value="NT">NU</option>
                        </select>
                    </div>
                    <div>
                        Country: 
                        <select class="form-control" id="select_4" name="country">
                            <option value="CAN">Canada</option>
                            <option value="US">United States</option>
                        </select>
                    </div>
                    <div>Postal code: <input type="text" class="form-control" name="postal-code"/></div>
                </div>
                <div>Guest capacity: <input type="number" class="form-control" name="capacity"></div>
                <div>Number of bathrooms: <input type="number" class="form-control" name="bathrooms"></div>
                <div>Number of bedrooms: <input type="number" class="form-control" name="bedrooms"></div>
                <div>Next available date: 
                    <input type="text" class="form-control" placeholder="yyyy-mm-dd" name="date"></div>
                <div>Description: <textarea class="form-control" rows="3" name="description"></textarea></div>
                <div>Rate: <input type="number" class="form-control" name="rate"></div>
                <div>Image: <input type="text" class="form-control" placeholder="File name" name="image"></div>
                <div>
                    Bed setup: 
                    <div>King: <input type="number" class="form-control" name="num_king"></div>
                    <div>Queen: <input type="number" class="form-control" name="num_queen"></div>
                    <div>Double: <input type="number" class="form-control" name="num_double"></div>
                    <div>Twin: <input type="number" class="form-control" name="num_twin"></div>
                </div>
                <div>
                    Rules: 
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" name="smoke-check">
                        <div class="form-check-label">No smoking</div>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" name="pet-check">
                        <div class="form-check-label">No pets</div>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" name="party-check">
                        <div class="form-check-label">No parties</div>
                    </div>
                </div>
                <div>
                    Amenities: 
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" name="laundry-check">
                        <div class="form-check-label">Laundry</div>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" name="ac-check">
                        <div class="form-check-label">A/C</div>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" name="heat-check">
                        <div class="form-check-label">Heat</div>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" name="wifi-check">
                        <div class="form-check-label">Wifi</div>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" name="stove-check">
                        <div class="form-check-label">Stove</div>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" name="dishwasher-check">
                        <div class="form-check-label">Dishwasher</div>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" name="towels-check">
                        <div class="form-check-label">Towels</div>
                    </div>
                </div>
                    <input type="submit" class="btn btn-primary" name="create"/>
                </form>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    </body>
</html>
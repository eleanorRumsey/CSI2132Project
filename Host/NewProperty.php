<?php
    session_start();
    $conn_string = $_SESSION['conn_string'];
    $dbh = pg_connect($conn_string) or die ('Connection failed.');

    $rules_stmt = pg_prepare($dbh, "rst", "SELECT rule_id, rule_type FROM rules");
    $rules_res = pg_execute($dbh, "rst", array());
    if(!$rules_res){
        die("Error in SQL query:" .pg_last_error());
    }
    $rules = pg_fetch_all($rules_res);

    $amen_stmt = pg_prepare($dbh, "ast", "SELECT amenity_id, amenity_type FROM amenity");
    $amen_res = pg_execute($dbh, "ast", array());
    if(!$amen_res){
        die("Error in SQL query:" .pg_last_error());
    }
    $amenities = pg_fetch_all($amen_res);

    if(isset($_POST['create'])){
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

        $p_type_id_stmt = pg_prepare($dbh, "ptypest", "SELECT property_type_id FROM property_type WHERE property_type = $1");
        $p_type_res = pg_execute($dbh, "ptypest", array($p_type));
        
        $r_type_id_stmt = pg_prepare($dbh, "rtypest", "SELECT room_type_id FROM room_type WHERE room_type = $1");
        $r_type_res = pg_execute($dbh, "rtypest", array($r_type));
        
        $a_type_id_stmt = pg_prepare($dbh, "atypest", "SELECT address_type_id FROM address_type WHERE address_type = 'Rental property'");
        $a_type_res = pg_execute($dbh, "atypest", array());

        if(!$p_type_res | !$r_type_res | !$a_type_res){
            die("Error in SQL query:" .pg_last_error());
        }

        $r_type_id = pg_fetch_row($r_type_res)[0];
        $p_type_id = pg_fetch_row($p_type_res)[0];
        $a_type_id = pg_fetch_row($a_type_res)[0];

        $insert_address = "INSERT INTO address(postal_code, address_type_id, street_number, unit, street_name, city, province, country) 
                            VALUES ('$postal_code', $a_type_id, $street_number, $unit, '$street_name', '$city', '$province', '$country')";

        $address_result = pg_query($dbh, $insert_address); 
        if(!$address_result){ 
            die("Error in SQL query:" .pg_last_error());
        }
        pg_free_result($address_result);

        $a_id_stmt = pg_prepare($dbh, "aidst", "SELECT address_id FROM address WHERE postal_code = $1");
        $a_id_res = pg_execute($dbh, "aidst", array($postal_code));
        if(!$a_id_res){
            die("Error in SQL query:" .pg_last_error());
        }
        $address_id = pg_fetch_row($a_id_res)[0];    
    
        $insert_property = "INSERT INTO property(property_name, host_id, property_type_id, room_type_id, address_id, guest_capacity, 
                                    num_bathrooms, num_bedrooms, next_available_date, description, rate, active, image) 
                            VALUES ('$p_name', $host_id, $p_type_id, $r_type_id, $address_id, $capacity, $bathrooms, $bedrooms, 
                                    '$date', '$description', $rate, 'Y', '$image')";

        $property_result = pg_query($dbh, $insert_property);
        if(!$property_result){
            die("Error in SQL query:" .pg_last_error());
        }
        pg_free_result($property_result);

        $p_id_stmt = pg_prepare($dbh, "pidst", "SELECT property_id FROM property WHERE property_name = $1 AND host_id = $2");
        $p_id_res = pg_execute($dbh, "pidst", array($p_name, $host_id));
        if(!$p_id_res){
            die("Error in SQL query:" .pg_last_error());
        }
        $property_id = pg_fetch_row($p_id_res)[0];

        $bed_values = array (
            "King" => $_POST['num_king'],
            "Queen" => $_POST['num_queen'],
            "Full" => $_POST['num_full'],
            "Twin" => $_POST['num_twin']
        );

        foreach($bed_values as $bname => $bval){
            if($bval > 0){
                $insert_bed_setup = "INSERT INTO bed_setup(property_id, bed_type, num_of_beds) VALUES ($property_id, '$bname', $bval)";
                $bed_result = pg_query($dbh, $insert_bed_setup);
                if(!$bed_result){
                    die("Error in SQL query:" .pg_last_error());
                }
                pg_free_result($bed_result);
            }
        }
        
        foreach($rules as $rule){
            $rid = $rule['rule_id'];
            $checkname = $rid .'-rule';
            if(isset($_POST[$checkname])){
                $insert_rule = "INSERT INTO property_rules(property_id, rule_id) VALUES ($property_id, $rid)";
                $rule_result = pg_query($dbh, $insert_rule);
                if(!$rule_result){
                    die("Error in SQL query:" .pg_last_error());
                }
            }
        }

        foreach($amenities as $amenity){
            $aid = $amenity['amenity_id'];
            $acheckname = $aid .'-amenity';
            if(isset($_POST[$acheckname])){
                $insert_amenity = "INSERT INTO property_amenities(property_id, amenity_id) VALUES ($property_id, $aid)";
                $amenity_result = pg_query($dbh, $insert_amenity);
                if(!$amenity_result){
                    die("Error in SQL query:" .pg_last_error());
                }
            }
        }
        header('Location: LandingPage.php');
    }
    
    session_destroy();
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
                            <option value="Entire property">Entire property</option>
                            <option value="Private room">Private room</option>
                            <option value="Shared room">Shared room</option>
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
                                <option value="AB">AB</option>
                                <option value="BC">BC</option>
                                <option value="MB">MB</option>
                                <option value="NB">NB</option>
                                <option value="NL">NL</option>
                                <option value="NS">NS</option>
                                <option value="NT">NT</option>
                                <option value="NU">NU</option>
                                <option value="ON">ON</option>
                                <option value="PE">PE</option>
                                <option value="QC">QC</option>
                                <option value="SK">SK</option>
                                <option value="YK">YK</option>
                            </select>
                        </div>
                        <div>
                            Country: 
                            <select class="form-control" id="select_4" name="country">
                                <option value="Canada">Canada</option>
                                <option value="United States">United States</option>
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
                        <div>Full: <input type="number" class="form-control" name="num_full"></div>
                        <div>Twin: <input type="number" class="form-control" name="num_twin"></div>
                    </div>
                    <div>
                        Rules: 
                        <?php
                            foreach($rules as $rule){
                                echo '<div class="form-group form-check">
                                        <input type="checkbox" class="form-check-input" name="'.$rule['rule_id'].'-rule">
                                        <div class="form-check-label">'. $rule['rule_type'] .'</div>
                                    </div>';
                            }
                        ?>
                    </div>
                    <div>
                        Amenities: 
                        <?php
                            foreach($amenities as $amenity){
                                echo '<div class="form-group form-check">
                                        <input type="checkbox" class="form-check-input" name="'.$amenity['amenity_id'].'-amenity">
                                        <div class="form-check-label">'. $amenity['amenity_type'] .'</div>
                                    </div>';
                            }
                        ?>
                    </div>
                    <input type="submit" class="btn btn-primary" name="create"/>
                </form>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    </body>
    <?php
        if(isset($_POST['create'])){
            header("Location: LandingPage.php");
        }
    ?>
</html>
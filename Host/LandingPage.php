<?php 
    session_start();
    $conn_string = "host=web0.eecs.uottawa.ca port = 15432 dbname=group_147 user=erums071 password = <password>";
    $dbh = pg_connect($conn_string) or die ('Connection failed.');

    $hostid = 1;
    
    $properties_sql = "SELECT property_id, property_name, property_type_id, room_type_id, address_id, guest_capacity, num_bathrooms, num_bedrooms, 
                    next_available_date, description, rate, active, image FROM property WHERE host_id = $1";
    $property_stmt = pg_prepare($dbh, "ps", $properties_sql);
    $property_result = pg_execute($dbh, "ps", array(1));
    if(!$property_result){
        die("Error in SQL query:" .pg_last_error());
    }

    $property_type_sql = "SELECT property_type FROM property_type WHERE property_type_id = $1";
    $p_type_stmt = pg_prepare($dbh, "pts", $property_type_sql);

    $room_type_sql = "SELECT room_type FROM room_type WHERE room_type_id = $1";
    $r_type_stmt = pg_prepare($dbh, "rts", $room_type_sql);

    $p_address_sql = "SELECT unit, street_number, street_name, city, province, country, postal_code FROM address WHERE address_id =$1";
    $p_address_stmt = pg_prepare($dbh, "pas", $p_address_sql);

    $beds_sql = "SELECT bed_type, num_of_beds FROM bed_setup WHERE property_id = $1";
    $beds_stmt = pg_prepare($dbh, "bs", $beds_sql);

    $rules_sql = "SELECT rule_type FROM rules WHERE rule_id IN (SELECT rule_id FROM property_rules WHERE property_id = $1)";
    $rules_stmt = pg_prepare($dbh, "rs", $rules_sql);

    $amenities_sql = "SELECT amenity_type FROM amenity WHERE amenity_id IN (SELECT amenity_id FROM property_amenities WHERE property_id = $1)";
    $amenities_stmt = pg_prepare($dbh, "as", $amenities_sql);

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
            <button type="button" class="btn btn-light">Log out</button>
        </div>
        <div class="page">
            <nav class="nav flex-column">
                <a class="nav-link" href="#">My properties</a>
                <a class="nav-link" href="NewProperty.php">New property</a>
                <a class="nav-link" href="#">History</a>
            </nav>
            <div class="main-container">
                <?php
                    $propertyArr = pg_fetch_all($property_result);

                    foreach($propertyArr as $array){
                        $img_path = "../Images/" . $array['image'];
                        
                        $p_type_result = pg_execute($dbh, "pts", array($array['property_type_id'])); 
                        $r_type_result = pg_execute($dbh, "rts", array($array['room_type_id']));
                        $p_address_result = pg_execute($dbh, "pas", array($array['address_id']));
                        $beds_result = pg_execute($dbh, "bs", array($array['property_id']));
                        $rules_result = pg_execute($dbh, "rs", array($array['property_id']));
                        $amenities_result = pg_execute($dbh, "as", array($array['property_id']));
                        
                        if(!$p_type_result | !$r_type_result | !$p_address_result | !$beds_result | !$rules_result | !$amenities_result) {
                            die("Error in SQL query:" .pg_last_error());
                        }

                        $p_type = pg_fetch_row($p_type_result);
                        $r_type = pg_fetch_row($r_type_result);
                        $p_address = pg_fetch_row($p_address_result);
                        $beds = pg_fetch_all($beds_result);
                        $rules = pg_fetch_all($rules_result);  
                        $amenities = pg_fetch_all($amenities_result);  

                        $bedstring = "";
                        if(is_array($beds)) {
                            foreach($beds as $b_setup){
                                $btype = $b_setup['bed_type'];
                                $bnum = $b_setup['num_of_beds'];
                                $bedstring .= $btype . " beds : " . $bnum . " ";
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

                        echo '<div class="property">
                                <div class="image-desc">
                                    <img src = "'.$img_path.'" class="property-image"/>
                                    <div class="property-info">
                                        <h3>'. $array['property_name'] .'</h3>
                                        <h5>'. $p_type[0] .', '. $r_type[0] .', $'. $array['rate'] .'/nt</h5>
                                        <div>'. $array['num_bedrooms'].' bedroom, '. $array['num_bathrooms'] .' bathroom</div>
                                        <div>'. $array['description'].'</div>
                                        <div>'. $bedstring .'</div>
                                        <div>'. $rulestring .'</div>
                                        <div>'. $amenitystring .'</div>
                                    </div>
                                </div>
                                <div class="address-date">
                                    <p> Available: '. $array["next_available_date"] .'</p>
                                    <br/>
                                    <br/>
                                    <h5>'. $p_address[0] .' '. $p_address[1] .' '. $p_address[2] .'</h5>
                                    <h5>'. $p_address[3] .', '. $p_address[4] .', '. $p_address[5] .'</h5>
                                    <h5>'. $p_address[6] .'</h5>
                                </div>
                              </div>';
                        
                    }
                ?>
            </div>
        </div>
    </body>
</html>
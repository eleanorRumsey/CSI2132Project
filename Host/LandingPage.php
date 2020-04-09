<?php 
    session_start();
    $conn_string = $_SESSION['conn_string'];
    $dbh = pg_connect($conn_string) or die ('Connection failed.');

    $host_id = $_SESSION['user_id'];
    
    $properties_stmt = pg_query("SELECT p.property_id, p.property_name, p.address_id, p.guest_capacity, p.num_bathrooms, p.num_bedrooms, 
                        p.next_available_date, p.description, p.rate, p.active, p.image, pt.property_type, rt.room_type, 
                        rev_avg.avg_ovr, rev_avg.avg_comm, rev_avg.avg_clean, rev_avg.avg_val,
                        ad.postal_code, ad.street_number, ad.unit, ad.street_name, ad.city, ad.province, ad.country
                        FROM property p
                            JOIN room_type rt ON p.room_type_id = rt.room_type_id
                            JOIN property_type pt ON pt.property_type_id = p.property_type_id
                            JOIN address ad ON ad.address_id = p.address_id
                            JOIN address_type adt ON adt.address_type_id = ad.address_type_id
                            LEFT JOIN (SELECT property_id, avg(overall_rating) as avg_ovr, avg(communication_rating) as avg_comm, 
                                avg(clean_rating) as avg_clean, avg(value_rating) as avg_val FROM review GROUP BY property_id) 
                                as rev_avg on rev_avg.property_id = p.property_id
                        WHERE p.host_id = $host_id");
    if($properties_stmt){
        $properties = pg_fetch_all($properties_stmt);
    }

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
                <a class="nav-link" href="#">My properties</a>
                <a class="nav-link" href="NewProperty.php">New property</a>
                <a class="nav-link" href="History.php">History</a>
                <a class="nav-link" href="EditUser.php">Edit profile</a>
            </nav>
            <div class="main-container">
                <?php
                    if(is_array($properties)){
                        foreach($properties as $array){
                            $img_path = "../Images/" . $array['image'];
                            
                            $beds_result = pg_execute($dbh, "bs", array($array['property_id']));
                            $rules_result = pg_execute($dbh, "rs", array($array['property_id']));
                            $amenities_result = pg_execute($dbh, "as", array($array['property_id']));
                            
                            if(!$beds_result | !$rules_result | !$amenities_result) {
                                die("Error in SQL query:" .pg_last_error());
                            }

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
                                            <h5>'. $array['property_type'] .', '. $array['room_type'] .', $'. $array['rate'] .'/nt</h5>
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
                                        <h5>'. $array['unit'] .' '. $array['street_number'] .' '. $array['street_name'] .'</h5>
                                        <h5>'. $array['city'] .', '. $array['province'] .', '. $array['country'] .'</h5>
                                        <h5>'. $array['postal_code'] .'</h5>
                                        <br/>
                                        <p>REVIEWS</p>
                                        <div>Overall: '. round($array['avg_ovr'], 2).'</div>
                                        <div>Cleanliness: '. round($array['avg_clean'], 2) .'</div>
                                        <div>Communication: '. round($array['avg_comm'], 2).'</div>
                                        <div>Value: '. round($array['avg_val'], 2).'</div>
                                    </div>
                                </div>';
                            
                        }
                    }
                ?>
            </div>
        </div>
    </body>
</html>
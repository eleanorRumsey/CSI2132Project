<?php
    session_start();

    $conn_string = "host=web0.eecs.uottawa.ca port = 15432 dbname=group_147 user=erums071 password = <password>";
    $dbh = pg_connect($conn_string) or die ('Connection failed.');

    $host_id = 1;

    if(isset($_POST['create'])){
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
        $street_number = $_POST['street-number'];
        $street_name = $_POST['street-name'];
        $city = $_POST['city'];
        $province = $_POST['province'];
        $country = $_POST['country'];
        $postal_code = $_POST['postal-code'];

        $p_type_id = "SELECT property_type_id FROM property_type WHERE property_type = $p_type";
        $r_type_id = "SELECT room_type_id FROM room_type WHERE room_type = $r_type";

        $insert_address = "INSERT INTO address(postal_code, address_type_id, street_number, unit, street_name,
                                                city, province, country) 
                            VALUES($postal_code, (SELECT address_type_id FROM address_type WHERE address_type = 'Rental property'),
                                    $street_number, $unit, $street_name, $city, $province, $country, $postal_code)";

        $address_id = "SELECT address_id FROM address WHERE postal_code = $postal_code";        

        $insert_property = "INSERT INTO property(property_name, host_id, property_type_id, room_type_id,
                                                    address_id, guest_capacity, num_bathrooms, num_bedrooms) 
                            VALUES($p_name, $host_id, $p_type_id, $r_type_id, $address_id, $capacity, $bathrooms, $bedrooms)";

        $property_id = "SELECT property_id FROM property WHERE property_name = $p_name AND host_id = $host_id";


        $insert_bed_setup = "INSERT INTO bed_setup(property_id, bed_type, num_of_beds) 
                            VALUES ($property_id, $)";

        $king = $_POST['num_king'];
        $queen = $_POST['num_queen'];
        $double = $_POST['num_double'];
        $twin = $_POST['num_twin'];

        $smoke = $_POST['smoke-check'];
        $pet = $_POST['pet-check'];
        $party = $_POST['party-check'];

        $laundry = $_POST['laundry-check'];
        $ac = $_POST['ac-check'];
        $heat = $_POST['heat-check'];
        $wifi = $_POST['wifi-check'];
        $stove = $_POST['stove-check'];
        $dishwasher = $_POST['dishwasher-check'];
        $towels = $_POST['towels-check'];
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
            <button type="button" class="btn btn-light">Log out</button>
        </div>
        <div class="page">
            <nav class="nav flex-column">
                <a class="nav-link" href="LandingPage.php">My properties</a>
                <a class="nav-link" href="#">New property</a>
                <a class="nav-link" href="#">History</a>
            </nav>
            <div class="main-container">
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
                    <div>Unit: <input type="text" class="form-control" name="unit"></div>
                    <div>Street number: <input type="text" class="form-control" name="street-number"></div>
                    <div>Street name: <input type="text" class="form-control" name="street-name"></div>
                    <div>City: <input type="text" class="form-control" name="city"></div>
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
                        </select></div>
                    <div>Postal code: <input type="text" class="form-control" name="postal-code"></div>
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
                <button type="button" class="btn btn-primary" name="create">Create property</button>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    </body>
</html>
<?php
    session_start();
    $conn_string = $_SESSION['conn_string'];
    $dbh = pg_connect($conn_string) or die ('Connection failed.');

    $property_id = $_SESSION['property_id'];
    $nights = $_SESSION["num-nights"];
    
    $property_stmt = pg_query("SELECT p.property_name, p.rate, pt.property_type, n.first_name, n.last_name, a.unit, a.street_number, 
                                a.street_name, a.city, a.postal_code, a.country, a.province
                                FROM property p
                                    JOIN host h ON h.host_id = p.host_id 
                                    JOIN person_name n ON n.name_id = h.name_id
                                    JOIN address a ON a.address_id = p.address_id
                                    JOIN property_type pt ON pt.property_type_id = p.property_type_id 
                                WHERE property_id = $property_id");
    $property = pg_fetch_row($property_stmt);
    if(!$property){
        die("Error in SQL query:" .pg_last_error());
    } 

    $p_name = $property[0];
    $rate = $property[1];
    $p_type = $property[2];
    $first_name = $property[3];
    $last_name = $property[4];
    $unit = $property[5];
    $street_num = $property[6];
    $street_name = $property[7];
    $city = $property[8];
    $postal_code = $property[9];
    $country = $property[10];
    $province = $property[11];

    $full_name = pg_fetch_row(pg_query("SELECT * FROM firstNameFirst('$first_name', '$last_name')"))[0];

    $total = $nights * $rate;

    $payment_type_stmt = pg_query("SELECT payment_type FROM payment_type");
    $payment_types = pg_fetch_all($payment_type_stmt);

    $payment_id = $_SESSION['payment-id'];

    if(isset($_POST['pay-btn'])){
        $payment_type = $_POST['payment-type'];

        $update_payment = "UPDATE payment SET payment_type_id = $payment_type, amount = $total, status = 'Approved'
                                        WHERE payment_id = $payment_id";
        $payment_result = pg_query($update_payment);
        if(!$payment_result){
            die("Error in SQL query:" .pg_last_error());
        }   
    }

    $output = '<div class="property">
    <div class="image-desc">
            <div class="property-info">
                <h3>'. $p_name .'</h3>
                <h5>'. $p_type .'</h5>
                <h5> Hosted by: '. $full_name .' </h5>
                <br/>
                <h5>'. $unit .' '. $street_num .' '. $street_name .'</h5>
				<h5>'. $city .', '. $province .', '. $country .'</h5>
                <h5>'. $postal_code .'</h5>
                <br/>
                <div> Rate: $'. $rate . '/nt </div>
            </div>
        </div>
    </div>
    <p>Total for '. $nights .' nights: $'. $total .'</p>';
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
				<a class="nav-link" href="EditProfile.php">Edit Profile</a>
            </nav>
            </nav>
            <form class="main-container" method="post" action="CurrentBookings.php">
                <h3>Payment</h3>
                <br/>
                <?php
                    echo $output;   
                ?>
                <div> 
                    <p>Select your method of payment: </p>
                    <select class="form-control" name="payment-type" id="payment-type">
                        <?php
                            foreach($payment_types as $id => $type){
                                $pid = $id +1;
                                echo '<option value="' . $pid . '">' . $type['payment_type'] . '</option>';
                            }
                        ?>
                    </select>
                    <br/>
                    <input type="submit" class="btn btn-primary" name="pay-btn" value="Pay now and submit"/>
                </div>
            </form>
        </div>
    </body>
</html>
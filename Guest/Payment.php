<?php
    session_start();
    $conn_string = $_SESSION['conn_string'];
    $dbh = pg_connect($conn_string) or die ('Connection failed.');

    $property_id = $_SESSION['property_id'];
    $nights = $_SESSION["num-nights"];
    
    $property_stmt = pg_query("SELECT property_name, host_id, rate FROM property WHERE property_id = $property_id");
    $property = pg_fetch_row($property_stmt);
    if(!$property){
        die("Error in SQL query:" .pg_last_error());
    } 
    $total = $nights * $property[2];

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
            <form class="main-container" method="post" action="CurrentBookings.php">
                <h3>Payment</h3>
                <br/>
                <?php
                    echo '<div class="property">
                    <div class="image-desc">
                        <div class="property-info">
                            <h3>'. $property[0] .'</h3>
                            <div> Rate: $'. $property[2] . '/nt </div>
                        </div>
                    </div>
                  </div>
                  <p>Total: $'. $total .'</p>';
                  ?>
                <div> 
                    <p>Select your method of payment: </p>
                    <select class="form-control" name="payment-type">
                        <?php
                            foreach($payment_types as $id => $type){
                                echo '<option value="' . $id . '">' . $type['payment_type'] . '</option>';
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
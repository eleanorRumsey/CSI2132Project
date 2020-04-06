<?php
    $conn_string = "host=web0.eecs.uottawa.ca port = 15432 dbname=group_147 user=<user> password = <password>";
    $dbh = pg_connect($conn_string) or die ('Connection failed.');

    $property_id = $_POST['property-id'];
    
    $property_stmt = pg_prepare($dbh, "pst", "SELECT property_name, host_id, property_type_id, room_type_id, address_id, rate, image
                                FROM property WHERE property_id = $1");
    $property_result = pg_execute($dbh, "pst", array($property_id));
    if(!$property_result){
        die("Error in SQL query:" .pg_last_error());
    }
    $property = pg_fetch_row($property_result);
   
    $img_path = "../Images/" . $property[6];

    $start_date = $_POST['start-date-y'] . "-" . $_POST['start-date-m'] . "-" . $_POST['start-date-d'];
    $end_date = $_POST['end-date-y'] . "-" . $_POST['end-date-m'] . "-" . $_POST['end-date-d'];

    $year_diff = $_POST['end-date-y'] - $_POST['start-date-y'];
    $month_diff = $_POST['end-date-m'] - $_POST['start-date-m'];
    $day_diff = $_POST['end-date-d'] - $_POST['start-date-d'];
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
                <a class="nav-link" href="#">Current Bookings</a>
                <a class="nav-link" href="#">Past Bookings</a>
            </nav>
            <form class="main-container" method="post" action="">
                <h3>Payment</h3>
                <?php
                    echo '<div class="property">
                    <div class="image-desc">
                        <img src = "'.$img_path.'" class="property-image"/>
                        <div class="property-info">
                            <h3>'. $property[0] .'</h3>
                            <div>'. $property[9].'</div>
                            <div> Rate: $'. $property[5] . '/nt </div>
                        </div>
                    </div>
                  </div>';
                ?>
            </form>
        </div>
    </body>
</html>
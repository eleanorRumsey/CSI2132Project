<?php
    session_start();
	$conn_string = $_SESSION['conn_string'];
    $dbh = pg_connect($conn_string) or die ('Connection failed.');

    $host_id = $_SESSION['user_id'];

    $output = "";
    
    $booking_history_stmt = pg_query("SELECT p.property_name, p.rate, ra.signing_date, ra.start_date, ra.end_date, r.overall_rating, 
                                        r.communication_rating, r.clean_rating, r.value_rating, pm.amount, pmt.payment_type, pm.status, glv.first_name as first_name, 
                                        glv.last_name AS last_name, glv.postal_code AS guest_postal_code
                                        FROM rental_agreement ra 
                                            JOIN property p ON ra.property_id = p.property_id
                                            JOIN payment pm ON pm.payment_id = ra.payment_id
                                            JOIN payment_type pmt ON pmt.payment_type_id = pm.payment_type_id
                                            JOIN guestlistview glv ON glv.guest_id = ra.guest_id
                                            JOIN review r ON (r.property_id = p.property_id AND r.guest_id = ra.guest_id)
                                        WHERE ra.host_id = $host_id
                                        ORDER BY pmt.payment_type ASC, ra.signing_date DESC");
    
    $booking_history = pg_fetch_all($booking_history_stmt);
    
    $output = '<table class="table">
                            <thead>
                                <tr>
                                    <th colspan="2">Property</th>
                                    <th colspan="1"></th>
                                    <th colspan="3">Dates</th>
                                    <th colspan="3">Payment</th>
                                    <th colspan="4">Rating</th>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <th>Rate</th>
                                    <th>Guest Name</th>
                                    <th>Signed</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Overall</th>
                                    <th>Communication</th>
                                    <th>Clean</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>';
    if(is_array($booking_history)){
        foreach($booking_history as $booking){
            $first = $booking['first_name'];
            $last = $booking['last_name'];
            $guest_name_stmt = pg_query("SELECT * FROM firstNameFirst('$first', '$last')"); 
            $guest_name = pg_fetch_row($guest_name_stmt)[0];

            $output .= '<tr>
            <td>'. $booking['property_name'] .'</td>
            <td>'. $booking['rate'] .'</td>
            <td>'. $guest_name .'</td>
            <td>'. $booking['signing_date'] .'</td>
            <td>'. $booking['start_date'] .'</td>
            <td>'. $booking['end_date'] .'</td>
            <td>$'. $booking['amount'] .'</td>
            <td>'. $booking['payment_type'] .'</td>
            <td>'. $booking['status'] .'</td>
            <td>'. $booking['overall_rating'].'</td>
            <td>'. $booking['communication_rating'].'</td>
            <td>'. $booking['clean_rating'].'</td>
            <td>'. $booking['value_rating'].'</td>
            </tr>';
        }
    }
    $output .= '</tbody></table>';

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
                <h3>Activity for hosted properties</h3>
                <br/>
                <?php
                    echo $output;
                ?>
            </div>
        </div>
    </body>
</html>
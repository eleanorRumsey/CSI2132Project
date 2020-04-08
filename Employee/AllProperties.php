<?php
    session_start();
	$conn_string = $_SESSION['conn_string'];
    $dbh = pg_connect($conn_string) or die ('Connection failed.');

    $output = "";

    $branch_properties_sql = "SELECT p.property_id, ad.country, p.property_name, p.rate, ra.signing_date, ra.guest_id, ra.start_date, ra.end_date, 
                                    pm.amount, pmt.payment_type, pm.status, glv.first_name, glv.last_name, glv.postal_code as guest_postal_code
                                    FROM rental_agreement ra 
                                        JOIN property p ON ra.property_id = p.property_id
                                        JOIN payment pm on pm.payment_id = ra.payment_id
                                        JOIN payment_type pmt on pmt.payment_type_id = pm.payment_type_id
                                        JOIN guestlistview glv on glv.guest_id = ra.guest_id
                                        JOIN address ad ON ad.address_id = p.address_id
                                        JOIN branch b ON b.country = ad.country
                                    WHERE b.country = $1
                                    ORDER BY pmt.payment_type ASC, ra.signing_date DESC";

    pg_prepare($dbh, "bp", $branch_properties_sql);

    function getProperties($dbh, $country){
        
        $branch_properties_result = pg_execute($dbh, "bp", array($country));

        if(pg_num_rows($branch_properties_result) > 0){
            $output = '<table class="table">
                            <thead>
                                <tr>
                                    <th>Property ID</th>
                                    <th>Property Name</th>
                                    <th>Nightly Rate</th>
                                    <th>Branch</th>
                                    <th>Signing date</th>
                                    <th>Start date</th>
                                    <th>End date</th>
                                    <th>Payment amount</th>
                                    <th>Payment type</th>
                                    <th>Payment status</th>
                                    <th>First name</th>
                                    <th>Last name</th>
                                    <th>Guest postal code</th>
                                </tr>
                            </thead>
                            <tbody>';
                    
                    
            $branch_properties = pg_fetch_all($branch_properties_result);

            foreach($branch_properties as $property){
                
                $output .= '<tr>
                                <td>'. $property['property_id'] .'</td>
                                <td>'. $property['property_name'] .'</td>
                                <td>'. $property['rate'] .'</td>
                                <td>'. $property['country'] .'</td>
                                <td>'. $property['signing_date'] .'</td>
                                <td>'. $property['start_date'] .'</td>
                                <td>'. $property['end_date'] .'</td>
                                <td>'. $property['amount'] .'</td>
                                <td>'. $property['payment_type'] .'</td>
                                <td>'. $property['status'] .'</td>
                                <td>'. $property['first_name'] .'</td>
                                <td>'. $property['last_name'] .'</td>
                                <td>'. $property['guest_postal_code'] .'</td>
                            </tr>';
                
            }
            $output .= '</tbody></table>';
        } else {
            $output = "No properties found for the selected branch";
        }
        return $output;
    }
    
    $output = getProperties($dbh, 'Canada');

    if(isset($_POST['branch-go'])){
        $output = getProperties($dbh, $_POST['select-branch']);
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
                <a class="nav-link" href="#">Guest activity</a>
            </nav>
            <div class="main-container">
                <form class="form-inline" method="post">
                    <h3>All guest bookings</h3>
                    <hr/>
                    <select class="form-control" name="select-branch">
                        <option value="Canada">Canada</option>
                        <option value="United States">United States</option>
                    </select>
                    <input type="submit" class="btn btn-primary" name="branch-go" value="Go"/>
                </form>
                <?php
                    echo $output;
                ?>
            </div>
        </div>
    </body>
</html>
<?php
    session_start();
	$conn_string = $_SESSION['conn_string'];
	$dbh = pg_connect($conn_string) or die ('Connection failed.');
 
	if (isset($_POST['signup_btn'])) {
		$email =  $_POST['username'];
		$firstName = $_POST['firstName'];
		$middleName = $_POST['middleName'];
		if(empty($middleName)){
			$middleName = 'NULL';
		}
		$lastName = $_POST['lastName'];
		$phone = $_POST['phone'];
		$userType = $_POST['user'];

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

		//INSERT ADDRESS AND GET ID
		$a_type_id_stmt = pg_prepare($dbh, "ats", "SELECT address_type_id FROM address_type WHERE address_type = 'Personal residence'");
        $a_type_res = pg_execute($dbh, "ats", array());
		if(!$a_type_res){
			die("Error in SQL query:" .pg_last_error());
		}
		$a_type_id = pg_fetch_row($a_type_res)[0];

		$address_sql = "INSERT INTO address(postal_code, address_type_id, street_number, unit, street_name, city, province, country) 
							VALUES ('$postal_code', $a_type_id, $street_number, $unit, '$street_name', '$city', '$province', '$country')
							RETURNING address_id";
		$address_stmt = pg_prepare($dbh, "as", $address_sql);
		$address_result = pg_execute($dbh, "as", array()); 
        if(!$address_result){ 
            die("Error in SQL query:" .pg_last_error());
		}
		$address_id = pg_fetch_row($address_result)[0];
        pg_free_result($address_result);

    
		//INSERT NAME AND GET ID
		$name_sql = "INSERT INTO person_name (first_name, middle_name, last_name) VALUES('$firstName', '$middleName', '$lastName')
							RETURNING name_id";
		$name_stmt = pg_prepare($dbh, "ns", $name_sql);
		$name_result = pg_execute($dbh, "ns", array());
		if(!$name_result){
			die("Error in SQL query:" .pg_last_error());
		}
		$name_id = pg_fetch_row($name_result)[0];
		pg_free_result($name_result);

		//INSERT INTO GUEST OR HOST
		if ($userType = 'guest') {
			$insert_guest_result = pg_query("INSERT INTO guest (address_id, name_id, email, phone_number) VALUES($address_id, $name_id, '$email', '$phone')");
			if(!$insert_guest_result){
				die("Error in SQL query:" .pg_last_error());
			}
		}
		elseif($userType = 'host'){
			$insert_host_sql = "INSERT INTO host (email, name_id, phone_number, address_id, active) VALUES ('$email', $name_id, '$phone', $address_id, 'Y')";
			pg_query($dbh,$query3);
			pg_query($dbh,$query4);
		}

		header('location: LoginPage.php');
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
            <button type="button" class="btn btn-light" onclick="window.location.href = 'LoginPage.php';">Login</button>
        </div>
        <div class="page">
            <div class="main-container" align-self="center">
                <h3>Sign Up</h3>
				<br/>
                <form action="" method="post">
					<select id="userType" name="user">
					  <option value="guest">Guest</option>
					  <option value="host">Host</option>
					</select>
					<input type="text" name="username" placeholder="Enter Your Email"/>
					<br/>
					<br/>
					<input type="text" name="firstName" placeholder="Enter Your First Name"/>
					<br/>
					<br/>
					<input type="text" name="middleName" placeholder="Enter Your Middle Name"/>
					<br/>
					<br/>
					<input type="text" name="lastName" placeholder="Enter Your Last Name"/>
					<br/>
					<br/>
					<input type="tel" name="phone" placeholder="Enter Your Phone Number"/>
					<br/>
					<br/>
					<div>
                        Address: 
                        <div>Unit: <input type="text" class="form-control" name="unit"/></div>
                        <div>Street number: <input type="text" class="form-control" name="street-number"/></div>
                        <div>Street name: <input type="text" class="form-control" name="street-name"/></div>
                        <div>City: <input type="text" class="form-control" name="city"/></div>
                        <div>Province/State: 
                            <select class="form-control" name="province">
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
                            <select class="form-control" name="country">
                                <option value="Canada">Canada</option>
                                <option value="United States">United States</option>
                            </select>
                        </div>
                        <div>Postal code: <input type="text" class="form-control" name="postal-code"/></div>
                    </div>
					<br/>
					<br/>
					<input type="submit" value="Create Account!" name="signup_btn"/>
				</form>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    </body>
</html>
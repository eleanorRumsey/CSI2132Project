<?php
    session_start();
	$conn_string = $_SESSION['conn_string'];
	$dbh = pg_connect($conn_string) or die ('Connection failed.');
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
            <div class="main-container"></div>
        </div>
    </body>
</html>
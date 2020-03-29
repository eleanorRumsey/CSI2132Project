<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    </head>
    <body>
        <p>HELLO</p>
    </body>
</html>
<?php
    session_start();
    $conn_string = "host=web0.eecs.uottawa.ca port = 15432 dbname=group_147 user=erums071 password = Chs22745er";
    $dbh = pg_connect($conn_string) or die ('Connection failed.');
?>
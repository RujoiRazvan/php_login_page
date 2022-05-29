<?php
session_start();

if (isset($_SESSION['username'])) {
} else {
    header('Location: login.php');
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Google Maps Page</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <style>

    </style>
</head>

<body>
    <div style="padding: 50px 20px 20px 20px; flex: 1">
        <h3>Google Maps Page</h3>
        <?php

        echo "Welcome, {$_SESSION['username']} !";
        ?>
        <div id="map"></div>
        <br>
        <a href="logout.php"><input type="button" value="Logout" name="logout"></a>
        <?php

        $conn = mysqli_connect("localhost", "root", "") or die(mysqli_error());
        mysqli_select_db($conn, "users");

        $sql_read = "SELECT * FROM points";

        $result = mysqli_query($conn, $sql_read);
        if (!$result) {
            die('Could not read data: ' . mysqli_error());
        }
        ?>

        <script>
            function initMap() {
                var uluru = {
                    lat: 23,
                    lng: 31
                };
                var map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 4,
                    center: uluru
                });

                <?php
                while ($row = mysqli_fetch_array($result)) {

                    $id = $row['ID'];
                    $lat = $row['lat'];
                    $long = $row['long'];
                    $description = $row['description'];

                    for ($i = 0; $i <= $id; $i++) {
                        echo "var marker$i = new google.maps.Marker({position:{lat:$lat,lng:$long}, label:'$description', map: map});";
                    };
                }

                ?>
            }
        </script>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCEl64vITi4s1Vf0t5CpgmA0uSCQR8P0-U&callback=initMap&v=weekly">
        </script>
    </div>
    <footer>
        <p> Proiect "Sisteme de distributie si arhitecturi WEB" </p>
        <p>Created in <script>
                document.write(new Date().getFullYear())
            </script> by Rujoi Razvan 
        </p>
    </footer>
</body>

</html>
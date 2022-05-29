<?php
session_id("mainID");
session_start();
?>

<html>

<head>
   <title>Login page</title>

   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
   <link rel="stylesheet" href="style.css">
   <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
   <script type="text/javascript">
      $(document).ready(function() {
         $('.search-box input[type="text"]').on("keyup input", function() {
            /* Get input value on change */
            var inputVal = $(this).val();
            var resultDropdown = $(this).siblings(".result");
            if (inputVal.length) {
               $.get("backend-search.php", {
                  term: inputVal
               }).done(function(data) {
                  // Display the returned data in browser
                  resultDropdown.html(data);
               });
            } else {
               resultDropdown.empty();
            }
         });

         // Set search input value on click of result item
         $(document).on("click", ".result p", function() {
            $(this).parents(".search-box").find('input[type="text"]').val($(this).text());
            $(this).parent(".result").empty();
         });
      });
   </script>
</head>

<body>
   <div style="padding: 50px 20px 20px 20px; flex: 1">
      <?php

      $servername = "localhost";
      $username = "root";
      $password = "";
      $db_name = "users";

      // Create connection
      $conn = mysqli_connect("localhost", "root", "", "users") or die(mysqli_error());

      $sql_read = "SELECT * FROM `users`";

      $result = mysqli_query($conn, $sql_read);


      // Check login credentials
      $user = '';
      $password = '';
      if (
         isset($_POST['login']) && !empty($_POST['username'])
         && !empty($_POST['password'])
      ) {
         if (isset($_REQUEST["term"])) {
            $readUsers = "SELECT * FROM `users` where `user` LIKE ?";

            if ($stmt = mysqli_prepare($conn, $readUsers)) {
               // Bind variables to the prepared statement as parameters
               mysqli_stmt_bind_param($stmt, "s", $param_term);

               // Set parameters
               $param_term = $_REQUEST["term"] . '%';

               // Attempt to execute the prepared statement
               if (mysqli_stmt_execute($stmt)) {
                  $autoCompleteResult = mysqli_stmt_get_result($stmt);

                  // Check number of rows in the result set
                  if (mysqli_num_rows($autoCompleteResult) > 0) {
                     // Fetch result rows as an associative array
                     while ($row = mysqli_fetch_array($autoCompleteResult, MYSQLI_ASSOC)) {
                        echo "<p>" . $row["user"] . "</p>";
                     }
                  } else {
                     echo "<p>No matches found</p>";
                  }
               } else {
                  echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
               }
            }
         }
         while ($row = mysqli_fetch_array($result)) {
            if ($row['user'] == $_POST['username']) {
               $user = $row['user'];
               $password = $row['password'];
            }
         }
         if (
            $_POST['username'] == $user &&
            $_POST['password'] == $password
         ) {
            $_SESSION['valid'] = true;
            $_SESSION['timeout'] = time();
            $_SESSION['username'] = $user;
            header('Location: maps_page.php');
         } else {
            echo "Wrong username or password";
         }
      }

      // New user register PHP

      if (
         isset($_POST['register']) && !empty($_POST['username']) && !empty($_POST['password']) &&
         !empty($_POST['lat']) && !empty($_POST['long']) && !empty($_POST['descriere'])
      ) {
         $maxID = mysqli_query($conn, "SELECT max(`ID`) as `max` from `points`");

         $result = mysqli_fetch_array($maxID);
         $r = $result[0];
         $next_id = $r + 1; // Incrementarea urmatorului ID
         $sqli_insert = "INSERT INTO `points`(`ID`, `lat`, `long`, `description`) VALUES ('$next_id','$_POST[lat]','$_POST[long]','$_POST[descriere]')";
         $retval = mysqli_query($conn, $sqli_insert);
         $sqli_insert1 = "INSERT INTO `users` (`ID`, `user`, `password`) values ('$next_id', '$_POST[username]', '$_POST[password]')";
         $retval = mysqli_query($conn, $sqli_insert1);

         $_SESSION['timeout'] = time();
         $_SESSION['username'] = $_POST['username'];
         header('Location: maps_page.php');
      }

      ?>

      <!-- FORMS Design -->

      <div class="container">
         <h2>Login form</h2>
         <form action="" method="POST">
            <div class="form-group search-box">
               <label for="user">User</label>
               <input type="text" autocomplete="off" class="form-control" placeholder="Enter username" name="username">
               <div class="result"></div>
            </div>
            <div class="form-group">
               <label for="pwd">Password</label>
               <input type="password" class="form-control" placeholder="Enter password" maxlength="32" size="32" name="password" required>
            </div>

            <button type="submit" name="login" class="btn btn-default">Submit</button>
         </form>
      </div>

      <br><br>

      <div class="container">
         <h2>Register new user</h2>
         <form action="" method="POST">
            <div class="form-group search-box">
               <label for="user">User</label>
               <input type="text" autocomplete="off" class="form-control" maxlength="32" size="32" placeholder="Enter username" name="username">
            </div>
            <div class="form-group">
               <label for="pwd">Password</label>
               <input type="password" class="form-control" maxlength="32" size="32" placeholder="Enter password" name="password" required>
            </div>
            <div class="form-group">
               <label for="pwd">Latitude</label>
               <input type="password" class="form-control" placeholder="Enter latitude" maxlength="32" size="32" name="lat">
            </div>
            <div class="form-group">
               <label for="pwd">Longitude</label>
               <input type="password" class="form-control" placeholder="Enter longitude" maxlength="32" size="32" name="long">
            </div>
            <div class="form-group">
               <label for="pwd">Description (hint: enter location name)</label>
               <input type="password" class="form-control" placeholder="Enter description" maxlength="32" size="32" name="descriere">
            </div>

            <button type="submit" name="register" class="btn btn-default">Register</button>
         </form>
      </div>
   </div>

   <!-- Footer Design -->
   <footer>
      <p> Proiect "Sisteme de distributie si arhitecturi WEB" </p>
      <p>Created in <script>
            document.write(new Date().getFullYear())
         </script> by Rujoi Razvan </p>
   </footer>
</body>

</html>
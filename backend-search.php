<?php
$conn = mysqli_connect("localhost", "root", "", "users") or die(mysqli_error());
if(isset($_REQUEST["term"])){
         $readUsers = "SELECT * FROM `users` where `user` LIKE ?";

         if($stmt = mysqli_prepare($conn, $readUsers)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_term);
            
            // Set parameters
            $param_term = $_REQUEST["term"] . '%';
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $autoCompleteResult = mysqli_stmt_get_result($stmt);
                
                // Check number of rows in the result set
                if(mysqli_num_rows($autoCompleteResult) > 0){
                    // Fetch result rows as an associative array
                    while($row = mysqli_fetch_array($autoCompleteResult, MYSQLI_ASSOC)){
                        echo "<p>" . $row["user"] . "</p>";
                    }
                } else{
                    echo "<p>No matches found</p>";
                }
            } else{
                echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
            }
        }
        mysqli_stmt_close($stmt);
      }
      
      ?>
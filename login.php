<html>
    <body>
        <?php
            include ("serverConfig.php");
            $conn = new mysqli($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE);
            if ($conn -> connect_error) {
                die("Connection failed:" .$conn -> connect_error);
            }

            session_start();

            $email = $_POST['email'];
            $password = $_POST['pass'];
            
            $sql = "select * from Users where email=\"{$email}\";";
            $result = $conn -> query($sql);
            $row = $result->fetch_assoc();
            $userID = $row["userID"];
            $sqlEmail = $row["email"];
            $sqlPass = $row["password"];

            function emailMatches ($inputEmail, $DBEmail) {
                return strcasecmp($inputEmail, $DBEmail);
            }

            if(emailMatches($email, $sqlEmail) == 0) {
                
                $_SESSION['user'] = $userID;
                $_SESSION['username'] = $row['username'];
                $_SESSION['loggedin'] = true;
                header( "Location: home.php" );
            }
            else {
                header( "Location: login.html" );
            }

            $conn->close(); 
        ?>
    </body>
</html>
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
            
            $sql = "select * from users where email=\"{$email}\";";
            $result = $conn -> query($sql);
            $row = $result->fetch_assoc();
            $userID = $row["userID"];
            $sqlEmail = $row["email"];
            $sqlPass = $row["password"];

            function emailMatches ($inputEmail, $DBEmail) {
                return strcasecmp($inputEmail, $DBEmail) == 0;
            }

            // if(emailMatches($email, $sqlEmail) && password_verify($password, $sqlPass)) {
            if(emailMatches($email, $sqlEmail)) {
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
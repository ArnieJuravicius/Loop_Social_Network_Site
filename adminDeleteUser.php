<html>
    <body>
        <?php

            include ("validateAdmin.php");
            include ("serverConfig.php");

            $conn = new mysqli($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE);
            if ($conn -> connect_error) {
                die("Connection failed:" .$conn -> connect_error);
            }

            $userToDelete = $_GET['id'];

            $connectionsSQL = "DELETE FROM users WHERE userID={$userToDelete};";

            if ($conn->query($connectionsSQL) === TRUE) {
                echo "Sucessful";
                header( "Location: admin.php" );

            } 
            else {
                echo "Error: " . $connectionsSQL . "<br>" . $conn->error;
            }

            header( "Location: admin.php" );
            $conn->close();
        ?>
    </body>
</html>
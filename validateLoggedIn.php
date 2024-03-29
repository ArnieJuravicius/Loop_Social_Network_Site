<?php
    session_start();

    if (!(isset($_SESSION["loggedin"])) || $_SESSION["loggedin"] == false) {
        header( "Location: login.php" );
    } 

    include ("serverConfig.php");

    $conn = new mysqli($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE);
    if ($conn -> connect_error) {
        die("Connection failed:" .$conn -> connect_error);
    }

    if(isset($_SESSION['user'])) {
        $userID = $_SESSION['user'];
        $bannedSql = "SELECT * FROM banneduser 
                      WHERE userID = {$userID};";
        $bResult = $conn -> query($bannedSql);
    }
    else if(isset($_SESSION['company'])) {
        $companyID = $_SESSION['company'];
        $bannedSql = "SELECT * FROM bannedcompany 
                      WHERE companyID = {$companyID};";
        $bResult = $conn -> query($bannedSql);
    }
    
    if(mysqli_num_rows($bResult) !== 0 ) {
        header( "Location: login.php" );
    }

?>
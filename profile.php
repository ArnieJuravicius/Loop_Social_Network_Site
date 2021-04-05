<html>
    <head>
        <title>Loop : User Profie</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="css/profile_user.css?v=<?php echo time() ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
    <?php 
            
            session_start();

            function getUserData($uID) {
                include ("serverConfig.php");
                $conn = new mysqli($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE);
                if ($conn -> connect_error) {
                    die("Connection failed:" .$conn -> connect_error);
                }

                $sql = "select * from users where userID =\"{$uID}%\";";
                $result = $conn -> query($sql);
                $conn->close();

                return $result->fetch_assoc();
            }

            
            if(isset($_SESSION['user'])) include("headerTemplate.html");
            else include("companyTemplate.html");
            $userID = $_GET["userID"];

            $row = getUserData($userID);

            print "<h1 class='page-header'>{$row['username']}</h1>";
            
        ?>
        <hr>
        <div class = "profile-container" >
            <div class = "profileImage" >
                <?php

                    $row = getUserData($userID);

                    $profileImage = null;

                    if (isset($row['profileImage'])) $profileImage = $row['profileImage'];

                    if($profileImage === null) {
                        print '<img src = "images/blank-profile-picture.png" alt="profile image" height="25%" width="25%" style="min-width:180px; min-height:180px; border-radius:50%;" >';
                    }
                    else {
                        print "<img src = 'profileImages/{$profileImage}' alt='profile image' height='25%' width='25%' style='min-width:180px; min-height:180px; border-radius:50%; object-fit: cover; overflow:hidden;' >";
                    }

                ?>
            </div>
        </div>
        <div class = "description-container">
            <div class = "description-heading">
                <H1 style = "text-align: center;">Description</H1>
            </div>
            <div class = "bio-description">
                <h3>Bio:</h3>
                <?php
                    $row = getUserData($userID);
                    if($row) {
                        print "<p class='userDetails'>{$row['description']}</p>";
                    } 
                    else {
                        print "<p>No Bio found.</p>";
                    }
                ?>
            </div>

            <div class = "skills-description">
                <h3>Skills:</h3>
                <?php
                    fetchProfileElement("skills");
                ?>
            </div>
            
            <div class = "Qualifications-description">
                <h3>Employment History:</h3>
                <?php
                    fetchProfileElement("employment-history");
                ?>
            </div>

            <div class = "Certs-description">
            <h3>Qualifications:</h3>
                <?php
                    fetchProfileElement("qualifications");                    
                ?>
            </div>

            <div class = "Qualifications-description">
                <h3>Current Employer:</h3>
                <?php
                    fetchProfileElement("current-employer");
                ?>
            </div>
        </div>
    </body>
</html>

<?php 

    function fetchProfileElement($elementToFetch) {
        include ("serverConfig.php");
        $conn = new mysqli($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE);
        if ($conn -> connect_error) {
            die("Connection failed:" .$conn -> connect_error);
        }

        $userID = $_GET["userID"];

        switch($elementToFetch){
            case ("current-employer") : 
                $sql = "SELECT a.companyName
                    FROM companies a
                    INNER JOIN users b
                    ON a.companyID = b.companyID
                    WHERE b.userID = {$userID};";
                $result = $conn -> query($sql);
                if($row = $result->fetch_assoc()) {
                    print "<p class='userDetails'>{$row['companyName']}</p>";
                } else {
                    print "<p>No Current Employer.</p>";
                }
                break;

            case ("qualifications") : 
                $sql = "SELECT a.academicTitle, a.academicDescription, a.academicLevel, b.completionDate
                    FROM accademicdegrees a
                    INNER JOIN userqualificaion b
                    ON a.academicID = b.academicID
                    WHERE b.userID = {$userID};";
                $result = $conn -> query($sql);
                if(mysqli_num_rows($result) != 0) {
                    while($resultRow = $result->fetch_assoc()) {
                        print "<p>Graduated {$resultRow['academicDescription']}, {$resultRow['academicLevel']} at {$resultRow['academicTitle']} on {$resultRow['completionDate']}</p>";
                    }
                } else {
                    print "<p>No Previous Job History Found.</p>";
                }
                break;

            case ("employment-history") :
                $sql = "SELECT a.companyName, b.FromDate, b.ToDate
                    FROM companies a
                    INNER JOIN jobhistory b
                    ON a.companyID = b.companyID
                    WHERE b.userID = {$userID};";
                $result = $conn -> query($sql);
                if(mysqli_num_rows($result) != 0) {
                    while($resultRow = $result->fetch_assoc()) {
                        print "<p>{$resultRow['companyName']}, {$resultRow['FromDate']} - {$resultRow['ToDate']}</p>";
                    }
                } else {
                    print "<p>No Previous Job History Found.</p>";
                }
                break;
            
            case ("skills") :
                $sql = "SELECT a.skillTitle
                    FROM skills a
                    INNER JOIN userskills b
                    ON a.skillID = b.skillID
                    WHERE b.userID = {$userID};";
                $result = $conn -> query($sql);
                if(mysqli_num_rows($result) != 0) {
                    while($resultRow = $result->fetch_assoc()) {
                        print "<p>{$resultRow['skillTitle']}</p>";
                    }
                } else {
                    print "<p>No Skills Found.</p>";
                }
                break;

            default : break;
        }

        $conn->close();
        
    }

?>
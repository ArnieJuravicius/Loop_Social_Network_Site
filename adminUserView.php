<html>
    <head>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="css/profile_user.css?v=<?php echo time(); ?>">
        <link rel="shortcut icon" type="image/x-icon" href="favicon.ico"/>

        <meta content="width=device-width, initial-scale=1" name="viewport" />

        <title>Loop : Admin</title>
    </head>
    <body>
        
        <?php 

            include ("validateLoggedIn.php");
            include("adminTemplate.html");
            function getUserData($uID) {
                include ("serverConfig.php");
                $conn = new mysqli($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE);
                if ($conn -> connect_error) {
                    die("Connection failed:" .$conn -> connect_error);
                }

                $sql = "select * from users where userID ={$uID};";
                $result = $conn -> query($sql);
                $conn->close();

                return $result->fetch_assoc();
            }

            
          

            $userID = $_GET['userID'];

            $row = getUserData($userID);
            print "<h1 class='page-heading'>{$row['username']}</h1>";
        ?>

        <hr>
        <div class = "profile-container" >
            <div class = "profileImage" >
                <?php

                    $row = getUserData($userID);

                    $profileImage = null;

                    if (isset($row['profileImage'])) $profileImage = $row['profileImage'];

                    if($profileImage === null) {
                        print '<img src = "images/blank-profile-picture.png" alt="profile image" height="25%" width="18%" style="min-width:180px; min-height:180px; border-radius:50%;" >';
                    }
                    else {
                        print "<img src = 'profileImages/{$profileImage}' alt='profile image' height='25%' width='18%' style= 'min-width:160px; min-height:160px; border-radius:50%; object-fit: cover; overflow:hidden;' >";
                    }
                    print "<br><button class='button' onClick=\"location.href='adminEditProfile.php?userID={$_GET['userID']}'\">Edit Profile</button>";
                ?>
            </div>
            
        </div>
        
        <div class = "description-container">
            <div class = "bio-description">
                <h3 class="text-left">Bio:</h3>
                <?php
                    $userID = $_GET['userID'];
                    $row = getUserData($userID);
                    if(isset($row['description']) && $row['description'] !== null ){
                        print "<p class='userDetails text-left'>{$row['description']}</p>";
                        // setcookie("description",$row['description'],time()+3600);
                        $_SESSION['description'] = $row['description'];
                    } 
                    else {
                        print "<p class='text-left'>No Bio found.</p>";
                    }
                ?>

            </div>
            <div class = "skills-description">
                <h3 class="text-left">Skills:</h3>
                <?php fetchProfileElement("skills"); ?>
            </div>
            
            <div class = "Qualifications-description">
                <h3 class="text-left">Employment History:</h3>
                <?php fetchProfileElement("employment-history"); ?>
            </div>

            <div class = "Certs-description">
                <h3 class="text-left">Qualifications:</h3>

                <?php fetchProfileElement("qualifications"); ?>

            </div>

            <div class = "Qualifications-description">
                <h3 class="text-left">Current Employer:</h3>

                <?php fetchProfileElement("current-employer"); ?>

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
        $userID = $_GET['userID'];

        switch($elementToFetch){
            case ("current-employer") : 
                $sql = "SELECT a.companyName
                        FROM companies a
                        INNER JOIN users b
                        ON a.companyID = b.companyID
                        WHERE b.userID = {$userID};";
                $result = $conn -> query($sql);
                if($row = $result->fetch_assoc()) {
                    print "<p class='userDetails text-left'>{$row['companyName']}</p>";
                    $_SESSION['currentEmployer'] = $row['companyName'];

                } 
                else {
                    print "<p class='text-left'>No Current Employer.</p>";
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
                        print "<p class='text-left'>Graduated {$resultRow['academicDescription']}, {$resultRow['academicLevel']} at {$resultRow['academicTitle']} on {$resultRow['completionDate']}</p>";
                    }
                } else {
                    print "<p class='text-left'>No Qualifications Found.</p>";
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
                        print "<p class='text-left'>{$resultRow['companyName']}, {$resultRow['FromDate']} - {$resultRow['ToDate']}</p>";
                    }
                } else {
                    print "<p class='text-left'>No Previous Job History Found.</p>";
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
                        print "<p class='text-left'>{$resultRow['skillTitle']}</p>";
                    }
                } else {
                    print "<p class='text-left'>No Skills Found.</p>";
                }
                break;

            default : break;
        }
        $conn -> close();
        
    }

?>
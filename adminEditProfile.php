

<html>
    <head>
        <title>Loop : Home</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="css/profile_user.css?v=<?php

            use function PHPSTORM_META\type;

        echo time(); ?>">

    </head>
    <script type="text/javascript">
        function refreshPage() {
            window.location.href = "editProfile.php";
        }
    </script>
    <body>
        <?php 
            
            include ("validateLoggedIn.php");

            function getUserData($uID) {
                include ("serverConfig.php");
                $conn = new mysqli($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE);
                if ($conn -> connect_error) {
                    die("Connection failed:" .$conn -> connect_error);
                }

                $sql = "select * from users where userID =\"{$uID}%\";";
                $result = $conn -> query($sql);
                $conn -> close();
                $row = $result->fetch_assoc();
                return $row;
            }

            include("headerTemplate.html");
            
        ?>

        <h1 class="page-header">Edit Profile</h1>
        <hr>
        <div class = "profile-container" >
            <div class = "profileImage">
                <?php

                    $userID = $_SESSION["user"];

                    $row = getUserData($userID);
                    $profileImage = null;

                    if (isset($row['profileImage'])) $profileImage = $row['profileImage'];

                    if($profileImage === null) {
                        print '<img src = "images/blank-profile-picture.png" alt="profile image" height="25%" width="18%" style="min-width:160px; min-height:160px; border-radius:50%;" >';
                    }
                    else {
                        print "<img src = 'profileImages/{$profileImage}' alt='profile image' height='25%' width='18%' style='min-width:160px; min-height:160px; border-radius:50%; object-fit: cover; overflow:hidden;' >";
                    }

                ?>
            </div>

            <div class="changeProfileImage">
                <form method="post" action="editProfile.php" enctype="multipart/form-data">
                    <input type="file" name="image">
                    <input type="submit" name="submitImage" value="Upload">
                </form>
            </div>
        </div>

        <div class = "description-container">
            <div class = "description-heading">
                <H1 style = "text-align: center;">Description</H1>
            </div>
            <div class = "bio-description">
                <form method="post" action="editProfile.php">
                    <h3>Enter Bio:</h3>
                    <?php

                        include ("serverConfig.php");
                        $conn = new mysqli($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE);
                        if ($conn -> connect_error) {
                            die("Connection failed:" .$conn -> connect_error);
                        }

                        $userID = $_SESSION['user'];
                        $sql = "select * from users where userID={$userID};";
                        $result = $conn -> query($sql);

                        if(isset($_POST['submitImage']) && $_FILES["image"]["name"]) {
                            
                            $profileImageName = time() . "_" . $_FILES["image"]["name"];
                            $target = "profileImages/" . $profileImageName;

                            str_replace(" ", "", $target);
                            if(copy($_FILES["image"]["tmp_name"], $target)) {
                                $userProfileImage ="UPDATE users 
                                                    SET profileImage='$profileImageName'
                                                    WHERE userID={$userID};";
                            }
                            
                            if($conn->query($userProfileImage)) {
                                header( "Location: profile_user.php" );
                            }
                        }

                        //Sets the description if one exists
                        $description = '';
                        if(isset($_COOKIE['description'])) $description = $_COOKIE['description'];
                        print "<textarea id='description' rows='5' cols='60' 
                                    name='description' pattern='[A-Za-z][0-9]{6,}' 
                                    title='Please input more than 6 characters. Letters and numbers only.'>
                                
                                    {$description}
                                    
                                </textarea>
                                <br>";

                        //User can select all skills they want
                        print '<h3>Select Skills</h3>
                                <select name="skills[]" multiple>';
                        $skillsSql = "select * from skills;";
                        $skillsResult = $conn -> query($skillsSql);
                        while($skillsRow = $skillsResult->fetch_assoc())
                        {   
                            $getUsersSkillsSql = "select * from userskills WHERE userID={$userID}";
                            $getUsersSkillsResult = $conn -> query($getUsersSkillsSql);
                            //Loop is incorrect prints all skills for how many times the user has skills
                            // while($getUsersSkillsRow = $getUsersSkillsResult->fetch_assoc()) {
                            // if($skillsRow['skillID'] == $getUsersSkillsRow['skillID']) print "<option value='{$skillsRow['skillTitle']}' selected>{$skillsRow['skillTitle']}</option>";
                            print "<option value='{$skillsRow['skillTitle']}'>{$skillsRow['skillTitle']}</option>";
                            // else print "<option value='{$skillsRow['skillTitle']}'>{$skillsRow['skillTitle']}</option>";
                            // }
                        }
                        print '</select><br>';

                        //User can select employer, if current one exists automatically selected
                        print '<h3>Select Current Employer</h3>
                                <select name="currentEmployer">
                                <option name="None">None</option>';
                        $currentEmployerSQL = "select * from companies;";
                        $currentEmployerResult = $conn -> query($currentEmployerSQL);
                        while($currentEmployerRow = $currentEmployerResult->fetch_assoc())
                        {   
                            if(isset($_COOKIE['currentEmployer']) && $_COOKIE['currentEmployer'] == $currentEmployerRow['companyName']) {
                                print "<option name='{$currentEmployerRow['companyName']}' selected>{$currentEmployerRow['companyName']}</option>";
                            } else print "<option name='{$currentEmployerRow['companyName']}'>{$currentEmployerRow['companyName']}</option>";
                        }
                        print '</select><br>';

                        //Added qualifications
                        print '<h3>Qualifications</h3>
                                <input type="text" placeholder="Enter University Name" name="University"></input>
                                <input type="text" placeholder="Enter Course Name" name="Course"></input>
                                <input type="text" placeholder="Enter QCA Level Name" name="Level"></input>
                                <label for="DateCompleted" style="padding-left:1%">Date Completed:</label>
                                <input type="date" name="DateCompleted">
                                <input type="submit" name="addQualification" value="Add Qualification"/>
                                <br>';

                        $qualificationSQL = "SELECT a.academicID, a.academicTitle, a.academicDescription, a.academicLevel, b.completionDate
                            FROM accademicdegrees a
                            INNER JOIN userqualificaion b
                            ON a.academicID = b.academicID
                            WHERE b.userID = {$_SESSION['user']};";
                        $qualificationResult = $conn -> query($qualificationSQL);
                        if(mysqli_num_rows($qualificationResult) != 0) {
                            while($qualificationRow = $qualificationResult->fetch_assoc()) {
                                print "<p>Graduated {$qualificationRow['academicDescription']}, {$qualificationRow['academicLevel']} at {$qualificationRow['academicTitle']} on {$qualificationRow['completionDate']}</p>
                                <a id='deletedQualification' href='editProfile.php?deletedQualification=true&currentUser={$userID}&academicID={$qualificationRow['academicID']}'>&#x2716;</a>";
                            }
                        }
                        
                        //User can select previous history
                        print '<h3>Select Job History</h3>
                                <select name="employementHistory">
                                <option name="None">None</option>';
                        $employerSQL = "select * from companies;";
                        $employerResult = $conn -> query($employerSQL);
                        while($employerRow = $employerResult->fetch_assoc())
                        {   
                            print "<option name='{$employerRow['companyName']}'>{$employerRow['companyName']}</option>";
                        }
                        print '</select>
                                <label for="dateStarted" style="padding-left:1%">Job Start:</label>
                                <input type="date" name="dateStarted">
                                <label for="dateEnded" style="padding-left:1%">Job End:</label>
                                <input type="date" name="dateEnded">
                                <input type="submit" name="addJobHistory" value="Add Employment History"/>
                                <br>';

                        $previousHistorySQL = "SELECT a.FromDate, a.ToDate, b.companyName, b.companyID
                            FROM jobhistory a
                            INNER JOIN companies b
                            ON a.companyID = b.companyID
                            WHERE a.userID = {$userID};";
                        $previousHistoryResult = $conn -> query($previousHistorySQL);
                        if(mysqli_num_rows($previousHistoryResult) != 0) {
                            while($previousHistoryRow = $previousHistoryResult->fetch_assoc()) {
                                print "<p>Graduated {$previousHistoryRow['companyName']}, {$previousHistoryRow['FromDate']} at {$previousHistoryRow['ToDate']}</p>
                                <a id='deleteJobHistory' href='editProfile.php?deleteJobHistory=true&currentUser={$userID}&companyID={$previousHistoryRow['companyID']}'>&#x2716;</a>";
                            }
                        }

                        $conn -> close();
                    ?>
                    <br>
                    <input type="submit" name="submit" value="Submit Edit"/>
                </form>
            </div>
        </div>
    </body>
</html>

<?php 

    include ("serverConfig.php");
    $conn = new mysqli($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE);
    if ($conn -> connect_error) {
        die("Connection failed:" .$conn -> connect_error);
    }

    function updateProfile($conn) {
        
        //Update user description
        $userID = $_SESSION['user'];
        $sql = "UPDATE users
                SET description = '{$_POST['description']}'
                WHERE userID = {$userID}";

        //Updates the skills if selected
        if(isset($_POST['skills'])) {
            $values = $_POST['skills'];
            
            $deleteSkills = "DELETE FROM userskills WHERE userID={$userID};";
            $conn -> query($deleteSkills);
            if ($conn->query($deleteSkills) === TRUE) {
                foreach($values as $value) {
                    $skillsSqlForm = "select * from skills where skillTitle=\"{$value}\";";
                    $skillsResultForm = $conn -> query($skillsSqlForm);
                    $skillsRowForm = $skillsResultForm->fetch_assoc();

                    $userSkillsSQL = "INSERT INTO userskills (userID, skillID)
                    VALUES ('{$_SESSION['user']}', '{$skillsRowForm['skillID']}')";
                    $conn->query($userSkillsSQL);
                }
            }
        } else {
            $deleteSkills = "DELETE FROM UserSkills WHERE userID={$userID};";
            $conn -> query($deleteSkills);
        }

        //Update users current employer
        if(isset($_POST['currentEmployer'])) {
            $currentEmployerSQLForm = "select * from companies where companyName=\"{$_POST['currentEmployer']}\";";
            $currentEmployerResultForm = $conn -> query($currentEmployerSQLForm);
            $currentEmployerRowForm = $currentEmployerResultForm->fetch_assoc();
            $companyName = $currentEmployerRowForm['companyID'];
            if($_POST['currentEmployer'] === "None") $companyName = 'NULL';
            $userCurrentEmployerSQL = "UPDATE users
            SET companyID = {$companyName}
            WHERE userID = {$userID}";
            $conn->query($userCurrentEmployerSQL);
        }


        if ($conn->query($sql) === TRUE) {
            // header( "Location: profile_user.php" );

        } 
        else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        
    }

    if(isset($_POST["submit"])) updateProfile($conn);

    if(isset($_POST["addQualification"])) {
        //Adds a qualification
        if(isset($_POST['University']) && isset($_POST['Course']) && isset($_POST['Level']) && isset($_POST['DateCompleted'])) {
            $accademicSQL = "INSERT INTO accademicdegrees (academicTitle, academicDescription, academicLevel)
            VALUES ('{$_POST['University']}', '{$_POST['Course']}', '{$_POST['Level']}')";
            $conn->query($accademicSQL);
            
            $accademicSQLGet = "select * from accademicdegrees where academicTitle=\"{$_POST['University']}\"
            AND academicDescription=\"{$_POST['Course']}\" AND academicLevel=\"{$_POST['Level']}\";";
            $accademicSQLGetResult = $conn -> query($accademicSQLGet);
            $accademicSQLGetRow = $accademicSQLGetResult->fetch_assoc();

            $insertAcademicIntoUser = "INSERT INTO userqualificaion (userID, academicID, CompletionDate)
            VALUES ('{$userID}', '{$accademicSQLGetRow['academicID']}', '{$_POST['DateCompleted']}')";
            $conn->query($insertAcademicIntoUser);
            echo "<script> refreshPage(); </script>";
        }
    };

    if(isset($_POST["addJobHistory"])) {
        //Adds a qualification
        if(isset($_POST['employementHistory']) && isset($_POST['dateStarted']) && isset($_POST['dateEnded']) && $_POST['employementHistory'] != 'None') {
            $companySQL = "select * from companies where companyName=\"{$_POST['employementHistory']}\";";
            $companySQLResult = $conn -> query($companySQL);
            $companySQLRow = $companySQLResult->fetch_assoc();

            $jobHistorySQL = "INSERT INTO jobhistory (userID, companyID, FromDate, ToDate)
            VALUES ('{$userID}', '{$companySQLRow['companyID']}', '{$_POST['dateStarted']}', '{$_POST['dateEnded']}')";
            $conn->query($jobHistorySQL);

            echo "<script> refreshPage(); </script>";
        }
    };

    if (isset($_GET['deletedQualification'])) {
        $deleteUserQualification = "DELETE FROM userqualificaion WHERE userID={$_GET['currentUser']} AND academicID={$_GET['academicID']};";
        $conn -> query($deleteUserQualification);
        echo "<script> refreshPage(); </script>";
    }

    if (isset($_GET['deleteJobHistory'])) {
        $deleteUserJobHistory = "DELETE FROM userqualificaion WHERE userID={$_GET['currentUser']} AND academicID={$_GET['academicID']};";
        $conn -> query($deleteUserQualification);
        echo "<script> refreshPage(); </script>";
    }

    $conn ->close();
?>
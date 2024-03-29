

<html>
    <head>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="css/addVacancy.css?v=<?php echo time(); ?>">
        <link rel="shortcut icon" type="image/x-icon" href="favicon.ico"/>

        <meta content="width=device-width, initial-scale=1" name="viewport" />

        <title>Loop : Add Vacancy</title>
    </head>
    <body>
        
        <?php 
            include ("validateLoggedIn.php");
            include ("companyTemplate.html"); 
        ?>

        <h1 class="page-heading">Add Vacancy</h1>
        <hr>
        <div class = "description-container">
            <div class = "bio-description">
                <form method="post" action="addVacancy.php">
                    <h3>Vacancy Title:</h3>
                    <input class="text-input" type='text' placeholder='Enter Vacancy Title' name='vacancyTitle' required></input>
                    <h3>Vacancy Description:</h3>
                    <textarea id='description' name='description' class='description-textarea' required></textarea><br>
                    <h3>Required Experience:</h3>
                    <input class="text-input" type='text' placeholder='Enter Req. Experience' name='reqExperience' required></input>
                    <h3>Vacancy Role:</h3>
                    <input class="text-input" type='text' placeholder='Enter Vacancy Role' name='vacancyRole' required></input>
                    <?php
                    include ("serverConfig.php");
                    $conn = new mysqli($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE);
                    if ($conn -> connect_error) {
                        die("Connection failed:" .$conn -> connect_error);
                    }

                    //User can select all skills they want
                    print '<h3>Select Skills for Vacancy</h3>
                    <p>Please Hold Ctrl to select multiple skills</p>
                    <select class="skills" name="skills[]" multiple>';
                    $skillsSql = "select * from skills;";
                    $skillsResult = $conn -> query($skillsSql);
                    while($skillsRow = $skillsResult->fetch_assoc())
                    {   
                        print "<option value='{$skillsRow['skillID']}'>{$skillsRow['skillTitle']}</option>";
                    }
                    print '</select><br>';
                    ?>
                    <br>
                    <br>
                    <input class="button" type="submit" name="submit" value="Submit Vacancy"/>
                </form>
            </div>
        </div>
    </body>
</html>

<?php 

    function addVacancy() {
        
        include ("serverConfig.php");
        $conn = new mysqli($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE);
        if ($conn -> connect_error) {
            die("Connection failed:" .$conn -> connect_error);
        }
        $companyID = $_SESSION['company'];
        if(isset($_POST['skills'])) $skills = $_POST['skills'];

        $sql = "INSERT INTO vacancies (companyID, vacancyTitle, vacancyDescription, requiredExperience, role)
                VALUES ('{$companyID}', '{$_POST['vacancyTitle']}', '{$_POST['description']}', '{$_POST['reqExperience']}', '{$_POST['vacancyRole']}')";

        if ($conn->query($sql) === TRUE) {
            $last_id = mysqli_insert_id($conn);
            if(isset($skills)) {
                foreach($skills as $skill) {
                    $skillSQL = "INSERT INTO skillsforvacancy (vacancyID, skillID)
                    VALUES ('{$last_id}', '{$skill}')";
                    $conn->query($skillSQL);
                }
            }
            header( "Location: organizationHome.php" );

        } 
        else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        $conn->close();
    }

    if(isset($_POST["submit"])) addVacancy();
?>
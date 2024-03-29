<html>
    <head>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="css/loopedJobs.css?v=<?php echo time(); ?>">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <link rel="shortcut icon" type="image/x-icon" href="favicon.ico"/>

        <title>Loop : Looped Jobs</title>

    </head>
    <body>
        <?php include ("headerTemplate.html"); ?>
        <h1 class='page-heading'>My Looped Jobs</h1>
        <hr>
        <div class="page-box">
            <?php
                include ("validateLoggedIn.php");
                include ("serverConfig.php");

                $conn = new mysqli($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE);
                if ($conn -> connect_error) {
                    die("Connection failed:" .$conn -> connect_error);
                }
                $sql = "SELECT a.companyName, b.vacancyTitle, c.status
                        FROM companies a 
                        INNER JOIN vacancies b
                        ON a.companyID = b.companyID
                        INNER JOIN looped c
                        ON b.vacancyID = c.vacancyID
                        WHERE c.userID = {$_SESSION['user']};";
                $result = $conn -> query($sql);
                    print "<table class='loopedJobs'>";
                    print "<thead>
                                <tr>
                                    <th>Company Name</th>
                                    <th>Job Title</th>
                                    <th>Status</th>
                                </tr>
                            </thead>";
                            
                    if(mysqli_num_rows($result) != 0) {
                        while($row = $result->fetch_assoc())
                        {   
                            print "<TR>";
                            print "<TD>{$row['companyName']}</TD>";
                            print "<TD>{$row['vacancyTitle']}</TD>";
                            print "<TD>{$row['status']}</TD>";
                            print "</TR>";
                        }
                    } else {
                        print "<TR>";
                        print "<TD colspan='3'>No Looped Jobs</TD>";
                        print "</TR>";
                    }
                    $conn->close();
                    print "</table>";

            ?>
        </div>
    </body>
</html>
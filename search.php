<html>
    <head>
        <title>Loop : Search</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="css/search.css?v=<?php echo time(); ?>">
        <link rel="shortcut icon" type="image/x-icon" href="favicon.ico"/>
        
        <meta content="width=device-width, initial-scale=1" name="viewport" />

        <script type="text/javascript">
            function makeConnection(variable) {
                window.location.href= 'addConnection.php?id=' + variable;
            }

            function deleteConnection(variable) {
                if (confirm("Are you sure you want to delete this connection?") == true) {
                    window.location.href= 'deleteConnection.php?id=' + variable;
                };
            }

            function changeFunc() {
                var selectBox = document.getElementById("select");
                var selectedValue = selectBox.options[selectBox.selectedIndex].value;
                window.location.href= 'search.php?search=' + selectedValue;
            }

            function jsfunction() {
                const queryString = window.location.search;
                const urlParams = new URLSearchParams(queryString);
                const product = urlParams.get('search');
                document.getElementById('select').value = product;
            }
        </script>
    </head>
    <body>
        <?php 
            include ("headerTemplate.html");
            include ("validateLoggedIn.php");
            include ("serverConfig.php"); 
        ?>
        
        <h1 class="page-heading">Search Page</h1>
        <hr>
        <form class="search" method="post" action="search.php">
            <div class="selectedForm">
                <select class="select" id="select" name="selectVal" onchange="changeFunc()">
                    <option value="name">Name</option>
                    <option value="companyName">Company Name</option>
                    <option value="skill">Skill</option>
                    <option value="previousHistory">Previous History</option>
                    <option value="currentlyEmployed">Currently Employed</option>
                </select>
            </div>
            <?php
                $conn = new mysqli($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE);
                if ($conn -> connect_error) {
                    die("Connection failed:" .$conn -> connect_error);
                }

                if(!isset($_GET['search'])) print '<input class="input" type="text" name="value" placeholder="Search for User">';
                if(isset($_GET['search']) && $_GET['search'] == "name") {
                    print '<input class="input" type="text" name="value" placeholder="Search for User">';
                    echo '<script type="text/javascript">jsfunction();</script>';
                }
                if(isset($_GET['search']) && $_GET['search'] == "companyName") {
                    print '<input class="input" type="text" name="value" placeholder="Search for Company">';
                    echo '<script type="text/javascript">jsfunction();</script>';
                }
                if(isset($_GET['search']) && $_GET['search'] == "skill") {
                    $skillsSql = "select * from skills;";
                    $skillsResult = $conn -> query($skillsSql);
                    print "<div class='selectedForm'><select class='select' name='skill'><option value=''>Select A Skill</option>";
                    while($skillsRow = $skillsResult->fetch_assoc())
                    {   
                        print "<option value='{$skillsRow['skillTitle']}'>{$skillsRow['skillTitle']}</option>";
                    }
                    print '</select></div>';
                    echo '<script type="text/javascript">jsfunction();</script>';
                }
                if(isset($_GET['search']) && $_GET['search'] == "previousHistory") {
                    print '<input class="input" type="text" name="value" placeholder="Search for User">';
                    echo '<script type="text/javascript">jsfunction();</script>';
                }
                if(isset($_GET['search']) && $_GET['search'] == "currentlyEmployed") {
                    $companySQL = "select * from companies;";
                    $companyResult = $conn -> query($companySQL);
                    print "<div class='selectedForm'><select class='select' name='company'><option value=''>Select A Company</option>";
                    while($companyRow = $companyResult->fetch_assoc())
                    {   
                        print "<option value='{$companyRow['companyName']}'>{$companyRow['companyName']}</option>";
                    }
                    print '</select></div>';
                    echo '<script type="text/javascript">jsfunction();</script>';
                }
                
            ?>
            <input class="submit" type="submit" value="Search">
        </form>
        <div class="page-box">
            <?php
                if(isset($_POST["selectVal"])) {
                    
                    $conn = new mysqli($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE);
                    if ($conn -> connect_error) {
                        die("Connection failed:" .$conn -> connect_error);
                    }

                    if ($_POST["selectVal"] == "name") {
                        $name = $_POST["value"];
                        printSearchFor("Name", $name);
                        $sql = "select * from users where username LIKE \"{$name}%\" AND userID !={$_SESSION['user']} AND Admin IS NULL;";
                        $result = $conn -> query($sql);

                        if(mysqli_num_rows($result) != 0) {
                            while($row = $result->fetch_assoc())
                            {   
                                print "<div class='user'>";
                                
                                $profileImage = null;

                                if (isset($row['profileImage'])) $profileImage = $row['profileImage'];
                                
                                if($profileImage === null) {
                                    print '<img class="userImage" src ="images/blank-profile-picture.png" 
                                            alt="profile image"><br>';
                                }
                                else {
                                    print "<img class='userImage' src = 'profileImages/{$profileImage}' 
                                            alt='profile image'><br>";
                                
                                }
                                
                                print "<a class='userDetails' style='margin-top: 10%' href='profile.php?userID={$row['userID']}'><b>{$row['username']}</b></a><br><br>";

                                $connectionsSQL = "select * from connections where userIDFirst = \"{$_SESSION['user']}%\" AND userIDSecond = \"{$row['userID']}%\";";
                                $result2 = $conn -> query($connectionsSQL);
                                $connectionsRow = $result2->fetch_assoc();
                                print "<div class='friend-req-details'>";
                                if($connectionsRow) {
                                    if($connectionsRow['status'] !== "Pending") {
                                        print "<button class='btn-unconnect' onClick='deleteConnection({$row['userID']})'> Disconnect </button><br>";
                                    }
                                    else {
                                        print "<br><a class='pending'> Pending </a><br>";
                                    }
                                } 
                                else {
                                    print "<button class='btn-friend-req' onClick='makeConnection({$row['userID']})'> Connect </button><br>";
                                }

                                print "</div> </div>";
                            }
                            $conn->close();
                        } 
                        else {
                            print "<h1>No Users found.</h1>";
                        }
                    }

                    if ($_POST["selectVal"] == "companyName") {
                        $companyName = $_POST["value"];
                        printSearchFor("Company Name", $companyName);
                        $sql = "select * from companies where companyName LIKE \"{$companyName}%\";";
                        $result = $conn -> query($sql);
                        if(mysqli_num_rows($result) != 0) {
                            while($row = $result->fetch_assoc())
                            {   
                                print "<div class='user'>";

                                $profileImage = null;

                                if (isset($row['profileImage'])) $profileImage = $row['profileImage'];
                                
                                if($profileImage === null) {
                                    print '<img class="userImage" src ="images/blank-company.png" 
                                            alt="profile image" >';
                                }
                                else {
                                    print "<img class='userImage' src = 'profileImages/{$profileImage}' 
                                            alt='profile image' >";
                                
                                }

                                print "<a class='userDetails' href='company.php?companyID={$row['companyID']}'><b>{$row['companyName']}</b></a>";
                                print "</div>";
                            }
                            $conn->close();
                        } else {
                            print "<h1>No Company found.</h1>";
                        }
                    }

                    if ($_POST["selectVal"] == "skill") {
                        $skill = $_POST["skill"];
                        printSearchFor("skills", $skill);
                        $SQL = "select a.userID, a.email, a.username, a.Admin, a.profileImage
                                from users a
                                INNER JOIN userskills b
                                ON a.userID = b.userID
                                INNER JOIN skills c
                                ON b.skillID = c.skillID
                                WHERE a.userID != {$_SESSION['user']} AND c.skillTitle LIKE \"{$skill}%\"  AND a.Admin IS NULL;";
                        $skillResult = $conn -> query($SQL);

                        if(mysqli_num_rows($skillResult) != 0) {
                                while($skillRow = $skillResult->fetch_assoc()) {
                                    print "<div class='user'>";

                                    $profileImage = null;

                                    if (isset($skillRow['profileImage'])) $profileImage = $skillRow['profileImage'];
                                    
                                    if($profileImage === null) {
                                        print '<img class="userImage" src ="images/blank-profile-picture.png" 
                                                alt="profile image"><br>';
                                    }
                                    else {
                                        print "<img class='userImage' src = 'profileImages/{$profileImage}' 
                                                alt='profile image'><br>";
                                    
                                    }

                                    print "<a class='userDetails' href='profile.php?userID={$skillRow['userID']}'><b>{$skillRow['username']}</b></a>";
                                    $connectionsSQL = "select * from connections where userIDFirst = \"{$_SESSION['user']}%\" AND userIDSecond = \"{$skillRow['userID']}%\";";
                                    $result2 = $conn -> query($connectionsSQL);
                                    $connectionsRow = $result2->fetch_assoc();
                                    print "<div class='friend-req-details'>";
                                if($connectionsRow) {
                                    if($connectionsRow['status'] !== "Pending") {
                                        print "<button class='btn-unconnect' onClick='deleteConnection({$skillRow['userID']})'> Disconnect </button><br>";
                                    }
                                    else {
                                        print "<br><a class='pending'> Pending </a><br>";
                                    }
                                } 
                                else {
                                    print "<button class='btn-friend-req' onClick='makeConnection({$skillRow['userID']})'> Connect </button><br>";
                                }

                                print "</div> </div>";
                                }
                        } 
                        else {
                            print "<h1>No Users found with the skill \"{$skill}\".</h1>";
                        }
                        $conn->close();
                    }

                    if ($_POST["selectVal"] == "currentlyEmployed") {
                        $currentlyEmployed = $_POST["company"];
                        printSearchFor("Currently Employed", $currentlyEmployed);
                        $SQL = "select a.userID, a.email, a.username, a.Admin, a.profileImage
                                from users a
                                INNER JOIN companies b
                                ON a.companyID = b.companyID
                                WHERE a.userID != {$_SESSION['user']} AND b.companyName LIKE \"{$currentlyEmployed}%\" AND a.Admin IS NULL;";
                        $currentlyEmployedResult = $conn -> query($SQL);
                        if(mysqli_num_rows($currentlyEmployedResult) != 0) {
                                while($currentlyEmployedRow = $currentlyEmployedResult->fetch_assoc()) {
                                    print "<div class='user'>";

                                    $profileImage = null;

                                    if (isset($currentlyEmployedRow['profileImage'])) $profileImage = $currentlyEmployedRow['profileImage'];
                                    
                                    if($profileImage === null) {
                                        print '<img class="userImage" src ="images/blank-profile-picture.png" 
                                                alt="profile image"><br>';
                                    }
                                    else {
                                        print "<img class='userImage' src = 'profileImages/{$profileImage}' 
                                                alt='profile image'><br>";
                                    
                                    }

                                    print "<a class='userDetails' href='profile.php?userID={$currentlyEmployedRow['userID']}'><b>{$currentlyEmployedRow['username']}</b></a>";
                                    $connectionsSQL = "select * from connections where userIDFirst = \"{$_SESSION['user']}%\" AND userIDSecond = \"{$currentlyEmployedRow['userID']}%\";";
                                    $result2 = $conn -> query($connectionsSQL);
                                    $connectionsRow = $result2->fetch_assoc();
                                    print "<div class='friend-req-details'>";
                                    if($connectionsRow) {
                                        if($connectionsRow['status'] !== "Pending") {
                                            print "<button class='btn-unconnect' onClick='deleteConnection({$currentlyEmployedRow['userID']})'> Disconnect </button><br>";
                                        }
                                        else {
                                            print "<br><a class='pending'> Pending </a><br>";
                                        }
                                    } 
                                    else {
                                        print "<button class='btn-friend-req' onClick='makeConnection({$currentlyEmployedRow['userID']})'> Connect </button><br>";
                                    }
    
                                    print "</div> </div>";
                                }
                        } else {
                            print "<h1>No Users found employed by \"{$currentlyEmployed}\".</h1>";
                        }
                        $conn->close();
                    }

                    if ($_POST["selectVal"] == "previousHistory") {
                        $previousHistory = $_POST["value"];
                        printSearchFor("Previous History", $previousHistory);
                        $SQL = "select a.userID, a.email, a.username, a.Admin, a.profileImage
                                from users a
                                INNER JOIN jobhistory b
                                ON a.userID = b.userID
                                INNER JOIN companies c
                                On b.companyID = c.companyID
                                WHERE a.userID != {$_SESSION['user']} AND c.companyName LIKE \"{$previousHistory}%\" AND a.Admin IS NULL;";
                        $previousHistoryResult = $conn -> query($SQL);
                        if(mysqli_num_rows($previousHistoryResult) != 0) {
                                while($previousHistoryRow = $previousHistoryResult->fetch_assoc()) {
                                    print "<div class='user'>";

                                    $profileImage = null;

                                    if (isset($previousHistoryRow['profileImage'])) $profileImage = $previousHistoryRow['profileImage'];
                                    
                                    if($profileImage === null) {
                                        print '<img class="userImage" src ="images/blank-profile-picture.png" 
                                                alt="profile image"><br>';
                                    }
                                    else {
                                        print "<img class='userImage' src = 'profileImages/{$profileImage}' 
                                                alt='profile image'><br>";
                                    
                                    }

                                    print "<a class='userDetails' href='profile.php?userID={$previousHistoryRow['userID']}'><b>{$previousHistoryRow['username']}</b></a>";
                                    $connectionsSQL = "select * from connections where userIDFirst = \"{$_SESSION['user']}%\" AND userIDSecond = \"{$previousHistoryRow['userID']}%\";";
                                    $result2 = $conn -> query($connectionsSQL);
                                    $connectionsRow = $result2->fetch_assoc();
                                    print "<div class='friend-req-details'>";
                                    if($connectionsRow) {
                                        if($connectionsRow['status'] !== "Pending") {
                                            print "<button class='btn-unconnect' onclick='deleteConnection({$previousHistoryRow['userID']})'> Disconnect </button><br>";
                                        }
                                        else {
                                            print "<br><a class='pending'> Pending </a><br>";
                                        }
                                    } 
                                    else {
                                        print "<button class='btn-friend-req' onClick='makeConnection({$previousHistoryRow['userID']})'> Connect </button><br>";
                                    }
    
                                    print "</div> </div>";
                                }
                        } else {
                            print "<h1>No Users found that worked at \"{$previousHistory}\".</h1>";
                        }
                        $conn->close();
                    }
                }

                function printSearchFor($searchVal, $searchTerm) {
                    print "<h1 class='page-header'>Search by {$searchVal} for {$searchTerm}</h1>";
                }
            ?>
        </div>
    </body>
</html>
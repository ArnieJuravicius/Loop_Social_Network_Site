<html>
    <head>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="css/login.css?v=<?php echo time(); ?>">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <link rel="shortcut icon" type="image/x-icon" href="favicon.ico"/>

        <title>Loop : Company Login</title>

        <script> 
            function showLoginError(variable) {
                var alert = document.getElementById("alert");
                var node = document.createTextNode(variable);
                alert.appendChild(node);
            }
        </script>


    </head>
    <body>
        <div class="wrapper">
            <div class="banner">
                    <div class="login">
                        <h2 style="text-align: left;">Company Login</h2>
                        <form method="post" action="organizationLogin.php">
                            <input class="input" type="email" name="email" placeholder="Organization Email" required
                                value="<?php if(isset($_COOKIE['email'])){echo $_COOKIE['email'];} else{echo "";} ?>">
                            <br>
                            <input class="input" type="password" name="pass" placeholder="Password" required
                                id="password">
                            <br>
                            <h6 id="alert"></h6>
                            <input class="button" name="submit" type="submit" value="Login" style="margin-left: 27%;">
                        </form>
                            <div class="buttons">
                                <div class="register">
                                    <h5>New Here?</h5>
                                    <button class="button" onClick="location.href='organizationRegister.php'">Register</button>
                                </div>
                            </div>
                        <a href="login.php" style="color: white; padding-left: 10%">Are you a User? Click here</a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

<?php

    if(isset($_POST['submit'])) {

        include ("serverConfig.php");
        
        $conn = new mysqli($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE);
        if ($conn -> connect_error) {
            die("Connection failed:" .$conn -> connect_error);
        }

        session_start();
        session_unset();

        $email = $_POST['email'];
        $password = $_POST['pass'];

        setcookie("email", $email, time()+250);
        
        $sql = "select * from companies where email=\"{$email}\";"; //get the companys details
        $result = $conn -> query($sql);
        $row = $result->fetch_assoc();
        $companyID = $row["companyID"];
        $sqlEmail = $row["email"];
        $sqlPass = $row["password"];

        $bannedSql = "SELECT * FROM bannedcompany 
                      WHERE companyID = {$companyID};";
        $bResult = $conn -> query($bannedSql);
        $bRow = $bResult->fetch_assoc();

        function emailMatches ($inputEmail, $DBEmail) {
            return strcasecmp($inputEmail, $DBEmail) == 0;
        }
        if(mysqli_num_rows($bResult) !== 0 ) {
            echo "<script> showLoginError('This company is banned.') </script>";
        }
        else if(emailMatches($email, $sqlEmail) && password_verify($password, $sqlPass)) {
            $_SESSION['company'] = $companyID;
            $_SESSION['loggedin'] = true;
            $_SESSION['companyName'] = $row['companyName'];
            header( "Location: organizationHome.php" );
        }
        else {
            echo "<script> showLoginError('Incorrect email or password.') </script>";
        }

        $conn->close(); 
    }
?>
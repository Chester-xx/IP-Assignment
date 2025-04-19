<?php session_start(); ?>
<?php
    try {
        $conn = mysqli_connect(hostname: "localhost", username: "root", password: "", database: "usersdb");
    } catch(mysqli_sql_exception) {
        echo "Error : Could not establish connection to database";
        exit;
    }
?>
<?php // did the user logout
    if (isset($_GET["logout"])) {
        session_destroy();
        mysqli_close(mysql: $conn);
        header(header: "Location: index.php");
        exit;
    }
?>
<?php // has to be here as header redirection doesnt like any html at all
// Did the user click on create account
    if (isset($_POST["processaccount"])) {
        // decl vars and sanitize | trim removes spaces
        $nemail = filter_input(type: INPUT_POST, var_name: 'createemail', filter: FILTER_VALIDATE_EMAIL);
        $npassword = trim(string: filter_input(type: INPUT_POST, var_name: 'createpassword', filter: FILTER_DEFAULT));
        $npasswordconfirm = filter_input(type: INPUT_POST, var_name: 'createpasswordconfirm', filter: FILTER_DEFAULT);

        // check if empty
        if ($nemail == "" || $npassword == "" || $npasswordconfirm == "") {
            header(header: "Location: index.php?createaccount=true&error=Please+ensure+you+have+entered+all+the+fields");
            exit;
        // check if of length
        } elseif (strlen(string: $npassword) < 8) {
            header(header: "Location: index.php?createaccount=true&error=Please+enter+a+password+with+atleast+8+characters");
            exit;
        // check if valid email
        } elseif ($nemail === false) {
            header(header: "Location: index.php?createaccount=true&error=Please+enter+a+valid+email");
            exit;
        } elseif ($npassword != $npasswordconfirm) {
            header(header: "Location: index.php?createaccount=true&error=Please+ensure+both+passwords+are+the+same");
            exit;
        } elseif (!$npassword || !$npasswordconfirm) {
            header(header: "Location :index.php?createaccount=true&error=Something+went+wrong+when+you+entered+your+password");
            exit;
        }
        // hash password
        $npassword = password_hash(password: $npassword, algo: PASSWORD_DEFAULT);
        // LOGIC FOR DATABASE
        $nemail = strtolower(string: $nemail);
        // Ignoring sql injection as a factor
        $qry = "Select `Email` From tblUsers Where LOWER('$nemail') = LOWER(`Email`)";
        $duplicate = mysqli_query(mysql: $conn, query: $qry);
        // does email already exist in db
        if (mysqli_num_rows(result: $duplicate) > 0) {
            header(header: "Location: index.php?createaccount=true&error=An+account+with+that+email+already+exists");
            exit;
        }
        // insert into table new info
        $qry = "Insert Into tblUsers (`Email`, `Password`) Values ('$nemail', '$npassword')";
        mysqli_query(mysql: $conn, query: $qry);
        // redirect to login
        header(header: "Location: index.php");
        exit;
    }
?>
<?php
    if (isset($_POST["login"])) {
        // decl vars and sanitize
        $email = filter_input(type: INPUT_POST, var_name: 'email', filter: FILTER_VALIDATE_EMAIL);
        $password = filter_input(type: INPUT_POST, var_name: 'password', filter: FILTER_DEFAULT);

        if ($email == "" || $password == "") {
            header(header: "Location: index.php?error=Please+ensure+you+have+entered+all+the+fields");
            exit;
        }
        if (strlen(string: $password) < 8) {
            header(header: "Location: index.php?error=Please+enter+a+password+with+atleast+8+characters");
            exit;
        }

        // LOGIC FOR DATABASE

        $qry = "Select `Email`, `Password` From tblUsers Where LOWER('$email') = LOWER(`Email`)";
        $result = mysqli_query(mysql: $conn, query: $qry);
        // are there any accounts with that email
        if (mysqli_num_rows(result: $result) < 1) {
            header(header: "Location: index.php?error=Email+or+password+incorrect");
            exit;
        } // saves processing power? / time?
        $Password = mysqli_fetch_assoc(result: $result)["Password"];
        // to verify password
        if (!password_verify(password: $password, hash: $Password)) {
            header(header: "Location: index.php?error=Email+or+password+incorrect");
            exit;
        }

        // STORE EMAIL IN SESSION - Also redirects due to var tracker
        $_SESSION["email"] = $email;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Processing System</title>
    <style>
        body {
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            background-color:rgb(223, 223, 223);
        }
        .cont {
            display: flex;
            justify-content: center;
            margin-left: 5%;
            margin-right: 5%;
        }
        .column {
            display: flex;
            flex-direction: column;
        }
        .box {
            background-color:rgb(255, 255, 255);
            margin: 20px auto;
            padding-left: 50px;
            padding-right: 50px;
            padding-top: 40px;
            padding-bottom: 30px;
            width: 66%;
            height: fit-content - 1%;
            display: flex;
            flex-direction: column;
            border-radius: 10px;
            color:rgb(0, 0, 0);
        }
        hr {
            border: 1px solid #3498db;
            width: 100%;
        }
        .desc {
            font-size: x-large;
            margin-bottom: 5px;
            font-weight: 400;
        }
        button {
            align-self: center;
            width: 100%;
            color: white;
            font-size: large;
            background-color: #3498db;
            height: 45px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
        }
        input {
            width: 100%;
            height: 40px;
            font-size: larger;
            border: none;
            border-radius: 8px;
            padding: 5px 10px;
            box-sizing: border-box;
            background-color:rgb(223, 223, 223);
            margin-bottom: 5%;
        }
        select {
            width: 100%;
            height: 40px;
            font-size: larger;
            border: none;
            border-radius: 8px;
            padding: 5px 10px;
            appearance: none;
            cursor: pointer;
            background-color:rgb(223, 223, 223);
            font-weight: normal;
            color:rgb(100, 100, 100);
            margin-bottom: 5%;
        }
        h1 {
            margin-top: 0rem;
            margin-bottom: 0rem;
        }
        .err {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php
        // prompt user to login or create an account - !isset to check if the user is currently making an account
        if ($_SERVER["REQUEST_METHOD"] == "GET" && !isset($_GET["createaccount"]) && empty($_SESSION["email"])) {
            ?>
            <header style="background-color:rgb(43, 115, 156); text-align: center; padding: 1%;">
                <h1>Payment Processing System</h1>
                <p>Log Into Your Existing Account | Create A New Account</p>
            </header>
            <div class="box" style="width: 30%;">
                <form method="POST">
                    <h1>Log Into Your Account</h1>
                    <hr><br>
                    <span class="desc">Email</span>
                    <input type="email" name="email">
                    <span class="desc">Password</span>
                    <input type="password" name="password">
                    <button type="submit" name="login">Login</button>
                    <p style="text-align: center;">Don't have an account? <a style="color: #3498db;" href="?createaccount=true">Create One</a></p>
                </form>
            </div>
        <?php
            if (isset($_GET["error"])) {
                echo "<h3 class='err'>" . (htmlspecialchars(string: $_GET["error"])) . "</h3>";
            }
        }
    ?>
    <?php
        // Did the user login
        if (!empty($_SESSION["email"])) {
            ?>
            <header style="background-color:rgb(43, 115, 156); text-align: center; padding: 1%;">
                <h1>Payment Processing System</h1>
                <p>Manage Funds | Make Payments And Deposits | View Transaction History</p>
            </header>
            <!-- contains two dividers in columns -->
            <div class="cont">
                <!-- contains two dividers in rows -->
                <div class="column">
                    <!-- user account -->
                    <div class="box">
                        <form method="POST">
                            <h1>User account</h1>
                            <hr>
                            <span class="desc" style="text-align: center;">Current Balance<b><br><span style="font-size: xx-large;"><?php echo "R" . (number_format(num: 149975.50, decimals: 2));?></b></span></span>
                            <button type="submit" style="background-color:rgb(47, 184, 92);" name="addfunds">Add Funds</button>
                        </form>
                        <!-- GET Method to redirect and resend header on logout -->
                        <form method="GET" action="index.php">
                            <button type="submit" name="logout">Log Out</button>
                        </form>
                    </div>
                    <!-- payment -->
                    <div class="box">
                        <form method="POST">
                            <h1>Make Payment</h1>
                            <hr>
                            <span class="desc">Amount (R)</span>
                            <input type="number" name="amount">
                            <span class="desc">Payment Method</span>
                            <select name="paymentmethod">
                                <option value="" disabled selected>Select payment method</option>
                                <option value="creditcard">Credit Card</option>
                                <option value="paypal">PayPal</option>
                                <option value="cryptocurrency">Crypto Currency</option>
                            </select>
                            <span class="desc">Description</span>
                            <input type="text" name="description" placeholder="What is this payment for?">
                            <button type="submit" name="processpayment">Process Payment</button>
                        </form>
                    </div>
                </div>
                <!-- transaction history -->
                <div class="box" style="overflow-y: auto; overflow-x: hidden;">
                    <form method="POST">
                        <h1>Transaction History</h1>
                        <hr>
                    </form>
                    
                </div>
            </div>
            <?php
        }
    ?>
    <?php
        // Did the user go to the account creation page
        if (isset($_GET["createaccount"])) {
            ?>
            <header style="background-color:rgb(43, 115, 156); text-align: center; padding: 1%;">
                <h1>Payment Processing System</h1>
                <p>Create A New Account</p>
            </header>
            <div class="box" style="width: 30%;">
                <form method="POST">
                    <h1>Create A New Account</h1>
                    <hr><br>
                    <span class="desc">Email</span>
                    <input type="email" name="createemail" placeholder="example@gmail.com">
                    <span class="desc">Password</span>
                    <input type="password" name="createpassword" placeholder="Enter password">
                    <span class="desc">Confirm Password</span>
                    <input type="password" name="createpasswordconfirm" placeholder="Confirm password">
                    <button type="submit" name="processaccount">Create Account</button>
                </form>
            </div>
            <?php
            if (isset($_GET["error"])) {
                echo "<h3 class='err'>" . (htmlspecialchars(string: $_GET["error"])) . "</h3>";
            }
        }
    ?>
</body>
</html>
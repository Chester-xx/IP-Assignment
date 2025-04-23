<?php
    // -- DATABASE NOTES --

        // UsersDB ->
            // tblUsers ->
                // - UserID | PK | int auto_inc | not null | 
                // - Email | Unique | varchar(255) | not null |
                // - Password | Hashed | varchar(255) | not null |
                // - Balance | Unsigned | decimal(20,2) | default 0.00 |

            // tblTransactions ->
                // - ID | PK | int auto_inc | not null |
                // - UserID | FK | int | not null |
                // - Cost | Unsigned | decimal(20,2) | not null |
                // - Fee | Unsigned | decimal(20,2) | default 0.00 |
                // - Description | varchar(30) | utf8 general | not null |
                // - Time | TimeStamp | default Current_TimeStamp |
                // - Type | varchar(1) | utf8 general | default 'p' |

    // -- OTHER NOTES --

        // Event Listeners contain header redirects - have to be placed above any html
        // Basic dynamic structure with php and html indentation within main header
        // Session keeps user logged in until they log out
        // Could have implemented functions but didnt think of it at first - wouldve reduced the code by quite a bit
        // As you can tell, my skills with structure improved by alot from doing q1 -> q3 -> q2
        // So my best code and logic was implemented in question 2 as i learnt the most from before

    session_start();
?>
<?php
    try {
        // connect database
        $conn = mysqli_connect(hostname: "localhost", username: "root", password: "", database: "usersdb");
    } catch(mysqli_sql_exception) {
        // catch any error
        echo "Error : Could not establish connection to database";
        exit;
    }
?>
<?php // did the user logout
    if (isset($_GET["logout"])) {
        // stop the session
        session_destroy();
        // close the database connection
        mysqli_close(mysql: $conn);
        // redirect the user
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
        // is the entered information not reflective
        } elseif ($npassword != $npasswordconfirm) {
            header(header: "Location: index.php?createaccount=true&error=Please+ensure+both+passwords+are+the+same");
            exit;
        // if sanitization returned false : invalid input
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
    // did the user click on login
    if (isset($_POST["login"])) {
        // decl vars and sanitize
        $email = filter_input(type: INPUT_POST, var_name: 'email', filter: FILTER_VALIDATE_EMAIL);
        $password = filter_input(type: INPUT_POST, var_name: 'password', filter: FILTER_DEFAULT);
        // are the fields empty
        if ($email == "" || $password == "") {
            header(header: "Location: index.php?error=Please+ensure+you+have+entered+all+the+fields");
            exit;
        }
        // is the password too short
        if (strlen(string: $password) < 8) {
            header(header: "Location: index.php?error=Please+enter+a+password+with+atleast+8+characters");
            exit;
        }
        // get the email matching the users input
        $qry = "Select `Email`, `Password` From tblUsers Where LOWER('$email') = LOWER(`Email`)";
        $result = mysqli_query(mysql: $conn, query: $qry);
        // are there any accounts with that email
        if (mysqli_num_rows(result: $result) < 1) {
            header(header: "Location: index.php?error=Email+or+password+incorrect");
            exit;
        } // saves processing power? / time?
        // get the password
        $Password = mysqli_fetch_assoc(result: $result)["Password"];
        // to verify password
        if (!password_verify(password: $password, hash: $Password)) {
            header(header: "Location: index.php?error=Email+or+password+incorrect");
            exit;
        }
        // STORE EMAIL IN SESSION - Also redirects due to variable tracker within the embedded html 
        $_SESSION["email"] = $email;
    }
?>
<?php
    // mimics the deposit of money with no payment system
    // did the user click on add funds
    if (isset($_POST["addfunds"])) {
        // decl vars and sanitize | trim removes spaces
        $amount = filter_input(type: INPUT_POST, var_name: 'addamount', filter: FILTER_VALIDATE_FLOAT);
        // did the user enter the deposit amount
        if (isset($_POST["addamount"]) && !empty($amount)) {
            // check if empty
            if (empty($amount)) {
                header(header: "Location: index.php?errorfunds=Please+enter+a+deposit+amount");
                exit;
              // isnumeric?
            } elseif (!is_numeric(value: $amount)) {
                header(header: "Location: index.php?errorfunds=Please+enter+a+number+as+a+deposit+amount");
                exit;
              // is less than 1?
            } elseif ($amount < 1) {
                header(header: "Location: index.php?errorfunds=Please+enter+a+deposit+amount+of+atleast+R1");
                exit;
            }
            // Update users balance
            $Updateqry = "Update tblUsers Set `Balance` = `Balance` + '$amount' Where LOWER(`Email`) = LOWER('" . $_SESSION["email"] . "')";
            mysqli_query(mysql: $conn, query: $Updateqry);
            // Get UserID
            $Getqry = "Select `UserID` From tblUsers Where LOWER(`Email`) = LOWER('" . $_SESSION["email"] . "')";
            $result = mysqli_query(mysql: $conn, query: $Getqry);
            if (!$result) {
                header(header: "Location: index.php?errorfunds=User+not+found+in+database");
                exit;
            }
            $ID = mysqli_fetch_assoc(result: $result)["UserID"];
            // Insert into tblTransactions - no deposit fee - default d type for deposit
            $Insertqry = "Insert Into `tblTransactions` (`UserID`, `Cost`, `Description`, `Type`) Values ('" . $ID . "', '" . $amount . "', 'Deposit', 'd')";
            mysqli_query(mysql: $conn, query: $Insertqry);
            // as im doing all these isset checks above, i can ensure that actions are not redone multiple times due to the redirect using header to clear post attributes
            header(header: "Location: index.php");
            exit;
        } else { // user didnt enter a value
            header(header: "Location: index.php?errorfunds=Please+enter+a+deposit+amount");
            exit;
        }
    }
?>
<?php
    // did the user click make payment
    if (isset($_POST["makepayment"])) {
        // decl vars and sanitize input
        $paymentamount = filter_input(type: INPUT_POST, var_name: 'paymentamount', filter: FILTER_VALIDATE_FLOAT);
        $paymentmethod = filter_input(type: INPUT_POST, var_name: 'paymentmethod', filter: FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $paymentdescription = filter_input(type: INPUT_POST, var_name: 'paymentdescription', filter: FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        // have they passed filtering - returns false if either doesnt pass
        if (!$paymentamount || !$paymentmethod || !$paymentdescription) {
            header(header: "Location: index.php?errorpay=Please+ensure+you+have+entered+all+the+fields");
            exit;
        // is it a number
        } elseif (!is_numeric(value: $paymentamount)) {
            header(header: "Location: index.php?errorpay=Please+ensure+you+have+entered+a+number+for+the+payment+amount");
            exit;
        // is it empty
        } elseif ($paymentmethod == "") {
            header(header: "Location: index.php?errorpay=Please+ensure+you+have+selected+a+payment+method");
            exit;
        // is it empty
        } elseif ($paymentdescription == "") {
            header(header: "Location: index.php?errorpay=Please+ensure+you+have+entered+a+payment+description");
            exit;
        }
        // fraud detection
        if ($paymentamount > 100000) {
            header(header: "Location: index.php?errorpay=Automatically+rejected+\t+Fraud+Detected");
            exit;
        }
        // Check for invalid payment methods
        if ($paymentmethod != "creditcard" && $paymentmethod != "paypal" && $paymentmethod != "cryptocurrency") {
            header(header: "Location: index.php?errorpay=Error+:+Invalid+payment+method");
            exit;
        }
        // Get user balance from database
        $Getqry = "Select `Balance` From tblUsers Where LOWER(`Email`) = LOWER('" . $_SESSION["email"] . "')";
        $result = mysqli_query(mysql: $conn, query: $Getqry);
        // does the result exist
        if (!$result) {
            header(header: "Location: index.php?errorpay=User+not+found+in+database");
            exit;
        }
        // NB: Balance is stored as an unsigned decimal in the database
        // My logic is therefore the user cannot have a negative balance
        $Balance = mysqli_fetch_assoc(result: $result)["Balance"];
        // get specific fee
        $fee = 0.00;
        switch ($paymentmethod) {
            case "creditcard":
                // 1% trans fee
                $fee = $paymentamount * 0.01;
                break;
            case "paypal":
                // 5% trans fee
                $fee = $paymentamount * 0.05;
                break;
            case "cryptocurrency":
                // 10% trans fee
                $fee = $paymentamount * 0.10;
                break;
        }
        // Does the user have enough money to make the payment
        if ($Balance < $paymentamount + $fee) {
            header(header: "Location: index.php?errorpay=Cannot+make+payment+:+Balance+is+less+than+payment+and+fee+amount");
            exit;
        }
        // Get UserID to append transaction to tbltransactions in database
        $Getqry = "Select `UserID` From tblUsers Where LOWER(`Email`) = LOWER('" . $_SESSION["email"] . "')";
        $result = mysqli_query(mysql: $conn, query: $Getqry);
        // Does User exist
        if (!$result) {
            header(header: "Location: index.php?errorpay=User+not+found+in+database");
            exit;
        }
        $ID = mysqli_fetch_assoc(result: $result)["UserID"];
        // Insert transaction into database
        $Insertqry = "Insert Into tblTransactions (`UserID`, `Cost`, `Fee`, `Description`) Values ('" . $ID . "', '" . $paymentamount . "', '" . $fee . "', '" . $paymentdescription . "')";
        mysqli_query(mysql: $conn, query: $Insertqry);
        // Update user balance - reduce the payment amount and fee
        $Updateqry = "Update tblUsers Set `Balance` = `Balance` - '" . ($paymentamount + $fee) . "' Where `UserID` = '" . $ID . "'";
        mysqli_query(mysql: $conn, query: $Updateqry);
        // Redirect back to index
        header(header: "Location: index.php?status=Payment+Successful");
        exit;
    }
?>
<?php
    // did the user request a refund
    if (isset($_POST["refund"])) {
        // i do not need to check any input | no user input | input is set by php logic ID
        // decl var
        $refundID = $_POST["refund"]["ID"];
        // Get Cost and UserID before deletion
        $Getqry = "Select `Cost`, `UserID` From `tblTransactions` Where `ID` = '" . $refundID . "'";
        $result = mysqli_query(mysql: $conn, query: $Getqry);
        // does the transaction exist
        if (!$result) {
            header(header: "Location: index.php?refunderror=Refund+Unsuccessful");
            exit;
        }
        $refunddata = mysqli_fetch_assoc(result: $result);
        // Get Cost and UserID
        $refundcost = $refunddata["Cost"];
        $UserID = $refunddata["UserID"];
        // Delete transaction
        $Deleteqry = "Delete From `tblTransactions` Where `ID` = '" . $refundID . "'";
        mysqli_query(mysql: $conn, query: $Deleteqry);
        // Update users balance
        $Updateqry = "Update `tblUsers` Set `Balance` = `Balance` + '" . $refundcost . "' Where `UserID` = '" . $UserID . "'";
        mysqli_query(mysql: $conn, query: $Updateqry);
        // Redirect back to index
        header(header: "Location: index.php?refundstatus=Refund+Successful");
        exit;
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
            margin-top: 15px;
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
        .trans {
            width: 100%;
            height: fit-content;
            margin-top: 1px;
            margin-bottom: 1px;
            display: flex;
            flex-direction: row;
        }
        .refund {
            align-self: center;
            width: fit-content;
            color: white;
            font-size: small;
            background-color: red;
            height: fit-content;
            border: none;
            border-radius: 4px;
            cursor: pointer;
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
                            <p class="desc" style="text-align: center;">
                                Current Balance <br>
                                <b><?php
                                    $qry = "Select `Balance` From tblUsers Where LOWER(`Email`) = LOWER('" . $_SESSION["email"] . "')";
                                    $result = mysqli_fetch_assoc(result: mysqli_query(mysql: $conn, query: $qry));
                                    if (!$result) {
                                        echo "Error: Could not find user balance";
                                    } else {
                                        echo "<span style='font-size: xx-large;'>R" . (number_format(num: $result["Balance"], decimals: 2)) . "</span>";
                                    }
                                ?></b>
                            </p>
                            <input type="number" name="addamount" placeholder="Enter Deposit" min="1" style="margin-bottom: 0; margin-top: 2rem;">
                            <button type="submit" style="background-color:rgb(47, 184, 92);" name="addfunds">Add Funds</button>
                        </form>
                        <!-- GET Method to redirect and resend header on logout -->
                        <form method="GET" action="index.php">
                            <button type="submit" name="logout">Log Out</button>
                        </form>
                        <?php
                            if (isset($_GET["errorfunds"])) {
                                echo "<h3 class='err'>" . (htmlspecialchars(string: $_GET["errorfunds"])) . "</h3>";
                            }
                        ?>
                    </div>
                    <!-- payment -->
                    <div class="box">
                        <form method="POST">
                            <h1>Make Payment</h1>
                            <hr>
                            <?php
                                if (isset($_GET["status"])) {
                                    echo "<h3 class='err' style='color: green;'>" . (htmlspecialchars(string: $_GET["status"])) . "</h3>";
                                }
                            ?>
                            <span class="desc">Amount (R)</span>
                            <input type="number" name="paymentamount" style="margin-bottom: 1%;">
                            <span class="desc">Payment Method</span>
                            <select name="paymentmethod" style="margin-bottom: 1%;">
                                <option value="" disabled selected>Select payment method</option>
                                <option value="creditcard">Credit Card - 1% Fee</option>
                                <option value="paypal">PayPal - 5% Fee</option>
                                <option value="cryptocurrency">Crypto Currency - 10% Fee</option>
                                <option value="other">Other</option>
                            </select>
                            <span class="desc">Description</span>
                            <input type="text" style="margin-bottom: 1%;" name="paymentdescription" placeholder="What is this payment for?">
                            <button type="submit" name="makepayment">Make Payment</button>
                        </form>
                        <?php
                            if (isset($_GET["errorpay"])) {
                                echo "<h3 class='err'>" . (htmlspecialchars(string: $_GET["errorpay"])) . "</h3>";
                            }
                        ?>
                    </div>
                </div>
                <!-- transaction history -->
                <div class="box">
                    <form method="POST">
                        <h1>Transaction History</h1>
                        <hr>
                        <?php
                            // check for refund success
                            if (isset($_GET["refundstatus"])) {
                                echo "<h3 class='err' style='color: green'>" . (htmlspecialchars(string: $_GET["refundstatus"])) . "</h3><hr style='border: 1px solid rgb(208, 223, 231);'>";
                            }
                            // check for refund failure
                            if (isset($_GET["refunderror"])) {
                                echo "<h3 class='err'>" . (htmlspecialchars(string: $_GET["refunderror"])) . "</h3><hr style='border: 1px solid rgb(208, 223, 231);'>";
                            }
                        ?>
                        <div style="overflow-y: auto; overflow-x: hidden; max-height: 703px;">
                            <?php
                                // Get UserID
                                $Getqry = "Select `UserID` From tblUsers Where LOWER(`Email`) = LOWER('" . $_SESSION["email"] . "')";
                                $result = mysqli_query(mysql: $conn, query: $Getqry);
                                if (!$result) {
                                    header(header: "Location: index.php?errortransaction=User+not+found+in+database");
                                    exit;
                                }
                                $ID = mysqli_fetch_assoc(result: $result)["UserID"];
                                // Get the transactions made by the user
                                $Getqry = "Select `ID`, `Cost`, `Fee`, `Description`, `Time`, `Type` From `tblTransactions` Where `UserID` = '" . $ID . "'";
                                $result = mysqli_query(mysql: $conn, query: $Getqry);
                                // Are there no transactions
                                if (!$result) {
                                    echo "<p class='desc'>No Transactions Available</p>";
                                // Successfully got transactions ->
                                } else {
                                    // NOTE: Did not think it was necessary to track which payment methods were used for each payment
                                    //       This is because you arent really actually implementing a refund system to that method of payment
                                    // Get associative array of data
                                    $Data = mysqli_fetch_all(result: $result, mode: MYSQLI_ASSOC);
                                    // for each row in the data - also reverse_array used as most current payments/deposits appear at the top due to db being updated incrementally with each timestamp
                                    foreach (array_reverse(array: $Data) as $row) {
                                        ?>
                                        <!-- each transactions container -->
                                        <div class="trans">
                                            <!-- left hand side -->
                                            <div style="width: 50%;">
                                                <p>
                                                    <?php
                                                        // Description : if is deposit - add deposit symbol | otherwise = payment - add payment symbol
                                                        if ($row["Type"] == "d") echo "💰 <b>" . $row["Description"] . "</b> ✅<br>"; else echo "💵 <b>" . $row["Description"] . "</b> ✅<br>";
                                                        // Time
                                                        echo "&nbsp;" . $row["Time"] . "<br>";
                                                        // Fee
                                                        if ($row["Type"] == "p") echo "&nbsp;Fee : R" . $row["Fee"] . "<br>";
                                                    ?>
                                                </p>
                                            </div>
                                            <!-- right hand side -->
                                            <div style="display: flex; justify-content: flex-end; width: 45%;">
                                                <?php
                                                    // if deposit - green and +
                                                    if ($row["Type"] == "d") {
                                                        ?>
                                                            <p style="color: green;">
                                                                +R<?php echo $row["Cost"]; ?>
                                                            </p>
                                                        <?php
                                                    } else { // if payment red and - | plus a refund button
                                                        ?>
                                                            <p style="color: red;">
                                                                -R<?php echo $row["Cost"]; ?>&nbsp;&nbsp;
                                                                <!-- track refund with associative array ID, lets me get the transaction ID with value $row["ID"] -->
                                                                <button type="submit" name="refund[ID]" value="<?php echo $row["ID"]; ?>" class="refund">Refund</button>
                                                            </p>
                                                        <?php
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                        <hr style="border: 1px solid rgb(208, 223, 231); width: 98%;">
                                        <?php
                                    }
                                }
                            ?>
                        </div>
                        <?php
                            // check for transaction error
                            if (isset($_GET["errortransaction"])) {
                                echo "<h3 class='err'>" . (htmlspecialchars(string: $_GET["errortransaction"])) . "</h3>";
                            }
                        ?>
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
            // error check and print
            if (isset($_GET["error"])) {
                echo "<h3 class='err'>" . (htmlspecialchars(string: $_GET["error"])) . "</h3>";
            }
        }
    ?>
</body>
</html>
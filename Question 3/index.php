<?php
    // start session to store arrays and student count
    session_start();
    if (isset($_POST["closelisteners"])) {
        // destroy session if user clicked on close
        session_destroy();
    }
?>
<?php
    // did user click on continue
    if (isset($_POST["continue"])) {
       // is student count set or null
       if (!isset($_POST["studentcount"]) || $_POST["studentcount"] == "" || !is_numeric(value: $_POST["studentcount"])) {
           // set error
           header(header: "Location: index.php?error=Please+enter+a+valid+number+for+the+amount+of+students+you+will+be+using");
           exit;
       }
       // set student count to be accessed after get resets post vars
       $_SESSION["studentcount"] = $_POST["studentcount"];
       // redirect to mark entering page
       header(header: "Location: index.php?marks=true");
       exit;
    }
?>
<?php
    // did the user click on submit
    if (isset($_POST["submit"])) {
        // are named inputs set and not null
        if (!isset($_POST["names"]) || !isset($_POST["marks"])) {
            // redirect error
            header(header: "Location: index.php?marks=true&error=Please+ensure+you+have+entered+all+student+names+and+marks");
            exit;
        }
        // set arrays in session
        $_SESSION["names"] = $_POST["names"];
        $_SESSION["marks"] = $_POST["marks"];

        // null and invalid value checks
        for ($i = 0; $i < count(value: $_SESSION["names"]); $i++) {
            // are there any empty names
            if ($_SESSION["names"][$i] == "") {
                header(header: "Location: index.php?marks=true&error=Please+ensure+you+have+entered+all+student+names");
                exit;
            }
            for ($j = 0; $j <= 4; $j++) {
                // are they numeric
                if (!is_numeric(value: $_SESSION["marks"][$i][$j])) {
                    header(header: "Location: index.php?marks=true&error=Please+ensure+you+have+entered+all+required+marks");
                    exit;
                }
                // are any marks invalid, less than 0 or greater than 100
                if ($_SESSION["marks"][$i][$j] < 0 || $_SESSION["marks"][$i][$j] > 100) {
                    header(header: "Location: index.php?marks=true&error=Please+ensure+you+have+entered+valid+student+marks+between+0+and+100");
                    exit;
                }
                // are there any empty marks
                if ($_SESSION["marks"][$i][$j] == "") {
                    header(header: "Location: index.php?marks=true&error=Please+ensure+you+have+entered+all+required+marks");
                    exit;
                }
            }
        }
        // redirect to calculated info page
        header(header: "Location: index.php?form=true");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Grades Manager</title>
    <style>
        body {
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            background-color:rgb(223, 223, 223);
        }
        .cont {
            display: flex;
            justify-content: center;
            padding-left: 10%;
            padding-right: 10%;
        }
        .box {
            background-color:rgb(255, 255, 255);
            margin: 30px auto;
            padding: 60px;
            width: fit-content;
            display: flex;
            flex-direction: column;
            border-radius: 10px;
            color:rgb(0, 0, 0);
        }
        .tc {
            text-align: center;
        }
        button {
            align-self: center;
            width: 100%;
            color: white;
            font-size: large;
            background-color: #3498db;
            height: 45px;
            border: none;
            border-radius: 8px;
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
        }
        h1 {
            margin-top: 0rem;
            margin-bottom: 0rem;
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
        .err {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <header style="background-color: #3498db; padding: 1%;">
        <h1 class="tc">Student Grades Manager</h1>
        <p class="tc" style="font-size: larger;">Enter Amount Of Students | Enter 5 Grades For Each Student | Calculates Individial Average And Grade | Calculates Class Statistics</p>
    </header>
    <div class="cont">
        <!-- Continue -->
        <?php
            // tracker
            $singlestudent = false;
            // only show below on get
            // did the user come from a fresh start/not from other pages/from closing the form page
            if ($_SERVER["REQUEST_METHOD"] == "GET" && !isset($_GET["marks"]) && !isset($_GET["form"])) { ?>
                <div class="box">
                    <form method="POST">
                        <h1>Students</h1>
                        <hr>
                        <h2>Number of Students</h2><br><br>
                        <input type="number" name="studentcount" placeholder="Enter number of students" min="1" required>
                        <button type="submit" name="continue">Continue</button>
                    </form>
                </div>
                <?php
            }
        ?>
        <!-- Continue Logic -->
        <?php
            if (isset($_GET["marks"])) {
                // get student count from user input
                $studentcount = intval(value: $_SESSION["studentcount"]);
                ?>
                <div class="box">
                    <form method="POST">
                        <h1>Marks</h1>
                        <hr>
                        <h2>Please Enter Student Name<?php if ($studentcount != 1) {echo "s And Marks";} else echo " And Their Marks";?></h2>
                        <div style="max-height: 300px; overflow-y: auto; overflow-x: hidden; margin-top: 0rem;">
                            <?php
                                // create input elements for each student
                                for ($i = 0; $i < $studentcount; $i++) {
                                    echo "<hr>";
                                    echo "<p class='desc'>Student Name " . ($i + 1) . "</p><input style='width: 95%; margin-left: 1%;' type='text' placeholder='Enter student name' style='width: 97%' name='names[" . ($i) . "]' required>";
                                    // create 5 mark inputs for each student
                                    for ($j = 1; $j <= 5; $j++) {
                                        echo "<p class='desc'>Mark " . $j . "</p><input style='width: 95%; margin-left: 1%;' type='number' placeholder='Enter Mark' style='width: 97%' name='marks[" . ($i) . "][]' min='0' max='100' required>";
                                    }
                                    // end of scrollable element
                                    if ($i == $studentcount - 1) {
                                        echo "<hr>";
                                    }
                                }
                            ?>
                        </div>
                        <!-- takes user to next 'page' -->
                        <button type="submit" name="submit">Calculate</button>
                    </form>
                </div>
                <?php
            }
        ?>
        <!-- Submit Logic -->
        <?php
            if (isset($_GET["form"])) {
                // decl vars
                $names = $_SESSION["names"];
                $marks = $_SESSION["marks"];
                $averages = [];
                $grades = [];
                // ref associative array
                $gradecategories = ["A" => 0, "B" => 0, "C" => 0, "D" => 0, "F" => 0];
                // decl vars before entering for loop for mass calc
                $top_name = "";
                $top_avg = 0.0;
                $classavg = 0.0;
                // mass calculations
                for ($i = 0; $i < count(value: $names); $i++) {
                    // decl vars
                    $avg = 0;
                    // total avg for each student
                    for ($j = 0; $j <= 4; $j++) {
                        $avg += $marks[$i][$j];
                        $classavg += $marks[$i][$j];
                    }
                    // set avg for student
                    $averages[$i] = (float)$avg / 5;
                    // set letter grade for student and increment total letter grades for class
                    if ($averages[$i] >= 80) {$grades[$i] = "A"; $gradecategories["A"]++;}
                    elseif ($averages[$i] >= 70) {$grades[$i] = "B"; $gradecategories["B"]++;}
                    elseif ($averages[$i] >= 60) {$grades[$i] = "C"; $gradecategories["C"]++;}
                    elseif ($averages[$i] >= 50) {$grades[$i] = "D"; $gradecategories["D"]++;}
                    else {$grades[$i] = "F"; $gradecategories["F"]++;}
                    // check top student
                    if (($averages[$i]) > $top_avg) {
                        $top_avg = $averages[$i];
                        $top_name = $names[$i];
                    }
                }
                // class avg calc
                $classavg = $classavg / (count(value: $names) * 5);
                ?>
                <div class="box">
                    <form action="index.php" method="GET">
                        <h1>Student Grades</h1>
                        <hr>
                        <div style="max-height: 300px; overflow-y: auto; overflow-x: hidden; margin-top: 0rem;">
                            <?php
                                // output every students attributes
                                for ($i = 0; $i < count(value: $names); $i++) {
                                    $strmarks = "";
                                    // create a substring of all 5 marks for each student
                                    for ($j = 0; $j <= 4; $j++) $strmarks .= $marks[$i][$j] . ", ";
                                    echo "
                                    <p>
                                        <span class='desc' style='font-weight: bold'>" . ($names[$i]) . "</span><br>
                                        Grades : " . $strmarks . "<br>
                                        Average : " . (number_format(num: $averages[$i], decimals: 2)) . "<br>
                                        Final Grade : " . ($grades[$i]) . "
                                    </p>";
                                }
                            ?>
                        </div>
                        <!-- returns user back to start -->
                        <button type="submit" name="closelisteners">Close</button>
                    </form>
                </div>
                <?php
                // we only print class stats if there is more than one student
                if (count(value: $names) > 1) {
                    ?>
                    <div class="box">
                        <h1>Class Statistics</h1>
                        <hr>
                        <p class="desc"> <!-- print out variables to user -->
                            <b>Class Average</b> : <?php echo number_format(num: $classavg, decimals: 2)?><br>
                            <b>Top Student</b> : <?php echo ($top_name) . " with an average of " . (number_format(num: $top_avg, decimals:2))?><br><br>
                            <b>Grade Distrobution</b><br>
                            A : <?php echo $gradecategories["A"] ?><br>
                            B : <?php echo $gradecategories["B"] ?><br>
                            C : <?php echo $gradecategories["C"] ?><br>
                            D : <?php echo $gradecategories["D"] ?><br>
                            F : <?php echo $gradecategories["F"] ?><br>
                        </p>
                    </div>
                    <?php // show message to inform user that there will be no class statistics
                } else $singlestudent = true;
            }
        ?>
    </div>
    <!-- error handling and message -->
    <?php if ($singlestudent) echo "<p class='err'>Note : Class Statistics not available as only one student was listed</p>"; ?>
    <?php
        // is there an error
        if (isset($_GET["error"])) {
            // display
            echo "<p class='err'>" . htmlspecialchars(string: $_GET["error"]) . "</p>";
        }
    ?>
</body>
</html>
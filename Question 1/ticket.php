<?php
    // Check file access, write to dir - if not exists
    if (!file_exists(filename: "server.json")) {
        $base = [
            "tickets" => [
                "left" => 60000,
                "VVIP" => 0,
                "VIP" => 0,
                "General Access" => 0
            ],
            "gender" => [
                "male" => [
                    "total" => 0,
                    "age-group" => [
                        "16-21" => 0,
                        "22-35" => 0,
                        "36-50" => 0,
                        "51-65" => 0,
                        "65+" => 0
                    ]
                ],
                "female" => [
                    "total" => 0,
                    "age-group" => [
                        "16-21" => 0,
                        "22-35" => 0,
                        "36-50" => 0,
                        "51-65" => 0,
                        "65+" => 0
                    ]
                ]
            ]
        ];
        file_put_contents(filename: "server.json", data: json_encode(value: $base));
    }
    // Check if page access method is post, redirect from index process
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Does username exist and contain a value
        if (!isset($_POST["username"]) || $_POST["username"] == "") {
            header(header: "Location: index.php?error=Please+enter+your+name");
            exit;
        }
        // Valid Name
        if (strlen(string: $_POST["username"]) < 3) {
            header(header:"Location: index.php?error=Name+must+be+longer+than+3+characters");
            exit;
        }
        // Does ticket type exist and contain a value
        if (!isset($_POST["type"]) || $_POST["type"] == "") {
            header(header:"Location: index.php?error=Please+select+a+ticket+type");
            exit;
        }
        // Does gender exist and contain a value
        if (!isset($_POST["gender"]) || $_POST["gender"] == "") {
            header(header: "Location: index.php?error=Please+enter+your+gender");
            exit;
        }
        // Does age exist and contain a value
        if (!isset($_POST["age"]) || $_POST["age"] == "") {
            header(header: "Location: index.php?error=Please+enter+your+age");
            exit;
        }
        // decl vars and filter/sanitize
        $username = filter_input(type: INPUT_POST, var_name: 'username', filter: FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $age = filter_input(type: INPUT_POST, var_name: 'age', filter: FILTER_VALIDATE_INT);
        $gender = filter_input(type: INPUT_POST, var_name: 'gender', filter: FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $tickettype = filter_input(type: INPUT_POST, var_name: 'type', filter: FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $file = json_decode(json: file_get_contents(filename: "server.json"), associative: true);
        // Are there enough tickets available to be sold
        if ($file["tickets"]["left"] < 1) {
            header(header: "Location: index.php?error=No+tickets+left+for+sale");
            exit;
        }
        // Is the user of age | filter_int returns false if not a valid integer
        if ($age < 16 || $age > 100 || $age === false) {
            header(header: "Location: index.php?error=Under+age,+Attendees+must+be+16+or+older");
            exit;
        }
        // Dec tickets left for sale and inc gender total counter
        $file["tickets"]["left"] -= 1;
        $file["tickets"][$tickettype] += 1;
        $file["gender"][$gender]["total"] += 1;
        // Get the age group of the user
        $agegroup = "";
        if ($age < 22) $agegroup = "16-21";
        elseif ($age < 36) $agegroup = "22-35";
        elseif ($age < 51) $agegroup = "36-50";
        elseif ($age < 66) $agegroup = "51-65";
        else $agegroup = "65+";
        // With the age group, inc the gender specific stats
        $file["gender"][$gender]["age-group"][$agegroup] += 1;
        // rewrite contents as we dont need access anymore
        file_put_contents(filename: "server.json", data: json_encode(value: $file));
    } 
    // Below: user cannot access the ticket processing page without submitting from index.php
    else exit('<h1 class="center">403 Forbidden: You are not allowed to access this page.</h1>');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Ticket</title>
    <style>
        body {
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }
        .div {
            background-color: #3498db; 
            margin: 20px auto;
            padding: 20px;
            width: fit-content;
            display: flex;
            flex-direction: column;
            gap: 10px;
            border-radius: 10px;
            color: white;
        }
        .center {
            text-align: center;
        }
    </style>
</head>
<body>
<!-- Start of script -->
<?php
    // DOC:
    // Originally had 3 files that would interact with eachother,
    // changed it so that the user could not manipulate the url passed variables
    // which would allow them to generate multiple tickets for themselves

    // Now i need to print the users ticket and details
    echo "<h1 class='center'>Ticket</h1>";
    if ($tickettype == "VVIP") echo "<h2 class='center'>Total : R3000";
    elseif ($tickettype == "VIP") echo "<h2 class='center'>Total : R2000";
    else echo "<h2 class='center'>Total : R500";
    // Logic for ticket
    $ID = str_pad(string: (string)(60000 - $file["tickets"]["left"]), length: 5, pad_string: "0", pad_type: STR_PAD_LEFT);
    $row = ""; $class = "";
    // set the row letter
    switch ($ID[0]) {
        case "0": $row = "A"; break;
        case "1": $row = "B"; break;
        case "2": $row = "C"; break;
        case "3": $row = "D"; break;
        case "4": $row = "E"; break;
        case "5": $row = "F"; break;
        default: $row = "Z";
    }
    // set the row class 'vip' etc
    switch ($tickettype) {
        case "General Access": $class = "G"; break;
        case "VIP": $class = "V"; break;
        case "VVIP": $class = "P"; break;
        default: $class = "G";
    }
    // create unique seat number
    $seat = ($row) . ($class) . (substr(string: ($ID), offset: 2));
    // print ticket
    echo "<div class='div'>
                    <h1>Ticket: " . ($ID) ."</h1>
                    <hr style='border: 1px solid white; width: 100%'>
                    <p>
                        Beyonce Concert Monte Casino<br><br>
                        Date: December 25 2025<br><br>
                        Location: Monte Casino<br><br>
                        Name: " . ($username) . "<br><br>
                        Type: " . htmlspecialchars(string: $tickettype) ."<br><br>
                        Seat: " . ($seat) . "<br><br><br>
                        |/|#/|;|\|\|\#\\||||#/||#||\#\\|||;\|||
                    </p>
                </div>";
?>
<!-- End of script -->
</body>
<script>
    // resubmit/POST check so that it doesnt overwrite server side data, like tickets left
    // although this fixes the post issue, if the user refreshes the page, they loose access to their ticket data
    // - ChatGPT
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    // - ChatGPT
</script>
</html>
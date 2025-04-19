<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beyonce Concert Bookings</title>
    <style>
        body {
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            background-color:rgb(223, 223, 223);
        }
        .div {
            background-color:rgb(255, 255, 255);
            margin: 20px auto;
            padding: 60px;
            width: fit-content;
            display: flex;
            flex-direction: column;
            border-radius: 10px;
            color:rgb(0, 0, 0);
        }
        .btn {
            align-self: center;
            width: 100%;
            color: white;
            font-size: large;
            background-color: #3498db;
            height: 45px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .sel {
            width: 100%;
            height: 40px;
            font-size: larger;
            border: none;
            border-radius: 8px;
            padding: 5px 10px;
            appearance: none;
            cursor: pointer;
            background-color:rgb(223, 223, 223);
        }
        .inp {
            width: 100%;
            height: 40px;
            font-size: larger;
            border: none;
            border-radius: 8px;
            padding: 5px 10px;
            box-sizing: border-box;
            background-color:rgb(223, 223, 223);
        }
        .center {
            text-align: center;
        }
        .block {
            text-align: center;
            background-color: rgb(223, 223, 223);
            padding-left: 6%;
            padding-right: 6%;
            border-radius: 8px;
            width: fit-content;
            margin-left: 2%;
            margin-right: 2%;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border-top: 1px solid rgb(223, 223, 223);
            border-bottom: 1px solid rgb(223, 223, 223);
            border-left: none;
            border-right: none;
            padding: 8px;
            text-align: left;
        }
        th:first-child,
        td:first-child {
            padding-left: 0;
        }
        th:last-child,
        td:last-child {
            padding-right: 0;
        }
        .tcont {
            max-height: 150px;
            overflow-y: auto;
            overflow-x: hidden;
        }
    </style>
</head>
<body>
    <?php
        // File access for stats overview
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
        $file = json_decode(json: file_get_contents(filename:"server.json"), associative: true);
    ?>
    <header style="height: fit-content; align-content: center; text-align: center; background-color:rgb(94, 163, 243); padding-bottom: 1%;">
        <h1>Beyonce Renaissance World Tour</h1>
        December 25, 2025 | Monte Casino Outer Stadium | Doors Open: 6:00 PM
    </header>
    <div style="display: flex; justify-content: center; gap: 20px;">
        <div class="div" style="width: 30%;">
            <form action="ticket.php" method="POST">
                <h1 style="margin-top: 1%;">Ticket Booking</h1>
                <hr style="border: 1px solid #3498db;">
                <!-- Description -->
                <p class="center" style="font-weight: bold;">
                    Date: December 25 2025<br><br>
                    Tickets : <?php // are there tickets available for purchase?
                                // server saved statistics in json file
                                if (json_decode(json: file_get_contents(filename: "server.json"), associative: true)["tickets"]["left"] <= 0) {
                                    echo " <span style='color: red'>Not Available</span>";
                                } else echo " <span style='color: green'>Available</span>"
                               ?>
                </p><br>
                <!-- Name on Ticket -->
                <p>
                    <p style="font-size: x-large; margin-bottom: 5px; font-weight: 400;">&nbsp;Name<br></p>
                    <input class="inp" type="text" name="username" id="username" placeholder="Enter your full name">
                </p>
                <!-- Ticket types -->
                <p>
                <p style="font-size: x-large; margin-bottom: 5px; font-weight: 400;">&nbsp;Ticket Type<br></p>
                    <select class="sel" name="type" id="type">
                        <option value="General Access">General Access - R500</option>
                        <option value="VIP">VIP - R2,000</option>
                        <option value="VVIP">VVIP - R3,000</option>
                    </select>
                </p>
                <!-- Gender -->
                <p>
                <p style="font-size: x-large; margin-bottom: 5px; font-weight: 400;">&nbsp;Gender<br></p>
                    <select class="sel" name="gender" id="gender" required>
                        <option value="" disabled selected>Select your gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </p>
                <!-- Age -->
                <p>
                <p style="font-size: x-large; margin-bottom: 5px; font-weight: 400;">&nbsp;Age<br></p>
                    <input class="inp" placeholder="Enter your age" type="number" name="age" id="age" required min="16">
                </p> <br><br>
                <button class="btn" type="submit">Buy Ticket</button>
            </form>
        </div>
        <div class="div" style="width: 40%">
            <h1 style="margin-top: 1%;">Sales Dashboard</h1>
            <hr style="border: 1px solid #3498db; width: 100%;">
            <div style="display: flex; justify-content: center; gap: 20px;">
                <div class="block">
                    <h3>Tickets Sold</h3>
                    <h2 style="color: #3498db;"><?php echo (60000 - $file["tickets"]["left"]) ?></h2>
                </div>
                <div class="block">
                    <h3>Remaining</h3>
                    <h2 style="color: #3498db;"><?php echo ($file["tickets"]["left"]) ?></h2>
                </div>
                <div class="block">
                    <h3>Revenue</h3>
                    <h2 style="color: #3498db;"><?php echo "R" . number_format(($file["tickets"]["VVIP"] * 3000) + ($file["tickets"]["VIP"] * 2000) + ($file["tickets"]["General Access"] * 500)); ?></h2>
                </div>
            </div>
            <h2>Ticket Categories</h2>
            <table>
                <thead style="color: #3498db;">
                    <tr style="text-align: left;">
                        <th>Category</th>
                        <th>Tickets Sold</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo "General Access" ?></td>
                        <td><?php echo ($file["tickets"]["General Access"]) ?></td>
                        <td><?php echo ("R" . number_format($file["tickets"]["General Access"] * 500)) ?></td>
                    </tr>
                    <tr>
                        <td><?php echo "VIP" ?></td>
                        <td><?php echo ($file["tickets"]["VIP"]) ?></td>
                        <td><?php echo ("R" . number_format($file["tickets"]["VIP"] * 2000)) ?></td>
                    </tr>
                    <tr>
                        <td><?php echo "VVIP" ?></td>
                        <td><?php echo ($file["tickets"]["VVIP"]) ?></td>
                        <td><?php echo ("R" . number_format($file["tickets"]["VVIP"] * 3000)) ?></td>
                    </tr>
                </tbody>
            </table>
            <!-- this table is scrollable, with a locked head -->
            <h2>Demographics</h2>
            <div class="tcont">
                <table>
                    <thead style="color: #3498db; position: sticky; top: 0; background-color: white; z-index: 1; border-bottom: 2px solid rgb(223, 223, 223);">
                        <tr>
                            <th>Gender</th>
                            <th>Age Group</th>
                            <th>Tickets</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr> <!-- 1 -->
                            <td>Female</td>
                            <td>16-21</td>
                            <td><?php echo $file["gender"]["female"]["age-group"]["16-21"]?></td>
                        </tr>
                        <tr> <!-- 2 -->
                            <td>Male</td>
                            <td>16-21</td>
                            <td><?php echo $file["gender"]["male"]["age-group"]["16-21"]?></td>
                        </tr>
                        <tr> <!-- 3 -->
                            <td>Female</td>
                            <td>22-35</td>
                            <td><?php echo $file["gender"]["female"]["age-group"]["22-35"]?></td>
                        </tr>
                        <tr> <!-- 4 -->
                            <td>Male</td>
                            <td>22-35</td>
                            <td><?php echo $file["gender"]["male"]["age-group"]["22-35"]?></td>
                        </tr>
                        <tr> <!-- 5 -->
                            <td>Female</td>
                            <td>36-50</td>
                            <td><?php echo $file["gender"]["female"]["age-group"]["36-50"]?></td>
                        </tr>
                        <tr> <!-- 6 -->
                            <td>Male</td>
                            <td>36-50</td>
                            <td><?php echo $file["gender"]["male"]["age-group"]["36-50"]?></td>
                        </tr>
                        <tr> <!-- 7 -->
                            <td>Female</td>
                            <td>51-65</td>
                            <td><?php echo $file["gender"]["female"]["age-group"]["51-65"]?></td>
                        </tr>
                        <tr> <!-- 8 -->
                            <td>Male</td>
                            <td>51-65</td>
                            <td><?php echo $file["gender"]["male"]["age-group"]["51-65"]?></td>
                        </tr>
                        <tr> <!-- 9 -->
                            <td>Female</td>
                            <td>65+</td>
                            <td><?php echo $file["gender"]["female"]["age-group"]["65+"]?></td>
                        </tr>
                        <tr> <!-- 10 -->
                            <td>Male</td>
                            <td>65+</td>
                            <td><?php echo $file["gender"]["male"]["age-group"]["65+"]?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
    // check if a previously set error is present - if so push it to the page
        if (isset($_GET["error"])) {
            echo "<h2 class='center' style='color: red;'>" . htmlspecialchars(string: $_GET["error"]) . "</h2>";
        }
    ?>
</body>
</html>
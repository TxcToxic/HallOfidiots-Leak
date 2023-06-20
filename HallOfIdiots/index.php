<?php 
session_start();

$servername = "localhost";
$username = "hoi"; // hoi
$password = "e72rcjx7mnIkkpsAJKT2TJ8Ax5Vmnf2Y"; // e72rcjx7mnIkkpsAJKT2TJ8Ax5Vmnf2Y
$dbname = "hoi"; // hoi

$conn = new mysqli($servername, $username, $password, $dbname);
$activated = false;

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hall of Idiots</title>
    <script src="additional/js/main.js"></script>
    <link rel="stylesheet" href="additional/css/style.css">
</head>
<body>
    <div class="header">
        <h1><a class="head-link" href='https://discord.gg/J2tQ9D6pjH' target='_blank'>Hall of Idiots</a></h1>
        <div class="search-bar">
            <form action="search.php" method="GET">
                <input type="text" name="search" id="search-input" placeholder="Search...">
                <button type="submit" id="search-button">Search</button>
            </form>
        </div>
    </div>
    <div class="bottom">
        <p>Invented by Rikaza#7115 © 2023</p>
    </div>
    <?php
        if (isset($_SESSION["loggedin"]) && isset($_SESSION["username"]) && $_SESSION["loggedin"] === true) {
            $stmt = $conn->prepare("SELECT * FROM `users` WHERE `username` = ?");
            $stmt->bind_param("s", $_SESSION["username"]);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                echo "<p>Welcome to HoI, ". $_SESSION['username'] . "</p>";
                $row = $result->fetch_assoc();
                if (!$row['activated']) {
                    echo '<p><span class="bold">WARNING! your account is deactivated!</span> Contact us on <a href="https://discord.gg/J2tQ9D6pjH" target="_blank">discord</a> or <a href="https://t.me/+oL06M_RIAk8yMjQy" target="_blank">telegram</a></p>';
                    
                } else {
                    $activated = true;
                    if ($row['role'] !== 'user') {
                        echo '<p><a href="acp">click here</a> to enter the admin page</p>';
                    }
                }
            } else {
                unset($_SESSION["username"]);
                $_SESSION["loggedin"] = false;
                echo "<p>Welcome to HoI, <a href='login.php'>click here to login again</a>!</p>";
                $activated = true;
            }
        } else {
            echo "<p>Welcome to HoI, <a href='login.php'>click here to login or register</a>!</p>";
            $activated = true;
        }
    ?>
    <p><a href="cap">click here</a> to create a post</p>
    <form>
        <button id="music-start" onclick="playNextSong()">Start Music</button>
        <label for="music-volume">Music Volume:</label>
        <input type="range" min="0" max="100" value="100" id="music-volume">
    </form>
    <div class="doxes">
        <?php

            if ($conn->connect_error) {
                echo '<div class="ohADox">';
                echo "<h3>Error</h3>";
                echo "<p><span class='bold'>What does it mean?</span> The connection to the DataBase could not be established!</p>";
                echo "<p>Contact us on <a href='https://discord.gg/J2tQ9D6pjH' target='_blank'>discord</a> or <a href='https://t.me/+oL06M_RIAk8yMjQy' target='_blank'>telegram</a>!</p>";
                die("</div>");
            }

            if (!$activated) {
                echo '<div class="ohADox">';
                echo "<h3>Error</h3>";
                echo "<p><span class='bold'>What does it mean?</span> Your account was disabled!</p>";
                echo "<p><span class='bold'>You're not able to use our services!</span></p>";
                die("</div>");
            }
        
            $sql = "SELECT * FROM `doxes`";
            $result = $conn->query($sql);
        
            if ($result->num_rows > 0) {
                foreach ($result as $row) {
                    if ($row["activated"]) {
                        echo '<div class="ohADox">';
                        echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
                        echo "<p><span class='bold'>Alias: </span> " . htmlspecialchars($row['alias']) . "</p>";
                        echo "<p><span class='bold'>Born: </span> " . htmlspecialchars($row['born']) . "</p>";
                        echo "<p><span class='bold'>Phone: </span> " . htmlspecialchars($row['phone']) . "</p>";
                        echo "<p><span class='bold'>Address: </span>" . htmlspecialchars($row['address']) ."</p>";
                        echo "<p><span class='bold'>Family: </span> " . htmlspecialchars($row['family']) . "</p>";
                        echo "<p><span class='bold'>Additional: </span> " . htmlspecialchars($row['additional']) . "</p>";
                        echo "</div>";
                    }
                }
            } else {
                echo '<div class="ohADox">';
                echo "<h3>Error</h3>";
                echo "<p><span class='bold'>What does it mean?</span> There is no data in our DataBase</p>";
                echo "</div>";
            }

            $conn->close();
        ?>
    </div>
</body>
</html>

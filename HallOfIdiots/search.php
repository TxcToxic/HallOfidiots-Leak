<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HoI: Search</title>
    <link rel="stylesheet" href="additional/css/style.css">
    <script src="additional/js/main.js"></script>
</head>
<body>
    <div class="header">
        <h1><a class="head-link" href='/'>HoI</a>: Search</h1>
        <div class="search-bar">
            <form method="GET" action="">
                <input type="text" name="search" id="search-input" placeholder="Search...">
                <button type="submit" id="search-button">Search</button>
            </form>
        </div>
    </div>
    <div class="bottom">
        <p>Invented by Rikaza#7115 © 2023</p>
    </div>
    <form>
        <button id="music-start" onclick="playNextSong()">Start Music</button>
        <label for="music-volume">Music Volume:</label>
        <input type="range" min="0" max="100" value="100" id="music-volume">
    </form>
    <div class="doxes">
        <?php
            $servername = "localhost";
            $username = "hoi"; // hoi
            $password = "e72rcjx7mnIkkpsAJKT2TJ8Ax5Vmnf2Y"; // e72rcjx7mnIkkpsAJKT2TJ8Ax5Vmnf2Y
            $dbname = "hoi"; // hoi
    
            $conn = new mysqli($servername, $username, $password, $dbname);

            $activated = false;
    
            if ($conn->connect_error) {
                echo '<div class="ohADox">';
                echo "<h3>Error</h3>";
                echo "<p><span class='bold'>What does it mean?</span> The connection to the DataBase could not be established!</p>";
                echo "<p>Contact us on <a href='https://discord.gg/J2tQ9D6pjH' target='_blank'>discord</a> or <a href='https://t.me/+oL06M_RIAk8yMjQy' target='_blank'>telegram</a>!</p>";
                die("</div>");
            }

            $stmt = $conn->prepare("SELECT * FROM `users` WHERE `username` = ?");
            $stmt->bind_param("s", $_SESSION["username"]);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                if ($row["activated"]) {
                    $activated = true;
                }
            } else {$activated = true;}

            if (!$activated) {
                echo '<div class="ohADox">';
                echo "<h3>Error</h3>";
                echo "<p><span class='bold'>What does it mean?</span> Your account was disabled!</p>";
                echo "<p><span class='bold'>You're not able to use our services!</span></p>";
                die("</div>");
            }
        
            $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
        
            $sql = "SELECT * FROM `doxes` WHERE `name` LIKE '%$searchTerm%'";
            $result = $conn->query($sql);
        
            if ($result->num_rows > 0) {
                foreach ($result as $row) {
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
            } else {
                echo '<div class="ohADox">';
                echo "<h3>Error</h3>";
                echo "<p><span class='bold'>What does it mean?</span> '$searchTerm' was not found</p>";
                echo "</div>";
            }
        
            $conn->close();
        ?>
    </div>
</body>
</html>

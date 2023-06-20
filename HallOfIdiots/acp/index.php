<?php
session_start();

$servername = "localhost";
$username = "hoi";
$password = "e72rcjx7mnIkkpsAJKT2TJ8Ax5Vmnf2Y";
$dbname = "hoi";

$conn = new mysqli($servername, $username, $password, $dbname);

$role = NULL;
$activated = false;

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /login.php");
    exit;
}
if (!isset($_SESSION["username"]) || $_SESSION["username"] == "") {
    unset($_SESSION["username"]);
    $_SESSION["loggedin"] = false;
    header("location: /login.php");
    exit;
} else {
    $stmt = $conn->prepare("SELECT * FROM `users` WHERE `username` = ?");
    $stmt->bind_param("s", $_SESSION["username"]);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $role = $row['role'];
        if ($row["activated"]) {
            $activated = true;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HoI: Admin Control Panel</title>
    <link rel="stylesheet" href="/additional/css/style.css">
    <script src="/additional/js/main.js"></script>
</head>
<body>
    <div class="header">
        <h1><a class="head-link" href='/'>HoI</a>: ACP</h1>
    </div>
    <div class="bottom">
        <p>Invented by Rikaza#7115 © 2023</p>
    </div>
    <div class="doxes">
        <?php
            if ($conn->connect_error) {
                echo '<div class="ohADox">';
                echo "<h3>Error</h3>";
                echo "<p><span class='bold'>What does it mean?</span> The connection to the DataBase could not be established!</p>";
                echo "<p>Contact us on <a href='https://discord.gg/J2tQ9D6pjH' target='_blank'>discord</a> or <a href='https://t.me/+oL06M_RIAk8yMjQy' target='_blank'>telegram</a>!</p>";
                die("</div>");
            }

            if ($role == 'user') {
                echo '<div class="ohADox">';
                echo "<h3>Error</h3>";
                echo "<p><span class='bold'>What does it mean?</span> You're not a Staff Member!</p>";
                die("</div>");
            }

            if (!$activated) {
                echo '<div class="ohADox">';
                echo "<h3>Error</h3>";
                echo "<p><span class='bold'>What does it mean?</span> Your account was disabled!</p>";
                echo "<p><span class='bold'>You're not able to use our services!</span></p>";
                die("</div>");
            }

            echo '<div class="ohADox">';
            echo "<h3>Menus:</h3>";
            echo "<p><span class='bold'>Manage Doxes:</span> <a href='doxes'>click here!</a></p>";
            echo "<p><span class='bold'>Manage Users:</span> <a href='users'>click here!</a></p>";
            die("</div>");

            $conn->close();
        ?>
    </div>
</body>
</html>
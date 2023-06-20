<?php
session_start();

$servername = "localhost";
$username = "hoi";
$password = "e72rcjx7mnIkkpsAJKT2TJ8Ax5Vmnf2Y";
$dbname = "hoi";

$conn = new mysqli($servername, $username, $password, $dbname);

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
        if ($row["activated"]) {
            $activated = true;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
    $name = $_POST["name"];
    $alias = $_POST["alias"];
    $born = $_POST["born"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    $family = $_POST["family"];
    $additional = $_POST["additional"];
    
    if (empty($alias)) {
        $alias = "NOT SET";
    }
    if (empty($born)) {
        $born = "NOT SET";
    }
    if (empty($phone)) {
        $phone = "NOT SET";
    }
    if (empty($address)) {
        $address = "NOT SET";
    }
    if (empty($family)) {
        $family = "NOT SET";
    }
    if (empty($additional)) {
        $additional = "NOT SET";
    }

    $stmt = $conn->prepare("SELECT * FROM `doxes` WHERE `name` = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO `doxes` (`name`, `alias`, `born`, `phone`, `address`, `family`, `additional`, `creator_name`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $name, $alias, $born, $phone, $address, $family, $additional, $_SESSION["username"]);
        $stmt->execute();

        header("location: /search.php?search=".$name);
    } else {
        $loginError = "This dox already exists!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>HoI: Create a Paste</title>
    <script src="/additional/js/main.js"></script>
    <link rel="stylesheet" href="/additional/css/style.css">
</head>
<body>
    <div class="header">
        <h1><a class="head-link" href='/' target='_blank'>HoI</a>: Create a Paste</h1>
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

            if (!$activated) {
                echo '<div class="ohADox">';
                echo "<h3>Error</h3>";
                echo "<p><span class='bold'>What does it mean?</span> Your account was disabled!</p>";
                echo "<p><span class='bold'>You're not able to use our services!</span></p>";
                die("</div>");
            }
        ?>
        <div class="ohADox">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <input type="text" name="name" maxlength="500" required><br>
                <label for="alias"><span class='bold'>Alias: </span></label>
                <input type="text" maxlength="500" name="alias"><br>
                <label for="born"><span class='bold'>Born: </span></label>
                <input type="text" maxlength="500" name="born"><br>
                <label for="phone"><span class='bold'>Phone: </span></label>
                <input type="text" maxlength="500" name="phone"><br>
                <label for="address"><span class='bold'>Address: </span></label>
                <input type="text" maxlength="500" name="address"><br>
                <label for="family"><span class='bold'>Family: </span></label>
                <input type="text" maxlength="500" name="family"><br>
                <label for="additional"><span class='bold'>Additional: </span></label>
                <input type="text" maxlength="500" name="additional"><br>
                <input type="submit" value="Post & Accept the rules">
            </form>
        </div>
    </div>
</body>
</html>

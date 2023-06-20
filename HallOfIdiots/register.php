<?php
session_start();

$servername = "localhost";
$username = "hoi";
$password = "e72rcjx7mnIkkpsAJKT2TJ8Ax5Vmnf2Y";
$dbname = "hoi";

$conn = new mysqli($servername, $username, $password, $dbname);

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: /");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    if (isset($_SESSION["loggedin"])) {
        $loginError = "You can't create 2 accounts! If you forgot your password, please contact us on <a href='https://discord.gg/J2tQ9D6pjH' target='_blank'>Discord</a>.";
    } 
    if (strlen($username) > 45) {
        $loginError = "Your username is too long! (MAX. 45 characters)";
    }
    if (strlen($username) < 5) {
        $loginError = "Your username is too short! (MIN. 5 characters)";
    }
    if (strlen($password) > 65) {
        $loginError = "Your password is too long! (MAX. 65 characters)";
    }
    if (strlen($password) < 8) {
        $loginError = "Your password is too short! (MIN. 8 characters)";
    }
    $hashedPassword = hash('sha256', $password);

    $stmt = $conn->prepare("SELECT * FROM `users` WHERE `username` = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO `users` (`username`, `password`) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashedPassword);
        $stmt->execute();

        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $username;

        header("location: /");
    } else {
        $loginError = "This username already exists!";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>HoI: Register</title>
    <link rel="stylesheet" href="additional/css/style.css">
</head>
<body>
    <div class="header">
        <h1><a class="head-link" href='/'>HoI</a>: Register</h1>
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
        ?>
        <div class="ohADox">
            <?php if(isset($loginError)) { echo "<p>".$loginError."</p>"; } ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <label for="username">Username:</label>
                <input type="text" name="username" minlength="5" maxlength="45" id="username" placeholder="Gaming Gamer" required><br>
                <label for="password">Password:</label>
                <input type="password" name="password" minlength="8" maxlength="65" id="password" placeholder="MAX. length 65 characters" required><br>
                <input type="submit" id="login" value="Sign up!">
            </form>
        </div>
    </div>
</body>
</html>

<?php
session_start();

$servername = "localhost";
$username = "hoi"; // hoi
$password = "e72rcjx7mnIkkpsAJKT2TJ8Ax5Vmnf2Y"; // e72rcjx7mnIkkpsAJKT2TJ8Ax5Vmnf2Y
$dbname = "hoi"; // hoi

$conn = new mysqli($servername, $username, $password, $dbname);

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: /");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $hashedPassword = hash('sha256', $password);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $hashedPassword);

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $username;

        if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] === "/register.php"){
            header("location: /");
        } else {
            if(isset($_SERVER['HTTP_REFERER'])){
                $previousPage = $_SERVER['HTTP_REFERER'];
            } else {
                $previousPage = "/";
            }
            header("location: $previousPage");
        }

        exit;
    } else {
        $loginError = "Wrong username or password.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>HoI: Login</title>
    <link rel="stylesheet" href="additional/css/style.css">
</head>
<body>
    <?php if($_SESSION["loggedin"] !== true){ ?>
        <div class="header">
            <h1><a class="head-link" href='/'>HoI</a>: Login</h1>
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
                <?php if(isset($loginError)){?><p><?php echo $loginError; ?></p><?php } ?>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <label for="username" >Username:</label>
                    <input type="text" id="username" name="username" minlength="5" maxlength="45" placeholder="Gaming Gamer" required><br>
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" minlength="8" maxlength="65" placeholder="MAX. length 65 length" required><br>
                    <input type="submit" id="login" value="Login!">
                </form>
                <a href="register.php">
                    <p>no account? click here to signup!</p>
                </a>
            </div>
        </div>
        
    <?php } ?>
</body>
</html>

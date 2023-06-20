<?php
session_start();

$servername = "localhost";
$username = "hoi";
$password = "e72rcjx7mnIkkpsAJKT2TJ8Ax5Vmnf2Y";
$dbname = "hoi";

$conn = new mysqli($servername, $username, $password, $dbname);

$role = 'user';
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
    } else {
        unset($_SESSION["username"]);
        $_SESSION["loggedin"] = false;
        header("location: /login.php");
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["_method"])) {
        if (isset($_POST["_id"])) {
            if ($_POST["_method"] === "DELETE") {
                if ($role == 'owner') {
                    $stmt = $conn->prepare("DELETE FROM `doxes` WHERE `id`=?");
                    $stmt->bind_param("i", $_POST["_id"]);
                    $success = $stmt->execute();
                    if ($success) {
                        $feedback = "ID: '".$_POST["_id"]."' was deleted";
                    } else {
                        $feedback = "an error occurred";
                    }
                }
                else {
                    $feedback = "You're not able to delete pastes!";
                }
            } elseif ($_POST["_method"] === "OPTIONS") {
                if ($role == 'assistant' || $role == 'owner') {
                    if (isset($_POST["activate"])) {
                        $stmt = $conn->prepare("UPDATE `doxes` SET `activated`='1' WHERE `id`=?");
                        $stmt->bind_param("i", $_POST["_id"]);
                        $success = $stmt->execute();
                        if ($success) {
                            $feedback = "ID: '".$_POST["_id"]."' was activated";
                        } else {
                            $feedback = "an error occurred";
                        }
                    } elseif (isset($_POST["deactivate"])) {
                        $stmt = $conn->prepare("UPDATE `doxes` SET `activated`='0' WHERE `id`=?");
                        $stmt->bind_param("i", $_POST["_id"]);
                        $success = $stmt->execute();
                        if ($success) {
                            $feedback = "ID: '".$_POST["_id"]."' was deactivated";
                        } else {
                            $feedback = "an error occurred";
                        }
                    }
                }
                else {
                    $feedback = "You're not able to De- / Activate pastes!";
                }
            }
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
        <h1><a class="head-link" href='/acp'>HoI</a>: ACP</h1>
    </div>
    <div class="bottom">
        <p>Invented by Rikaza#7115 © 2023</p>
    </div>
    <div class="doxes">
        <?php
            if (isset($feedback)) {
                echo '<div class="ohADox">';
                echo "<h3>Result:</h3>";
                echo "<p><span class='bold'>".$feedback."</span></p>";
                echo '</div>';
            } 
        ?>
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

        ?>
        <div class="ohADox">
            <h3>Information:</h3>
            <p>deactivating a paste will hide the paste for everyone</p>
            <p>that means the paste is visible in the ACP ONLY!</p>
        </div>
        <?php
            $sql = "SELECT * FROM `doxes`";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                foreach ($result as $row) {
                    echo '<div class="ohADox">';
                    echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
                    echo '<div class="ohADoxSplit">';
                    echo '<div class="ohADoxLeft">';
                    echo '<span class="bold">DATA:</span>';
                    echo "<p>";
                    $parameters = array('alias', 'born', 'phone', 'address', 'family', 'additional');
                    $parameterCount = count($parameters);
                    foreach ($parameters as $index => $parameter) {
                        if ($row[$parameter] !== 'NOT SET') {
                            echo '<span class="bold">'.$parameter.'</span>';
                        } else {
                            echo '<span class="notset">'.$parameter.'</span>';
                        }
                        if ($index !== $parameterCount - 1) {
                            echo ' | ';
                        }
                    }
                    echo "</p>";
                    echo "<p><span class='bold'>Creator: </span>".$row['creator_name']."</p>";
                    echo "<p><span class='bold'>ID: </span>".$row['id']."</p>";
                    if ($row['activated']) { echo "<p><span class='bold'>Status: </span>Activated</p>"; }
                    else { echo "<p><span class='bold'>Status: </span>Deactivated</p>"; }
                    echo '</div>';
                    echo '<div class="ohADoxRight">';
                    if ($row['activated']) {
                        
                        if ($role == 'assistant' || $role == 'owner') {
                            echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST" class="button-form"><input type="hidden" name="_id" value="'.$row["id"].'"><input type="hidden" name="_method" value="OPTIONS"><button type="submit" name="deactivate">deactivate</button></form>';
                        }
                    } else {
                        
                        if ($role == 'assistant' || $role == 'owner') {
                            echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST" class="button-form"><input type="hidden" name="_id" value="'.$row["id"].'"><input type="hidden" name="_method" value="OPTIONS"><button type="submit" name="activate">activate</button></form>';
                        }
                    }
                    if ($role == 'owner') {
                        echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST" class="button-form"><input type="hidden" name="_id" value="'.$row["id"].'"><input type="hidden" name="_method" value="DELETE"><button type="submit">delete</button></form>';
                    }
                    echo '<form action="/search.php" method="GET" class="button-form"><input type="hidden" name="search" value="'.$row["name"].'"><button type="submit">search</button></form>';
                    echo '</div>';
                    echo "</div>";
                    echo "</div>";
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
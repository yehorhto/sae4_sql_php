<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login page</title>
</head>

<body>
    <?php
    if (isset($_SESSION["flash_message"])) {
        echo "<p>" . $_SESSION["flash_message"] . "<p>";
        unset($_SESSION["flash_message"]);
    }
    ?>
    <form action="../process/inserer_utilisateur.php" method="post">
        <label for="login">Login</label> : <input type="text" name="username" /><br>
        <label for="login">Password</label> : <input type="password" name="passwd" /><br>

        <button type="submit" name="action" value="login">Log in</button>
        <button type="submit" name="action" value="register">Register</button>
    </form>
</body>

</html>
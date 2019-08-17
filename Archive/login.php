<?php
session_start();

require "lib/connection.php";
require "lib/encrypt.php";
require_once "lib/action.php";

$flag = false;

if (checkLogin()) {
    header("location: index.php");
}

else if (isset($_POST["username"]) && isset($_POST["password"])) {
    // Connect with database using own class
    $auth = Database::conn();
    // SELECT if there is a user which matchs username and password
    $login = $auth->search(
        "SELECT * FROM user WHERE username=? AND password=? AND disabled=0",
        array($_POST["username"], encrypt($_POST["password"]))
    );
    if ($login) {
        $_SESSION["user_id"] = $login["id"];
        $_SESSION["user_name"] = $login["name"];
        header("location: index.php");
    }
    $flag = true;
}
?>

<!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="UTF-8">
    <title>Lost & Found - University of Bristol!</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="css/custom.css">
</head>

<body>
    <div class="banner">
        <img src="images/banner.jpg" class="img-fluid" alt="banner">
    </div>
    <div class="container">
        <div class="mb-4">
            <h3>Lost & Found - University of Bristol!</h3>
            <p class="lead mb-0">A simple website helps our students find the things we lost, also a place to return
                things we found!</p>
            <p class="lead">Supported By AWS!</p>
        </div>
        <a href="index.php">Back to index</a>
        <h3 class="pb-2 pt-2">Login</h3>
        <div class="col-md-4 pl-0">
            <?php if ($flag): ?>
            <div class="alert alert-warning hidden" role="alert" id="alert">Login Error!</div>
            <?php endif ?>
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <input type="submit" class="btn btn-primary" value="Login">
                <a href="register.php" class="btn btn-outline-secondary" role="button" aria-pressed="true">Register</a>
            </form>
        </div>
    </div>
</body>

</html>
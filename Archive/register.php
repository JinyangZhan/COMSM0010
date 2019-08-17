<?php
session_start();

require "lib/connection.php";
require "lib/encrypt.php";
require_once "lib/action.php";
$flag = false;

if (checkLogin()) {
    header("location: index.php");
}

else if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["nickname"])) {
    $auth = Database::conn();
    // Check if is registered
    $isRegister = $auth->search("SELECT * FROM user WHERE username=?", array($_POST["username"]));
    if (!$isRegister) {
        $lastId = $auth->execute(
            "INSERT INTO user(`username`,`password`,`name`) VALUES (?,?,?)",
            array($_POST["username"], encrypt($_POST["password"]), $_POST["nickname"])
        );
        if ($lastId !== 0)
            header("location: login.php");
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
        <h3 class="pb-2">Registration</h3>
        <div class="col-md-4 pl-0">
            <?php if ($flag): ?>
            <div class="alert alert-warning hidden" role="alert" id="alert">Register Fail!</div>
            <?php endif ?>
            <form action="register.php" method="POST">
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" id="username" name="username" class="form-control">
                </div>
                <div class="form-group ">
                    <label>Nickname: </label>
                    <input type="text" id="nickname" name="nickname" class="form-control">
                </div>
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" id="password" name="password" class="form-control">
                </div>
                <input type="submit" class="btn btn-primary" value="Register">
                <a href="login.php" class="btn btn-outline-secondary" role="button" aria-pressed="true">Return to
                    login!</a>
            </form>
        </div>
    </div>
    <p></p>
</body>
</html>
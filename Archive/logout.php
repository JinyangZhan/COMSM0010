<?php
session_start();

// Delete session to log out
if (isset($_GET["logout"])) {
    unset($_SESSION["user_id"]);
    unset($_SESSION["user_name"]);
    header("location: index.php");
}

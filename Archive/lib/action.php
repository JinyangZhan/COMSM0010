<?php
session_start();

require_once "connection.php";
require_once "entities.php";

// Check user is logined
function checkLogin() {
    return isset($_SESSION["user_id"]);
}

// Check if is priviledged
function checkPrivilege($item_id = NULL, $comment_id = NULL) {
    $user_id = $_SESSION["user_id"];
    $conn = Database::conn();
    
    // Manager has full privilege
    $user = $conn->search("SELECT manager FROM user WHERE id=?", array($user_id));
    if ($user["manager"]) {
        return true;
    }
    
    // If user is the item publisher?
    if (isset($item_id)) {
        $item = $conn->search("SELECT publisher_id FROM item WHERE id=?", array($item_id));
        if ($item["publisher_id"] === $user_id)
            return true;
    }
    
    // If user is the post publisher?
    if (isset($comment_id)) {
        $post = $conn->search("SELECT poster_id FROM post WHERE id=?", array($comment_id));
        if ($post["poster_id"] === $user_id)
            return true;
    }
    
    return false;
}

// Deal with _GET or _POST parameters
function initalParams($requests) {
    $params = array();
    foreach ($requests as $index => $value) {
        $params[$index] = $value;
    }
    $page = array_key_exists("page", $params) ? intval($params["page"]) : 1;
    $params["page"] = max($page, 1);
    return $params;
}

// Get single Item
function getItem($itemID) {
    $item = new Item();
    $package = $item->getItem($itemID);
    return $package->item ? $package : NULL;
}

// Echo Nav Bar
function navBar($page = 1) {
    if (checkLogin()) {
        echo "<p class=\"mb-1\">Hello, {$_SESSION["user_name"]}! [ " . (checkPrivilege() ? "<a href=\"user_manage.php\">User Management</a> | " : "") . "<a href=\"item_manage.php\">My Items</a> | <a href=\"logout.php?logout\">Logout</a> ]</p>";
    } else {
        echo "<p class=\"mb-1\">[ <a href=\"login.php\">Login</a> | <a href=\"register.php\">Register</a> ]</p>";
    }
    if (isset($page)) {
        echo "<a href=\"index.php?page=$page\">Back to index</a>";
    } else if(checkLogin()) {
        echo "<a href=\"item.php\">Publish a new item here!</a>";
    }
}
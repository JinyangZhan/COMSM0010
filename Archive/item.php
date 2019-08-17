<?php
// Start session
session_start();

require_once "lib/connection.php";
require_once "lib/entities.php";
require_once "lib/action.php";

$params = initalParams($_REQUEST);
$page = $params["page"];

if (!checkLogin()) {
    header("location: index.php?page=$page");
}

$item = new Item();

// Remove Item
if (array_key_exists("rid", $params) && checkPrivilege($params["rid"])) {
    $item->removeItem($params["rid"]);
    header("location: index.php?page=$page");
}

// Add Item
if (array_key_exists("itemname", $params) && array_key_exists("description", $params) && array_key_exists("lof", $params)) {
    if (array_key_exists("status", $params) && array_key_exists("id", $params)) {
        $item->updateItem($params["id"], $params["lof"], $params["itemname"], $params["description"], $params["status"], $_SESSION["user_id"]);
    } else {
        $item->addItem($params["lof"], $params["itemname"], $params["description"], $_SESSION["user_id"]);
    }
    header("Location: index.php?page=$page");
}

if (array_key_exists("id", $params)) {
    $id = intval($params["id"]);
    if (!checkPrivilege($id)) {
        header("location: index.php?page=$page");
        die();
    }
    $packages = $item->getItem($id);
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
        <div class="mb-4">
            <?= navBar($params["page"]) ?>
        </div>
        <div>
            <?php if (!array_key_exists("id", $params)): ?>
            <h3 class="pb-2">Publish a new item!</h3>
            <?php else: ?>
            <h3 class="pb-2">Modification</h3>
            <?php endif ?>
            <form method="POST">
                <div class="form-group row">
                    <label for="itemname" class="col-sm-2 col-form-label">Item Name</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="itemname" name="itemname" placeholder="Item Name" value="<?= $packages ? $packages->item["item_name"] : "" ?>" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="description" class="col-sm-2 col-form-label">Description</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="description" name="description" rows="6"
                            placeholder="When? Where? How?"><?= $packages ? $packages->item["description"] : "" ?></textarea>
                    </div>
                </div>
                <fieldset class="form-group">
                    <div class="row">
                        <legend class="col-form-label col-sm-2 pt-0">Lost or Found</legend>
                        <div class="col-sm-10">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="lof" id="lost_status" value="0" <?= $packages ? ($packages->item["lost_or_found"] ? "" : "checked") : "checked" ?>>
                                <label class="form-check-label" for="lost_status">
                                    Lost
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="lof" id="found_status" value="1" <?= $packages ? ($packages->item["lost_or_found"] ? "checked" : "") : "" ?>>
                                <label class="form-check-label" for="found_status">
                                    Found
                                </label>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <?php if ($packages): ?>
                <fieldset class="form-group">
                    <div class="row">
                        <legend class="col-form-label col-sm-2 pt-0">Status</legend>
                        <div class="col-sm-10">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="open_status" value="0" <?= $packages ? ($packages->item["closed"] ? "" : "checked") : "checked" ?>>
                                <label class="form-check-label" for="open_status">
                                    Open
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="close_status" value="1" <?= $packages ? ($packages->item["closed"] ? "checked" : "") : "" ?>>
                                <label class="form-check-label" for="close_status">
                                    Closed
                                </label>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <?php endif ?>
                <input type="submit" class="btn btn-primary" value="Publish!"></button>
            </form>
        </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
    integrity="sha256-pasqAKBDmFT4eHoN2ndd6lN370kFiGUFyTiUHWhU7k8=" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
</script>

</html>
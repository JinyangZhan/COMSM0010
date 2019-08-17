<?php
// Start session
session_start();

require_once "lib/action.php";

$params = initalParams($_REQUEST);

$itemID = array_key_exists("id", $params) ? intval($params["id"]) : header("location: index.php?page={$params["page"]}");
$itemPackage = getItem($itemID);
if (!$itemPackage) {
    header("location: index.php?page={$params["page"]}");
    die();
}

$comment = new Comment($itemID);
if (checkLogin() && array_key_exists("comment", $params)) {
    $comment->addComment(nl2br($params["comment"]), $_SESSION["user_id"]);
    header("Location: {$_SERVER['REQUEST_URI']}");
}
if (array_key_exists("rid", $params) && checkPrivilege($itemID, $params["rid"])) {
    $comment->removeComment($params["rid"]);
    header("Location: detail.php?id=$itemID");
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

<body class="pb-5">
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
            <p class="lead d-flex w-100 justify-content-between"><b>ITEM INFORMATION</b>
            <?php if(checkPrivilege($itemID, NULL)): ?>
            <small>[ <a href="item.php?id=<?= $itemID ?>">Modify</a> | <a href="item.php?rid=<?= $itemID ?>">Delete</a> ]</small>
            <?php endif ?>
            </p>
            <div>
                <table class="table table-dark table-bordered">
                    <tbody>
                        <tr>
                            <td class="bg-info" width="20%">Lost/Found</td>
                            <td width="30%"><?= $itemPackage->item["closed"] ? "Closed" : ($itemPackage->item["lost_or_found"] ? "Found" : "Lost") ?></td>
                            <td class="bg-info" width="20%">Publisher</td>
                            <td width="30%"><?= $itemPackage->item["username"] ?></td>
                        </tr>
                        <tr>
                            <td class="bg-info" width="20%">Item Name</td>
                            <td colspan="5"><?= $itemPackage->item["item_name"] ?></td>
                        </tr>
                        <tr>
                            <td class="bg-info" width="20%">Description</td>
                            <td colspan="5"><?= html_entity_decode($itemPackage->item["description"]) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div>
            <p class="lead"><b>COMMENTS</b></p>
            <ul class="list-group">
                <?php foreach ($itemPackage->comment as $key => $value): ?>
                <li class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                    <small>Posted by: <?= $value["username"] ?> - <?= $value["post_time"] ?> </small>
                    <?php if(checkPrivilege($itemID, $value["id"])): ?>
                    <small>[ <a href="?id=<?= $itemID ?>&rid=<?= $value["id"] ?>">Delete</a> ]</small>
                    <?php endif ?>
                    </div>
                    <p class="mb-1"><?= html_entity_decode($value["content"]) ?></p>
                </li>
                <?php endforeach ?>
            </ul>
            <br>
            <?php if (checkLogin()): ?>
            <form method="POST">
                <div class="form-group row">
                    <div class="col-sm-12">
                        <textarea class="form-control" id="description" name="comment" rows="4"
                            placeholder="Comment" required></textarea>
                    </div>
                </div>
                <div class="text-right">
                    <button type="button" class="btn btn-secondary" onclick="$('#description').val('')">Clear</button>
                    <input type="submit" class="btn btn-primary" value="Publish!"></button>
                </div>
            </form>
            <?php endif ?>
        </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
    integrity="sha256-pasqAKBDmFT4eHoN2ndd6lN370kFiGUFyTiUHWhU7k8=" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
</script>

</html>
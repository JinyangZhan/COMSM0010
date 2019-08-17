<?php
// Start session
session_start();

require_once "lib/connection.php";
require_once "lib/entities.php";
require_once "lib/action.php";

$params = initalParams($_REQUEST);
$page = $params["page"];

$items = new Items($page);
$packages = $items->getItems();
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
            <?= navBar(NULL) ?>
        </div>
        <div>
            <p class="mb-1">Current page: <?= $packages->page ?> / <?= $packages->total ?> pages in total -
                <?= $items->getNavigator() ?>
            </p>
            <div>
                <table class="table table-striped table-bordered">
                    <thead class="bg-info text-white">
                        <tr>
                            <th class="pt-1 pb-1" colspan="<?= checkPrivilege() ? 3 : 2 ?>">ITEM_NAME</th>
                            <th class="pt-1 pb-1">PUBLISHER</th>
                            <th class="pt-1 pb-1">VIEWS</th>
                            <th class="pt-1 pb-1">REPLIES</th>
                            <th class="pt-1 pb-1">LAST REPLY</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($packages->result as $key => $value): ?>
                        <tr>
                            <td><?= $value["id"] ?></td>
                            <?php if (checkPrivilege()): ?>
                            <td><u><a href="item.php?id=<?= $value["id"] ?>">MOD</a></u> | <u><a
                                    href="item.php?rid=<?= $value["id"] ?>">DEL</a></u></td>
                            <?php endif ?>
                            <td><u><a href="detail.php?id=<?= $value["id"] ?>&page=<?= $page ?>"><?= $value["item_name"] ?>
                                        - <?= $value["closed"] ? "Closed" : ($value["lost_or_found"] ? "Found" : "Lost") ?></a></u></td>
                            <td><?= $value["publisher"] ?></td>
                            <td><?= $value["view_count"] ?></td>
                            <td><?= $value["reply_count"] ?></td>
                            <?php if ($value["post_time"]): ?>
                            <td><?= $value["post_time"] ?><br>By: <?= $value["replyer"] ?></td>
                            <?php else: ?>
                            <td></td>
                            <?php endif ?>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pb-5">
            <form action="search.php" method="GET" class="form-inline my-2 my-lg-0">
                <input name="q" class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
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
<?php
// Start session
session_start();

require_once "lib/connection.php";
require_once "lib/entities.php";
require_once "lib/action.php";

$params = initalParams($_REQUEST);
if (!checkPrivilege()) {
    header("location: index.php?page={$params["page"]}");
}

$users = new Users($params["page"]);
$packages = $users->getUsers();
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
            <p class="lead"><b>User Management</b></p>
            <p class="mb-1">Current page: <?= $packages->page ?> / <?= $packages->total ?> pages in total -
                <?= $users->getNavigator() ?>
            </p>
            <div>
                <table class="table table-striped table-bordered">
                    <thead class="bg-info text-white">
                        <tr>
                            <th class="pt-1 pb-1"></th>
                            <th class="pt-1 pb-1">Action</th>
                            <th class="pt-1 pb-1">Username</th>
                            <th class="pt-1 pb-1">Nickname</th>
                            <th class="pt-1 pb-1">Disabled</th>
                            <th class="pt-1 pb-1">Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($packages->result as $key => $value): ?>
                        <tr>
                            <td><?= $value["id"] ?></td>
                            <td><u><a href="?eid=<?= $value["id"] ?>">Elevate</a></u> | <u><a
                                    href="?rid=<?= $value["id"] ?>">Disable</a></u></td>
                            <td><?= $value["username"] ?></td>
                            <td><?= $value["name"] ?></td>
                            <td><?= $value["disabled"] ? "Disabled" : "Active" ?></td>
                            <td><?= $value["manager"] ? "Manager" : "User" ?></td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
    integrity="sha256-pasqAKBDmFT4eHoN2ndd6lN370kFiGUFyTiUHWhU7k8=" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
</script>

</html>
<?php
include_once('../../IdP/private/User.php');

if (!isset($_SESSION)) {
    session_start();
}

if (!$_SESSION['role'] === 'admin') {
    header("Location: index.php");
}

$user = new User();
$users = $user->selectAppliedUsers();

if (isset($_GET['approveUser'])) {
    $user->approveUser($_GET['approveUser']);
    header("Location: dashboard.php");
}

if (isset($_GET['denyUser'])) {
    $user->denyUser($_GET['denyUser']);
    header("Location: dashboard.php");
}

$imgpath = "../../public/img/user.jpg";
$approveUser = "?approveUser=";
$denyUser = "?denyUser=";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel kamers</title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>

<body>
    <div class="header">
        <div class="home"> 
            <a class="button mr-2" href='../../getStedentrips.php'>Home page</a>
        </div>
        <div class="logout">
            <a class="button mr-2" href=''>Applied users</a>
            <a class="button mr-2" href=''>Approved users</a>
            <a class="button" href='public/?logout'>Denied users</a>
        </div>
    </div>
    <?php if (isset($users)) {
        foreach ($users as $user) { ?>
            <section class="archive">
                <div class="items">
                    <div class="item">
                        <div class="item-out">
                            <div class="item-in">
                                <div class="item-top">
                                    <img src=<?php echo $imgpath;?> alt="">
                                </div>
                                <div class="item-bottom">
                                    <div class="top">
                                        <h2 class="item-title"><?php echo $user['username']; ?></h2>
                                        <div class="">
                                        </div>
                                    </div>
                                    <div class="mid">
                                        <p><?php echo $user['email']; ?></p>
                                        <div class="price">
                                        </div>
                                    </div>
                                    
                                    <a href=<?php echo $approveUser . $user['id'] ?>>
                                        <div class="mid-bottom">
                                            <h2 class="ml">Accepteer</h2>
                                        </div>
                                    </a>

                                    <a href=<?php echo $denyUser . $user['id'] ?>>
                                        <div class="bottom">
                                            <h2 class="ml">Weiger</h2>
                                        </div>
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
    <?php }
    } ?>

</body>

</html>
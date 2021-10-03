<?php
include_once('IdP/IdP.php');

$username = "reisbureau ZIP";
$password = "123";
$credentials = array();
$credentials['username'] = $username;
$credentials['password'] = $password;
$credentials['exp'] = time() + (60 * 60);
$credentials['apiKey'] = '12345';

$idp = new IdP($credentials);
$token = $idp->getToken();

$APIurl = "http://localhost/reisbureau/IdP/microservices/GetStedentripsApi.php";

$ch = curl_init($APIurl);
$curl_post_data = array(
    'apiKey' => '1234567890',
    'username' => $username,
    'password' => $password
);

curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . $token));
curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_post_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$resultStatus = curl_getinfo($ch);

// print_r($response);
// print_r($resultStatus);

$decoded = json_decode($response, true);

// print_r($decoded);
// print_r($decoded['room_sale']);

// echo "<br>Message: " . $decoded['room_city'];
// echo "<br>Status: " . $decoded['status'];
// echo "<br>Bearer token: " . $decoded['bearerToken'];

curl_close($ch);

$imgpath = "./public/room_img/";
$updatePath = "./updateStedentrips.php/?cancel=false&id=";
$updatePathCancel = "./updateStedentrips.php/?cancel=true&id=";
$euro = "€"
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./public/css/style.css">
    <title>Hotel kamers</title>
</head>
<body>
    <section class="archive">
        <div class="items">
            <?php 
            foreach($decoded as $decode) {?>
                    <div class="item">
                        <div class="item-out">
                            <div class="item-in">
                                <div class="item-top">
                                    <img src=<?php echo $imgpath . $decode['room_image']; ?> alt="">
                                    <?php if ($decode['room_sale'] == 'Y') { ?>
                                        <div class="sale-label">Aanbieding</div>
                                    <?php } ?>
                                </div>
                                <div class="item-bottom">
                                    <div class="top">
                                        <h2 class="item-title"><?php echo $decode['room_city']; ?></h2>
                                        <div class="rating">
                                            <?php echo $decode['room_rating']; ?>
                                        </div>
                                    </div>
                                    <div class="mid">
                                        <p><?php echo $decode['room_shortad']; ?></p>
                                        <div class="price">
                                            <p>vanaf <span><?php echo $decode['room_price']; ?></span></p>
                                            <p>p.p.</p>
                                        </div>
                                    </div>
                                    <a href="#">
                                        <div class="mid-bottom">
                                            <h2 class="ml">Bekijk details ></h2>
                                        </div>
                                    </a>
                                    <?php if ($decode['room_reserved'] == 'N') {?>
                                        <a href=<?php echo $updatePath . $decode['id']?>>
                                            <div class="bottom">
                                                <h2 class="ml">Reserveer ></h2>
                                            </div>
                                        </a>
                                    <?php } else {?>
                                        <a href=<?php echo $updatePathCancel . $decode['id']?>>
                                            <div class="bottom">
                                                <h2 class="ml">Annuleer Reservering ></h2>
                                            </div>
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php } ?>
        </div>
    </section>
</body>
</html>
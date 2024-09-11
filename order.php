<?php
session_start();

include_once "config.php";
include_once "functions.php";
include_once "API/usersRequests.php";
include_once "API/ordersRequests.php";

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    die;
}

$userID = $_SESSION['user_id'];
$user = GetCurrentUser($pdo);
if ($userID != $user['id']) {
    echo "<script>
        console.
        alert('Vous ne pouvez pas accéder à cette page');
        window.location.href = 'index.php';
    </script>";
    die;
}

$userName = $user['nom'];

$orderID = $_GET['id'];
$order = GetUserOrderByNumber($pdo, $userID, $orderID);
if ($order == false) {
    echo "<script>
        alert('Commande introuvable');
        window.location.href = 'index.php';
    </script>";
    die;
}

$total = $order['prix_total'];
$date = $order['date_creation'];
$status = $order['statut'];

$orderDetails = GetOrderDetails($pdo, $orderID);
if ($orderDetails == false) {
    echo "<script>
        alert('Détails de la commande introuvables');
        window.location.href = 'index.php';
    </script>";
    die;
}


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Commande</title>
    <link rel="icon" type="image/x-icon" href="assets/img/logo-black.png" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="assets/css/order.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>
    <main>

        <?php include "header.php"; ?>

        <div class="container-fluid">

            <div class="container">
                <div class="d-flex justify-content-between align-items-center py-3">
                    <h2 class="h5 mb-0">Commande n° <?php echo "$orderID" ?></h2>
                </div>

                <div class="row">

                    <div class="col-lg-8">


                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-between">
                                    <div>
                                        <?php

                                        $orderStatusTag = "<p class='badge rounded-pill bg-danger text-black'>Etat Inconnu</p>";

                                        switch ($status) {
                                            case 1:
                                                $orderStatusTag = "<p class='badge rounded-pill bg-danger text-black' style='--bs-bg-opacity: .5;'>En attente d'expedition</p>";
                                                break;
                                            case 2:
                                                $orderStatusTag = "<p class='badge rounded-pill bg-warning text-black' style='--bs-bg-opacity: .5;'>Expediée</p>";
                                                break;
                                            case 3:
                                                $orderStatusTag = "<p class='badge rounded-pill bg-success text-black' style='--bs-bg-opacity: .5;'>En cours de livraison</p>";
                                                break;
                                            case 4:
                                                $orderStatusTag = "<p class='badge rounded-pill bg-primary text-black' style='--bs-bg-opacity: .5;'>Livrée</p>";
                                                break;
                                        }

                                        echo " <span class='me-3'>$date</span>
                                    $orderStatusTag
                                    " ?>
                                    </div>
                                </div>

                                <hr>
                                <table class="table table-borderless">
                                    <tbody>
                                        <?php

                                        foreach ($orderDetails as $orderDetail) {
                                            $productID = $orderDetail['produit_id'];
                                            $product = GetProductById($pdo, $productID, 0);
                                            $productName = $product['nom'];
                                            $productQuantity = $orderDetail['quantite'];
                                            $price = $product['prix'] * $productQuantity;
                                            $pathProductImg = PATH_PRODUCTS_IMAGES . $productID . ".webp";

                                            echo "<tr>
                                                <td>
                                                    <div class='d-flex mb-2'>
                                                        <div class='flex-shrink-0'>
                                                            <img src='$pathProductImg' width='35' class='img-fluid'>
                                                        </div>
                                                        <div class='flex-lg-grow-1 ms-3'>
                                                            <h6 class='small mb-0'><a href='product.php?id=$productID' class='text-reset'>$productName</a></h6>
                                                            <span class='small'>Taille : Petit</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>$productQuantity</td>
                                                <td class='text-end'>$price €</td>
                                            </tr>";
                                        }

                                        ?>


                                    </tbody>
                                    <tfoot>
                                        <tr class="fw-bold">
                                            <td colspan="2">TOTAL</td>
                                            <td class="text-end"><?php echo "$total" ?> €</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>


                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="row">

                                    <?php
                                    $stringPayementMethod = "";
                                    $payementMethod = GetPaymentMethodByID($pdo, $order['moyen_de_paiement'], 0);
                                    if ($payementMethod === false) {
                                        echo "<script>alert('Moyen de paiement introuvable');</script>";
                                        die;
                                    } else {

                                        if ($payementMethod['type'] == "paypal") {
                                            $paypalEmail = $payementMethod['paypal_email'];
                                            $stringPayementMethod = "<div><p>Paypal $paypalEmail</p></div>";
                                        } else {
                                            $cardNumberLast4Digits = substr($payementMethod['numero_carte'], -4);;
                                            $stringPayementMethod = "<div><p>Visa **** **** **** $cardNumberLast4Digits</p></div>";
                                        }

                                        $billingAdresssID = $order["adresse_de_facturation"];
                                        $billingAdresssInfo = GetAddressByID($pdo, $billingAdresssID, 0);

                                        if ($billingAdresssInfo === false) {
                                            echo "<script>alert('Adresse de facturation introuvable');</script>";
                                            die;
                                        } else {
                                            $billingAddress = $billingAdresssInfo['voie'];
                                            $billingCity = $billingAdresssInfo['ville'];
                                            $billingCountry = $billingAdresssInfo['pays'];
                                            $billingPostalCode = $billingAdresssInfo['code_postal'];
                                            $billingName = $userName;

                                            if(isset($payementMethod['nom_titulaire']) && !empty($payementMethod['nom_titulaire'])){
                                                $billingName = $payementMethod['nom_titulaire'];
                                            }

                                            echo "
                                            <div class='col-lg-6'>
                                                <h3 class='h6'>Adresse de facturation</h3>
                                                <hr>
                                                <address>
                                                    <strong>$billingName</strong><br>
                                                    $billingAddress<br>
                                                    $billingCity, $billingCountry<br>
                                                </address>
                                                </div>
                                                <div class='col-lg-6'>
                                                    <h3 class='h6'>Methode de paiement</h3>
                                                    <hr>
                                                    $stringPayementMethod
                                                </div>
                                            </div>";
                                        }
                                    }

                                    ?>
                                </div>
                            </div>
                        </div>


                        <div class="col-lg-4">

                            <div class="card mb-4">
                                <div class="card-body">
                                    <?php
                                    $shippingAdresssID = $order["adresse_de_livraison"];
                                    $shippingAdresssInfo = GetAddressByID($pdo, $shippingAdresssID, 0);
                                    if ($shippingAdresssInfo === false) {
                                        echo "<script>alert('Adresse de livraison introuvable');</script>";
                                        die;
                                    } else {
                                        $shippingAddress = $shippingAdresssInfo['voie'];
                                        $shippingCity = $shippingAdresssInfo['ville'];
                                        $shippingCountry = $shippingAdresssInfo['pays'];
                                        $shippingPostalCode = $shippingAdresssInfo['code_postal'];

                                        echo "
                                            <h3 class='h6'>Adresse de livraison</h3>
                                            <hr>
                                            <address>
                                                <strong>$userName</strong><br>
                                                $shippingAddress<br>
                                                $shippingCity, $shippingPostalCode<br>
                                            </address>";
                                    }

                                    ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="push"></div>
    </main>

    <?php include "footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
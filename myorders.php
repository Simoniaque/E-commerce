<?php
session_start();

include_once "config.php";
include_once "functions.php";
include_once "API/ordersRequests.php";

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    die;
}

if (isset($_GET['orderNumber'])) {
    $searchOrderNumber = (int)$_GET['orderNumber'];
} else {
    $searchOrderNumber = '';
}

$orders = array();
if ($searchOrderNumber > 0) {
    $orders = GetUserOrderByNumber($pdo, $_SESSION['user_id'], $searchOrderNumber);
} else {
    $orders = GetUserOrdersByNewest($pdo, $_SESSION['user_id']);
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Mes Commandes</title>
    <link rel="icon" type="image/x-icon" href="assets/img/logo-black.png" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="assets/css/myorders.css" />

    <script>
        function ordersSort() {
            let select = document.getElementById('ordersOrder');
            let ordersDiv = document.getElementById('ordersList');

            if (select.value == 1) {
                ordersDiv.setAttribute('style', 'display: flex; flex-direction: column;');
            } else {
                ordersDiv.setAttribute('style', 'display: flex; flex-direction: column-reverse;');
            }
        }
    </script>

</head>

<body>
    <main>
        <?php include "header.php"; ?>

        <section class="section mt-3">
            <div class="container">
                <div class="justify-content-center row d-flex justify-content-around">
                    <span class="col-lg-5 row">
                        <form method="GET" action="" class="d-flex align-items-center">
                            <div class="input-group">
                                <input name="orderNumber"
                                    placeholder="Numéro de commande" type="search" class="form-control "
                                    value="<?php if ($searchOrderNumber != 0) echo $searchOrderNumber ?>" />
                                <button type="submit" class="btn btn-dark ms-2">Rechercher</button>
                            </div>
                        </form>
                    </span>
                    <div class="col-lg-4 mb-2">
                        <select class="form-select" id="ordersOrder" onchange="ordersSort()">
                            <option value="1">Plus récentes</option>
                            <option value="2">Plus anciennes</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div id="ordersList">

                            <?php

                            if (is_array($orders)) {
                                foreach ($orders as $order) {
                                    $orderId = $order['id'];
                                    $orderDate = $order['date_creation'];
                                    $orderTotal = $order['prix_total'];
                                    $orderStatus = $order['statut'];
                                    $orderStatusTag = "<p class='badge rounded-pill bg-danger text-black'>Etat Inconnu</p>";

                                    switch ($orderStatus) {
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

                                    echo "<div class='order-list-box card mt-4'>
                                            <div class='p-4 card-body'>
                                                <div class='align-items-center row'>
                                                    <div class='col-lg-5'>
                                                        <div class='mt-3 mt-lg-0'>
                                                            <h5 class='fs-19 mb-4'>
                                                                <a class='primary-link' href='order.php?id=$orderId'>Commande n° $orderId</a>
                                                            </h5>
                                                            <h5>$orderTotal €</h5>
                                                        </div>
                                                    </div>
                                                <div class='col-lg-4'>
                                                    <div class='mt-2 mt-lg-0 d-flex flex-wrap align-items-start gap-1'>
                                                        <span class='badge bg-soft-secondary fs-14 mt-1'>$orderDate</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class='shipping-state'>
                                                $orderStatusTag
                                            </div>
                                        </div>
                                    </div>";
                                }
                            } else {
                                DisplayDismissibleAlert("Erreur lors de la récupération de vos commandes");
                            }

                            if(empty($orders)){
                                echo "<h2 class='mt-5 text-center'>Vous n'avez pas encore passé de commande.</h2>";
                            }

                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="push"></div>
    </main>

    <?php include "footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
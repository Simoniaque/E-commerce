<?php
session_start();

include_once "../config.php";
include_once "../API/usersRequests.php";
include_once "../API/ordersRequests.php";
include_once "../functions.php";

$user = GetCurrentUser($pdo);

if($user === false){
    header('Location: ../index.php');
    exit;
}

if($user['est_admin'] == 0){
    header('Location: ../index.php');
    exit;
}

if(isset($_GET['delete_id'])){
    $orderID = intval($_GET['delete_id']);
    if(DeactivateOrder($pdo, $orderID)){
        DisplayDismissibleSuccess("Commande désactivée avec succès.");
    } else {
        DisplayDismissibleAlert("Erreur lors de la désactivation de la commande.");
    }

}

if(isset($_GET['activate_id'])){
    $orderID = intval($_GET['activate_id']);
    if(ActivateOrder($pdo, $orderID)){
        DisplayDismissibleSuccess("Commande activée avec succès.");
    } else {
        DisplayDismissibleAlert("Erreur lors de l'activation de la commande.");
    }
}

if (isset($_GET['order_id']) && isset($_GET['status'])) {
    $orderID = intval($_GET['order_id']);
    $status = intval($_GET['status']);
    
    if (UpdateOrderStatus($pdo, $orderID, $status)) {
        DisplayDismissibleSuccess("Statut de la commande mis à jour avec succès.");
    } else {
        DisplayDismissibleAlert("Erreur lors de la mise à jour du statut de la commande.");
    }
}

$orders = GetOrders($pdo);


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commandes</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="icon" type="image/x-icon" href="../assets/img/logo-black.png" />
    <style>
        .open ul.dropdown-menu {
    display: block;
}
    </style>

</head>

<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-2 p-0 bg-dark text-white">
                <?php include 'navbar.php'; ?>
            </div>

            <!-- Main content area -->
            <div class="col-md-10 p-0">
                <div id="alertContainer"></div>

                <?php include 'header.php'; ?>
                    <h1>Commandes</h1>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Adresse mail</th>
                                <th scope="col">Prix total</th>
                                <th scope="col">Adresse de facturation</th>
                                <th scope="col">Adresse de livraison</th>
                                <th scope="col">Moyen de paiement</th>
                                <th scope="col">Statut</th>
                                <th scope="col">Actions</th>
                                <th scope="col">Est active</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            foreach ($orders as $order){
                                $orderID = $order['id'];
                                $userEmail = GetUserByID($pdo, $order['utilisateur_id'])['email'];
                                $totalPrice = $order['prix_total'];
                                $billingAddress = GetAddressByID($pdo,$order['adresse_de_facturation'],0);
                                $fullBillingAddress = $billingAddress['voie'] . ', ' . $billingAddress['code_postal'] . ' ' . $billingAddress['ville'] . ', ' . $billingAddress['pays'];

                                $shippingAddress = GetAddressByID($pdo,$order['adresse_de_livraison'],0);
                                $fullShippingAddress = $shippingAddress['voie'] . ', ' . $shippingAddress['code_postal'] . ' ' . $shippingAddress['ville'] . ', ' . $shippingAddress['pays'];
                                $paymentMethod = GetPaymentMethodByID($pdo,$order['moyen_de_paiement'],0)['type'];
                                $paymentMethod == "card" ? $paymentMethod = "Carte bancaire" : $paymentMethod = "Paypal";
                                $statusInt = $order['statut'];
                                $statusString = "";
                                switch($statusInt){
                                    case 1:
                                        $statusString = "En attente d'expedition";
                                        break;
                                    case 2:
                                        $statusString = "Expediée";
                                        break;
                                    case 3:
                                        $statusString = "En cours de livraison";
                                        break;
                                    case 4:
                                        $statusString = "Livrée";
                                        break;
                                }

                                $isActive = $order['est_actif'];
                                $isActiveTag = "<td class='bg-" . ($isActive ? "success" : "danger") . "'></td>";

                                echo "
                                <tr>
                                    
                                    <th scope='row'><a href='orderdetail.php?id=$orderID'/>$orderID</th>
                                    <td>$userEmail</td>
                                    <td>$totalPrice</td>
                                    <td>$fullBillingAddress</td>
                                    <td>$fullShippingAddress </td>
                                    <td>$paymentMethod</td>

                                    <td>
                                        <div class='dropdown'>
                                            <button class='btn  dropdown-toggle' type='button' id='dropdownMenuButton$orderID' data-bs-toggle='dropdown' aria-expanded='false'>
                                                $statusString
                                            </button>
                                            <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton$orderID'>
                                                <li><a class='dropdown-item' href='orders.php?order_id=$orderID&status=1'>En attente d'expédition</a></li>
                                                <li><a class='dropdown-item' href='orders.php?order_id=$orderID&status=2'>Expédiée</a></li>
                                                <li><a class='dropdown-item' href='orders.php?order_id=$orderID&status=3'>En cours de livraison</a></li>
                                                <li><a class='dropdown-item' href='orders.php?order_id=$orderID&status=4'>Livrée</a></li>
                                            </ul>
                                        </div>
                                    </td>



                                    <td>
                                    <a href='orders.php?activate_id=$orderID' class='btn btn-dark'>Activer</a>
                                    <a href='orders.php?delete_id=$orderID' class='btn btn-danger'>Désactiver</a>
                                    </td>
                                    $isActiveTag
                                    
                                </tr>
                                ";}
                                ?>
                        </tbody>
                    </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
    </script>
</body>
</html>


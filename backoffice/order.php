<?php
// order.php

include 'header.php';
include 'db.php';

$order_id = '';
$order = null;

if (isset($_GET['id'])) {
    $order_id = $_GET['id'];
    $query = "SELECT * FROM orders WHERE id = $order_id";
    $result = mysqli_query($conn, $query);
    $order = mysqli_fetch_assoc($result);
}

// On pourrait ajouter ici la logique pour modifier les détails de la commande si nécessaire
?>

<h1>Order #<?php echo $order_id; ?></h1>

<?php if ($order) { ?>
    <p>User ID: <?php echo $order['user_id']; ?></p>
    <p>Total: <?php echo $order['total']; ?></p>
    <p>Date: <?php echo $order['created_at']; ?></p>
    <!-- Ajouter d'autres détails si nécessaire -->
<?php } else { ?>
    <p>Order not found.</p>
<?php } ?>

<?php
include 'footer.php';
?>

<?php
// orders.php

include 'header.php';
include 'db.php';

$query = "SELECT * FROM orders";
$result = mysqli_query($conn, $query);
?>

<h1>Orders</h1>

<table>
    <tr>
        <th>ID</th>
        <th>User</th>
        <th>Total</th>
        <th>Date</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['user_id']; ?></td>
            <td><?php echo $row['total']; ?></td>
            <td><?php echo $row['created_at']; ?></td>
            <td>
                <a href="order.php?id=<?php echo $row['id']; ?>">View</a>
                <a href="delete_order.php?id=<?php echo $row['id']; ?>">Delete</a>
            </td>
        </tr>
    <?php } ?>
</table>

<?php
include 'footer.php';
?>

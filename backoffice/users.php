<?php
// users.php

include 'header.php';
include 'db.php';

$query = "SELECT * FROM users";
$result = mysqli_query($conn, $query);
?>

<h1>Users</h1>

<a href="user.php">Add New User</a>

<table>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['username']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td>
                <a href="user.php?id=<?php echo $row['id']; ?>">Edit</a>
                <a href="delete_user.php?id=<?php echo $row['id']; ?>">Delete</a>
            </td>
        </tr>
    <?php } ?>
</table>

<?php
include 'footer.php';
?>

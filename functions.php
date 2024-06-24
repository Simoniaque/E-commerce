<?php
function checkLogin($con){
    if(isset($_SESSION['user_id'])){
        $id = $_SESSION['user_id'];
        $query = "SELECT * FROM utilisateurs WHERE id = '$id' limit 1";

        $result = mysqli_query($con,$query);

        if($result && mysqli_num_rows($result)> 0){
            $userData = mysqli_fetch_assoc($result);
            return $userData;
        }
    }
}
?>

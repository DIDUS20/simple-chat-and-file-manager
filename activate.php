<?php

    require('config.php');

    if(isset($_GET['email']) && isset($_GET['avtivation_code'])){
        $mail = $_GET['email'];
        $code = $_GET['avtivation_code'];
        $is_user = mysqli_query($link,"Select user_id from users where email = $mail AND activationcode = $code");
        $user_id = mysqli_fetch_array($is_user)['user_id'];
        active_user($link,$user_id);
    }

    function active_user($link,$id){
        if(mysqli_query($link,"UPDATE users set avtivated = 1 where user_id = $id")){
            echo "<script>alert('Succes')</script>";
        }else{
            echo "<script>alert('Error')</script>";
        }
    }
?>
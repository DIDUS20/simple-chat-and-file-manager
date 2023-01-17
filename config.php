<?php
    // Conection variables
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'con');
    define('DB_PASSWORD', '1234');
    define('DB_NAME', 'example');
    const APP_URL = 'http://localhost/example';
    const SENDER_EMAIL_ADDRESS = 'no-reply@example.com';
    
    // Connect to mysql db
    $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    // If Error
    if($link == false){
        echo"\n Oh No!! Server Not Working !!!";
    }

    // Expire time (min)
    $e_time = 30;

    // Titles
    $title_index = "Login";
    $title_loged = "Home";
    $title_signup = "Sign Up";

    // Icons
    $icon_path = "icon.ico";

    // Max lenght of inputs in signup.php
    $max_pass = 30;
    $max_username = 30;
    $max_surname = 30;
    $max_name = 30;
    $max_mail = 50;

    // Backgrouds
    $background_signup = "";
    $background_index = "";
    $background_loged = "";


?>
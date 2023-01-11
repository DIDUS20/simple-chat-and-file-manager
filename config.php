<?php
    // Conection variables
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'con');
    define('DB_PASSWORD', '1234');
    define('DB_NAME', 'example');
    
    // Connect to mysql db
    $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    // If Error
    if($link === false){
        die("ERROR: " . mysqli_connect_error());
        echo"Oh No!! Server Not Working !!!";
    }

    // Titles
    $title_index = "Login";
    $title_loged = "Home";
    $title_signup = "Sign Up";

 



?>
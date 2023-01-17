<?php
    require_once 'config.php';

    //Users folder
    if(!file_exists('users')){
        mkdir('users');
    }

    // Define variables
    $username = $password = "";
    $username_err = $password_err = $login_err = "";
    $htmlspecialchars = htmlspecialchars($_SERVER['PHP_SELF']);

    // Session control
    session_start();
    if(@$_SESSION['loggedin'] == true){
        header("location: loged.php");
    }else{
        session_destroy();
    }

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        // Check if username is empty
        if(empty(trim($_POST["username"]))){
            $username_err = "Please enter username.";
        } else{
            $username = trim($_POST["username"]);
        }
        // Check if password is empty
        if(empty(trim($_POST["password"]))){
            $password_err = "Please enter your password.";
        } else{
            $password = trim($_POST["password"]);
        }
        // Show error
        if(!empty($login_err)){
            echo"<script>alert('ERROR : $login_err');</script>";
        }
        // LOGIN
        if(empty($username_err) && empty($password_err)){
            $sql = "SELECT `user_id`, `username`, `password` FROM `users` WHERE `username` = ?";
            if($stmt = mysqli_prepare($link, $sql)){
                // Prepare statement
                mysqli_stmt_bind_param($stmt,"s",$param_username);
                $param_username = $username;
                if(mysqli_stmt_execute($stmt)){
                    mysqli_stmt_store_result($stmt);
                    if(mysqli_stmt_num_rows($stmt) == 1){              
                        mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                        if(mysqli_stmt_fetch($stmt)){
                            if(password_verify($password,$hashed_password)){
                                session_start();
                                // Send data to session
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username; 
                                $_SESSION["homepath"] = "users/$username";
                                $_SESSION["path"] = "users/$username/storage";   
                                $_SESSION["start_time"] = time();
                                $_SESSION['expire'] = $_SESSION["start_time"]+($e_time*60);                   
                                // Redirect user to welcome page
                                header("location: loged.php");
                            }else{
                                // Password is invalid, display a generic error message
                                $login_err = "<script>alert('ERROR : Invalid username or password.);</script>";
                            }
                        }
                    } else{
                        // Username doesn't exist, display a generic error message
                        $login_err = "<script>alert('ERROR : Invalid username or password.);</script>";
                    }
                } else{
                    $login_err = "<script>alert('ERROR : Oops! Something went wrong. Please try again later.);</script>";
                }
                // Close statement
                mysqli_stmt_close($stmt);
            }
        }
        // Close connection
        mysqli_close($link);
    }

    //**********************LOGIN PAGE**********************//
?>    
    <html>
        <head>
            <title><?php echo $title_index; ?></title>
            <link rel='icon' href='<?php echo $icon_path; ?>'>
            <link rel='stylesheet' href='style.css?v=<?php echo time(); ?>'>
            <style>
            body{
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            }
            </style> 
        </head>
             
            <body background='<?php echo "$background_index"; ?>'>
                <div class='login_form'>
                    <form action=<?php echo $htmlspecialchars ?> method='post'>
                        <input type='text' class='input_text' <?php $username_err ?> name='username' placeholder='Username'><br>
                        <?php echo $username_err ?><br>
                        <input type='password' class='input_text' <?php $password_err ?> name='password' placeholder='Password'><br>
                        <?php echo $password_err ?><br>
                        <input type='submit' class='input_login' value='Login'>
                    </from>
                    <?php echo $login_err ?><hr>
                    <a href='signup.php'>Sign Up</a>
                </div>
                <br>
                
            </body>
    </html>
    


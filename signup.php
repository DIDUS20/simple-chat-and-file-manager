<?php
    require_once "config.php";
    // Define variables
    $username = $password = $name = $surname = $confirm_password = "";
    $username_err = $password_err = $login_err = $name_err = $surname_err = $confirm_password_err = "";
    $background_signup = "bg.jpg";
    $form_action = htmlspecialchars($_SERVER['PHP_SELF']);

    if(session_status() == 2){
        session_destroy();
    }

    if($_SERVER["REQUEST_METHOD"] == "POST" ){
        // Validate username
        if(empty(trim($_POST["username"]))){
            $username_err = "Please enter a username.";
        } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
            $username_err = "Username can only contain letters, numbers, and underscores.";
        } else{
            //Prepare a select statement
            $sql = "SELECT `user_id` FROM users WHERE username = ?";
            if($stmt = mysqli_prepare($link, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "s", $param_username);
                // Set parameters
                $param_username = trim($_POST["username"]);
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    mysqli_stmt_store_result($stmt);
                    if(mysqli_stmt_num_rows($stmt) == 1){
                        $username_err = "This username is already taken.";
                    } else{
                        $username = trim($_POST["username"]);
                    }
                } else{
                    echo "Oops! Something went wrong. Please try again later.";
                }
                // Close statement
                mysqli_stmt_close($stmt);
            }
        }
        
        // Validate password
        if(empty(trim($_POST["password"]))){
            $password_err = "Please enter a password.";     
        } elseif(strlen(trim($_POST["password"])) < 3){
            $password_err = "Password must have atleast 3 characters.";
        } else if(empty(trim($_POST["password"])) != empty(trim($_POST["confirm_password"]))){
            $confirm_password_err = "Password did not match";
        } else{
            $password = trim($_POST["password"]);
        }

        // Check if name is empty
        if(empty(trim($_POST["name"]))){
            $name_err = "Please enter your Name.";
        } else{
            $name = trim($_POST["name"]);
        } 

        // Check if surname is empty
        if(empty(trim($_POST["surname"]))){
            $surname_err = "Please enter your Surame.";
        } else{
            $surname = trim($_POST["surname"]);
        }

        if(empty(trim($username_err)) && empty(trim($password_err)) && empty(trim($login_err)) && empty(trim($name_err)) && empty(trim($surname_err)) && empty(trim($confirm_password_err))){

            // Prepare an insert statement and storage directory
            $sql = "INSERT INTO users (`username`, `password`, `name`, `surname`, `storage_path`) VALUES (?, ?, ?, ?, ?)";
            mkdir("users/$username");
            mkdir("users/$username/storage");
            $storage_path = "users/$username/storage";  
         
            if($stmt = mysqli_prepare($link, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "sssss", $param_username, $param_password, $param_name, $param_surname, $param_storage_path);
                // Set parameters
                $param_username = $username;
                $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
                $param_name = $name;
                $param_surname = $surname;
                $param_storage_path = $storage_path;
                
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    // Redirect to login page
                    header("location: index.php");
                } else{
                    echo "Oops! Something went wrong. Please try again later.";
                }
                // Close statement
                mysqli_stmt_close($stmt);
            }
        }

    }
        
    
?>
<html>
        <head>
            <title><?php echo $title_signup; ?></title>
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
            <body background='<?php echo "$background_signup"; ?>'>
                <div class='signup_form'>
                    <form action='<?php echo $form_action ?>' method='post'>
                        
                        <input class='input_text' $username_err type='text' name='username' value='<?php echo $username ?>' placeholder='Username'><br>
                        <span class='empty'><?php echo $username_err ?></span><br>

                        <input class='input_text' $name_err type='text' name='name' value='<?php echo $name ?>' placeholder='Name'><br>
                        <span class='empty'><?php echo $name_err ?></span><br>

                        <input class='input_text' $surname_err type='text' name='surname' value='<?php echo $surname ?>' placeholder='Surname'><br>
                        <span class='empty'><?php echo $surname_err ?></span><br>
                        
                        <input class='input_text' $password_err type='password' name='password' placeholder='Password'><br>
                        <span class='empty'><?php echo $password_err ?></span><br>

                        <input class='input_text' $confirm_password_err type='password' name='confirm_password' placeholder='Confirm password'><br>
                        <span class='empty'><?php echo $confirm_password_err ?></span><br><br>
            
                        <input class='input_login' type='submit' class='loginbut' value='Sign Up' ><br><br>
                        <hr>
                        <a href='index.php'>Log In</a>
                    </form>
                </div>

                
            </body>
</html>
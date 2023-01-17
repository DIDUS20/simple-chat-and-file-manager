<?php
    require_once "config.php";
    // Define variables
    $username = $password = $name = $surname = $confirm_password = $mail = "";
    $username_err = $password_err = $login_err = $name_err = $surname_err = $mail_err = $confirm_password_err = "";
    $form_action = htmlspecialchars($_SERVER['PHP_SELF']);
    

    // Session control
    session_start();
    if(@$_SESSION['loggedin'] == true){
        header("location: loged.php");
    }else{
        session_destroy();
    }
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
        } else if(trim($_POST["password"]) != trim($_POST["confirm_password"])){
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

    // Check email 
        if(empty(trim($_POST["mail"]))){
            $mail_err = "Please enter your e-mail.";
        }else if(preg_match('/@./',$mail)){
            $mail_err = "Please check your e-mail.";
        }else{
            //Prepare a select statement
            $sql = "SELECT `user_id` FROM users WHERE email = ?";
            if ($stmt = mysqli_prepare($link, $sql)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "s", $param_username);
                // Set parameters
                $param_username = trim($_POST["mail"]);
                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_store_result($stmt);
                    if (mysqli_stmt_num_rows($stmt) == 1) {
                        $mail_err = "This e-mail is already taken.";
                    } else {
                        $mail = trim($_POST["mail"]);
                    }
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
                // Close statement
                mysqli_stmt_close($stmt);
            }
        }
        
        // Mail verification 
        function generate_activation_code(): string{
            return bin2hex(random_bytes(16));
        }
        function send_activation_email(string $email, string $activation_code): void{
            // Create the ver link
            $activation_link = APP_URL . "/activate.php?email=$email&activation_code=$activation_code";

            // Email header
            $header = "From:" . SENDER_EMAIL_ADDRESS;

            // Email
            $subject = 'Please activate your account :)';
            $message = <<<MESSAGE
                    Hi,
                    Please click the following link to activate your account:
                    $activation_link
                    MESSAGE;

            // Send the email
            mail($email, $subject, nl2br($message), $header);
        }

    // Send mail
    $activation_code = generate_activation_code();

    // Send ver mail
    if (send_activation_email($mail, $activation_code)) {
        $mail_err = "Can't send mail. :/";
    }

    // Send values and verification mail
        if(empty(trim($username_err)) && empty(trim($password_err)) && empty(trim($login_err)) && empty(trim($name_err)) && empty(trim($surname_err)) && empty(trim($confirm_password_err)) && empty(trim($mail_err))){

            // Prepare an insert statement and storage directory
            $sql = "INSERT INTO users (`username`,`password`,`name`,`surname`,`email`,`activationcode`,`storage_path`) VALUES (?, ?, ?, ?, ?, ?, ?)";
            mkdir("users/$username");
            mkdir("users/$username/storage");
            $storage_path = "users/$username/storage";  
         
            if($stmt = mysqli_prepare($link, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "sssssss", $param_username, $param_password, $param_name, $param_surname, $param_mail, $activation_code, $param_storage_path);
                // Set param
                $param_username = $username;
                $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
                $param_name = $name;
                $param_surname = $surname;
                $param_mail = $mail;
                $param_storage_path = $storage_path;
                
                
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    // Redirect to login page
                    // header("location: index.php");
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
                        
                        <input class='input_text' $username_err type='text' name='username' value='<?php echo $username ?>' placeholder='Username'  maxlength="<?php echo $max_username ?>"><br>
                        <span class='empty'><?php echo $username_err ?></span><br>

                        <input class='input_text' $name_err type='text' name='name' value='<?php echo $name ?>' placeholder='Name' maxlength="<?php echo $max_name ?>"><br>
                        <span class='empty'><?php echo $name_err ?></span><br>

                        <input class='input_text' $surname_err type='text' name='surname' value='<?php echo $surname ?>' placeholder='Surname' maxlength="<?php echo $max_surname ?>"><br>
                        <span class='empty'><?php echo $surname_err ?></span><br>

                        <input class='input_text' $mail_err type='text' name='mail' value='<?php echo $mail ?>' placeholder='E-mail' maxlength="<?php echo $max_mail ?>"><br>
                        <span class='empty'><?php echo $mail_err ?></span><br>
                        
                        <input class='input_text' $password_err type='password' name='password' placeholder='Password' maxlength="<?php echo $max_pass ?>"><br>
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
<?php

    //Start session
    session_start();

    // Include config file
    require_once "config.php";
    
    
    if(isset($_SESSION['loggedin'])){
        //Variables 
        $message_err = $message = "";
        $userid = $_SESSION["id"];
        $username = $_SESSION["username"];
        $rid  = $_SESSION['reciver'];
        $chatidsql = mysqli_fetch_array(mysqli_query($link,"SELECT chat_id,`text` from chats where (user1 = $userid and user2 = $rid) or (user1 = $rid and user2 = $userid)"));
        $chatid = $chatidsql['chat_id'];
        $oldtext = $chatidsql['text'];

        //Send message
        if($_SERVER["REQUEST_METHOD"] == "POST"){

            //Validate message input and errors
            if(empty(trim($_POST["message"]))){
                $message_err = "Please enter message.";     
            }else if(!preg_match('/^[a-zA-Z0-9,._; ]+$/', trim($_POST["message"]))){
                $message_err = "message can only contain letters, numbers, and underscores.";
            }else if(empty($message_err)){
                $messageinput = "|<h4>$username:".$_POST['message']."</h4>";
                $messageinput2 = $oldtext.$messageinput;
                $mysqltextupdate = "UPDATE chats SET `text` = '$messageinput2' where chat_id = $chatid";
                if(mysqli_query($link,$mysqltextupdate)){
                    
                }else{
                    echo "error";
                }
            } 

        }
    ?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <title><?php echo $chatid; ?></title>
    <style>
        body{
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover; 
            background-color: grey;
        } 
    </style>
</head>
<body>
    <?php
        //Show old messages
        $text = mysqli_fetch_array(mysqli_query($link,"SELECT `text` FROM chats where chat_id = $chatid"));
        $oldtext = $text["text"];
        $texttab = explode("|",$oldtext);
        foreach($texttab as $t){
            echo $t;
        }
    ?>
    <br><hr><div class='Inputmessageholder' ><form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <input type="text" name="message" class="<?php echo (!empty($message_err)) ? 'is-invalid' : ''; ?>" value="" placeholder='message'>
    <input type="submit" class="loginbuttons" value="Send" style="height:30px;width:60px;">
    <br><span class="pustepola"><?php echo $message_err; ?></span>
    </form><br></div>
    <script>
       window.scrollTo(0, document.body.scrollHeight); 
    </script>
</body>
</html>
    <?php
        }else{
            echo "Please login into your account :) ";
        }
    ?>

    
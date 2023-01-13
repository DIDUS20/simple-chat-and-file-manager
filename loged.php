<?php

    require_once "config.php";
    session_start();

    // Session control
    if($_SESSION['loggedin'] == false){
        header("location: index.php");
    }

    // Define variables
    $htmlspecialchars = htmlspecialchars($_SERVER['PHP_SELF']);
    $path = $_SESSION['path'];
    $main_dir = realpath($_SERVER["DOCUMENT_ROOT"]);
    $username = $_SESSION['username'];
    $user_id = $_SESSION['id'];
    $new_dir_err = $copy_err = $delete_err = $new_message_err = "";
    $storage_path = "users/$username/storage";
    $storage_path_str = str_replace("users/$username/storage","~",$storage_path);
    $path_str = str_replace("users/$username/storage","~",$path);
    $opened = false;

    // Images
    $icon_path = "icon.ico";
    $profile = "users/$username/av.jpg";
    $background_loged = "logedbg.jpg";
    $background_signup = "";

    // File size
        function file_size_in_bytes($file){
            $size = filesize("$file");
            if($size > 1000 && $size < 1000000){
                $size = round((float)$size/1000)." KB"; 
            }else if($size > 1000000 && $size < 1000000000){
                $size = round((float)$size/1000000)." MB";
            }else if($size > 1000000000){
                $size = round((float)$size/1000000000)." GB";
            }else{
                $size = $size." B";
            }
            return $size;
        }
    //
    // Download file
        if(isset($_GET['download'])){
            $url = $_GET['download'];

            // Clear cache
            clearstatcache();

            // Check the file path exists or not
            if(file_exists($url)) {

                // Define header information
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($url).'"');
                header('Content-Length: '.filesize($url));
                header('Pragma: public');
                // Clear system output buffer
                flush();
                // Read the size of the file
                readfile($url,true);
                // Terminate from the script
                die();
                
            }else{
                echo "File path does not exist.";
            }
            unset($_GET['download']);
        }
    //
    // Logout
        if(isset($_GET['logout'])){
            $username = $_GET['logout'];
            session_destroy();
            header('location: index.php');
        }
    //
    // Nav in folders
        if(isset($_GET['open'])){
            $_SESSION['path'] = $_GET['open'];
            $path = $_SESSION['path'];
            header("location: loged.php");
        }
        if(isset($_GET['back'])){
            $_SESSION['path'] = dirname($path);
            $path = $_SESSION['path'];
            header("location: loged.php");
        }
    //
    // New folder
        if(isset($_GET['new_dir'])){ 
                echo "
                <div class='new_folder'>
                    <form method='post'>
                    <input type='text' class='input_text' name='new_folder_name' $new_dir_err Placeholder='New folder name'><br><hr><input type='submit' class='input' name='create_folder' value='New folder'>
                    <br>$new_dir_err 
                    </form>
                </div>
                ";
        }
        if(isset($_POST['new_folder_name'])){
            if(empty(trim($_POST['new_folder_name']))){
                $new_dir_err = "Name new folder.";
            }else if(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["new_folder_name"]))){
                $new_dir_err = "Change new folder name.";
            }else if(str_contains($_POST["new_folder_name"],".")){
                $new_dir_err = "Folder name can't have dots.";
            }else if(is_dir($path."/".$_POST["new_folder_name"])){
                $new_dir_err = "Folder already exist.";
            }else if(empty($new_dir_err)){
                mkdir($path."/".$_POST["new_folder_name"]);
                header("location: loged.php");
            }
            
        }
    //
    // Delete
        if(isset($_POST['delete'])){
            if(isset($_POST["check"])){
                foreach($_POST["check"] as $f){
                    if(is_file($f)){
                        unlink($f);
                    }else if(is_dir($f)){
                        rrmdir($f);
                    } 
                }
            }else{
                $delete_err = "Chose files";
            }
        }
        // Delete not empty directory
        function rrmdir($file){
            $objects = scandir($file);
            foreach ($objects as $object){
                if($object != '.' && $object != '..'){
                    if(filetype($file.'/'.$object) == 'dir'){
                        rrmdir($file.'/'.$object);
                    }else{
                        unlink($file.'/'.$object);
                    }
                }
            }
            reset($objects);
            rmdir($file);  
        }
    //
    // Copy
        if(isset($_POST['copy'])){
            if(isset($_POST['check'])){
                $_SESSION['files'] = $_POST["check"];
            ?>
                <div class='copy_files'>
                    <form method='post'>
                        Source folder: <?php echo $path_str; ?>/ <br>
                        Destination folder: <?php echo $storage_path_str; ?>/ <input class='input_text' type='text' <?php echo $copy_err ?> name='dest_path' Placeholder='path'><br><?php echo $copy_err ?>
                        <input type='submit' value='Copy' name='Copy_files'><br>
                    </form>
                </div>
            <?php  
            }else{
                $copy_err = "Chose files.";
            }
               
        }
        if(isset($_POST['Copy_files'])){
            if(!empty(trim($_POST['dest_path']))){
                // When dest is not storage_path
                if(is_dir($storage_path."/".$_POST['dest_path'])){
                    $target_path = $storage_path."/".$_POST['dest_path'];
                    $main_dir = str_replace("\\","/",$main_dir)."/example"."/";
                    foreach($_SESSION['files'] as $f){
                        if(is_file($f) && !is_file($main_dir.$target_path."/".basename($f))){
                            copy($main_dir.$f,$main_dir.$target_path."/".basename($f));
                        }else if(is_dir($f) && !is_dir($main_dir.$target_path."/".basename($f))){
                            dircopy($main_dir.$f,$main_dir.$target_path);  
                        }
                    }
                    $_SESSION['files'] = "";
                    header("location: loged.php");
                }else{
                    $copy_err = "There is no destination folder";
                }
            }else{
                // When dest is storage_path
                if(is_dir($storage_path."/".$_POST['dest_path'])){
                    $main_dir = str_replace("\\","/",$main_dir)."/example";
                    foreach($_SESSION['files'] as $f){
                        if(is_file($f)){
                            copy($main_dir."/".$f,$main_dir."/".$storage_path."/".basename($f));
                        }else if(is_dir($f)){
                            dircopy($main_dir."/".$f,$main_dir."/".$storage_path);
                        }
                    }
                    $_SESSION['files'] = "";
                }else{
                    $copy_err = "There is no destination folder";
                }
            }    
        }  
        function dircopy ($source, $dest,$permissions = 0755){
                // Folder Copy
            if(is_dir($source)){
                if(!is_dir($dest."/".basename($source))){
                    mkdir($dest."/".basename($source));
                    foreach(scandir($source) as $f){
                        if($f != '.' || $f != '..'){
                            dircopy("$source/$f", "$dest/".basename($source));
                        }
                    }
                }  
                // Based Copy
            }else if(is_file($source)){
                copy($source,$dest."/".basename($source));
            }
            
        }
    //
    // Upload
        if(isset($_GET['upload'])){
            ?>
            <div class='upload_files'>
                <form action='' method="post" enctype="multipart/form-data">
                        <input type="file" name="upfile[]" multiple required>
                        <input name='Upload_files' type="submit" value="Upload">
                </form>
            </div>
            <?php
        }
        if(isset($_POST['Upload_files'])){

            foreach($_FILES["upfile"]['error'] as $key => $error){
                if($error==UPLOAD_ERR_OK){
                    $tmp_name=$_FILES["upfile"]["tmp_name"][$key];
                    $name=basename($_FILES["upfile"]["name"][$key]);
                    move_uploaded_file($tmp_name,"$path/".$name);
                }
            }

            header("location: loged.php");
        }
    //
    // Chat
        if(isset($_GET['chat'])){
            // Send values to chat window
            
            $_SESSION['reciver'] = $_GET['chat'];
            echo "<script> window.open('chat.php','chat',config='height=400,width=700,toolbar=no,menubar=no,scrollbars=no,resizable=no,location=no,directories=no, status=no');</script>";
            
        }
    // 
    // New chat
        if(isset($_POST['create_chat'])){
            if(!empty(trim(isset($_POST['new_message_reciver']))) && $_POST['new_message_reciver'] != $username){
                $reciver = $_POST['new_message_reciver'];
                
                $mysql_users = mysqli_query($link,"SELECT username,user_id from users Where username = '$reciver'");
                $reciver_exist = mysqli_fetch_array($mysql_users);

                if(!is_null($reciver_exist)){
                    $reciver_id = $reciver_exist['user_id'];
                }else{
                    $new_message_err = "error";
                    $reciver_id = 0;
                }
                

                $mysql_chats = mysqli_query($link,"SELECT COUNT(`chat_id`) as 'how_mach' from chats Where (user1 = $reciver_id AND user2 = $user_id) OR (user2 = $reciver_id AND user1 = $user_id)");
                $chat_exist = mysqli_fetch_array($mysql_chats);
                
                if($chat_exist['how_mach'] == 0){
                    $chat_not_exist = true;
                }else{
                    $chat_not_exist = false;
                }
                
                if(!empty($reciver_exist['username']) && $chat_not_exist == true){
                    // Create new chat
                    $sql = "INSERT INTO chats (`user1`, `user2`, `text`) VALUES (?, ?, ?)";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, "sss", $param_user1, $param_user2, $param_text);
                    $param_user1 = $reciver_exist['user_id'];
                    $param_user2 = $_SESSION['id'];
                    if(!empty($_POST['new_message'])){
                        $param_text = "|<h4>$username:".$_POST['new_message']."</h4>";   
                    }else{
                        $param_text = "";
                    }
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }else if($chat_not_exist == false){
                    $new_message_err = "Chat already exist.";
                }else{
                    $new_message_err = "No Reciver.";
                }
            }else if($_POST['new_message_reciver'] == $username){
                $new_message_err = "Can't send message to yourself.";
            }
        }
    //
    // Settings
        if(isset($_GET['settings'])){
            // Features 
        }
    //

    if($_SESSION['loggedin'] == true && session_status() == 2){
?>
<html>
    <head>
        <title><?php echo $title_loged; ?></title>
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
        <body background='<?php echo "$background_loged"; ?>'>
            
            <div class='loged_header'>
                <a href='loged.php?logout=' ><input class='input_header' type='button' name='logout' value='Logout'></a>
                <a href='loged.php?settings=' ><input class='input_header' type='button' name='settings' value='Settings'></a>
                <?php echo "<span class='username_header'>$username</span>"; ?>
            </div>

            <div class='loged_chats'>
                <?php
                // Create new chat
                ?>
                    <div class='new_chat'>
                        <form method='post'>
                            <input type='text' class='input_text' name='new_message_reciver' $new_message_err Placeholder='Reciver'>
                            <input type='text' class='input_text' name='new_message' $new_message_err Placeholder='Message'>
                            <input type='submit' class='input' name='create_chat' value='New chat'><br><?php echo $new_message_err; ?><hr>
                            <br>
                        </form>
                    </div>
                <?php
                // Chats
                    function username($link,$id){
                        $a = mysqli_query($link,"SELECT username from users Where user_id = '$id'");
                        $user = mysqli_fetch_array($a);
                        
                        if(!is_null($user)){
                            $username = $user['username']; 
                            return $username;
                        }else{
                            echo "Error";
                        }
                    }
                    
                    $how_many_sql = mysqli_query($link,"SELECT COUNT(`chat_id`) as 'how_many',`chat_id` from chats Where user2 = $user_id OR user1 = $user_id");
                    $how_many = mysqli_fetch_array($how_many_sql)['how_many'];
                    $chatinfo_sql = mysqli_query($link,"SELECT `chat_id`,`user1`,`user2`,`text` from chats Where user2 = '$user_id' OR user1 = '$user_id'");
                    

                    if($how_many > 0){
                        for($i=0;$i<$how_many;$i++){
                            $chatinfo = mysqli_fetch_array($chatinfo_sql);
                            $u1 = $chatinfo['user1'];
                            $u2 = $chatinfo['user2'];
                            if($u1 == $user_id){
                                $reciver_id = $u2;
                            }else{
                                $reciver_id = $u1;
                            }
                            $reciver = username($link,$reciver_id);
                            echo "
                                <a href='loged.php?chat=$reciver_id'><input class='chat' type='button' Value='$reciver'></a><br><br>
                            ";
                        }  
                    }else{
                        echo "No chats";
                    }

                    
                
                ?>
            </div>
            
            <div class='file_manager'>
                <form action='<?php echo $htmlspecialchars ?>' method='post'>
                    <h1>File manager</h1>
                    <h4><?php  echo "PATH: $path_str" ?></h4>
                    <table>
                        <tr><th>#</th><th>Name</th><th>Size</th></tr>
                    <?php 
                        $tabfiles = scandir($path);
                        foreach($tabfiles as $file){
                            if($file != '.'){
                                if(is_file("$path/$file")){
                                    $size = file_size_in_bytes("$path/$file");
                                    echo "<tr><td><input type='checkbox' name='check[]' value='$path/$file'></td><td>$file</td><td>$size</td><td><a href='loged.php?download=$path/$file'><input type='button' name='download' value='V'></a></td></tr>";     
                                }else if(is_dir("$path/$file") && $file != '..'){
                                    $size = file_size_in_bytes("$path/$file");
                                    echo "<tr><td><input type='checkbox' name='check[]' value='$path/$file'></td><td>/$file</td><td>$size</td><td><a href='loged.php?open=$path/$file'><input type='button' name='open' value='->'></a></td></tr>";  
                                }else if(is_dir("$path/$file") && $file == '..' && $path != "users/$username/storage"){
                                    echo "<tr><td><a href='loged.php?back=$storage_path'><input type='button' name='back' value='<-'></a></td><td>..</td><td>-</td></tr>";
                                }
                            }
                        }
                    ?>
                    </table>
                    <div>
                        <hr>
                        <input type='submit' name='refresh' value='Refresh'>
                        <input type='submit' name='delete' <?php $delete_err ?> value='Delete'>
                        <input type='submit' name='copy' value='Copy'><br>
                        <?php echo $copy_err ?>
                        <?php echo $delete_err ?>
                        <br><br>
                        <a href='loged.php?new_dir='><input type='button' name='new_dir' value='New folder'></a>
                        <a href='loged.php?upload=' ><input type='button' name='upload' value='Upload'></a><br>
                    </div>
                </form>
            </div>
            
            
        </body>
</html>
<?php
    }else{
        header("location: index.php");
    }
?>
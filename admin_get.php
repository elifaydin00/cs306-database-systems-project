<?php
    // Initialize the session
    session_start();
    
    // Check if the user is logged in, if not then redirect him to login page
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: login.php");
        exit;
    }
    require_once "config.php";
    $usn= $_SESSION['username'] ;
    $chat_id=$_SESSION["id"];

    $URL = "https://trial-support-default-rtdb.europe-west1.firebasedatabase.app//Chats.json";

    header("refresh: 30");

    function get_messages() { 
        global $URL;
        $ch = curl_init();
        curl_setopt_array($ch, [ CURLOPT_URL => $URL,
                                CURLOPT_POST => FALSE, // It will be a get request 
                                CURLOPT_RETURNTRANSFER => true, ]);
        $response = curl_exec($ch); 
        curl_close($ch);
        return json_decode($response, true); 
    }
    
    

    $msg_res_json = get_messages();


?>


<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="styles.css">
</head>

<div class="menu">
<div class="back"><i class="fa fa-chevron-left"></i> <img src="https://i.imgur.com/DY6gND0.png" draggable="false"/></div>
<div class="name">Support</div>
<div class="last">All Messages</div>
</div>
<ol class="chat">
<?php
    $keys = ! empty($msg_res_json) ? array_keys($msg_res_json) : [];
    for ($i = 0; $i < count($keys); $i++){
        $chat_msg = $msg_res_json[$keys[$i]];  
        $name= $chat_msg['name']; 
        if ($name != 'admin') {    
        $id = $chat_msg['id'];
        $msg = $chat_msg['msg'];
        $issue = $chat_msg['issue'];
        
        $from = 'other';
                
        echo  
        '<li class="'.$from.'">
                <div class="msg">
                        <p><b>'.$id.'</b></p>
                        <p>'.$msg.'</p>
                        <time>'.$issue.'</time>
                </div>
            </li>';
        }
    }
?>
</ol>

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
    
    function send_msg($msg, $name,$id,$issue) { 
        global $URL;
        $ch = curl_init();
        $msg_json = new stdClass();
        $msg_json->id = $id;
        $msg_json->msg = $msg;
        $msg_json->name = $name;
        $msg_json->issue = $issue;
        $msg_json->time = date('H:i');
        $encoded_json_obj = json_encode($msg_json); 
        curl_setopt_array($ch, array(CURLOPT_URL => $URL,
                                    CURLOPT_POST => TRUE,
                                    CURLOPT_RETURNTRANSFER => TRUE,
                                    CURLOPT_HTTPHEADER => array('Content-Type: application/json' ),
                                    CURLOPT_POSTFIELDS => $encoded_json_obj ));
        $response = curl_exec($ch); 
        return $response;
    }

    $msg_res_json = get_messages();

    if ((isset($_POST['usermsg']) && isset($_POST['issue']))) {
        $user_msg = $_POST['usermsg'];
        $issue = $_POST['issue'];
        send_msg($user_msg, $usn,$chat_id,$issue);
        echo $user_msg;
    }

?>


<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="styles.css">
</head>

<div class="menu">
<div class="back"><i class="fa fa-chevron-left"></i> <img src="https://i.imgur.com/DY6gND0.png" draggable="false"/></div>
<div class="name">Support</div>
<div class="last">User-Admin Chat</div>
</div>
<ol class="chat">
<?php
    $keys = ! empty($msg_res_json) ? array_keys($msg_res_json) : [];
    for ($i = 0; $i < count($keys); $i++){
        $chat_msg = $msg_res_json[$keys[$i]];
        if($chat_id==$chat_msg['id']){
        $name = $chat_msg['name'];
        $msg = $chat_msg['msg'];
        $time = $chat_msg['time'];
        if ($name == 'admin') {
            $from = 'other';
        } else {
            $from = 'self';
        }
       echo  
       '<li class="'.$from.'">
            <div class="msg">
                    <p><b>'.$name.'</b></p>
                    <p>'.$msg.'</p>
                    <time>'.$time.'</time>
            </div>
        </li>';
    }
    }
?>
</ol>
<form name="form" action = "chats.php" method="POST">
    <input name="usermsg" class="textarea" type="text" placeholder="Type here your message"/>
    <label for="issue">Issue</label>
      <select name="issue" id="issue">
        <option value="bug">Bug Detected</option>
        <option value="feedback">Feedback</option>
        <option value="help">Help</option>
      </select>
    <input type="submit" style="display: none" />
</form>
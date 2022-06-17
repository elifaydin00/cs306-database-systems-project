<?php
session_start();
    
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
require_once "config.php";
$usn= $_SESSION['username'] ;

$URL = "https://trial-support-default-rtdb.europe-west1.firebasedatabase.app//Chats.json";

function send_msg($id,$admin_msg,$name) { 
    global $URL;
    $ch = curl_init();
    $msg_json = new stdClass();
    $msg_json->id = $id;
    $msg_json->msg = $admin_msg;
    $msg_json->name = $name;

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

if ((isset($_POST['id']) && isset($_POST['msg']))) {
    $id = $_POST['id'];
    $admin_msg = $_POST['msg'];
    send_msg($id,$admin_msg,$usn);
}
?>


<!DOCTYPE html>
<html>
<head>
    <style>
        #footer {
            position: fixed;
            padding: 10px 10px 0px 10px;
            bottom: 0;
            width: 100%;
            /* Height of the footer*/ 
            height: 40px;
            background: grey;
        }
    </style>
	<title>Admin Reply</title>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Sans&display=swap" rel="stylesheet">
</head>
<body>

<form name="form" action = "admin_reply.php" method="POST">
	ID: <input type="text" name="id" /><br />
	Message: <input type="text" name="msg"/><br />

	<input type ="submit"  name="edit" value="Send" class="btn btn-primary">
</form>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
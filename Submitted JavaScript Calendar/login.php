<?php
// login_ajax.php
header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json
ini_set("session.cookie_httponly", 1);
require 'database.php';
//Because you are posting the data via fetch(), php has to retrieve it elsewhere.
$json_str = file_get_contents('php://input');
//This will store the data into an associative array
$json_obj = json_decode($json_str, true);

//Variables can be accessed as such:

$username = $json_obj['username'];
$password = $json_obj['password'];


//This is equivalent to what you previously did with $_POST['username'] and $_POST['password']
$stmt = $mysqli->prepare("SELECT user_password FROM users WHERE user_name=?");
if(!$stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
}

$stmt->bind_param('s',$username);
$stmt->execute();
$stmt->bind_result($hashedpswd);
$stmt->fetch();
$stmt->close();


//$hashedpswd = "$2y$10\$b9TN9pd2zlKVTcBTEt6X.e9BgEpjEo0NoJU96n24Qf8";

if(password_verify($password, $hashedpswd)){
        session_start();
        $_SESSION['user'] = $username;
        //_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32)); 

	echo json_encode(array(
		"success" => true
	));
}else{
	echo json_encode(array(
		"success" => false,
		"message" => htmlentities($hashedpswd)
	));
}
?>
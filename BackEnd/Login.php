<?php
include_once('confi.php');
/* 
	DB Type:
	Name: account
	Properties:
		- ID
		- name
		- password
		- token
		- tokenExpiration
*/
if($_SERVER['REQUEST_METHOD'] == "GET"){
	//Get Data
	$username = isset($_GET['username']) ? mysql_real_escape_string($_GET['username']) : "";
	$password = isset($_GET['password']) ? mysql_real_escape_string($_GET['password']) : "";

	$qur = mysql_query("SELECT ID, token FROM `$serverName`.`account` WHERE username='$username' AND password='$password'");
	//$json = array("qur" => mysql_fetch_array($qur));
	if ($qur != false){
		$result =array();
		while ($r = mysql_fetch_array($qur)){
			extract($r);
			$result = array(
				"ID" => $ID, 
				"token" => $token
			);
		}
	}
	if ($result){
		$token = md5(uniqid(mt_rand(), true));
		$id = $result['ID'];
		$qur = mysql_query("UPDATE `$serverName`.`account` SET `token` = '$token' WHERE  `account`.`ID` ='$id'");
		$json = array("status" => 1, "msg" => "Login success", "ID" => $id, 'token' => $token);
	} else {
		header('HTTP/1.1 403');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'ERROR', 'code' => 403)));
	}

	//expiring token code
	//$dtDb = DateTime::createFromFormat('Y-m-d H:i:s', $result['expire']);
	//$dtNow = date_create();

	//$dtDBFormat = $dtDb->format('Y-m-d H:i:s');
	//$dtNowFormat = $dtNow->format('Y-m-d H:i:s');

	//if ($dtNowFormat > $dtDBFormat){
		//set new token
		//date_add($dtNow, date_interval_create_from_date_string('24 hours'));
		//$json = array("result" => $dtNow);
	//}
} elseif ($_SERVER['REQUEST_METHOD'] == "POST"){
	//required
	$name = isset($_POST['name']) ? mysql_real_escape_string($_POST['name']) : "";
	$password = isset($_POST['password']) ? mysql_real_escape_string($_POST['password']) : "";
	$confirmPW = isset($_POST['confirmPW']) ? mysql_real_escape_string($_POST['confirmPW']) : "";

	//error var;
	$error = '';

	//pass error is pw is not same
	if ($password != $confirmPW){
		$error ="Passwords do not match";
	}
	if ($error == ''){
		$token = md5(uniqid(mt_rand(), true));
		$registerDate = date_create()->format('Y-m-d H:i:s');

		$sql = "INSERT INTO `$serverName`.`account` (`ID`, `name`, `password`, `registerDate`, `token` ) VALUES (NULL, '$name', '$email', '$password', '$goalWeight', '$initWeight', '', '$registerDate', '$token');";
		$qur = mysql_query($sql);

		$qur = mysql_query("SELECT ID, token FROM `account` WHERE name='$name' AND password='$password'");

		$result =array();
		while ($r = mysql_fetch_array($qur)){
			extract($r);
			$result = array(
				"ID" => $ID, 
				"token" => $token
			);
		}

		if ($result['ID']){
			$json = array("status" => 1, "msg" => "Login success", "ID" => $result['ID'], 'token' => $token, 'name' => $name);
		} else {
			$json = array("status" => 0, "msg" => "There was an issue with your request");
		}
	} else {
		$json = array("status" => 0, "msg" => $error);
	}
} elseif ($_SERVER['REQUEST_METHOD'] == "PUT"){
	parse_str(file_get_contents("php://input"),$post_vars);
	$token = isset($post_vars['token']) ? mysql_real_escape_string($post_vars['token']) : "";
	$authqur = "SELECT ID FROM `$serverName`.`account` WHERE token='$token'";
	$authResult = mysql_query($authqur);
	$authArrayresult =array();
	while($r = mysql_fetch_array($authResult)){
		extract($r);
		$authArrayresult[] = array('id' => $ID);
	}
	if (sizeof($authArrayresult) != 0 ){
		$name = isset($post_vars['name']) ? mysql_real_escape_string($post_vars['name']) : "";
		$password = isset($post_vars['password']) ? mysql_real_escape_string($post_vars['password']) : "";
		$confirmPW = isset($post_vars['confirmPW']) ? mysql_real_escape_string($post_vars['confirmPW']) : "";

		//error var;
		$error = '';
	
		//pass error is pw is not same
		if ($password != $confirmPW){
			$error ="Passwords do not match";
		}
		if ($error == ''){
			$sql = "UPDATE `$serverName`.`account` SET `username` = '$name', 
													`password` = '$password' WHERE `account`.`token` = '$token'";
			$qur = mysql_query($sql);
			if($qur){
				$json = array("status" => 1, "msg" => "Account Updated!");
			}else{
				header('HTTP/1.1 500');
				header('Content-Type: application/json; charset=UTF-8');
				die(json_encode(array('message' => 'ERROR: Internal error', 'code' => 500)));
			}

		} else {
			header('HTTP/1.1 400');
			header('Content-Type: application/json; charset=UTF-8');
			die(json_encode(array('message' => 'ERROR: $error', 'code' => 400)));
		}


	} else {
		header('HTTP/1.1 403');
		header('Content-Type: application/json; charset=UTF-8');
		die(json_encode(array('message' => 'ERROR', 'code' => 403)));
	}
} else {

	$json = array("status" => 0, "msg" => "Request method not accepted");
}

@mysql_close($conn);

/* Output header */
	header('Content-type: application/json');
	echo json_encode($json);
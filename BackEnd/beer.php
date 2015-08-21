<?php
	// Include confi.php
	include_once('confi.php');
  	//`ID` int(11) NOT NULL AUTO_INCREMENT,
  	//`name` varchar(150) NOT NULL,
  	//`style` varchar(150) NOT NULL,
  	//`abv` double NOT NULL,
  	//`description` varchar(150) NOT NULL,
  	//`brewery` varchar(150) NOT NULL,
  	//`location` varchar(150) NOT NULL,

	if($_SERVER['REQUEST_METHOD'] == "GET"){
		$qur = mysql_query("SELECT ID, name, style, abv, brewery, location FROM `$serverName`.`beer`");
		if($qur === FALSE) {
   			die(mysql_error()); // TODO: better error handling
		}
		$result =array();
		while($r = mysql_fetch_array($qur)){
			extract($r);
			$result[] = array("ID" => $ID,
							  "name" => $name, 
							  "style" => $style, 
							  "abv" => $abv,
							  "brewery" => $brewery,
							  "location" => $location); 
		}
		if ( count( $result ) != 0 ){
			$json = array("status" => 200, "results" => $result);
		} else {
			$json = array("status" => 200, "results" => $result);
		}
	} else if ($_SERVER['REQUEST_METHOD'] == "POST"){
		//Get Data
		//$id = isset($_GET['id']) ? mysql_real_escape_string($_GET['id']) : "";
		//if ($id != null and $id != ''){
		$token = isset($_POST['token']) ? mysql_real_escape_string($_POST['token']) : "";
		$authqur = "SELECT ID FROM `$serverName`.`account` WHERE token='$token'";
		$authResult = mysql_query($authqur);
		$authArrayresult =array();
		while($r = mysql_fetch_array($authResult)){
			extract($r);
			$authArrayresult[] = array('id' => $ID);
		}
		if (sizeof($authArrayresult) != 0 ){
			$name = isset($_POST['name']) ? mysql_real_escape_string($_POST['name']) : "";
			$style = isset($_POST['style']) ? mysql_real_escape_string($_POST['style']) : "";
			$abv = isset($_POST['abv']) ? mysql_real_escape_string($_POST['abv']) : "";
			$brewery = isset($_POST["brewery"]) ? mysql_real_escape_string($_POST["brewery"]) : "";
			$location = isset($_POST["location"]) ? mysql_real_escape_string($_POST["location"]) : "";

			// Insert data into data base
			$sql = "INSERT INTO `$serverName`.`beer` (`ID`, `name`, `style`, `abv`, `brewery`, `location`) VALUES (	NULL, '$name', '$style', '$abv', '$brewery', '$location');";
			$qur = mysql_query($sql);
			$newId =  mysql_insert_id();
	
			if($qur){
				$json = array("status" => 1, "msg" => "Beer Added!", "id" => $newId);
			}else{
				$json = array("status" => 0, "msg" => "Error beer not added!");
			}
		} else {
			header('HTTP/1.1 403');
        	header('Content-Type: application/json; charset=UTF-8');
        	die(json_encode(array('message' => 'ERROR', 'code' => 403)));
		}
	} else if ($_SERVER['REQUEST_METHOD'] == "PUT"){
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
			$uid = isset($post_vars['id']) ? mysql_real_escape_string($post_vars['id']) : "";
			$name = isset($post_vars['name']) ? mysql_real_escape_string($post_vars['name']) : "";
			$style = isset($post_vars['style']) ? mysql_real_escape_string($post_vars['style']) : "";
			$abv = isset($post_vars['abv']) ? mysql_real_escape_string($post_vars['abv']) : "";
			$brewery = isset($post_vars["brewery"]) ? mysql_real_escape_string($post_vars["brewery"]) : "";
			$location = isset($post_vars["location"]) ? mysql_real_escape_string($post_vars["location"]) : "";

			$sql = "UPDATE `$serverName`.`beer` SET `name` = '$name', 
													`style` = '$style',
													`abv` = '$abv',
													`brewery` = '$brewery', 
													`location` = '$location' WHERE `beer`.`ID` = '$uid'";
			$qur = mysql_query($sql);
			if($qur){
				$json = array("status" => 1, "msg" => "Beer Updated!", "id" => $uid);
			}else{
				header('HTTP/1.1 500');
				header('Content-Type: application/json; charset=UTF-8');
				die(json_encode(array('message' => 'ERROR: Internal error', 'code' => 500)));
			}
		} else {
			header('HTTP/1.1 403');
			header('Content-Type: application/json; charset=UTF-8');
			die(json_encode(array('message' => 'ERROR', 'code' => 403, 'token' => $token)));
		}

	} else if($_SERVER['REQUEST_METHOD'] == "DELETE"){
		$token = isset($_GET['token']) ? mysql_real_escape_string($_GET['token']) : "";
		$authqur = "SELECT ID FROM `$serverName`.`account` WHERE token='$token'";
		$authResult = mysql_query($authqur);
		$authArrayresult =array();
		while($r = mysql_fetch_array($authResult)){
			extract($r);
			$authArrayresult[] = array('id' => $ID);
		}
		if (sizeof($authArrayresult) != 0 ){

			$id = isset($_GET['id']) ? mysql_real_escape_string($_GET['id']) : "";
			$json = array("status" => 1, "id" => $id);
	
			$sql = "DELETE FROM `$serverName`.`beer` WHERE ID='$id'";
			$qur = mysql_query($sql);
			if ($qur){
				$json = array("status" => 1, "msg" => "Beer Deleted!", 'id' => $id);
			} else {
				$json = array("status" => 0, "msg" => "Error deleting that beer!");
			}
		} else {
			header('HTTP/1.1 403');
        	header('Content-Type: application/json; charset=UTF-8');
        	die(json_encode(array('message' => 'ERROR', 'code' => 403)));
		}
	} else {
		header('HTTP/1.1 403');
        header('Content-Type: application/json; charset=UTF-8');
		die(json_encode(array("code" => 404, "msg" => "BAD REQUEST")));
	}
	@mysql_close($conn);

	/* Output header */
	header('Content-type: application/json');
	echo json_encode($json);
<?php

//production
$serverName = 'borectha_beer_listing';
$login = '';
$pw = '';

//test
//$serverName = 'beer_listing';
//$login = 'root';
//$pw = '';

$conn = mysql_connect("localhost", $login, $pw);
mysql_select_db($serverName, $conn);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
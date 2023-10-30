<?php

$dbhost = "localhost";
$dbname = "tovarnyj_znak_ru";
$username = "tovarnyj_znak_ru";
$password = "tovarnyj_znak_ru";

$db = new PDO("mysql:host=$dbhost; dbname=$dbname", $username, $password);

function get_data() {
	global $db;
	$sql = "SELECT * FROM mktu_grouped_categories";
	$data = $db->query($sql)->fetchAll();
	return $data;
}
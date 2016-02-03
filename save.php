<?php
#   uloz a presmeruj na dalsie
session_start();
 
if (isset($_POST)) {
	var_dump($_POST);
	if (empty($_GET['noun']) or empty($_GET['verb'])) {
		header('Location:index.php');
		die();
	}
	
	$verb = $_GET['verb'];
	$noun =  $_GET['noun'];
	
	$filename = "results.json";
	$file = file_get_contents($filename, FILE_USE_INCLUDE_PATH);
	$data = json_decode($file, true);
	$data[$verb][$noun] = $_POST;
	file_put_contents($filename, json_encode($data), LOCK_EX) or print("File writing failed.");
	
	header("Location:index.php?noun={$noun}&verb={$verb}");
	die();
} else {
	print "chyba";
	header("Location:indexa");
	die();
}

?>


<?php
#   uloz a presmeruj na dalsie
session_start();
 
if (isset($_POST)) {
   var_dump($_SESSION);
   var_dump($POST);
   $filename = "results.json";

   $file = file_get_contents($filename, FILE_USE_INCLUDE_PATH);
   $data = json_decode($file, true);
   $data[$_SESSION['verb']][$_SESSION['noun']] = $_POST;
   file_put_contents($filename, json_encode($data), LOCK_EX) or print("File writing failed.");
 

 #  $MAXSIZE = count(glob($_SESSION['type']."/*.php", GLOB_BRACE));
 #  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
 #        $_SESSION['id'] = min($_SESSION['id']+1,$MAXSIZE);
 
  header('Location:index.php');
  die();
}

?>


<?php

$message = file_get_contents("php://input");

//$fields = urldecode($message);

$my_file = './file.txt';
$handle = fopen($my_file, 'a') or die('Cannot open file:  '.$my_file);
fwrite($handle, $message . "\n");
fclose($handle);

?>
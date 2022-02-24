<?php

include './filter.php';

$message = file_get_contents("php://input");

$my_file = './file.txt';
$handle = fopen($my_file, 'a') or die('Cannot open file:  '.$my_file);

$ticket = processJsonTicket($message);
if($ticket !== NULL){
	fwrite($handle, $message . "\n");
}

fclose($handle);

?>
<?php

include './filter.php';

function readTicketsFile(){

	$tickets = [];
	
	$my_file = './file.txt';
	
	$handle = @fopen($my_file, 'r');
	
	if($handle == false) return;

	while (($line = fgets($handle)) !== false) {
		$ticket = processJsonTicket($line);
		if($ticket !== NULL){
			array_push($tickets, $ticket);
		}
	}

	fclose($handle);
	
	header('Content-Type: application/json');
	// Add new lines to avoid memory limits, that handles the buffer output according to new line characters
	$tickets = str_replace( '}},"', '}},' . PHP_EOL . '"', $tickets );
	echo json_encode($tickets);
}

readTicketsFile();

?>
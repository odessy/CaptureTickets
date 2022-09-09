<?php

include './filter.php';

function readTicketsFile(){

	$tickets = [];
	
	$my_file = './file.txt';
	
	$handle = @fopen($my_file, 'r');
	
	if($handle == false) return;

	$count = 0;
	while (($line = fgets($handle)) !== false) {
		$ticket = json_decode($line, true);
		if($ticket !== NULL){
			$ticketID = $ticket["url"];
			if($count == 0 || $ticketID !== $tickets[$count - 1]["url"]){
			  array_push($tickets, $ticket);
			  $count++;
			} else {
				//replace old ticket with same URL with updated content
				$tickets[$count - 1] = $ticket;
			}
			$oldTicketID = $ticketID;
		}
	}
	fclose($handle);
	
	//Write updated updated_file.txt to reduce file size
	//$handle = @fopen('./updated_file.txt', 'w');
	//foreach($tickets as $ticket){
	//	fwrite($handle, json_encode($ticket) . "\n");
	//}
	//fclose($handle);
	
	header('Content-Type: application/json');
	// Add new lines to avoid memory limits, that handles the buffer output according to new line characters
	//$tickets = str_replace( '}},"', '}},' . PHP_EOL . '"', $tickets );
	echo json_encode($tickets, JSON_PRETTY_PRINT);
}

readTicketsFile();

?>
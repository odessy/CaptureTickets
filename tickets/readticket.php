<?php

/*
* Output a file containing all tickets that got filtered
*/

define('OUTPUT_CLEAN_FILE', false);

/*
* contain the filters
*/
$filters = [];
$tickets = [];
//read tickets file

function match_wildcard( $wildcard_pattern, $haystack ) {
   $regex = str_replace(
     array("\*", "\?", "\d"), // wildcard chars
     array('.*','.', 'd'),   // regexp chars
     preg_quote($wildcard_pattern, '/')
   );
   
   //echo $regex."<br>";
   return preg_match('/^'.$regex.'$/', $haystack);
}


//match_wildcard($parts[1], $json->$p );

function processJsonTicket($line){
	global $filters;
	global $tickets;
	
	$json = json_decode($line);
	
	foreach ($filters as $key=>$filter) {
		
		$parts = explode('=', $filter[0]);
	
		if(sizeof($parts) > 1){
			
			$p =  $parts[0];
			
			if( $json && !match_wildcard($parts[1], $json->{$p} )) continue;
			
			$ticket = array();

			$keyValid = true;
			foreach ($filter as $key) {
				$checkKey = false;
				$parts = explode('=', $key);
				if(sizeof($parts) > 1){
					$key = $parts[0];
					$checkKey = true;
				}
			
				$keys = explode("->", $key);
				$obj = $json;
				
				$e = 1;
				foreach($keys as $s)
				{
					if (is_array($obj) && isset($obj[$s]))
					{
						if (end($keys) == $s && $e == sizeof($keys) )
						{
							$ticket[$s] = $obj[$s];
						}
						$obj = $obj[$s];
						$e++;
					}
					else if (isset($obj->{$s}))
					{
						if (end($keys) == $s && $e == sizeof($keys) )
						{
							$ticket[$s] = $obj->{$s};
						}
						$obj = $obj->{$s};
						$e++;
					}
				}
				
				if($checkKey) {
					if(isset($ticket[$s])){
						if(is_bool($ticket[$s]) && (stringToBool($parts[1]) == $ticket[$s]) == false  ){
							$keyValid = false;
							break;
						} else	if(!is_bool($ticket[$s]) && !match_wildcard($parts[1], $ticket[$s] )){
							$keyValid = false;
							break;
						}
					}
					else{
						$keyValid = false;
						break;
					}
				}
			}

			if($keyValid == true) {
				array_push($tickets, $ticket);
				
				return true;
			}
		}
	}
	
	return false;
}

function stringToBool($val){
    return ( mb_strtoupper( trim( $val)) == mb_strtoupper ("true")) ? TRUE : FALSE;
}

function readTicketsFile(){
	global $tickets;
	
	$my_output_file = './output_file.txt';
	$my_file = './file.txt';
	
	$handle = @fopen($my_file, 'r');
	$handle2 = NULL;
	
	if($handle == false) return;
	
	if(OUTPUT_CLEAN_FILE){
		$handle2 = @fopen($my_output_file, 'w');
		if($handle2 == false) return;
	}
	
	while (($line = fgets($handle)) !== false) {
		
		if( processJsonTicket($line) && OUTPUT_CLEAN_FILE ){
			fwrite($handle2, $line);
		}
	}

	fclose($handle);
	
	if(OUTPUT_CLEAN_FILE){
		fclose($handle2);
	}

	
	//echo processHeader();
	//echo "<pre>";
	//var_dump($tickets);
	//echo "</pre>";
	//echo processFooter();
	header('Content-Type: application/json');
	echo json_encode($tickets);
}

function processKey($tag, $key, $value){
	return "<$tag class='$key'>$value</$tag>";
}

function processHeader(){
	return "<!DOCTYPE html><html><head></head><body>";
}

function processFooter(){
	return "</body></html>";
}

/*
* Add new filter to parse tickets
* $filter [url, method]
*/
function addFilter($name, $filter){
	global $filters;
	$filters[$name] = $filter;
}

addFilter("woocurve", ["url=*woocurve.groovehq.com*/tickets/*\d", "body->ticket->title","currentDate","body->ticket->comments_attributes->0->body", "body->ticket->comments_attributes->0->private=false", "body->draft=false"]);
addFilter("helpscout", ["url=*secure.helpscout.net*/conversations/*reply/", "body->body","currentDate", "body->ticketID"]);
addFilter("shareThis", ["url=*sharethis-publishers.zendesk.com/*/tickets/*\d", "body->ticket->subject","currentDate", "body->ticket->comment->html_body", "body->ticket->comment->public=true"]);

readTicketsFile();

?>

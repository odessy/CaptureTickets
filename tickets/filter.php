<?php

/*
* contain the filters
*/
$filters = [];
//read tickets file

function match_wildcard( $wildcard_pattern, $haystack ) {
   $regex = str_replace(
     array("\*", "\?", "\d", "\s"), // wildcard chars
     array('.*','.', 'd', "s"),   // regexp chars
     preg_quote($wildcard_pattern, '/')
   );

   return preg_match('/^'.$regex.'$/', $haystack);
}

function processJsonTicket($line){
	global $filters;
	
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
				$prev_s = "";
				foreach($keys as $s)
				{
					if (is_array($obj) && isset($obj[$s]))
					{
						if (end($keys) == $s && $e == sizeof($keys) )
						{
							if( ctype_digit(strval($s)) ){
								$ticket[$prev_s] = $obj[$s];
							} else {
								$ticket[$s] = $obj[$s];
							}
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
					
					if( ctype_digit(strval($s)) ){
						$s = $prev_s;
					}
					$prev_s = $s;
				}
				
				if($checkKey) {
					if(isset($ticket[$s])){
						if(is_bool($ticket[$s]) && (stringToBool($parts[1]) == $ticket[$s]) == false  ){
							$keyValid = false;
							break;
						} else	if(!is_bool($ticket[$s]) && !match_wildcard($parts[1], $ticket[$s] ) && !match_wildcard($parts[1], preg_replace('/[^A-Za-z0-9\-]/', '', $ticket[$s]) )){

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
				return $ticket;
			}
		}
	}
	
	return NULL;
}

function stringToBool($val){
    return ( mb_strtoupper( trim( $val)) == mb_strtoupper ("true")) ? TRUE : FALSE;
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

//addFilter("woocurve", ["url=*woocurve.groovehq.com*/tickets/*\d", "body->ticket->title","currentDate","body->ticket->comments_attributes->0->body", "body->ticket->comments_attributes->0->private=false", "body->draft=false"]);
addFilter("helpscout", ["url=*secure.helpscout.net*/conversations/*reply/", "body->body","currentDate", "body->ticketID"]);
//addFilter("shareThis", ["url=*sharethis-publishers.zendesk.com/*/tickets/*\d", "body->ticket->subject","currentDate", "body->ticket->comment->html_body", "body->ticket->comment->public=true"]);
//addFilter("themenectar", ["url=*themenectar.ticksy.com*/ticket/*", "body->ticket_id->0", "currentDate", "body->comment->0=*", "body->comment_type->0=comment"]);
//addFilter("fuelthemes", ["url=*fuelthemes.ticksy.com*/ticket/*", "body->ticket_id->0", "currentDate", "body->comment->0=*", "body->comment_type->0=comment"]);

?>
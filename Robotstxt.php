<?php

ini_set("display_errors",1);
error_reporting(E_ALL);

class Robotstxt {

	var $rules = array();

	function __construct($text = '') {
		if ($text != '') {
			$this->init($text);
		}
	}
   
    function init($text) {
	   $text = explode("\n", $text);
	   $user_agent = "";
	   foreach($text as $t) {
		   $t = trim($t);
		   if ( $t !=  '') {
			   $params = explode(':', $t);
			   if ( count($params) == 2) {
				   $params[0] = mb_strtolower(trim($params[0]), "utf-8");
				   $params[1] = mb_strtolower(trim($params[1]), "utf-8");
				   
				   if ( $params[0] == 'user-agent') {
					   $user_agent = $params[1];
					   continue;
				   } elseif ($params[0] == 'allow' || $params[0] == 'disallow') {
						if ($user_agent != '') {
							if (mb_substr($params[1], -1 , 1, "UTF-8") == '$' ||
								mb_substr($params[1], -1 , 1, "UTF-8") == '*') {
									$value = $params[1];
							} else {
								$value = $params[1].'*';
							}
							
							
						    $this->rules[$user_agent][] = array(
													'name' => $params[0],
													'value' => $value
												);
					   }
				   }
				   
				   
			   }
		   }
		   
	   }
	   
	    function cmp($a, $b) {
			
			if (mb_strlen($a['value']) == mb_strlen($b['value'])) {
				return 0;
			}
			return (mb_strlen($a['value']) < mb_strlen($b['value'])) ? -1 : 1;
		}
		foreach($this->rules as $key=>$user_agent) {
				uasort($user_agent, 'cmp');
				$this->rules[$key] = $user_agent;
		}
	   
	    return true;

   }

   function isAllowed($url, $user_agent="*") {
   		$status = true;
   		foreach($this->rules[$user_agent] as $rl) {
   			$parts = explode('*', $rl['value']);
   			$allowed = true;
   			foreach($parts as $part) {
   				if ($part != '') {
   					$pos = stripos($url, $part);
   					if ($pos === false ) {
   						$allowed = false;
   						break;
   					}
   				} 
   			}
   			if ($allowed === true) {
   				if ( $rl['name'] == 'disallow') {
   					$status = false;
   				} else {
   					$status = true;
   				}
   			}
   		}
   		return $status;
   }
}
/*
$robots = new robots();

$robots->init(file_get_contents('http://keepfoto.ru/robots.txt'));

var_dump($robots->isAllowed('/page/help/section'));
*/
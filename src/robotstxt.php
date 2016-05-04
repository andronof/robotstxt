<?php
class Robotstxt 
{

	var $rules = array();
	var $current_user_agent = null;

	function __construct($text = '') 
	{
		if ($text != '') {
			$this->init($text);
		}
	}
	
	function setUserAgent($user_agent = null) {
		$this->current_user_agent = $user_agent;
	}
   
    function init($text) 
	{
	   $text = explode("\n", $text);
	   $user_agent = "";
	   foreach($text as $t) {
		   $t = explode("#", $t);
		   $t = trim($t[0]);
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

							// Если пустые параметры
							if ($value == "*") {
								if ($params[0] === 'disallow') {
									$params[0] = 'allow';
								} else {
									$params[0] = 'disallow';
								}
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
			if (mb_strlen($a['value'], "UTF-8") == mb_strlen($b['value'], "UTF-8")) {
				if ($a['name'] == 'allow') {
					return 1;
				} else {
					return -1;
				}
				return 0;
			}
			return (mb_strlen($a['value'], "UTF-8") < mb_strlen($b['value'], "UTF-8")) ? -1 : 1;
		}
		
		foreach($this->rules as $key=>$user_agent) {
				uasort($user_agent, 'cmp');
				$this->rules[$key] = $user_agent;
		}
	   
	    return true;

   }

   function isUserAgent($user_agent) {
	   return isset($this->rules[$user_agent]);
   }
   
   function isAllowed($url, $user_agent="*") {

   	    if (mb_substr($url, 0, 1, "UTF-8") !== '/') {
   	   		return true;
   	    }
	    if ($this->current_user_agent !== null) {
			$user_agent = $this->current_user_agent;
	    }
   		$status = true;
   		if ( isset($this->rules[$user_agent])) {
	   		foreach($this->rules[$user_agent] as $rl) {
	   			$parts = explode('*', $rl['value']);
	   			$allowed = true;
	   			foreach($parts as $part) {

	   				if ($part != '') {
	   					if (mb_substr($part, -1, 1, "UTF-8") == '$') {
	   						$part = rtrim($part, "$");
	   						$pos = stripos($url, $part);
		   					if ($pos === false || $pos + mb_strlen($part, "UTF-8") != mb_strlen($url, "UTF-8")) {
		   						$allowed = false;
		   						break;
		   					}
	   					} else {
		   					$pos = stripos($url, $part);

		   					if ($pos === false ) {
		   						$allowed = false;
		   						break;
		   					}
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
	   	}
   		return $status;
   }
}

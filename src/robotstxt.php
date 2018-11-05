<?php

class Robotstxt
{
    private $rules = array();
    private $current_user_agent = null;

    public function __construct($text = '')
    {
        if ($text != '') {
            $this->init($text);
        }
    }

    public function setUserAgent($user_agent = null)
    {
        $this->current_user_agent = $user_agent;
    }

    private function preg_quote_robotstxt($url)
    {
        $str = preg_quote($url, "#");
        $str = str_replace(array("\\*", "\\$"), array("*", "$"), $str);
        return $str;
    }

    public function init($text)
    {
        $text = explode("\n", $text);
        $user_agent = "";
        $this->rules = array();
        foreach ($text as $t) {
            $t = explode("#", $t);
            $t = trim($t[0]);
            $params = explode(':', $t);
            if (count($params) !== 2) {
                continue;
            }
            $params[0] = mb_strtolower(trim($params[0]), "utf-8");
            $params[1] = trim($params[1]);

            if ($params[0] == 'user-agent') {
                $user_agent = mb_strtolower($params[1], "UTF-8");
                $this->rules[$user_agent] = array();
                continue;
            } elseif ($params[0] == 'allow' || $params[0] == 'disallow') {
                if ($user_agent == '') {
                    continue;
                }
                if ($params[1] == "") { // Если пустой параметр
                    if ($params[0] === 'disallow') {
                        $params[0] = 'allow';
                    } else {
                        continue;
                    }
                    $pattern = "#^(.*)$#";
                } else {
                    $value = str_replace("*", "(.*)", $this->preg_quote_robotstxt($params[1]));
                    if (mb_substr($value, 0, 1, "UTF-8") === "/") {
                        $pattern = "#^" . $value;
                    } else {
                        $pattern = "#^(.*)" . $value;
                    }

                    if (mb_substr($value, -1, 1, "UTF-8") === "$") {
                        $pattern .= "#";
                    } else {
                        $pattern .= "(.*)$#";
                    }
                }

                $this->rules[$user_agent][] = array(
                    'name' => $params[0],
                    'value' => $params[1],
                    'pattern' => $pattern
                );

            }
        }

        foreach ($this->rules as $key => $user_agent) {
            uasort($user_agent, array($this, 'cmp'));
            $this->rules[$key] = $user_agent;
        }
        return true;
    }

    private function cmp($a, $b)
    {
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

    public function isUserAgent($user_agent)
    {
        return isset($this->rules[$user_agent]);
    }

    private function unparse_url($parsed_url)
    {
        $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    public function isAllowed($url, $user_agent = "*")
    {
        $purl = parse_url($url);
        unset($purl['scheme']);
        unset($purl['host']);
        unset($purl['port']);
        unset($purl['user']);
        unset($purl['pass']);
        $url = $this->unparse_url($purl);
        $user_agent = mb_strtolower($user_agent, "UTF-8");

        if ($this->current_user_agent !== null) {
            $user_agent = $this->current_user_agent;
        }
        $status = true;
        if (isset($this->rules[$user_agent])) {
            foreach ($this->rules[$user_agent] as $rl) {
                $allowed = (preg_match($rl['pattern'], $url) === 1);
                if ($allowed === true) {
                    if ($rl['name'] == 'disallow') {
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
<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);
require_once(__DIR__ . '/../src/robotstxt.php');


$robots = new Robotstxt();

$count_error = 0;


$robots->init("User-agent: *
Disallow: /auto
Disallow: /ff*/dd$
Allow: /
");

$task = array(
    "/catalog/auto" => true,
    "/ffaa/dd" => false,
);

foreach ($task as $url => $t) {
    if ($robots->isAllowed($url) === $t) {
        echo("[TRUE] Доступ для '" . $url . "' определен верно\n");
    } else {
        echo("[FALSE] Доступ для '" . $url . "' определен неверно\n");
        $count_error++;
    }
}


$robots->init("User-agent: *
Allow: /catalog
Disallow: /catalog/auto
Disallow: /cat
");

$task = array(
    "/" => true,
    "/catalog" => true,
    "/catalog/auto" => false,
    "/auto" => true,
    "/ru/cat" => true,
);

foreach ($task as $url => $t) {
    if ($robots->isAllowed($url) === $t) {
        echo("[TRUE] Доступ для '" . $url . "' определен верно\n");
    } else {
        echo("[FALSE] Доступ для '" . $url . "' определен неверно\n");
        $count_error++;
    }
}


$robots->init("
			    # github.com просто надпись
				User-agent: * # комментарий который может все испортить
				Disallow: /
						
				
				
				@@@Disallow: /
				Allow: /blogs # комментарий который может все испортить
				Allow:/car/m.php?page=*&sort=price
				Disallow: /dlaaa
				
				

				Allow:/m.php$

				User-agent: google
				Disallow: /

				User-agent: botd
				Disallow: 

				User-agent: bota
				Allow: 

				User-agent: botcarbon
				
				
				Disallow:/carbon.php
				Allow:/carbon.php
				Disallow: /*?rand=		

	");

$task = array(
    '/blogs' => true,
    '/ru/dlaaa' => false,
    '/blogs/page.php?page=1&sort=price' => true,
    '/home' => false,
    '/car/m.php?page=1000&sort=price' => true,
    '/m.php' => true,
    '/m.ph' => false,
    '/m.php?p=1' => false,
    '/' => false,
);

$task_botd = array(
    '/blogs' => true,
);

$task_bota = array(
    '/blogs' => true,
);

$task_botcarbon = array(
    '/carbon.php' => true,
    'http://www.matras-market.ru/aksessuary/135x200/tempur?rand=dprice' => false,
    'aksessuary/135x200/tempur?rand=dprice' => true,
);


foreach ($task as $url => $t) {
    if ($robots->isAllowed($url) === $t) {
        echo("[TRUE] Доступ для '" . $url . "' определен верно\n");
    } else {
        echo("[FALSE] Доступ для '" . $url . "' определен неверно\n");
        $count_error++;
    }
}


foreach ($task_botd as $url => $t) {
    if ($robots->isAllowed($url, 'botd') === $t) {
        echo("[TRUE] Доступ для '" . $url . "' определен верно\n");
    } else {
        echo("[FALSE] Доступ для '" . $url . "' определен неверно\n");
        $count_error++;
    }
}

foreach ($task_bota as $url => $t) {
    if ($robots->isAllowed($url, 'bota') === $t) {
        echo("[TRUE] Доступ для '" . $url . "' определен верно\n");
    } else {
        echo("[FALSE] Доступ для '" . $url . "' определен неверно\n");
        $count_error++;
    }
}

foreach ($task_botcarbon as $url => $t) {
    if ($robots->isAllowed($url, 'botcarbon') === $t) {
        echo("[TRUE] Доступ для '" . $url . "' определен верно\n");
    } else {
        echo("[FALSE] Доступ для '" . $url . "' определен неверно\n");
        $count_error++;
    }
}

$robots->init("User-agent: *
Allow: *
Disallow: /404.php
Disallow: */8049776
Host: faberlicsale.com");

$task = array(
    "https://stackoverflow.com/questions/8049776/xdebug-for-remote-server-not-connecting" => false,
    "http://faberlicsale.com/index.php" => true,
    "http://faberlicsale.com/Voprosy-otvety.php" => true,
    "http://faberlicsale.com/Consultant-Faberlic.php" => true,
    "http://faberlicsale.com/News-Faberlic.php" => true,
    "http://faberlicsale.com/O-Faberlic.php" => true,
    "http://faberlicsale.com/Contacts.php" => true,
);

foreach ($task as $url => $t) {
    if ($robots->isAllowed($url) === $t) {
        echo("[TRUE] Доступ для '" . $url . "' определен верно\n");
    } else {
        echo("[FALSE] Доступ для '" . $url . "' определен неверно\n");
        $count_error++;
    }
}

$robots->init("# Исходный robots.txt:
User-agent: Yandex
Allow: /
Allow: /catalog/auto
Disallow: /catalog");

$task = array(
    "/catalog" => false,
    "/catalog/moto" => false,
    "/catalog/auto" => true,
    "/catalog/auto/foto" => true,
);

foreach ($task as $url => $t) {
    if ($robots->isAllowed($url, "yAnDeX") === $t) {
        echo("[TRUE] Доступ для '" . $url . "' определен верно\n");
    } else {
        echo("[FALSE] Доступ для '" . $url . "' определен неверно\n");
        $count_error++;
    }
}

echo("Конец тестирования. Количество ошибок: " . $count_error . "\n");



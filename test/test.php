<?php
ini_set("display_errors",1);
error_reporting(E_ALL);
require_once(__DIR__.'/../src/robotstxt.php');


$robots = new Robotstxt();

$robots->init("
			    # github.com просто надпись
				User-agent: * # комментарий который может все испортить
				Disallow: /
						
				
				
				@@@Disallow: /
				Allow: /blogs # комментарий который может все испортить
				Allow:/car/m.php?page=*&sort=price
				
				
				

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
			'/blogs' => false,
		);

$task_botcarbon = array(
			'/carbon.php' => true,
			'http://www.matras-market.ru/aksessuary/135x200/tempur?rand=dprice' => false,
			'aksessuary/135x200/tempur?rand=dprice' => false,
		);

$count_error = 0;
foreach ($task as $url=>$t) {
	if ($robots->isAllowed($url) === $t) {
		echo("[TRUE] Доступ для '".$url."' определен верно\n");
	} else {
		echo("[FALSE] Доступ для '".$url."' определен неверно\n");
		$count_error++;
	}
}

foreach ($task_botd as $url=>$t) {
	if ($robots->isAllowed($url, 'botd') === $t) {
		echo("[TRUE] Доступ для '".$url."' определен верно\n");
	} else {
		echo("[FALSE] Доступ для '".$url."' определен неверно\n");
		$count_error++;
	}
}

foreach ($task_bota as $url=>$t) {
	if ($robots->isAllowed($url, 'bota') === $t) {
		echo("[TRUE] Доступ для '".$url."' определен верно\n");
	} else {
		echo("[FALSE] Доступ для '".$url."' определен неверно\n");
		$count_error++;
	}
}

foreach ($task_botcarbon as $url=>$t) {
	if ($robots->isAllowed($url, 'botcarbon') === $t) {
		echo("[TRUE] Доступ для '".$url."' определен верно\n");
	} else {
		echo("[FALSE] Доступ для '".$url."' определен неверно\n");
		$count_error++;
	}
}

echo("Конец тестирования. Количество ошибок: ".$count_error."\n");



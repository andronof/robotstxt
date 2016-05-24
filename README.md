# Parse (разбор) robots.txt
Библиотека для разбора файла robots.txt. Написаная на языке php. Основная задча библиотеки проверить любую ссылку, закрыта она от индексации или нет. Правила разбора используются из описания в справочнике [Яндекс](https://yandex.ru/support/webmaster/controlling-robot/robots-txt.xml).

## Правила разбора
1. Разбирается все данные по ботам.
2. Для каждого бота сортируются по длине ссылки. Если у нескольких правил длина одинаковая то предпочтение отдается разрешающему(allow) правилу.
3. Правила содержащие пустые значения изменяются на противоположные.

## Установка
Через composer:
```json
{
    "require": {
        "andronof/robotstxt": "dev-master"
    }
}
```
и запустить команду 
```
composer update
```
установится последний версия, но не обязательно стабильная или 
```sh
composer require andronof/robotstxt
```
установится последняя стабильная версия

## Использование
```php
$robotstxt = new \Robotstxt(file_get_contents('http://yandex.ru/robots.txt'));
```
или
```php
$robotstxt = new \Robotstxt();
$robotstxt->init(file_get_contents('http://yandex.ru/robots.txt'));
```
Проверка запрещена ли ссылка в robots.txt

```php
if ($robots->isAllowed('/msearch') ) {
    echo('Доступ разрешен');
} else {
    echo('Доступ запрещен');
}
```
Данная функция может принимать вторым параметром имя бота. Если не указан, то берутся правила для всех (*). Ссылку для проверки указывайте с начинающегося слеша (/).

## Остальные функции
* **isUserAgent($user_agent)** — Проверяет есть ли правила для конкретного бота
* **setUserAgent($user_agent = null)** — Устанавливает правила какого бота использовать. Имеет больший приоритет чем если указывать бота в функции isAllowed. Если необходимо сбросить глобального бота передайте в эту функцию null.

## Используется в проектах
* [http://gensitemap.ru/](http://gensitemap.ru/)

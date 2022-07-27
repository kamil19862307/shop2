<?php
defined('VG_ACCESS') or die('Access denied');
const TEMPLATE = 'templates/default/';
const ADMIN_TEMPLATE = 'core/admin/view';
const COOKIE_VERSION = '1.0.0';
const CRYPT_KEY = '';
const COOKIE_TIME = 60; //в минутах для админа(если бездействует)
const BLOCK_TIME = 3; // в часах, для блокировки злоумышленников
const QTY = 8; //Количество товара на одной страницы. ДЛЯ постраничной навигации
const QTY_LINKS = 3; //количества ссылок левее и правее активной ссылки. Для постраничной навигации
/* const ADMIN_CSS_JS  Хранятся пути к CSS и JavaScript файлам необходимых для работы сайта*/
const ADMIN_CSS_JS = [
  'styles' => [],
  'scripts' => []
];
/* const USER_CSS_JS  Хранятся пути к CSS и JavaScript файлам необходимых для работы сайта*/
const USER_CSS_JS = [
  'styles' => [],
  'scripts' => []
];

use core\base\exceptions\RouteException;

function autoloadMainClasses($class_name)
{
  $class_name = str_replace('\\', '/', $class_name);
  if (!include $class_name . '.php') {
    throw new RouteException('Неверное имя файла для подключения' . $class_name);
  }
}

spl_autoload_register('autoloadMainClasses');

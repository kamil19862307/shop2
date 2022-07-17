<?php
define('VG_ACCESS', TRUE);

header('Content-Type: text/html; charset=utf-8');
session_start();

require_once 'config.php'; //Настройки БД
require_once 'core/base/settings/internal_settings.php'; //Внутренние настройки 
require_once 'libraries/functions.php'; //Библиотека внуттрених функций 

use core\base\exceptions\RouteException; //Подключаем пространство имён для исключений
use core\base\controller\RouteController; //Контроллер

try {
  /* singleton контроллер */
  // RouteController::getInstance()->route();
  RouteController::getInstance();
} catch (RouteException $e) {
  exit($e->getMessage());
}

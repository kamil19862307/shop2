<?php

namespace core\base\controller;

use core\base\exceptions\RouteException;
use core\base\settings\Settings;
use core\base\settings\ShopSettings;

/* ----------- контроллер обработки адресной строки. Формирование маршрутов ------- */
//Разбирает адрессную строку по параметрам

class RouteController
{
  static private $_instance; // Синглтон

  protected $routes;

  protected $controller;
  protected $inputMethod;
  protected $outputMethod;
  protected $parameters;


  private function __clone()
  {
  }

  static public function getInstance()
  {
    // return self::$_instance;
    if (self::$_instance instanceof self) {
      return self::$_instance;
    }
    return self::$_instance = new self;
  }


  private function __construct()
  {
    $adress_str = $_SERVER['REQUEST_URI'];
    /* Если '/' не стоит в конце строки и это не корень сайта, перенаправляю пользователя на страницу сайта без этого сивола */
    if (strrpos($adress_str, '/') === strlen($adress_str) - 1 && strrpos($adress_str, '/') !== 0) {
      $this->redirect(rtrim($adress_str, '/'), 301);
    }
    /* Обрезаю строку, в которой хранится имя выполнения скрипта */
    $path = substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], 'index.php'));

    if ($path === PATH) {
      $this->routes = Settings::get('routes'); // Маршруты
      if (!$this->routes) throw new RouteException('Сайт находится на техническом обслуживании');

      /* Проверка на путь к админ панели в адресной строке*/
      if (strpos($adress_str, $this->routes['admin']['alias']) === strlen(PATH)) {

        /* Админка */

        $url = explode('/', substr($adress_str, strlen(PATH . $this->routes['admin']['alias']) + 1));
        /* Не лежит ли обращение к плагину в нулевом элементе */
        if ($url[0] && is_dir($_SERVER['DOCUMENT_ROOT'] . PATH . $this->routes['plugins']['path'] . $url[0])) {
          /* Если условие выполнилось, значит буду детать что-то для плагина, если нет, то мы попадём в админку */
          // Плагины:
          $plugin = array_shift($url);
          /* Существуют ли плагины */
          $pluginSettings = $this->routes['settings']['path'] . ucfirst($plugin . 'Settings'); //Путь к файлу для настроек плагина
          if (file_exists($_SERVER['DOCUMENT_ROOT'] . PATH . $pluginSettings . '.php')) {
            /* Переопределяем свойство $this->routes */
            $pluginSettings = str_replace('/', '\\', $pluginSettings);
            $this->routes = $pluginSettings::get('routes'); //Склеит/объеденит базовые и новые/скорректированные маршруты
          }

          $dir = $this->routes['plugins']['dir'] ? '/' . $this->routes['plugins']['dir'] . '/' : '/';
          $dir = str_replace('//', '/', $dir);
          $this->controller = $this->routes['plugins']['path'] . $plugin . $dir;
          $hrUrl = $this->routes['plugins']['hrUrl'];
          $route = 'plugins';
        } else {
          $this->controller = $this->routes['admin']['path'];
          $hrUrl = $this->routes['admin']['hrUrl'];
          $route = 'admin'; //Формирую ячейку маршрута, чтобы дальше метод понимал какие контроллеры подключать
        }
      } else {
        /* Пользовательская часть */
        $url = explode('/', substr($adress_str, strlen(PATH)));

        /* Тут система поймёт, ей работать с человекочитаемым адресом или нет */
        $hrUrl = $this->routes['user']['hrUrl'];

        /* Откуда подключать контроллеры */
        $this->controller = $this->routes['user']['path']; //строка для маршрута

        $route = 'user';
      }

      $this->createRoute($route, $url);

      if ($url[1]) {
        $count = count($url);
        $key = ''; //Первая итерация цикла, ключ пуст

        if (!$hrUrl) {
          $i = 1;
        } else {
          $this->parameters['alias'] = $url[1];
          $i = 2;
        }

        for (; $i < $count; $i++) {
          if (!$key) { //Ячейки массива нет.
            $key = $url[$i]; //Ключ попал в этот элемент и остаётся храниться. Пример: 'color'
            $this->parameters[$key] = ''; //Но при этом, создаю в свойстве parameters ячейку $key 
          } else {
            $this->parameters[$key] = $url[$i]; //записываю в свойство parameters, в ячейку $key то, что приходит уже на следующей итерации цикла. Пример: 'color' => 'red'
            $key = ''; // и обнуляю ключ
          }
        }
      }
    } else {
      try {
        throw new \Exception('Не корректная директория сайта');
      } catch (\Exception $e) {
        exit($e->getMessage());
      }
    }



    // $s = Settings::instance();
    // $s1 = shopSettings::instance();
    // $s1 = ShopSettings::get('property1');
    // print_arr($s);
    // print_arr($s1);
    // echo $s['admin']['name'];
    // echo $this->hair;
  }

  private function createRoute($var, $arr)
  {
    $route = [];
    if (!empty($arr[0])) {
      if ($this->routes[$var]['routes'][$arr[0]]) {
        $route = explode('/', $this->routes[$var]['routes'][$arr[0]]);
        $this->controller .= ucfirst($route[0] . 'Controller');
      } else {
        $this->controller .= ucfirst($arr[0] . 'Controller');
      }
    } else {
      $this->controller .= $this->routes['default']['controller'];
    }

    $this->inputMethod = $route[1] ? $route[1] : $this->routes['default']['inputMethod'];
    $this->outputMethod = $route[2] ? $route[2] : $this->routes['default']['outputMethod'];
    // print_arr($this);
    // echo $this->inputMethod;
    return;
  }
}

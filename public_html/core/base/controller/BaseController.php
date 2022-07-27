<?php

namespace core\base\controller;

use core\base\exceptions\RouteException;
use ReflectionMethod;

abstract class BaseController
{
  protected $page;
  protected $errors;

  protected $controller;
  protected $inputMethod;
  protected $outputMethod;
  protected $parameters;

  public function route()
  {
    $controller = str_replace('/', '\\', $this->controller); // правильное имя класса

    try {
      /* Проверяю, есть ли метод 'request' в классе "$controller", если есть передаю массив параметров "$args" */
      $object = new ReflectionMethod($controller, 'request');

      $args = [
        'parameters' => $this->parameters,// параметры адрессной строки
        'inputMethod' => $this->inputMethod,
        'outputMethod' => $this->outputMethod
      ];
      $object->invoke(new $controller, $args);
    } catch (\ReflectionException $e) {
      throw new RouteException($e->getMessage());
    }
  }
  public function request($args){
    $this->parameters = $args['parameters'];
    $inputData = $args['inputMethod'];
    $outputData = $args['outputMethod'];

    $this->$inputData();

    $this->page = $this->$outputData();//Собираю, что показывать пользователю

    if($this->errors){
      $this->writeLog($this->errors);// в логи ошики пихаю
    }

    $this->getPage();
  }

  protected function render($path = '', $parameters = []){//Два необязательных параметра, путь к подключаемому шаблону и массив передаваемых ему параметров
    extract($parameters);

    if(!$path){
      $path = TEMPLATE . explode('controller', strtolower((new \ReflectionClass($this))->getShortName()))[0];//Так как путь не пришёл, подключаю индекс из дефолтной дирректории. Придёт строка вида: indexcontroller, после explode останется только: index, в итоге получаю: templates/default/index
    }


  
    ob_start();// Открываю буфер обмена

    if(!include_once $path . '.php') throw new RouteException('Отсутствует шаблон - ' . $path);// templates/default/index.php

    return ob_get_clean();

  }

  protected function getPage(){
    exit($this->page);// показ собранной страницы пользователю
  }
}

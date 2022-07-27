<?php

namespace core\base\settings;

class Settings
{
  static private $_instance;
  private $routes = [
    'admin' => [
      'alias' => 'admin', //как входить в админ панель
      'path' => 'core/admin/controller/',
      'hrUrl' => false,
      'routes' => [
        // ЧПУ понятные роуты тут будут
        // 'product' => 'goods' // тут можно поменять путь директории

      ]
    ],
    'settings' => [
      'path' => 'core/base/settings/'
    ],
    'plugins' => [
      'path' => 'core/plugins/', // Путь к плагинам
      'hrUrl' => false,
      'dir' => false
    ],
    'user' => [
      'path' => 'core/user/controller/',
      'hrUrl' => true,
      'routes' => [
        // 'site' => 'index/hello'
        // 'catalog' => 'site/hello/by' // тут можно поменять путь директории
      ]
    ],
    'default' => [
      'controller' => 'IndexController',
      'inputMethod' => 'inputData',
      'outputMethod' => 'outputData'
    ]
  ];

  private $tmplateArr = [
    'text' => ['name', 'phone', 'adress'],
    'textarea' => ['content', 'keywords']
  ];

  public function __construct()
  {
  }

  public function __clone()
  {
  }

  static public function get($property)
  {
    return self::instance()->$property;
  }

  static public function instance()
  {
    if (self::$_instance instanceof self) {
      return self::$_instance;
    }
    return self::$_instance = new self;
  }

  public function clueProperties($class)
  {
    $baseProperties = [];/* массив свойств который будет возращатся */
    foreach ($this as $name => $item) {
      $property = $class::get($name);
      // $baseProperties[$name] = $property;
      if (is_array($property) && is_array($item)) {
        $baseProperties = [$name] = $this->arrayMergeRecursive($this->name, $property);
        continue;
      }
      if (!$property) $baseProperties[$name] = $this->name;
    }
    return $baseProperties;
  }

  // Объединяем массивы $routes и $tmplateArr 
  public function arrayMergeRecursive()
  {
    $arrays = func_get_args();
    $base = array_shift($arrays);
    foreach ($arrays as $array) {
      foreach ($array as $key => $value) {
        if (is_array($value) && is_array($base[$key])) {
          $base[$key] = $this->arrayMergeRecursive($base[$key], $value);
        } else {
          if (is_int($key)) {
            if (!in_array($value, $base)) array_push($base, $value);
            continue;
          }
          $base[$key] = $value;
        }
      }
    }
    return $base;
  }
}

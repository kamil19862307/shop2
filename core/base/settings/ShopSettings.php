<?php

namespace core\base\settings;

use core\base\settings\Settings;

class ShopSettings
{
  static private $_instance;
  private $baseSettings;

  private $routes = [
    'plugins' => [
      'dir' => false,
      'routes' => [
        // ЧПУ понятные роуты тут будут
        // 'product' => 'goods' // тут можно поменять путь директории
      ]
    ]
  ];

  /* объединяем массив $tmplateArr (в Setting папке) с этим: */
  private $tmplateArr = [
    'text' => ['name', 'phone', 'adress', 'price', 'short'],
    'textarea' => ['content', 'keywords', 'goods_content']
  ];

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

    /*  в свойство нашего класса self::$_instance->baseSettings, сохраним ссылку на объект класса Settings, вызвав у него метод instance() */
    self::$_instance->baseSettings = Settings::instance();
    $baseProperties = self::$_instance->baseSettings->clueProperties(get_class());
    self::$_instance->setProperty($baseProperties);

    return self::$_instance;
  }

  protected function setProperty($properties)
  {
    if ($properties) {
      foreach ($properties as $name => $property) {
        $this->$name = $property;
      }
    }
  }

  public function __construct()
  {
  }

  public function __clone()
  {
  }
}

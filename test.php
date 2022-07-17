<?php
define('VG_ACCESS', TRUE);

require_once 'core/base/settings/internal_settings.php';

use core\base\exceptions\RouteException;

function inverse($x)
{
  if (!$x) {
    throw new RouteException('Деление на ноль.');
  }
  return 1 / $x;
}

try {
  echo inverse(5) . "<br>";
  echo inverse(10) . "<br>";
  echo inverse(7) . "<br>";
  echo inverse(0) . "<br>";
} catch (RouteException $e) {
  echo 'Выброшено исключение: ',  $e->getMessage(), "<br>";
}

// Продолжение выполнения
echo "Привет, мир<br>";

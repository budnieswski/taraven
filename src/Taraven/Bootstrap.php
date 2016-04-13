<?php
namespace Taraven;

use Taraven\Wordpress\Cleaner;

class Bootstrap
{
  public function __construct()
  {
    echo "Starting fucking Theme ! <br>";
    echo Cleaner::hello();
  }
}
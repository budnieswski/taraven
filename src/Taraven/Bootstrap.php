<?php
namespace Taraven;

use Taraven\Wordpress\Cleaner;
use Timber;

global $timber;

class Bootstrap
{
  public function __construct()
  {
    $timber = new Timber();
    Timber::$dirname = 'views';

    echo "Starting fucking Theme ! <br>";
    echo Cleaner::hello();
  }
}
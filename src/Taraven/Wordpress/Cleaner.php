<?php
namespace Taraven\Wordpress;

use Taraven\Wordpress\Cleaner\Header;

class Cleaner
{
    public function hello()
    {
      echo Header::hello();
      return "Cleaner fucking trash WP<br>";
      // return true;
    }
}
<?php
namespace Taraven;


use Timber;
use Taraven\Setting;


global $timber;


class Bootstrap
{
  // Store theme settings, like CSS, JS, Menus ...
  private $settings;
  

  /**
   * Initialize Taraven features
   * @param boolean $settings [description]
   */
  public function __construct($settings = false)
  {
    // Initializing Timber
    $timber = new Timber();
    Timber::$dirname = 'views';

    // Processing settings
    $this->settings = Taraven\Setting($settings);
  }

}
<?php
namespace Taraven;

use Timber;

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

    $this->settingsMerge($settings);
  }


  /**
   * Merge user settings with default settings
   * @param  [array] $settings [description]
   * @return [type]           [description]
   */
  private function settingsMerge($settings)
  {
    $defaultSettings = array(
      'css'             => array(),
      'js'              => array(),
      'menu'            => array(),
      'acf'             => array(),
      'google-maps'     => true,
      'google-jquery'   => true,
      'blog'            => false,
    );

    if( is_array($settings) ):
      foreach ($settings AS $key => $value)
      {
        if( is_array($value) )
          $defaultSettings[$key] = array_merge($defaultSettings[$key], $value);
        else
          $defaultSettings[$key] = $value;
      }
    endif;

    $this->settings = $defaultSettings;
    return;
  }


  /**
   * Get current settings
   * @return [array] 
   */
  public function getSettings()
  {
    return $this->settings;
  }

}
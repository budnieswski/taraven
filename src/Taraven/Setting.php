<?php
namespace Taraven;

class Setting
{
  // Store theme settings, like CSS, JS, Menus ...
  private $settings = array(
    'css'             => array(),
    'js'              => array(),
    'menu'            => array(),
    'acf'             => array(),
    'google-maps'     => true,
    'google-jquery'   => true,
    'blog'            => false,
  );
  

  /**
   * 
   * @param boolean $settings [description]
   * @return array
   */
  public function __construct($settings = false)
  {
    return $this->setSettings($settings);
  }


  /**
   * Set settings
   * @return [array] 
   */
  public function setSettings($settings)
  {
    $this->settings = $this->merge($settings);

    return $this;
  }


  /**
   * Get current settings
   * @return [array] 
   */
  public final function getSettings()
  {
    return $this->settings;
  }


  /**
   * Merge user settings with default/current settings
   * @param  [array] $settings
   * @return [array] $settings
   */
  private function merge($settings)
  {
    $currentSettings = $this->getSettings();

    if( is_array($settings) ):
      foreach ($settings AS $key => $value)
      {
        if( is_array($value) )
          $currentSettings[$key] = array_merge($currentSettings[$key], $value);
        else
          $currentSettings[$key] = $value;
      }
    endif;

    return $currentSettings;
  }

}
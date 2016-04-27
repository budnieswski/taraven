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
    'google-jquery'   => true,
    'google-maps'     => false,
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
   * @return This class
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
   * Get CSS settings
   * @return [array] 
   */
  public final function getCSS()
  {
    return $this->settings['css'];
  }


  /**
   * Get Javascript settings
   * @return [array] 
   */
  public final function getJS()
  {
    return $this->settings['js'];
  }


  /**
   * Get Menus settings
   * @return [array] 
   */
  public final function getMenu()
  {
    return $this->settings['menu'];
  }


  /**
   * Get Advanced Custom Fields settings
   * @return [array] 
   */
  public final function getACF()
  {
    return $this->settings['acf'];
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
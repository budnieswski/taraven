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

    // Processing settings & use Setting Object
    $this->settings = new Setting($settings);
    $this->setSettings( $this->settings );
  }


  /**
   * Update current settings then update Timber Context
   * @param array/string $settings [Can be array with settings or only one setting]
   * @param array/string $data [Setting value. Only used if first param isnt array]
   * @return [array] 
   */
  public function updateSettings($settings, $data = '')
  {
    if( !is_array($settings) && $data !== '' )
      $settings = array( $settings => $data );

    $this->settings = $this->settings->setSettings($settings);
    $this->setSettings( $this->settings );
  }


  /**
   * Get current settings
   * @return [array] 
   */
  public function getSettings()
  {
    return $this->settings;
  }


  /**
   * Put settings into Timber Context
   * @return [array] 
   */
  private function setSettings($settings)
  {
    //
  }

}
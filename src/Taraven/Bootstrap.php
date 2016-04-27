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

    echo "<pre>";
    print_r(Timber);
    echo "</pre>";

    // Processing settings & use Setting Object
    $this->setSettings( new Setting($settings) );

    new Wordpress\EnqueueStyles($this->settings);
    new Wordpress\EnqueueScripts($this->settings);

    // Loading filters
    new Wordpress\Filter\PostGallery();
    // new Wordpress\Filter\BodyClass();
    new Wordpress\Filter\SanitizeFileName();

    // Initializing Taraven
    $taraven = new Taraven($this->settings);
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
    
    $this->setSettings( $settings );
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
   * Store Setting and put settings into Timber Context
   * @param  Setting Object/Array $settings [<description>]
   * @return [array] 
   */
  private function setSettings($settings)
  {
    if( is_object($settings) )
      $this->settings = $settings;

    else if( is_array($settings) )
      $this->settings = $this->settings->setSettings($settings);
  }
}
<?php
namespace Taraven\Wordpress;


class EnqueueScripts
{
  private $js = array();

  /**
   * [description]
   * @param Setting Object $settings [description]
   */
  public function __construct($settings)
  {
    $settings = $settings->getSettings();

    // jQuery must first load
    if( $settings['google-jquery'] != false )
      array_push($this->js, 'https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js');
    
    // Setting class variable
    $this->js = array_merge($this->js, $settings['js']);
    
    if( $settings['google-maps'] != false )
      array_push($this->js,
        'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false',
        'lib/google.maps.js'
        );

    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
  }


  public function enqueue()
  {
    $assets = \Taraven\Functions::assets('js');

    foreach ($this->js AS $key => $script) {
      if( preg_match('/^(http|https|\/\/)/',$script) )
        wp_enqueue_script( md5($script), $script, array(), '1.0.0', true );
      else
        wp_enqueue_script( md5($script), $assets . '/' . $script, array(), '1.0.0', true );
    }
  }

}
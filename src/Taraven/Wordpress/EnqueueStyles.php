<?php
namespace Taraven\Wordpress;


class EnqueueStyles
{
  private $css;

  /**
   * [description]
   * @param Setting Object $settings [description]
   */
  public function __construct($settings)
  {

    $css = $settings->getSettings();
    $this->css = $css['css'];

    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
  }


  public function enqueue()
  {
    $assets = \Taraven\Functions::assets('css');

    foreach ($this->css AS $key => $style) {
      if( preg_match('/^(http|https|\/\/)/',$style) )
        wp_enqueue_style( md5($style), $style );
      else
        wp_enqueue_style( md5($style), $assets . '/' . $style );      
    }
  }

}
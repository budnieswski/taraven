<?php
namespace Taraven;


class Functions
{
  
  /*
  * Forca uma subcategoria a usar o template da pai
  */
  public static function get_archive_template() {  
    $category = get_queried_object();

    $parent_id = $category->category_parent;

    $templates = array();
    
    if ( $parent_id == 0 ) {
        // Use default values from get_category_template()
        $templates[] = "{$category->slug}/index";
        $templates[] = "{$category->term_id}/index";
        $templates[] = "archive-{$category->slug}";
        $templates[] = "archive-{$category->term_id}";
        
    } else {
        // Create replacement $templates array
        $parent = get_category( $parent_id );

        // Current first
        $templates[] = "{$category->slug}/index";
        $templates[] = "{$category->term_id}/index";
        $templates[] = "archive-{$category->slug}";
        $templates[] = "archive-{$category->term_id}";

        // Parent second
        $templates[] = "{$parent->slug}/archive-{$category->slug}";
        $templates[] = "{$parent->slug}/archive-{$category->term_id}";
        $templates[] = "{$parent->slug}/archive";
        $templates[] = "{$parent->term_id}/archive-{$category->slug}";
        $templates[] = "{$parent->term_id}/archive-{$category->term_id}";
        $templates[] = "{$parent->term_id}/archive";
    }

    // Default
    $templates[] = 'archive';

    // Check file to be include/render for twig
    foreach ($templates AS $template) {
      if( file_exists(TEMPLATEPATH . "/views/{$template}.twig") ){
        $return = $template;
        break;
      }
    } 

    return $return;
  }

  public static function getCurrentCatID($post='') {
    global $wp_query;
    
    if( is_category() ) {
      $cat_ID = get_query_var('cat');     
    } else {
      $dd = get_the_category(get_the_ID());
      $cat_ID = $dd[0]->term_id;
    }

    return $cat_ID;
  }

  /*
  * Forca uma single a usar o template da categoria
  */
  public static function get_single_template() {  
    $category = get_category(getCurrentCatID());

    $templates = array();

    if ( $category->category_parent != 0 ) {
      // Still get primary category
      while ($category->category_parent != 0) {
        $category = get_category($category->category_parent);
      }
    }
    
    $templates[] = "{$category->slug}/single";
    $templates[] = "{$category->term_id}/single";

    // Default
    $templates[] = "single";

    // Check file to be include/render for twig
    foreach ($templates AS $template) {
      if( file_exists(TEMPLATEPATH . "/views/{$template}.twig") ){
        $return = $template;
        break;
      }
    }

    return $return;
  }

  /*
  * Forca uma page a usar o template especifico ou default
  */
  public static function get_page_template() {
    global $post;

    $templates = array();
    $templates[] = "page-{$post->post_name}";
    $templates[] = "page-{$post->ID}";

    // Default
    $templates[] = "page";

    // Check file to be include/render for twig
    foreach ($templates AS $template) {
      if( file_exists(TEMPLATEPATH . "/views/{$template}.twig") ){
        $return = $template;
        break;
      }
    }

    return $return;
  }

  /*
  * Retorna a categoria atual
  */
  public static function get_current_category() {
    global $wp_query;
    global $post;

    if( is_category() ) {
      $cat = (array) get_category( get_query_var('cat') );

    } elseif( is_single() ) {
      $cat = (array) current( get_the_category($post->ID) );

    } else {
      // Nothing found
      return array();
    }

    // Seting options
    $cat['id']      = $cat['term_id'];
    $cat['title']   = $cat['name'];
    $cat['link']    = get_category_link($cat['term_id']);

    return $cat;
  }

  /*
  * Atalho para funcao do YOAST
  */
  public static function breadcrumb ($show_home=false) {
    if ( !is_home() || !is_front_page() || $show_home ) {
      if ( function_exists('yoast_breadcrumb') ) {
          yoast_breadcrumb('<p id="breadcrumbs">','</p>');
      }
    }
  }

}
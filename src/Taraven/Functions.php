<?php
namespace Taraven;


class Functions
{

  public static function assets($item = '')
  {
    $url = get_template_directory_uri() . "/assets";
    return $url . '/'. $item;
  }

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

  /*
  * Forca uma single a usar o template da categoria
  */
  public static function get_single_template() {  
    $category = get_category(Functions::getCurrentCatID());

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

  /*
  * Pega os itens da galeria de um post, com todos os tamanhos
  */
  public static function get_gallery_images($post_id) {

    // Get gallery from post/page, without html
    $gallery = get_post_gallery($post_id, false);
    if( empty($gallery) ) return false;

    // Get just gallery itens ID
    $gallery = explode(',', $gallery['ids']);

    // Set up an empty array for the links.
    $return = array();

    // Get the intermediate image sizes and add the full size to the array. 
    $sizes = get_intermediate_image_sizes();
    $sizes[] = 'full';

    // Loop through each of image.
    foreach( $gallery AS $image_id ) {
      // If not viewing an image attachment page, continue
      if( !wp_attachment_is_image($image_id) ) continue;

      // Set up an empty array for the links.
      $image_sizes = array();
      
      // Loop through each of the image sizes.
      foreach( $sizes AS $size ):
        // Get the image source, width, height, and whether it's intermediate
        $image = wp_get_attachment_image_src( $image_id, $size );

        if( !empty($image) && ($image[3]==true || $size=='full') )
          $image_sizes[$size] = array(
            'src'       => $image[0],
            'width'     => $image[1],
            'height'    => $image[2]
          );
      endforeach;

      $return[] = $image_sizes;
    }

    return $return;
  }

  /*
  * Retorna a galeria criada pelo Advanced Custom Fields
  */
  public static function acf_gallery ($nameCF='banner') {

    // Verifica se o Advanced Custom Fields esta ativado
    if (!function_exists('get_field')) { return false; }
    
    $images = get_field($nameCF,'option');

    if( !empty($images) ):
      foreach ($images AS $image) {
        echo "<li>";
        if ($image['description']) {
          echo "<a href=\"{$image['description']}\">";
            echo "<img src=\"{$image['url']}\" alt=\"{$image['alt']}\" title=\"{$image['title']}\" />";
          echo "</a>";
        } else {
          echo "<img src=\"{$image['url']}\" alt=\"{$image['alt']}\" title=\"{$image['title']}\" />";
        }
        echo "</li>";
      }
    endif;
  }

  /*
  * This method return VIDEO URL, already for embed
  */
  public static function put_video ($link) {

    if( ereg("youtube", $link) ) {

      preg_match('/(\?v=|\/\d\/|\/embed\/|\/v\/|\.be\/)([a-zA-Z0-9\-\_]+)/', $link, $url);
      return "http://www.youtube.com/embed/".$url[2];
      
    } elseif( ereg("vimeo", $link) ) {
      
      preg_match('/(\d{4,10}+)/', $link, $url);
      return "http://player.vimeo.com/video/".$url[0];

    }

    return false;
  }

  /*
  * Sitemap (404)
  */
  public static function get_sitemap() {
    $spp_sitemap = '';
    $published_posts = wp_count_posts('post');

    $args = array(
      'exclude' => '', /* ID of categories to be excluded, separated by comma */
      'post_type' => 'post',
      'post_status' => 'publish'
    );
    $cats = get_categories($args);
    foreach ($cats as $cat) :
      $spp_sitemap .= '<div class="box">';
      $spp_sitemap .= '<h3><a href="'.get_category_link( $cat->term_id ).'">'.$cat->cat_name.'</a></h3>';
      $spp_sitemap .= '<ul>';

      query_posts('posts_per_page=-1&cat='.$cat->cat_ID);
      while(have_posts()) {
        the_post();
        $category = get_the_category();
        /* Only display a post link once, even if it's in multiple categories */
        if ($category[0]->cat_ID == $cat->cat_ID) {
          $spp_sitemap .= '<li class="cat-list"><a href="'.get_permalink().'" rel="bookmark">'.get_the_title().'</a></li>';
        }
      }
      $spp_sitemap .= '</ul>';
      $spp_sitemap .= '</div>';
    endforeach;

    $pages_args = array(
      'exclude' => '', /* ID of pages to be excluded, separated by comma */
      'post_type' => 'page',
      'post_status' => 'publish'
    ); 

    $spp_sitemap .= '<div class="box"><h3>Pages</h3>';
    $spp_sitemap .= '<ul>';
    $pages = get_pages($pages_args); 
    foreach ( $pages as $page ) :
      $spp_sitemap .= '<li class="pages-list"><a href="'.get_page_link( $page->ID ).'" rel="bookmark">'.$page->post_title.'</a></li>';
    endforeach;
    $spp_sitemap .= '<ul></div>';

    return $spp_sitemap;
  }

  /*
  * Pagination
  */
  public static function pagination($txt_previus=null, $txt_next=null) {

      if( is_singular() )
          return;

      global $wp_query;

      /** Stop execution if there's only 1 page */
      if( $wp_query->max_num_pages <= 1 )
          return;

      $paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
      $max   = intval( $wp_query->max_num_pages );

      /** Add current page to the array */
      if ( $paged >= 1 )
          $links[] = $paged;

      /** Add the pages around the current page to the array */
      if ( $paged >= 3 ) {
          $links[] = $paged - 1;
          $links[] = $paged - 2;
      }

      if ( ( $paged + 2 ) <= $max ) {
          $links[] = $paged + 2;
          $links[] = $paged + 1;
      }

      echo '<ul>' . "\n";

      /** Previous Post Link */
      if ( get_previous_posts_link() )
          printf( '<li class="prev">%s</li>' . "\n", get_previous_posts_link($txt_previus) );

      /** Link to first page, plus ellipses if necessary */
      if ( ! in_array( 1, $links ) ) {
          $class = 1 == $paged ? ' class="active"' : '';

          printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );

          if ( ! in_array( 2, $links ) )
              echo '<li>…</li>';
      }

      /** Link to current page, plus 2 pages in either direction if necessary */
      sort( $links );
      foreach ( (array) $links as $link ) {
          $class = $paged == $link ? ' class="active"' : '';
          printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
      }

      /** Link to last page, plus ellipses if necessary */
      if ( ! in_array( $max, $links ) ) {
          if ( ! in_array( $max - 1, $links ) )
              echo '<li>…</li>' . "\n";

          $class = $paged == $max ? ' class="active"' : '';
          printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $max );
      }

      /** Next Post Link */
      if ( get_next_posts_link() )
          printf( '<li class="next">%s</li>' . "\n", get_next_posts_link($txt_next) );

      echo '</ul>' . "\n";
  }


  /* @trash
  * Other trash functions
  *************************************************************************************
  */

  // Funcao que exibe H1 se for HOME, e text se for outras paginas
  public static function logo_seo ($text) {
      $return = (is_home() || is_front_page())
          ? "<h1>{$text}</h1>"
          : $text;
      echo $return;
  }

  // Retorna o DDD & telefone separados em array
  public static function splitPhone ($phone) {
    preg_match('/\(\s*[0-9]{2}\s*\)/', $phone, $matches);
    $ddd = $matches[0];
    $phone = trim(substr($phone, strpos($phone, $ddd)+strlen($ddd) ));
    return array($ddd, $phone);
    // return array('ddd'=>$ddd, 'phone'=>$phone);
  }


  public static function getExcerpt($post_or_id='', $sizeExcerpt = 35 , $excerpt_more = ' [...]') {

    if( is_object($post_or_id) && !empty($post_or_id) ) {
      $postObj = $post_or_id;
    }
    else {
      $postObj = get_post($post_or_id);
    }

    $raw_excerpt = $text = $postObj->post_excerpt;
    if( $text=="" ) {
      $text = $postObj->post_content;
      $text = strip_shortcodes( $text );
      $text = str_replace(']]>', ']]&gt;', $text);
      $text = strip_tags($text);
      $text = apply_filters('the_content', $text);

      $excerpt_length = apply_filters('excerpt_length', $sizeExcerpt);

      // don't automatically assume we will be using the global "read more" link provided by the theme
      // $excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
      $words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);

      if( count($words) > $excerpt_length ) {
        array_pop($words);
        $text = implode(' ', $words);
        $text = $text . $excerpt_more;
      } else {
        $text = implode(' ', $words);
      }
    }
    return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
  }
  //
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

  // Check if page is direct child
  public static function is_child($page_id) { 
      global $post; 
      if( is_page() && ($post->post_parent == $page_id) ) {
         return true;
      } else { 
         return false; 
      }
  }

  // Check if page is an ancestor
  public static function is_ancestor($post_id) {
      global $wp_query;
      $ancestors = $wp_query->post->ancestors;
      if ( in_array($post_id, $ancestors) ) {
          return true;
      } else {
          return false;
      }
  }


  public static function key_change($array, $keymap) {
    
    $accumulate = array();
    foreach ($array AS $key => $value) {
      $new_key = $keymap[$key];
      $accumulate[ $new_key ] = $value;
    }
    return $accumulate;
  }

}
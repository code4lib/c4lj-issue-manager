<?php
/*
Plugin Name: Issue Manager
Plugin URI: http://xplus3.net/2008/09/26/issue-manager/
Description: Allows an editor to publish an "issue", which is to say, all pending posts with a given category. Until a category is published, all posts with that category will remain in the pending state.
Version: 1.4.3
Author: Jonathan Brinley
Author URI: http://xplus3.net/
*/
  
function issue_manager_manage_page(  ) {
  if ( function_exists('add_submenu_page') ) {
    $page = add_submenu_page( 'edit.php', 'Manage Issues', 'Issues', 'publish_posts', 'manage-issues', 'issue_manager_admin' );
    add_action("admin_print_scripts-$page", 'issue_manager_scripts');
  }
}
function issue_manager_admin(  ) {
  $published = get_option( 'im_published_categories' );
  $unpublished = get_option( 'im_unpublished_categories' );
  $categories = get_categories( 'orderby=name&hierarchical=0&hide_empty=0&exclude=1' );
  
  // Make sure the options exist
  if ( $published === FALSE ) { $published = array(); update_option( 'im_published_categories', $published ); }
  if ( $unpublished === FALSE ) { $unpublished = array(); update_option( 'im_unpublished_categories', $unpublished ); }
  
  // See if we have GET parameters
  $cat_ID = isset($_GET['cat_ID'])?$_GET['cat_ID']:null;
  $action = isset($_GET['action'])?$_GET['action']:null;
    
  if ( $cat_ID ) {
    $cat_ID = (int)$cat_ID;
    switch($action) {
      case "list":
        include_once('im_article_list.php');
        break;
      case "publish":
        $post_IDs = isset($_GET['posts'])?$_GET['posts']:null;
        $pub_time['mm'] = isset($_GET['mm'])?$_GET['mm']:null;
        $pub_time['jj'] = isset($_GET['jj'])?$_GET['jj']:null;
        $pub_time['aa'] = isset($_GET['aa'])?$_GET['aa']:null;
        $pub_time['hh'] = isset($_GET['hh'])?$_GET['hh']:null;
        $pub_time['mn'] = isset($_GET['mn'])?$_GET['mn']:null;
        if ( $post_IDs ) issue_manager_publish($cat_ID, $post_IDs, $pub_time, $published, $unpublished);
        include_once('im_admin_main.php');
        break;
      case "unpublish":
        issue_manager_unpublish($cat_ID, $published, $unpublished);
        include_once('im_admin_main.php');
        break;
      case "ignore":
        // stop tracking the cat_ID
        $key = array_search($cat_ID, $published);
        if ( FALSE !== $key ) {
          array_splice($published, $key, 1);
          update_option( 'im_published_categories', $published );
        }
        $key = array_search($cat_ID, $unpublished);
        if ( FALSE !== $key ) {
          array_splice($unpublished, $key, 1);
          update_option( 'im_unpublished_categories', $unpublished );
        }
        include_once('im_admin_main.php');
        break;
      default:
        include_once('im_admin_main.php');
        break;
    }
  } else {
    include_once('im_admin_main.php');
  }
}

function issue_manager_publish( $cat_ID, $post_IDs, $pub_time, &$published, &$unpublished ) {
  // take the category out of the unpublished list
  $key = array_search( $cat_ID, $unpublished );
  if ( FALSE !== $key ) {
    array_splice( $unpublished, $key, 1 );
    update_option( 'im_unpublished_categories', $unpublished );
  }
  if ( !in_array( $cat_ID, $published ) ) {
    // add to the published list
    $published[] = $cat_ID;
    sort($published);
    update_option( 'im_published_categories', $published );
    
    // see if we have a valid publication date/time
    $publish_at = strtotime( $pub_time['aa'].'-'.$pub_time['mm'].'-'.$pub_time['jj'].' '.$pub_time['hh'].':'.$pub_time['mn'] );
    
    if ( !$publish_at ) {
      $publish_at = strtotime(current_time('mysql'));
    }
    
    // $post_IDs should have all pending posts' IDs in the category
    $counter = 0;
    foreach ( explode(',',$post_IDs) as $post_ID ) {
      $post_ID = (int)$post_ID;
      $post = get_post( $post_ID );
      // set the date to about the appropriate time, keeping a small gap so posts stay in order
      wp_update_post( array(
        'ID' => $post->ID,
        'post_date' => date( 'Y-m-d H:i:s', $publish_at-($counter+1) ),
        'post_date_gmt' => '',
        'post_status' => 'publish'
      ) );
      $counter++;
    }
  }
}

function issue_manager_unpublish( $cat_ID, &$published, &$unpublished ) {
  // take the category out of the published list
  $key = array_search( $cat_ID, $published );
  if ( FALSE !== $key ) {
    array_splice( $published, $key, 1 );
    update_option( 'im_published_categories', $published );
  }
  if ( !in_array( $cat_ID, $unpublished ) ) {
    // add to the unpublished list
    $unpublished[] = $cat_ID;
    sort( $unpublished );
    update_option( 'im_unpublished_categories', $unpublished );
    
    // change all published posts in the category to pending
    $posts = get_posts( "numberposts=-1&post_status=publish,future&category=$cat_ID" );
    foreach ( $posts as $post ) {
      wp_update_post( array(
        'ID' => $post->ID,
        'post_status' => 'pending'
      ) );
    }
  }
}

function issue_manager_publish_intercept( $post_ID ) {
  $unpublished = get_option( 'im_unpublished_categories' );
  $publishable = TRUE;
  // check if post is in an unpublished category
  foreach ( get_the_category($post_ID) as $cat ) {
    if ( in_array( $cat->cat_ID, $unpublished ) ) {
      $publishable = FALSE;
      break;
    }
  }
  // if post is in an unpublished category, change its status to 'pending' instead of 'publish'
  if ( !$publishable ) {
    wp_update_post( array(
      'ID' => $post_ID,
      'post_status' => 'pending'
    ) );
  }
}

function issue_manager_activation(  ) {
  // if option records don't already exist, create them
  if ( !get_option( 'im_published_categories' ) ) {
    add_option( 'im_published_categories', array() );
  }
  if ( !get_option( 'im_unpublished_categories' ) ) {
    add_option( 'im_unpublished_categories', array() );
  }
}
function issue_manager_deactivation(  ) {
  // they don't have to exist to be deleted
  delete_option( 'im_published_categories' );
  delete_option( 'im_unpublished_categories' );
}
function issue_manager_scripts(  ) {
  wp_enqueue_script( "jquery-ui-sortable", path_join(WP_PLUGIN_URL, basename( dirname( __FILE__ ) )."/jquery-ui-sortable-1.5.2.js"), array( 'jquery' ), '1.5.2' );
  wp_enqueue_script( "im_sort_articles", path_join(WP_PLUGIN_URL, basename( dirname( __FILE__ ) )."/im_sort_articles.js"), array( 'jquery' ) );
}

add_action('admin_menu', 'issue_manager_manage_page');
add_action('publish_post', 'issue_manager_publish_intercept');


// Register hooks for activation/deactivation.
register_activation_hook( __FILE__, 'issue_manager_activation' );
register_deactivation_hook( __FILE__, 'issue_manager_deactivation' );
<?php
/**
 * Plugin Name: جت لاگین
 * Plugin URI: https://melad.ir
 * Description: احراز هویت پیامکی جهت ثبت نام و ورود به وبسایت، در ساده ترین و سریعترین حالت ممکن.
 * Version: 1.0.0
 * Author: میلاد
 * Author URI: https://melad.ir
 * WC requires at least: 6.0.0
 * WC tested up to: 9.3.3
 */

if (! defined('ABSPATH')) {
  header('Location: https://melad.ir/');
  exit;
}

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
  require_once dirname(__FILE__) . '/vendor/autoload.php';
}


if (! defined('MSA_VERSION')) {
  define('MSA_VERSION', '1.0.0');
}

if (! defined('MSA_URL')) {
  define('MSA_URL', plugins_url('', __FILE__));
}

if (! defined('MSA_DIR')) {
  define('MSA_DIR', dirname(__FILE__));
}

if (! defined('MSA_PLUGIN_FILE')) {
  define('MSA_PLUGIN_FILE', __FILE__);
}

include_once('includes/helpers/general.php');
MSA\Mel_Sms_Auth::get_instance();

function my_login_logo_url_title() {
  return 'Your Site Name and Info';
}

add_filter( 'login_headertext', 'my_login_logo_url_title' );

function my_login_logo_url() {
  return home_url();
}
add_filter( 'login_headerurl', 'my_login_logo_url' );


// echo wp_login_url();

// function my_login_redirect( $redirect_to, $request, $user ) {
// 	//is there a user to check?
// 	if ( isset( $user->roles ) && is_array( $user->roles ) ) {
// 		//check for admins
// 		if ( in_array( 'administrator', $user->roles ) ) {
// 			// redirect them to the default place
// 			return $redirect_to;
// 		} else {
// 			return home_url();
// 		}
// 	} else {
// 		return $redirect_to;
// 	}
// }

// add_filter( 'login_redirect', 'my_login_redirect', 10, 3 );

// $current_url = home_url($_SERVER['REQUEST_URI']);
// echo $_SERVER['HTTP_REFERER'] ;


// var_dump(get_page_by_path('mel-auth')->ID);

// var_dump( $_GET['redirect_to']);
// var_dump($_REQUEST);
// $_SERVER['HTTP_REFERER']=null;



    // Redirect to https login if forced to use SSL
  //   if (force_ssl_admin() && !is_ssl()) {
  //     if (0 === strpos($_SERVER['REQUEST_URI'], 'http')) {
  //         wp_redirect(set_url_scheme($_SERVER['REQUEST_URI'], 'https'));
  //         exit();
  //     } else {
  //         wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
  //         exit();
  //     }
  // }

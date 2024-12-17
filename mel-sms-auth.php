<?php

/**
 * Plugin Name: Jet Login
 * Plugin URI: https://melad.ir
 * Description: جت لاگین، احراز هویت پیامکی جهت ثبت نام و ورود به وبسایت، در ساده ترین و سریعترین حالت ممکن.
 * Version: 1.0.8
 * Author: میلاد
 * Author URI: https://melad.ir
 * WC requires at least: 6.0.0
 * WC tested up to: 9.3.3
 */

use MSA\Classes\Git_Plugin_Updater;
use MSA\Classes\Plugin_Updater;

if (! defined('ABSPATH')) {
  header('Location: https://melad.ir/');
  exit;
}


if (! defined('SITE_URL_WITHOUT_HTTP')) {
$site_url_without_http = preg_replace("(^https?://)", "", site_url() );
define('SITE_URL_WITHOUT_HTTP',$site_url_without_http);
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

// ---------------------------------------- Git Updater ----------------------------------------
require 'plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
  'https://github.com/meladsharafi/Wordpress-Jet-Login',
  __FILE__,
  'Wordpress-Jet-Login'
);
//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('main');

//Optional: If you're using a private repository, specify the access token like this:
// $myUpdateChecker->setAuthentication('');
// ------------------------------------------------------------------------------------------------

// if (is_admin()) {
//   define('GH_REQUEST_URI', 'https://github.com/meladsharafi/Wordpress-Jet-Login.git');
//   define('GHPU_USERNAME', 'meladsharafi');
//   define('GHPU_REPOSITORY', 'Wordpress-Jet-Login');
//   define('GHPU_AUTH_TOKEN', 'YOUR_GITHUB_ACCESS_TOKEN');

//   include_once('includes/Classes/Plugin_Updater.php');
//   $updater = new Git_Plugin_Updater(__FILE__);
//   $updater->init();
// }

// global $pagenow;
// var_dump($pagenow);

// function my_custom_login_logo() {
//   echo '<style type="text/css">
//       h1 a { background-image:url('.get_option('msa_auth_form_logo').') !important; }
//   </style>';
// }

// add_action('login_head', 'my_custom_login_logo');


// function my_login_logo_url_title()
// {
//   return get_bloginfo();
// }
// add_filter('login_headertext', 'my_login_logo_url_title');


// function my_login_logo_url()
// {
//   return home_url();
// }
// add_filter('login_headerurl', 'my_login_logo_url');


// function magicalendar_get_event_page( $content ) {
//   global $post;
//    if ($post->post_title == ' dc') {
//         $single_template = MSA_DIR . '/dc.php';
//    }
//    return $single_template;
// }
// add_filter( 'the_content', 'magicalendar_get_event_page' );


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

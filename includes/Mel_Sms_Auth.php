<?php

/**
 * Kyzen Theme bootstraps
 * 
 * @package MSA
 */

namespace MSA;

use Melipayamak\MelipayamakApi;
use MSA\Traits\Singleton;

defined('ABSPATH') || exit;

class Mel_Sms_Auth
{
  use Singleton;
  private $msa_option_feilds = [
    'otp_expire_seconds' => 'msa_otp_expire_seconds'
  ];

  private $msa_post_type_name = 'msa_users_otp';
  private $msa_otp_expire_secounds;
  private $smsSoap;
  private $bodyId; // 228021
  private $auth_page_slug = 'mel-auth';

  public  function __construct()
  {

    $this->msa_otp_expire_secounds = null != get_option($this->msa_option_feilds['otp_expire_seconds']) ? get_option($this->msa_option_feilds['otp_expire_seconds']) : 120;
    $this->set_hooks();

    $username = get_option('msa_sms_panel_username'); //09124326535
    $password = get_option('msa_sms_panel_password'); // 'Y57BA'
    $api = new MelipayamakApi($username, $password);
    $smsRest = $api->sms();
    $this->smsSoap = $api->sms('soap');
    $to = '09383079900';
    $from = get_option('msa_sms_panel_sender_number'); // '50002710032653'
    $text = 'تست افزونه لاگین ساده وردپرسی';
    $this->bodyId = get_option('msa_sms_panel_text_pattern');
  }

  protected function set_hooks()
  {
    //create page whene plugin activated
    register_activation_hook(MSA_PLUGIN_FILE, [$this, 'create_auth_page']);
    // add_filter('the_title', [$this, 'hiden_auth_page_title'], 10, 2);

    add_action('wp_enqueue_scripts', [$this, 'register_styles']);
    add_action('plugins_loaded', [$this, 'is_user_logged_in_hooks']); //is_user_logged_in() is a pluggable function and not yet available when your plugin is included. You have to wait for the action plugins_loaded:
    add_action('admin_menu', [$this, 'admin_menu']);
    add_action('admin_init', [$this, 'register_setting_feilds']);
    add_action('wp_ajax_nopriv_auth_ajax_request_handler', [$this, 'auth_ajax_request_process']);
    add_action('init', [$this, 'msa_post_type']);

    // add_filter('theme_page_templates', [$this, 'wp_template_register'], 10, 4);
    add_filter('login_url', [$this, 'custom_login_url'], 10, 3);
    add_filter('plugin_action_links_' . plugin_basename(MSA_PLUGIN_FILE), [$this, 'add_setting_link_to_plugin_action']);

    add_shortcode('mel-sms-auth', [$this, 'view_template_by_shortcode']);
  }

  public function is_user_logged_in_hooks()
  {
    if (is_user_logged_in()) return;
    // add_action('wp_footer', [$this, 'disable_browser_devtools']);
    add_action('wp_enqueue_scripts', [$this, 'register_scripts']);
    // add_action('wp_head', [$this, 'view_template']);
    add_filter('script_loader_tag', [$this, 'add_module_to_script_tag'], 10, 3);
  }

  public function register_styles()
  {
    wp_enqueue_style('mel-sms-auth-style', MSA_URL . '/assets/css/main.css', [],  filemtime(MSA_DIR . '/assets/css/main.css'));
  }

  public function register_scripts()
  {
    wp_enqueue_script('mel-sms-auth-js', MSA_URL . '/assets/js/app.js', ['jquery'], filemtime(MSA_DIR . '/assets/js/app.js'), true);
    wp_localize_script('mel-sms-auth-js', 'msaJsVar', ['otpExpireSecounds' => $this->msa_otp_expire_secounds],);
  }

  function add_module_to_script_tag($tag, $handle, $src)
  {
    // if not your script, do nothing and return original $tag
    // check to add special js file by 'wp_enqueue_script'
    if ('mel-sms-auth-js' === $handle) {
      // change the script tag by adding type="module" and return it.
      $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
    }

    return $tag;
  }

  function admin_menu()
  {
    add_options_page('احراز هویت پیامکی', 'احراز هویت پیامکی', 'manage_options', 'mel_sms_auth', [$this, 'view_auth_admin_setting'], 9);
  }

  public function view_template_by_shortcode($atts = [], $content = null, $tag = '')
  {
    ob_start();
    $this->view_template($atts['view']);
    return ob_get_clean();
  }

  public function view_template($shortcut_parameter)
  {
    //$args array send to templates
    $args['view-modal-form'] = true;

    //load auth form template by default / 'view' parameter
    if ($shortcut_parameter == '') {
      $template_name = 'auth-form';
    }

    if ($shortcut_parameter == 'form-in-single-page') {
      $template_name = 'auth-form';
      $args['view-modal-form'] = false;
    }

    if ($shortcut_parameter == 'btn-show-auth-form') {
      $template_name = 'btn-show-auth-form';
    }

    if ($shortcut_parameter == 'form-in-single-page') {

      $template_file = MSA_DIR . '/templates/' . $template_name . '.php';
      if (file_exists($template_file)) {
        load_template($template_file, true, $args);
      }
    }
  }

  public function view_auth_admin_setting()
  {
    $file = MSA_DIR . '/templates/auth_admin_setting.php';
    if (file_exists($file)) {
      load_template($file, true);
    }
    echo "موجودی پیامک:" . intval($this->smsSoap->getCredit());
  }

  public function register_setting_feilds()
  {
    //register our settings
    register_setting('msa-settings-group', 'msa_otp_expire_seconds');
    register_setting('msa-settings-group', 'msa_auth_form_logo');
    register_setting('msa-settings-group', 'msa_auth_form_terms-url');
    register_setting('msa-settings-group', 'msa_sms_panel_username');
    register_setting('msa-settings-group', 'msa_sms_panel_password');
    register_setting('msa-settings-group', 'msa_sms_panel_sender_number');
    register_setting('msa-settings-group', 'msa_sms_panel_text_pattern');
  }
  public function create_auth_page()
  {

    $new_page = array(
      'post_type'     => 'page',         // Post Type Slug eg: 'page', 'post'
      'post_title'    => 'فرم عضویت | ورود',  // Title of the Content
      'post_content'  => '[mel-sms-auth view=form-in-single-page]',  // Content
      'post_status'   => 'publish',      // Post Status
      'post_author'   => 1,          // Post Author ID
      'post_name'     => $this->auth_page_slug,      // Slug of the Post
      // 'page_template' => $this->mel_sms_auth_templates[0]
    );

    if (!get_page_by_path($this->auth_page_slug, OBJECT, 'page')) { // Check If Page Not Exits
      $new_page_id = wp_insert_post($new_page);
    }
  }

  // public function hiden_auth_page_title($title, $id)
  // {
  //   if (get_post_field('post_name') == $this->auth_page_slug) {
  //     return '';
  //   }
  //   return $title;
  // }


  public function custom_login_url($login_url, $redirect, $force_reauth = false)
  {

    $login_url = site_url($this->auth_page_slug, 'login');
    if (! empty($redirect)) {
      $login_url = add_query_arg('redirect_to', urlencode($redirect), $login_url);
    }
    return $login_url;
  }


  // ========================================================================================Ajax Handler
  public function auth_ajax_request_process()
  {
    // check_ajax_referer('msa_ajax_nonce', 'msa-nonce');
    

    // if (!isset($_POST['formData']['msaNonce']) || wp_verify_nonce($_POST['formData']['msaNonce'], 'msa_ajax_nonce')) {

    //   $response['authResponse'] = [
    //     'authStep'             => 'invalidNonce',
    //     'message'              => 'درخواست نامعتبر است.',
    //   ];
    //   wp_send_json($response, 401);
    //   return;
    // }

    // check_ajax_referer('ajax-login-nonce', 'auth_ajax_request_handler');
    $user_ip = get_user_ip();

    $phone_number = $_POST['formData']['phoneNumber'];
    $input_otp_code = $_POST['formData']['inputOtpCode'];
    $js_auth_step = $_POST['formData']['jsAuthStep'];
    $redirect_link = get_url_query_value($_SERVER['HTTP_REFERER'], 'redirect_to');
    $otp_code = rand(1000, 9999);
    $otp_auth_sms = $otp_code; // ."\n". '@' . preg_replace('#^https?://#i', 'www.', get_site_url()) . ' #' . $otp_code;

    $query = new \WP_Query([
      'post_type' => $this->msa_post_type_name,
      'post_status' => 'draft',
      'title' =>  $phone_number
    ]);

    // var_dump( $this->smsSoap->sendByBaseNumber($otp_auth_sms, $phone_number, $this->bodyId));
    // return  ;

    // ============================If user UnAvailable in post type
    if (!$query->have_posts()) {
      $sms_send_state = $this->smsSoap->sendByBaseNumber($otp_auth_sms, $phone_number, $this->bodyId);
      if (intval($sms_send_state) > 100) {
        $post_array = [
          'post_type' => $this->msa_post_type_name,
          'post_title' => $phone_number
        ];
        $post_id = wp_insert_post($post_array); //Inserts or update a post. Return The post ID on success. The value 0 or WP_Error on failure.

        add_post_meta($post_id, 'msa_otp_user_ip', $user_ip);
        add_post_meta($post_id, 'msa_otp_code',  $otp_code);
        add_post_meta($post_id, 'msa_otp_create_time', time());


        $response['authResponse'] = [
          'authStep'             => 'otpSend',
          'message'              => 'کد تایید ارسال شد.',
        ];
        wp_send_json($response, 200);
        return;
      }

      $response['authResponse'] = [
        'authStep'             => 'otpSendFail',
        'message'              => 'خطا در ارسال پیامک.',
      ];
      wp_send_json($response, 401);
      return;
    }
    $query->the_post();
    $post_id = get_the_ID();
    $otp_create_time = get_post_meta($post_id, 'msa_otp_create_time', true);

    // ============================If OTP Code Expire 
    if ($otp_create_time + $this->msa_otp_expire_secounds < time()) {

      $sms_send_state = $this->smsSoap->sendByBaseNumber($otp_auth_sms, $phone_number, $this->bodyId);
      if (intval($sms_send_state) > 100) {
        // wp_delete_post($post_id);
        update_post_meta($post_id, 'msa_otp_user_ip', $user_ip);
        update_post_meta($post_id, 'msa_otp_code',  $otp_code);
        update_post_meta($post_id, 'msa_otp_create_time', time());
        $response['authResponse'] = [
          'authStep'             => 'otpTimeExpire',
          'message'              => 'کد تایید ارسال شد.',
        ];
        wp_send_json($response, 200); //401
        return;
      }
      $response['authResponse'] = [
        'authStep'             => 'otpSendFail',
        'message'              => 'خطا در ارسال پیامک.',
      ];
      wp_send_json($response, 401);
      return;
    }

    // ============================If OTP Code No Expire AND User Enter Phone Number Again
    if ($js_auth_step == 'enterPhoneNumber') {
      $response['authResponse'] = [
        'authStep'             => 'otpWait',
        'message'              => 'کد تایید به تازگی ارسال شده، لحظاتی بعد مجددا امتحان کنید.',
      ];
      wp_send_json($response, 200); //401
      return;
    }

    $otp_code = get_post_meta($post_id, 'msa_otp_code', true);
    // ============================If Invalid OTP Code
    if ($input_otp_code != $otp_code && $js_auth_step == 'enterOtpCode') {
      $response['authResponse'] = [
        'authStep'             => 'otpInvalid',
        'message'              => 'کد وارد شده اشتباه است.',
      ];
      wp_send_json($response, 401);
      return;
    }

    // ============================Create And Login New User
    $user_exist = get_user_by('login', $phone_number);
    if (!$user_exist) {
      $pass = password_generate(8);
      $user_id = wp_insert_user([
        'user_login'     =>  apply_filters('pre_user_login', $phone_number),
        'user_pass'      =>  apply_filters('pre_user_pass', $pass),
        'user_nicename'  =>  apply_filters('pre_user_nicename',  $phone_number),
      ]);
      // ============================login new user
      if (!is_wp_error($user_id)) {
        $response['authResponse'] = [
          'authStep'             => 'successLoginNewUser',
          'message'              => 'با موفقیت وارد شدید، در حال انتقال به سایت...',
          'redirectLink'         => $redirect_link,
        ];
        wp_set_auth_cookie($user_id);
        wp_send_json($response, 200);
        return;
      }
    }

    // ============================Login Exist User
    wp_set_auth_cookie($user_exist->ID);
    $response['authResponse'] = [
      'authStep'             => 'successLoginExistUser',
      'message'              => 'با موفقیت وارد شدید، در حال انتقال به سایت...',
      'redirectLink'         => $redirect_link,
    ];
    wp_send_json($response, 200);
    return;
  }

  public function add_setting_link_to_plugin_action($links)
  {

    $url = get_admin_url() . "options-general.php?page=mel_sms_auth";
    $settings_link = '<a href="' . $url . '">' . __('Settings') . '</a>';
    $links[] = $settings_link;
    return $links;
  }


  function msa_post_type()
  {

    $labels = array(
      'name'                  => _x('MSA User OTP', 'Post type general name', 'textdomain'),
      'singular_name'         => _x('MSA Users OTP', 'Post type singular name', 'textdomain'),
      'menu_name'             => _x('MSA User OTP', 'Admin Menu text', 'textdomain'),
      'name_admin_bar'        => _x('MSA Users OTP', 'Add New on Toolbar', 'textdomain'),
      'add_new'               => __('Add New', 'textdomain'),
      'add_new_item'          => __('Add New MSA Users OTP', 'textdomain'),
      'new_item'              => __('New MSA Users OTP', 'textdomain'),
      'edit_item'             => __('Edit MSA Users OTP', 'textdomain'),
      'view_item'             => __('View MSA Users OTP', 'textdomain'),
      'all_items'             => __('All MSA User OTP', 'textdomain'),
      'search_items'          => __('Search MSA User OTP', 'textdomain'),
      'parent_item_colon'     => __('Parent MSA User OTP:', 'textdomain'),
      'not_found'             => __('No MSA User OTP found.', 'textdomain'),
      'not_found_in_trash'    => __('No MSA User OTP found in Trash.', 'textdomain'),
      'featured_image'        => _x('MSA Users OTP Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain'),
      'set_featured_image'    => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain'),
      'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain'),
      'use_featured_image'    => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain'),
      'archives'              => _x('MSA Users OTP archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain'),
      'insert_into_item'      => _x('Insert into MSA Users OTP', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain'),
      'uploaded_to_this_item' => _x('Uploaded to this MSA Users OTP', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain'),
      'filter_items_list'     => _x('Filter MSA User OTP list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain'),
      'items_list_navigation' => _x('MSA User OTP list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain'),
      'items_list'            => _x('MSA User OTP list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain'),
    );

    $args = array(
      'labels'             => $labels,
      'public'             => false,
      'publicly_queryable' => false,
      'show_ui'            => false,
      'show_in_menu'       => false,
      'query_var'          => false,
      // 'capabilities' => ['create_posts' => false],
      // 'rewrite'            => array('slug' => 'book'),
      'capability_type'    => 'post',
      'has_archive'        => false,
      'hierarchical'       => false,
      'menu_position'      => null,
      // 'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
    );

    register_post_type($this->msa_post_type_name, $args);
  }


  /**
   * Include required core files used in admin and on the frontend.
   */
  public function includes()
  {
    $this->frontend_includes();
  }

  /**
   * Include required frontend files.
   */
  public function frontend_includes()
  {
    // include_once INSURANCE_DIR  . '/includes/template-hooks.php';
  }

  /**
   * Function used to Init WooCommerce Template Functions - This makes them pluggable by plugins and themes.
   */
  public function include_template_functions() {}


  function disable_browser_devtools()
  {
    if (is_super_admin()) {
      return;
    } ?>
    <script type="text/javascript">
      document.addEventListener('contextmenu', (e) => e.preventDefault());
      document.body.classList.add('select-none ');

      function ctrlShiftKey(e, keyCode) {
        return e.ctrlKey && e.shiftKey && e.keyCode === keyCode.charCodeAt(0);
      }
      document.onkeydown = (e) => {
        // Disable F12, Ctrl + Shift + I, Ctrl + Shift + J, Ctrl + U
        if (
          event.keyCode === 123 ||
          ctrlShiftKey(e, 'I') ||
          ctrlShiftKey(e, 'J') ||
          ctrlShiftKey(e, 'C') ||
          (e.ctrlKey && e.keyCode === 'U'.charCodeAt(0))
        )
          return false;
      };
    </script>
<?php }
}

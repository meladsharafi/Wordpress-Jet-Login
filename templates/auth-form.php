<?php
// $custom_logo_id = get_theme_mod('custom_logo');
// $image = wp_get_attachment_image_src($custom_logo_id, 'full');
// echo $image[0];

if (is_user_logged_in()) {
  wp_redirect(get_edit_profile_url());
}
// add_action('wp_head', 'view_templates');

// function view_templates(){

?>

<div class="container__auth-form  m-0 <?php echo  $args['view-modal-form'] ?  'hidden' : '' ?> w-72 md:w-80 flex-col mx-auto fixed top-5 md:top-[20%] right-1/2 translate-x-1/2 z-[9999] select-none">
  <form id="auth-form" class="!space-y-0 relative p-5 md:p-7 flex flex-col gap-3 bg-white shadow-lg rounded-xl overflow-hidden duration-1000" action="" method="post" data-url="<?php echo admin_url('admin-ajax.php') ?>">

    <?php if ($args['view-modal-form']): ?>
      <p class="btn-close__auth-form absolute top-0 right-0 px-2 pt-1 bg-gray- text-red-700 font-bold rounded-xl cursor-pointer">&times;</p>
    <?php endif ?>

    <span class="text-gray-700 text-base">عضویت | ورود</span>

    <div class="mx-auto text-center max-h-">
      <?php if (get_custom_logo()): ?>
        <a class="decoration-md" href="<?php echo get_site_url() ?>">
          <img class="max-h-20 md:max-h-28" src="<?php echo esc_url(wp_get_attachment_image_src(get_theme_mod('custom_logo'), 'full')[0]) ?>" alt="">
        </a>
      <?php elseif (!empty(get_option('msa_auth_form_logo'))): ?>
        <a href="<?php echo get_site_url() ?>">
          <img class="max-h-28" src="<?php echo get_option('msa_auth_form_logo') ?>" alt="">
        </a>
      <?php else: ?>
        <a class="font-semibold decoration-none" href="<?php echo get_home_url() ?>"><?php echo get_bloginfo() ?></a>
        <span class="block"><?php echo get_bloginfo('description') ?></span>
      <?php endif ?>
    </div>

    <p class="phone-number-view__auth-form hidden py-2 bg-gray-100 rounded-md text-center text-gray-400"></p>

    <input class="phone-number__auth-form py-2 text-base bg-gray-100 rounded-md text-center" type="tel" name="phone-number" maxlength="11" placeholder="شماره موبایل را وارد کنید" autofocus>

    <div class="container-otp-code__auth-form hidden items-center gap-3">
      <p class="whitespace-nowrap text-base">کد تایید:</p>
      <input class="otp-code__auth-form w-full py-2 text-base text-center bg-gray-100 rounded-md " type="tel" name="otp-code" maxlength="4">
    </div>

    <div class="container-btn__auth-form relative">
      <button id="btn-submit__auth-form" class="btn-main text-base opa">ارسال کد</button>
      <svg class="loading-spinner__auth-form w-5 absolute left-5 top-1/2 transform -translate-y-1/2 opacity-0 z-[1]" version="1.1" viewBox="25 25 50 50">
        <circle class="stroke-current text-white text-opacity-30" cx="50" cy="50" r="20" fill="none" stroke-width="8" stroke-linecap="round" stroke-dashoffset="0" stroke-dasharray="200, 300">
        </circle>
        <circle class="stroke-current text-white" cx="50" cy="50" r="20" fill="none" stroke-width="8" stroke-linecap="round" stroke-dashoffset="0" stroke-dasharray="100, 200">
          <animateTransform attributeName="transform" attributeType="XML" type="rotate" from="0 50 50" to="360 50 50" dur="2.5s" repeatCount="indefinite"></animateTransform>
          <animate attributeName="stroke-dashoffset" values="0;-30;-124" dur="1.25s" repeatCount="indefinite"></animate>
          <animate attributeName="stroke-dasharray" values="0,200;110,200;110,200" dur="1.25s" repeatCount="indefinite"></animate>
        </circle>
      </svg>

    </div>

    <div id="footer__auth-form" class="flex flex-col gap-3">
      <a href="<?php echo empty(get_option('msa_auth_form_terms-url')) ? '' : get_option('msa_auth_form_terms-url')  ?>"
        class="terms-link__auth-form w-fit mx-auto text-center text-xs mb-0 underline underline-offset-4">قوانین و مقررات</a>
      <span id="countdown__auth-form" class="text-base hidden"></span>
      <p class="status__auth-form hidden !mb-0 text-base" data-auth-message="status"></p>
    </div>
    <?php wp_nonce_field('msa_ajax_nonce', 'msa-nonce') ?>
  </form>

</div>


<div class="background__auth-form !m-0 fixed inset-0 bg-gray-100 z-[9998]"></div>
<?php if ($args['view-modal-form']): ?>
  <div class="backdrop__auth-form  hidden !m-0  fixed inset-0 bg-black opacity-50 z-40"></div>
<?php endif ?>

<script type="text/javascript">
  var root = document.documentElement;
  root.className += ' overflow-hidden';
</script>
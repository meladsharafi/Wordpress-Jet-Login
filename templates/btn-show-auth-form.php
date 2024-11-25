<?php if (!is_user_logged_in()): ?>

  <!-- <p class="btn-show__auth-form w-fit mx-auto py-2 px-4 btn-main">
    عضویت | ورود
  </p> -->

  <a href="<?php echo esc_url( wp_login_url( get_permalink() ) ) ?>" class="w-fit mx-auto py-2 px-4 btn-main">عضویت | ورود</a>

<?php else: ?>

  <a href="<?php echo get_edit_profile_url() ?>" class="btn-show__auth-form w-fit mx-auto py-2 px-4 btn-main">داشبورد</a>
  
<?php endif ?>
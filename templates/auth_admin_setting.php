<div class="wrap">
  <h1>تنظیمات "افزونه احراز هویت پیامکی ثبت نام و ورود" وردپرس</h1>
  <p>در حال حاضر افزونه فقط با پنل پیامکی "ملی پیامک" کار می کند.</p>
  <form method="post" action="options.php">
    <?php settings_fields('msa-settings-group'); //The setting fields will know which settings your options page will handle. ?>
    <?php do_settings_sections('msa-settings-group'); //This function replaces the form-field markup in the form itself. ?>

    <table class="form-table">
      <h2>تنظیمات پلاگین:</h2>
      <tr valign="top">
        <th scope="row">اعتبار کد تایید به ثانیه:</th>
        <td><input type="number" name="msa_otp_expire_seconds" value="<?php echo esc_attr(get_option('msa_otp_expire_seconds')); ?>" /></td>
      </tr>

      <tr valign="top">
        <th scope="row">آدرس لوگو</th>
        <td><input type="text" name="msa_auth_form_logo" value="<?php echo esc_attr(get_option('msa_auth_form_logo')); ?>" /></td>
      </tr>
      <tr valign="top">
        <th scope="row">آدرس صفحه قوانین و مقررات</th>
        <td><input type="text" name="msa_auth_form_terms-url" value="<?php echo esc_attr(get_option('msa_auth_form_terms-url')); ?>" /></td>
      </tr>
    </table>

    <hr>
   
    <table class="form-table">
      <h2>اطلاعات پنل پیامکی:</h2>
      <tr valign="top">
        <th scope="row">نام کاربری پنل پیامکی</th>
        <td><input type="text" name="msa_sms_panel_username" value="<?php echo esc_attr(get_option('msa_sms_panel_username')); ?>" /></td>
      </tr>

      <tr valign="top">
        <th scope="row">رمز ورود پنل پیامکی</th>
        <td><input type="text" name="msa_sms_panel_password" value="<?php echo esc_attr(get_option('msa_sms_panel_password')); ?>" /></td>
      </tr>

      <tr valign="top">
        <th scope="row">شماره ارسال کننده</th>
        <td><input type="text" name="msa_sms_panel_sender_number" value="<?php echo esc_attr(get_option('msa_sms_panel_sender_number')); ?>" /></td>
      </tr>

      <tr valign="top">
        <th scope="row">کد متن الگوی خط خدماتی</th>
        <td><input type="text" name="msa_sms_panel_text_pattern" value="<?php echo esc_attr(get_option('msa_sms_panel_text_pattern')); ?>" /></td>
      </tr>
    </table>

    <?php submit_button(); ?>
  </form>
</div>
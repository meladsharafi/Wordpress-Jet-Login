class AuthForm {
  constructor($) {
    window.$ = $;
    this.jsAuthStep = "enterPhoneNumber";
    this.phoneNumber = "";
    this.otpEnterTry = 1;
    this.events();
    this.otpExpireSecound = msaJsVar.otpExpireSecounds; //this var passed from Mle_Sms_Auth.php Class
  }

  events() {
    $(".btn-close__auth-form").click(() => this.hideAuthForm());
    $(".backdrop__auth-form").click(() => this.hideAuthForm());

    $(".btn-show__auth-form").click(() => this.showAuthForm());
    $("#auth-form").on("submit", this.processAuthForm.bind(this));
  }

  // =======================================================================Phone number validation
  isValidPhoneNumber(phoneNumber) {
    // Define the regular expression pattern using RegExp object
    const pattern = new RegExp("^(\\+98|0)?9\\d{9}$");
    // Test the phone number against the pattern
    if (pattern.test(phoneNumber)) {
      this.phoneNumber = phoneNumber;
      return true;
    } else {
      return false;
    }
  }

  convertNumberToEn(text = "") {
    text = text.replace(/[٠-٩۰-۹]/g, (a) => a.charCodeAt(0) & 15);
    //  console.log(this.convertNumberToEn("۳٣۶٦۵any٥۵٤۶32٠۰"));
    return text;
  }

  // =======================================================================Form and Container
  showAuthForm() {
    $(".backdrop__auth-form").fadeIn(300);
    $(".container__auth-form").fadeIn(300);
  }

  hideAuthForm() {
    $(".backdrop__auth-form").fadeOut(300);
    $(".container__auth-form").fadeOut(300);
  }

  showOtpCodeContainer() {
    $(".container-otp-code__auth-form").removeClass("hidden");
    $(".container-otp-code__auth-form").addClass("flex");
    $("container-otp-code__auth-form").slideDown(300);
    $("input[name='otp-code']").focus();
  }

  hideOtpCodeContainer() {
    $(".container-otp-code__auth-form").slideUp(300);
  }

  showPhoneNumberView(phoneNumber) {
    $(".phone-number-view__auth-form").html(phoneNumber);
    $(".phone-number-view__auth-form").fadeIn(300);
    $(".phone-number-view__auth-form").removeClass('hidden');
  
  }

  hidePhoneNumberView() {}

  // =======================================================================State & Spiner
  showStatus(message) {
    $(".status__auth-form").html(message);
    $(".status__auth-form").slideDown(300);
    $(".status__auth-form").removeClass('hidden');
    $("input[name='phone-number']").focus();
  }

  hideStatus(message) {
    $(".status__auth-form").slideUp(300);
  }

  showLoadingSpinner(message) {
    $("#btn-submit__auth-form").attr('disabled','disabled');
    $("#btn-submit__auth-form").addClass('opacity-80');
    $(".loading-spinner__auth-form").removeClass('opacity-0');
    $(".loading-spinner__auth-form").fadeTo(500, 1);
  }
  
  hideLoadingSpinner(message) {
    // $(".loading-spinner__auth-form").addClass('opacity-0');
    $('#btn-submit__auth-form').removeAttr('disabled');
    $("#btn-submit__auth-form").removeClass('opacity-80');
    $(".loading-spinner__auth-form").fadeTo(500, 0);
  }

  isExpireOtp(message) {
    $("#footer__auth-form").prepend(
      "<a class='block btn-main' href='javascript:window.location.href=window.location.href'>تلاش مجدد</a>"
    );
    $("#countdown__auth-form").html(
      "کد تایید منقضی شد، لطفا دوباره امتحان کنید."
    );
    $(".container-otp-code__auth-form").slideUp(500, function () {
      $(this).remove();
    });
    $(".container-btn__auth-form").slideUp(500, function () {
      $(this).remove();
    });
    $(".phone-number-view__auth-form").slideUp(500, function () {
      $(this).remove();
    });
    this.hideStatus();
  }
  
  // ========================================================================================CountDown
  startCountdown(secound) {
    let timeRemaining = secound - 1;
    $("#countdown__auth-form").removeClass('hidden');
    document.getElementById("countdown__auth-form").textContent = "اعتبار کد: ";
    const intervalId = setInterval(() => {
      const minutesLeft = Math.floor(timeRemaining / 60);
      const secondsLeft = timeRemaining % 60;
      document.getElementById("countdown__auth-form").textContent =
        "اعتبار کد: " +
        `${minutesLeft}:${secondsLeft.toString().padStart(2, "0")}`;
      timeRemaining--;
      if (timeRemaining < 0) {
        clearInterval(intervalId);
        document.getElementById("countdown__auth-form").textContent =
          "کد تایید منقضی شد. ";
        this.isExpireOtp();
      }
    }, 1000);
  }

  // =======================================================================Form Submit AJAX
  processAuthForm(e) {
    var thisClass = this;
    e.preventDefault();
    this.hideStatus();
    this.showLoadingSpinner();
    
    // ====================================Check Phone Number
    if (
      thisClass.jsAuthStep != "enterOtpCode" &&
      !this.isValidPhoneNumber($("input[name='phone-number']").val())
    ) {
      this.showStatus("یک شماره تلفن معتبر وارد کنید.");
      this.hideLoadingSpinner();
      return;
    }
   
    // ====================================collect all data
    let data = {
      msaNonce: $("input[name='msa-nonce']").val(),
      phoneNumber: this.phoneNumber,
      inputOtpCode: $("input[name='otp-code']").val(),
      jsAuthStep: this.jsAuthStep,
    };

    $.ajax({
      url: $("#auth-form").data("url"),
      type: "post",
      datatype: "json",
      data: {
        action: "auth_ajax_request_handler",
        formData: data,
      },

      success: function (response) {
        console.log(thisClass.jsAuthStep);
        // thisClass.showStatus(response.authResponse.message);
        if (response.authResponse.authStep == "otpSend" || response.authResponse.authStep == "otpTimeExpire" ) {
          $(".phone-number__auth-form").fadeOut(0, function () {
            $(this).remove();
          });
          $("#btn-submit__auth-form").html('بررسی');
          $("#btn-submit__auth-form").attr('disabled');
          thisClass.startCountdown(thisClass.otpExpireSecound);
          // thisClass.showStatus(response.authResponse.message);
          thisClass.showPhoneNumberView(data.phoneNumber);
          thisClass.showOtpCodeContainer();
          thisClass.hideLoadingSpinner();
          thisClass.jsAuthStep = "enterOtpCode";
        }

        if (response.authResponse.authStep == "successLoginExistUser" || response.authResponse.authStep == "successLoginNewUser" ) {          
          $('.otp-code__auth-form').prop('disabled',true);
          setTimeout(()=>{
            window.location=response.authResponse.redirectLink;
          },2000);
        }

        thisClass.hideLoadingSpinner();
      },
      
      error: function (error) {
        // console.log(thisClass.jsAuthStep);
        console.log(error.responseJSON.authResponse.message);

        if (error.responseJSON.authResponse.authStep == "otpSendFail") {
          thisClass.showStatus(error.responseJSON.authResponse.message);
        }

        if (thisClass.jsAuthStep == "enterOtpCode") {
          thisClass.showStatus(error.responseJSON.authResponse.message);
          $("input[name='otp-code']").focus();
          thisClass.hideLoadingSpinner();
        }

        thisClass.hideLoadingSpinner();
        // error.responseJSON.formData.messsage;

        // $("#loading-svg").fadeOut(300);
        // console.log(error);
        // if (error.responseJSON != null) {
        //   $("#captcha-alert").html(error.responseJSON.captcha.message);
        //   $("#inp-captcha").focus().select();
        // }
      },
    });
  }
}

export default AuthForm;

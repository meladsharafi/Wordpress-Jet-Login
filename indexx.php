<?php // Silence is golden

// $smsRest->send($to,$from,$text);
// $smsSoap->sendByBaseNumber($text,$to,$bodyId);

// $smsRest->send($to,$from,$text);
// $smsSoap->sendByBaseNumber($text,$to,$bodyId);

// try {
//   $response = $sms->send($to, $from, $text);
//   $json = json_decode($response);
//   echo $json->Value; //RecId or Error Number 
// } catch (Exception $e) {
//   echo $e->getMessage();
// }

/*
۰ : نام کاربری یا رمز عبور اشتباه می باشد.
۲ : اعتبار کافی نمی باشد.
۳ : محدودیت در ارسال روزانه
۴ : محدودیت در حجم ارسال
۵ : شماره فرستنده معتبر نمی باشد.
۶ : سامانه در حال بروزرسانی می باشد.
۷ : متن حاوی کلمه فیلتر شده می باشد.
۹ : ارسال از خطوط عمومی از طریق وب سرویس امکان پذیر نمی باشد.
۱۰ : کاربر مورد نظر فعال نمی باشد.
۱۱ : ارسال نشده
۱۲ : مدارک کاربر کامل نمی باشد.
۱۴: متن حاوی لینک می باشد.
۱۵: ارسال به بیش از 1 شماره همراه بدون درج "لغو11" ممکن نیست.
35 : در REST به معنای وجود شماره در لیست سیاه مخاربرات می‌باشد.
*/
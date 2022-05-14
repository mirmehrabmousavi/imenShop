<?php

return [

    /*
     *
     * Shared translations.
     *
     */
    'title' => 'نصب کننده لاراول',
    'next' => 'قدم بعدی',
    'back' => 'قبلی',
    'finish' => 'نصب',
    'forms' => [
        'errorTitle' => 'خطاهای زیر رخ داد:',
    ],

    /*
     *
     * Home page translations.
     *
     */
    'welcome' => [
        'templateTitle' => 'خوش آمدید',
        'title'   => 'به نصب کننده خوش آمدید',
        'message' => 'به نصب آسان خوش آمدید .',
        'next'    => 'الزامات را بررسی کنید',
    ],

    /*
     *
     * Requirements page translations.
     *
     */
    'requirements' => [
        'title' => 'نیازمندی ها',
        'templateTitle' => 'مرحله 1 | نیازمندی های سرور',
        'next'    => 'مجوزها را بررسی کنید',
    ],

    /*
     *
     * Permissions page translations.
     *
     */
    'permissions' => [
        'title' => 'مجوز های دسترسی',
        'templateTitle' => 'مرحله 2 | مجوزها',
        'next' => 'محیط را پیکربندی کنید',
    ],

    /*
     *
     * Environment page translations.
     *
     */
    'environment' => [
        'menu' => [
            'templateTitle' => 'مرحله 3 | تنظیمات محیط',
            'title' => 'تنظیمات محیط',
            'desc' => 'لطفاً نحوه پیکربندی برنامه ها را انتخاب کنید <code>.env</code> فایل.',
            'wizard-button' => 'فرم نصب آسان',
            'classic-button' => 'ویرایشگر متن کلاسیک',
        ],
        'wizard' => [
            'templateTitle' => 'مرحله 3 | تنظیمات محیط | راهنما نصب',
            'title' => 'هدایت شد <code>.env</code> فایل',
            'tabs' => [
                'environment' => 'محیط',
                'database' => 'پایگاه داده',
                'application' => 'کاربرد',
            ],
            'form' => [
                'name_required' => 'نام محیط مورد نیاز است.',
                'app_name_label' => 'نام برنامه',
                'app_name_placeholder' => 'نام برنامه',
                'app_environment_label' => 'محیط برنامه',
                'app_environment_label_local' => 'محلی',
                'app_environment_label_developement' => 'توسعه',
                'app_environment_label_qa' => 'Qa',
                'app_environment_label_production' => 'تولید',
                'app_environment_label_other' => 'دیگر',
                'app_environment_placeholder_other' => 'وارد محیط خود شوید ...',
                'app_debug_label' => 'اشکال زدایی برنامه',
                'app_debug_label_true' => 'تایید',
                'app_debug_label_false' => 'رد',
                'app_log_level_label' => 'سطح گزارش برنامه',
                'app_log_level_label_debug' => 'اشکال زدایی',
                'app_log_level_label_info' => 'اطلاعات',
                'app_log_level_label_notice' => 'اطلاع',
                'app_log_level_label_warning' => 'هشدار',
                'app_log_level_label_error' => 'ارور',
                'app_log_level_label_critical' => 'بحرانی',
                'app_log_level_label_alert' => 'هشدار',
                'app_log_level_label_emergency' => 'اضطراری',
                'app_url_label' => 'آدرس برنامه',
                'app_url_placeholder' => 'آدرس برنامه',
                'db_connection_failed' => 'اتصال به پایگاه داده امکان پذیر نیست.',
                'db_connection_label' => 'اتصال به پایگاه داده',
                'db_connection_label_mysql' => 'mysql',
                'db_connection_label_sqlite' => 'sqlite',
                'db_connection_label_pgsql' => 'pgsql',
                'db_connection_label_sqlsrv' => 'sqlsrv',
                'db_host_label' => 'میزبان پایگاه داده',
                'db_host_placeholder' => 'میزبان پایگاه داده',
                'db_port_label' => 'بندر پایگاه داده',
                'db_port_placeholder' => 'بندر پایگاه داده',
                'db_name_label' => 'نام پایگاه داده',
                'db_name_placeholder' => 'نام پایگاه داده',
                'db_username_label' => 'نام کاربری پایگاه داده',
                'db_username_placeholder' => 'نام کاربری پایگاه داده',
                'db_password_label' => 'رمز عبور پایگاه داده',
                'db_password_placeholder' => 'رمز عبور پایگاه داده',

                'app_tabs' => [
                    'more_info' => 'اطلاعات بیشتر',
                    'broadcasting_title' => 'پخش ، ذخیره سازی ، جلسه ، آمپر و صف',
                    'broadcasting_label' => 'درایور پخش',
                    'broadcasting_placeholder' => 'درایور پخش',
                    'cache_label' => 'درایور حافظه پنهان',
                    'cache_placeholder' => 'درایور حافظه پنهان',
                    'session_label' => 'درایور session',
                    'session_placeholder' => 'درایور session',
                    'queue_label' => 'صف درایور',
                    'queue_placeholder' => 'صف درایور',
                    'redis_label' => 'درایور Redis',
                    'redis_host' => 'میزبان Redis',
                    'redis_password' => 'Redis رمز عبور',
                    'redis_port' => 'Redis پورت',

                    'mail_label' => 'ایمیل',
                    'mail_driver_label' => 'درایور ایمیل',
                    'mail_driver_placeholder' => 'درایور ایمیل',
                    'mail_host_label' => 'میزبان ایمیل',
                    'mail_host_placeholder' => 'میزبان ایمیل',
                    'mail_port_label' => 'پورت ایمیل',
                    'mail_port_placeholder' => 'پورت ایمیل',
                    'mail_username_label' => 'نام کاربری ایمیل',
                    'mail_username_placeholder' => 'نام کاربری ایمیل',
                    'mail_password_label' => 'رمز عبور ایمیل',
                    'mail_password_placeholder' => 'رمز عبور ایمیل',
                    'mail_encryption_label' => 'رمزگذاری ایمیل',
                    'mail_encryption_placeholder' => 'رمزگذاری ایمیل',

                    'pusher_label' => 'پوشر',
                    'pusher_app_id_label' => 'آیدی پوشر',
                    'pusher_app_id_palceholder' => 'آیدی پوشر',
                    'pusher_app_key_label' => 'key پوشر',
                    'pusher_app_key_palceholder' => 'key پوشر',
                    'pusher_app_secret_label' => 'secret پوشر',
                    'pusher_app_secret_palceholder' => 'secret پوشر',
                ],
                'buttons' => [
                    'setup_database' => 'راه اندازی پایگاه داده',
                    'setup_application' => 'راه اندازی برنامه',
                    'install' => 'نصب',
                ],
            ],
        ],
        'classic' => [
            'templateTitle' => 'مرحله 3 | تنظیمات محیط | ویرایشگر کلاسیک',
            'title' => 'ویرایشگر محیط کلاسیک',
            'save' => 'ذخیره .env',
            'back' => 'از فرم نصب آسان استفاده کنید',
            'install' => 'ذخیره و نصب کنید',
        ],
        'success' => 'تنظیمات فایل .env ذخیره شده است',
        'errors' => 'فایل .env ذخیره نمی شود ، لطفاً آن را به صورت دستی ایجاد کنید.',
    ],

    /*
     *
     * Final page translations.
     *
     */
    'final' => [
        'title' => 'تمام شد',
        'finished' => 'اپلیکیشن با موفقیت نصب شد.',
        'exit' => 'برای خروج اینجا را کلیک کنید',
    ],
];

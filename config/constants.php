<?php

return [

    // oregonpastduerent.com
    'oregonpastduerent_com' => [
        'company_name' => 'Oregon Past Due Rent',
        'company_address_1' => '901 Brutscher Street',
        'company_address_2' => 'Suite D112',
        'company_city' => 'Newberg',
        'company_state' => 'OR',
        'company_zip' => '97132',
        'company_phone' => '971-264-0170',
        'support_email' => 'help@oregonpastduerent.com',
        'portal_url' => 'https://oregonpastduerent.com',
        'post_office_name' => 'Newberg Post Office',
        'post_office_address' => '116 N Everest Rd, Newberg, OR 97132',
    ],

    // Mailing configuration
    'mailing' => [
        'cutoff_time' => env('MAILING_CUTOFF_TIME', '13:00'), // 1:00 PM PST in 24-hour format
        'cutoff_timezone' => env('MAILING_CUTOFF_TIMEZONE', 'America/Los_Angeles'), // PST timezone
    ],

];

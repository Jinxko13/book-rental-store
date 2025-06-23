<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cấu hình quản lý tồn kho
    |--------------------------------------------------------------------------
    |
    | Các thông số cấu hình cho việc quản lý tồn kho
    |
    */

    // Số lượng tối thiểu cho mỗi loại sách, mặc định là 10
    'min_book_quantity' => env('MIN_BOOK_QUANTITY', 10),

    // Thông báo đến staff không
    'staff_notifications' => env('INVENTORY_NOTIFY_STAFF', true),

    // Có gửi thông báo hay không
    'send_notifications' => env('INVENTORY_SEND_NOTIFICATIONS', true),

    // Email nhận thông báo tồn kho
    'additional_emails' => array_filter(explode(',', env('INVENTORY_ADDITIONAL_EMAILS', ''))),
];

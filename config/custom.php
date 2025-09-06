<?php

return [
    /**
     * Прамаметры для изображений
     */
    'image_params' => [
        'watermark_text' => env('WATERMARK_TEXT', 'bondari.ru'),
        'size_images' => env('IMAGE_SIZE_IMAGES', 900),
        'size_thumbnails' => env('IMAGE_SIZE_THUMBNAILS', 450),
    ],

    /**
     * Общие параметры shared
     */
    'orgFullName' => env('ORGFULLNAME'),
    'orgShortName' => env('ORGSHORTNAME'),
    'email' => env('MAIL_FROM_ADDRESS'),
    'phoneFull' => env('PHONE_FULL'),
    'phoneShort' => env('PHONE_SHORT'),
    'telegramBot' => env('TELEGRAM_BOT'),
    'max' => env('MAX'),
    'vk' => env('VK'),
    'ok' => env('OK'),
    'rutube' => env('RUTUBE'),
];

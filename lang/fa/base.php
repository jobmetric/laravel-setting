<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Base Setting Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during Setting for
    | various messages that we need to display to the user.
    |
    */

    "messages" => [
        "saved" => ":type با موفقیت ذخیره شد.",
    ],

    "entity_names" => [
        "setting" => "تنظیمات",
    ],

    "exceptions" => [
        "setting_class_invalid" => "کلاس باید از :expected ارث‌بری کند، داده‌شده: :given.",
    ],

    'events' => [
        'setting_store' => [
            'title' => 'ذخیره تنظیمات',
            'description' => 'هنگامی که تنظیمات ذخیره می‌شود، این رویداد فعال می‌شود.',
        ],

        'setting_forget' => [
            'title' => 'حذف تنظیمات',
            'description' => 'هنگامی که تنظیمات حذف می‌شود، این رویداد فعال می‌شود.',
        ],
    ],

];

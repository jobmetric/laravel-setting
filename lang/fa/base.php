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
        "saved" => ":type saved successfully.",
    ],

    "entity_names" => [
        "setting" => "Setting",
    ],

    "exceptions" => [
        "setting_class_invalid" => "Class must extend :expected, given: :given.",
    ],

    'events' => [
        'setting_store' => [
            'title' => 'Store setting',
            'description' => 'This event is fired when a setting is stored.',
        ],

        'setting_forget' => [
            'title' => 'Forget setting',
            'description' => 'This event is fired when a setting is forgotten.',
        ],
    ],

];

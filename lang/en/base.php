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
        "saved" => ":type created successfully.",
    ],

    "entity_names" => [
        "setting" => "Setting",
    ],

    'events' => [
        'setting_store' => [
            'title' => 'Setting Stored',
            'description' => 'This event is triggered when setting is stored.',
        ],

        'setting_forget' => [
            'title' => 'Metadata Forget',
            'description' => 'This event is triggered when setting is forget.',
        ],
    ],

];

<?php

return [
    'application_name' => env('GOOGLE_APPLICATION_NAME', 'MoM Telkom'),
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
    'scopes' => [
        \Google_Service_Calendar::CALENDAR_EVENTS,
    ],
    'access_type' => 'offline',
    'approval_prompt' => 'force',
];

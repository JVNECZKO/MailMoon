<?php

return [
    'default' => 'mailmoon',

    'accounts' => [
        'mailmoon' => [
            'host'          => env('IMAP_HOST', ''),
            'port'          => (int) env('IMAP_PORT', 993),
            'protocol'      => 'imap',
            'encryption'    => env('IMAP_ENCRYPTION', 'ssl'),
            'validate_cert' => false,
            'username'      => env('IMAP_USERNAME', ''),
            'password'      => env('IMAP_PASSWORD', ''),
            'authentication'=> env('IMAP_AUTH', 'login'),
            'folder'        => env('IMAP_SENT_FOLDER', 'Sent'),
            'timeout'       => 10,
        ],
    ],

    'options' => [
        'decoder' => [
            'message' => 'utf-8',
            'attachment' => 'utf-8',
            'subject' => 'utf-8'
        ]
    ],
];

<?php

return [
    'login' => [
        'error' => [
            'status' => 401,
            'message' => 'Incorrect login',
        ],
    ],
    'login_empty' => [
        'error' => [
            'message' => 'Please enter a username and password'
        ]
    ],
    'server' => [
        'error' => [
            'message' => 'Error with server'
        ]
    ],
    'access' => [
        'error' => [
            'status' => 400,
            'message' => 'You dont have access to this section',
        ],
    ],
    'max_shifts' => [
        'error' => [
            'status' => 400,
            'message' => 'Maximum number of shifts reached for this job',
        ],
    ],
    'exist' => [
        'error' => [
            'status' => 400,
            'message' => ' does not exist',
        ],
    ],
    'results' => [
        'error' => [
            'status' => 400,
            'message' => 'No results',
        ],
    ],
];
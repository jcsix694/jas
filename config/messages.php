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
            'message' => 'Does not exist',
        ],
    ],
    'results' => [
        'error' => [
            'status' => 400,
            'message' => 'No results',
        ],
    ],
    'application' => [
        'error' => [
            'status' => 400,
            'message' => 'You already have a shift'
        ]
    ],
    'applied' => [
        'error' => [
            'status' => 400,
            'message' => 'You already applied to this shift'
        ]
    ],
    'shift' => [
        'error' => [
            'status' => 400,
            'message' => 'You already have a shift so therefore you cannot view applications'
        ]
    ]
];

<?php

return [
    'tables' => [
        'group' => 'group',
        'groups' => 'groups',
        'users' => 'users',
        'shifts' => 'shifts',
        'statuses' => 'statuses',
        'applications' => 'applications',
        'jobs' => 'jobs',
        'admin' => 'admin',
        'job' => 'job',
    ],
    'fields' => [
        'id' => 'id',
        'name' => 'name',
        'username' => 'username',
        'email' => 'email',
        'description' => 'description',
        'start' => 'start',
        'password' => 'password',
        'end' => 'end',
        'hours_per_week' => 'hours_per_week',
        'pay_per_hour' => 'pay_per_hour',
        'monday' => 'monday',
        'tuesday' => 'tuesday',
        'wednesday' => 'wednesday',
        'thursday' => 'thursday',
        'friday' => 'friday',
        'saturday' => 'saturday',
        'sunday' => 'sunday',
        'vacant' => 'vacant',
        'date_applied' => 'date_applied',
        'date_updated' => 'date_updated',
        'group_id' => 'group_id',
        'group_name' => 'group_name',
        'worker_id' => 'worker_id',
        'shift_id' => 'shift_id',
        'status_id' => 'status_id',
        'admin_id' => 'admin_id',
        'user_id' => 'user_id',
        'job_id' => 'job_id',
        'owner_id'=>'owner_id',
        'no_shifts' => 'no_shifts',
        'list' => 'list',
        'workers' => 'workers',
    ],
    'values' => [
        'groups' => [
            'admin' => [
                'id' => 1,
                'name' => 'admin'
            ],
            'worker' => [
                'id' => 2,
                'name' => 'worker'
            ],
        ],
        'statuses' => [
            'pending' => [
                'id' => 1,
                'name' => 'pending',
            ],
            'rejected' => [
                'id' => 2,
                'name' => 'rejected',
            ],
            'approved' => [
                'id' => 3,
                'name' => 'approved',
            ],
        ],
    ],
];

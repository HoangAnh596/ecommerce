<?php
return [
    'module' => [
        [
            'title' => 'Article',
            'icon' => 'fa fa-file',
            'name' => ['post'],
            'subModule' => [
                [
                    'title' => 'Article Groups',
                    'route' => 'post/catalogue/index',
                ],
                [
                    'title' => 'Article',
                    'route' => 'post/index',
                ]
            ]
        ],
        [
            'title' => 'Member',
            'icon' => 'fa fa-user',
            'name' => ['user'],
            'subModule' => [
                [
                    'title' => 'Member Groups',
                    'route' => 'user/catalogue/index',
                ],
                [
                    'title' => 'Member',
                    'route' => 'user/index',
                ],
                [
                    'title' => 'Permission',
                    'route' => 'permission/index',
                ]
            ]
        ],
        [
            'title' => 'General configuration',
            'icon' => 'fa fa-file',
            'name' => ['language'],
            'subModule' => [
                [
                    'title' => 'Language',
                    'route' => 'language/index',
                ],
            ]
        ],
    ],
];

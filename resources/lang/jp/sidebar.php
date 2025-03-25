<?php
return [
    'module' => [
        [
            'title' => 'メンバー',
            'icon' => 'fa fa-user',
            'name' => ['user'],
            'subModule' => [
                [
                    'title' => 'メンバーグループ',
                    'route' => 'user/catalogue/index',
                ],
                [
                    'title' => 'メンバー',
                    'route' => 'user/index',
                ],
                [
                    'title' => '権限',
                    'route' => 'permission/index',
                ]
            ]
        ],
        [
            'title' => '記事',
            'icon' => 'fa fa-file',
            'name' => ['post'],
            'subModule' => [
                [
                    'title' => '記事グループ',
                    'route' => 'post/catalogue/index',
                ],
                [
                    'title' => '記事',
                    'route' => 'post/index',
                ]
            ]
        ],
        [
            'title' => '一般設定',
            'icon' => 'fa fa-file',
            'name' => ['language'],
            'subModule' => [
                [
                    'title' => '言語',
                    'route' => 'language/index',
                ],
            ]
        ],
    ],
];

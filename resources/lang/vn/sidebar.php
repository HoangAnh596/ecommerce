<?php
return [
    'module' => [
        [
            'title' => 'QL Sản phẩm',
            'icon' => 'fa fa-cube',
            'name' => ['product', 'attribute'],
            'subModule' => [
                [
                    'title' => 'QL Nhóm Sản phẩm',
                    'route' => 'product/catalogue/index',
                ],
                [
                    'title' => 'QL Sản phẩm',
                    'route' => 'product/index',
                ],
                [
                    'title' => 'QL Loại thuộc tính',
                    'route' => 'attribute/catalogue/index',
                ],
                [
                    'title' => 'QL Thuộc tính',
                    'route' => 'attribute/index',
                ],
            ]
        ],
        [
            'title' => 'QL Bài Viết',
            'icon' => 'fa fa-file',
            'name' => ['post'],
            'subModule' => [
                [
                    'title' => 'QL Nhóm Bài Viết',
                    'route' => 'post/catalogue/index',
                ],
                [
                    'title' => 'QL Bài Viết',
                    'route' => 'post/index',
                ]
            ]
        ],
        [
            'title' => 'QL Hình ảnh',
            'icon' => 'fa fa-image',
            'name' => ['gallery'],
            'subModule' => [
                [
                    'title' => 'QL Nhóm Hình ảnh',
                    'route' => 'gallery/catalogue/index',
                ],
                [
                    'title' => 'QL Hình ảnh',
                    'route' => 'gallery/index',
                ]
            ]
        ],
        [
            'title' => 'QL Thành viên',
            'icon' => 'fa fa-user',
            'name' => ['user', 'permission'],
            'subModule' => [
                [
                    'title' => 'QL Nhóm Thành Viên',
                    'route' => 'user/catalogue/index',
                ],
                [
                    'title' => 'QL Thành Viên',
                    'route' => 'user/index',
                ],
                [
                    'title' => 'QL Quyền',
                    'route' => 'permission/index',
                ]
            ]
        ],
        [
            'title' => 'Cấu hình chung',
            'icon' => 'fa fa-file',
            'name' => ['language', 'generate'],
            'subModule' => [
                [
                    'title' => 'QL Ngôn ngữ',
                    'route' => 'language/index',
                ],
                [
                    'title' => 'QL Module',
                    'route' => 'generate/index',
                ],
            ]
        ],
    ],
];

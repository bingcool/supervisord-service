<?php
return [
    'route_model' => 1, //1代表pathinfo,2代表普通url模式
    'default_route' => 'site/index',
    'app_namespace' => 'App',
    //'catch_handle' => '网站维护中',
    'session_start' => false,

    'supervisord' => [
        'path' => "/home/wwwroot/daemon/",
        'username' => 'bingcool',
        'password' => '123456'
    ],

    'components' => [
        'view' => [
            // 'is_destroy' => 1,//每次请求后是否销毁对象
            'class' => 'Swoolefy\Core\View',
        ],

        'log' => [
            'class' => 'Swoolefy\Tool\Log',
        ]
    ]
];
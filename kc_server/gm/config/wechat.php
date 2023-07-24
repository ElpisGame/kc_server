<?php

return [

    "officialAccount" => [
        'app_id' => 'wx27fede30fa80dcf4',
        'secret' => '5d9db7d88713ff7542085a722dfca096',
        'response_type' => 'array',
        //https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=150788392&lang=zh_CN
        //服务器监听
        'listen_token' => 'noway123'
    ],

    "payment" => [
        'app_id' => 'wx27fede30fa80dcf4',
        'mch_id' => '1575585431',
        'key' => '1879CD63D43E451A62476B65FE754C80',
        // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
        'cert_path' => base_path('cert/apiclient_cert.pem'),//'path/to/your/cert.pem', // XXX: 绝对路径！！！！
        'key_path' => base_path('cert/apiclient_key.pem'),//'path/to/your/key',      // XXX: 绝对路径！！！！
        //'notify_url' => route("api.wechat.notify"),// '默认的订单回调地址',     // 你也可以在下单时单独设置来想覆盖它
    ],

    //这里是小程序的
    "miniProgram" => [
        'app_id' => 'wx27fede30fa80dcf4',
        'secret' => '5d9db7d88713ff7542085a722dfca096',
        'response_type' => 'array',
    ]
];

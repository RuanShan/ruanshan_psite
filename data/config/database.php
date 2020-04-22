<?php
/**
 * 配置文件
 */

return [
    // 数据库类型
    'type'     => 'mysql',
    // 服务器地址
    'hostname' => '127.0.0.1',
    // 数据库名
    'database' => Env::get('database_name', 'cmf'),
    // 用户名
    'username' => Env::get('database_user', 'user'),
    // 密码
    'password' => Env::get('database_password', 'password'), 
    // 端口
    'hostport' => '3306',
    // 数据库编码默认采用utf8
    'charset'  => 'utf8mb4',
    // 数据库表前缀
    'prefix'   => 'cmf_',
    "authcode" => '4hdlVxuqmRVBRxD2io',
    //#COOKIE_PREFIX#
];

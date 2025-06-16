<?php

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: inc/conn.php
// 文件大小: 1817 字节

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: inc/conn.php
// 文件大小: 1599 字节
/**
 * 本文件功能: 数据库连接及全局配置
 * 版权声明: 保留发行权和署名权
 * 作者信息: 15058593138@qq.com
 */

// 数据库连接配置
$dbConfig = [
    'host' => 'localhost',
    'user' => 'bus2025_chalide',
    'pass' => 'b7sdRnXYbTfsSsns',
    'name' => 'bus2025_chalide',
    'port' => 3306,
    'charset' => 'utf8mb4'
];

// 连接数据库
function connectDB() {
    global $dbConfig;
    $conn = mysqli_connect(
        $dbConfig['host'], 
        $dbConfig['user'], 
        $dbConfig['pass'], 
        $dbConfig['name'], 
        $dbConfig['port']
    );
    
    if (!$conn) {
        die("数据库连接失败: " . mysqli_connect_error());
    }
    
    mysqli_set_charset($conn, $dbConfig['charset']);
    return $conn;
}

// 网站版本号，用于更新浏览器缓存
$version = '1.0.011' . date("YmdHis");

// 网站配置默认值
$siteConfig = [  
    'sitestop' => '<?php exit(); ?>',
    'siteName' => '公交查询系统',
    'cityName' => '杭州市',
    'pageSize' => 10,
    'maxUploadSize' => 2, // MB
    'allowedFileTypes' => 'jpg,jpeg,png,gif',
    'mapKey' => '' // 百度地图API密钥
];

// 默认管理员账号
$defaultAdmin = [
    'sitestop' => '<?php exit(); ?>',
    'username' => 'admin',
    'password' => md5('admin123'), // 默认密码：admin123
    'lastLogin' => ''
];

// 菜单配置
$menuConfig = [
    'main' => '首页',
    'zhan' => '站点列表',
    'xian' => '线路列表',
];

// 管理菜单配置
$adminMenuConfig = [
    'site' => '系统设置',
    'zhan' => '站点管理',
    'xian' => '线路管理',
    'pass' => '修改密码',
    'lgout' => '退出系统'
];
?>
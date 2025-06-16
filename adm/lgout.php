<?php

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: adm/lgout.php
// 文件大小: 485 字节

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: adm/lgout.php
// 文件大小: 267 字节
/**
 * 本文件功能: 退出登录页面
 * 版权声明: 保留发行权和署名权
 * 作者信息: 15058593138@qq.com
 */

// 清除登录信息
session_destroy();

// 跳转到登录页
echo '<script>location.href="adm.php?do=login";</script>';
exit;
?>

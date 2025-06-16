<?php

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: inc/head.php
// 文件大小: 6735 字节

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: inc/head.php
// 文件大小: 6517 字节
/**
 * 本文件功能: 公共头部
 * 版权声明: 保留发行权和署名权
 * 作者信息: 15058593138@qq.com
 */

// 引入配置文件
require_once './inc/conn.php';
require_once './inc/pubs.php';

// 读取网站设置
$siteConfigFile = './inc/site.json.php';
if (file_exists($siteConfigFile)) {
    $siteConfig = json_decode(file_get_contents($siteConfigFile), true);
} else {
    // 创建默认配置文件
    file_put_contents($siteConfigFile, json_encode($siteConfig, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

// 当前菜单
$current_do = isset($_GET['do']) ? $_GET['do'] : 'main';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $siteConfig['siteName']; ?> - <?php echo $siteConfig['cityName']; ?></title>
    <link rel="stylesheet" href="./inc/pubs.css?v=<?php echo $version; ?>">
    <script src="./inc/js.js?v=<?php echo $version; ?>"></script>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="header-row">
                <div class="header-title"><?php echo $siteConfig['siteName']; ?></div>
              
                <div class="header-links">
                <a href="index.php?do=xian" class="header-link">线路列表</a>
                <a href="index.php?do=zhan" class="header-link">站点列表</a>
                <button class="header-btn" onclick="location.href='index.php'">返回主页</button>
                </div>
            </div>
            
            <!-- 搜索栏 -->
            <div class="search-bar">
                <div class="search-tabs">
                    <div class="search-tab <?php echo ($current_do == 'main' || $current_do == 'xshow') ? 'active' : ''; ?>" data-target="line-search">线路查询</div>
                    <div class="search-tab <?php echo $current_do == 'zshow' ? 'active' : ''; ?>" data-target="station-search">站点查询</div>
                    <div class="search-tab <?php echo $current_do == 'hshow' ? 'active' : ''; ?>" data-target="transfer-search">换乘查询</div>
                </div>
                
                <div class="search-content">
                    <!-- 线路查询 -->
                    <div class="search-pane <?php echo ($current_do == 'main' || $current_do == 'xshow') ? 'active' : ''; ?>" id="line-search">
                        <form action="index.php" method="get" id="line-search-form" class="search-form">
                            <input type="hidden" name="do" value="xshow">
                            <div class="search-input-container">
                                <input type="text" class="search-input" name="keyword" placeholder="请输入线路名称..." id="line-search-input" autocomplete="off">
                            </div>
                            <button type="submit" class="search-btn">查询</button>
                        </form>
                    </div>
                    
                    <!-- 站点查询 -->
                    <div class="search-pane <?php echo $current_do == 'zshow' ? 'active' : ''; ?>" id="station-search">
                        <form action="index.php" method="get" id="station-search-form" class="search-form">
                            <input type="hidden" name="do" value="zshow">
                            <div class="search-input-container">
                                <input type="text" class="search-input" name="keyword" placeholder="请输入站点名称..." id="station-search-input" autocomplete="off">
                            </div>
                            <button type="submit" class="search-btn">查询</button>
                        </form>
                    </div>
                    
                    <!-- 换乘查询 -->
                    <div class="search-pane <?php echo $current_do == 'hshow' ? 'active' : ''; ?>" id="transfer-search">
                        <form action="index.php" method="get" id="transfer-search-form">
                            <input type="hidden" name="do" value="hshow">
                            <div style="display: flex; gap: 10px;">
                                <input type="text" class="search-input" name="start" placeholder="请输入起点站..." id="transfer-start-input" autocomplete="off">
                                <input type="text" class="search-input" name="end" placeholder="请输入终点站..." id="transfer-end-input" autocomplete="off">
                                <button type="submit" class="search-btn">查询</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <script>
            // 初始化搜索tab切换
            document.addEventListener('DOMContentLoaded', function() {
                var tabs = document.querySelectorAll('.search-tab');
                var panes = document.querySelectorAll('.search-pane');
                
                tabs.forEach(function(tab) {
                    tab.addEventListener('click', function() {
                        var target = this.getAttribute('data-target');
                        
                        // 移除所有active类
                        tabs.forEach(function(t) {
                            t.classList.remove('active');
                        });
                        panes.forEach(function(p) {
                            p.classList.remove('active');
                        });
                        
                        // 添加active类
                        this.classList.add('active');
                        document.getElementById(target).classList.add('active');
                    });
                });
                
                // 初始化搜索提示
                searchSuggestion('line-search-input', 'line', function(id, text) {
                    document.getElementById('line-search-form').submit();
                });
                
                searchSuggestion('station-search-input', 'station', function(id, text) {
                    document.getElementById('station-search-form').submit();
                });
                
                searchSuggestion('transfer-start-input', 'station', null);
                searchSuggestion('transfer-end-input', 'station', null);
            });
        </script>

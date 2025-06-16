<?php

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: index.php
// 文件大小: 4051 字节

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: index.php
// 文件大小: 3836 字节

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: index.php
// 文件大小: 3621 字节
/**
 * 本文件功能: 公交查询系统入口文件
 * 版权声明: 保留发行权和署名权
 * 作者信息: 15058593138@qq.com
 */

// 检查是否安装
if (!file_exists('./inc/site.json.php') || !file_exists('./inc/user.json.php')) {
    header('Location: install.php');
    exit;
}

// 获取操作参数
$do = isset($_GET['do']) ? $_GET['do'] : 'main';
$act = isset($_GET['act']) ? $_GET['act'] : '';

// AJAX请求处理
if ($act) {
    require_once './inc/conn.php';
    require_once './inc/pubs.php';
    require_once './inc/sqls.php';
    
    $db = new Sqls();
    
    switch ($act) {
        // 搜索提示
        case 'suggest':
            $keyword = isset($_POST['keyword']) ? safeFilter($_POST['keyword']) : '';
            $type = isset($_POST['type']) ? safeFilter($_POST['type']) : '';
            $data = [];
            
            if ($keyword && $type) {
                if ($type == 'line') {
                    // 线路搜索提示
                    $sql = "SELECT id, name FROM line WHERE name LIKE '%{$keyword}%' ORDER BY name LIMIT 10";
                    $data = $db->execute($sql);
                } elseif ($type == 'station') {
                    // 站点搜索提示
                    $sql = "SELECT zid, zhan FROM zhan WHERE zhan LIKE '%{$keyword}%' OR ping LIKE '%{$keyword}%' ORDER BY zhan LIMIT 10";
                    $data = $db->execute($sql);
                }
            }
            
            jsonReturn(0, 'success', $data);
            break;
            
        // 分页数据
        case 'page':
            $table = isset($_POST['table']) ? safeFilter($_POST['table']) : '';
            $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $pageSize = isset($_POST['pageSize']) ? intval($_POST['pageSize']) : 10;
            $keyword = isset($_POST['keyword']) ? safeFilter($_POST['keyword']) : '';
            $field = isset($_POST['field']) ? safeFilter($_POST['field']) : '';
            
            $where = '';
            if ($keyword && $field) {
                $where = "{$field} LIKE '%{$keyword}%'";
            }
            
            $offset = ($page - 1) * $pageSize;
            $total = $db->getCount($table, $where);
            $totalPages = ceil($total / $pageSize);
            
            $data = [];
            if ($table == 'line') {
                $sql = "SELECT id, name, type, start, end, comp FROM line";
                if ($where) {
                    $sql .= " WHERE {$where}";
                }
                $sql .= " ORDER BY id DESC LIMIT {$offset}, {$pageSize}";
                $data = $db->execute($sql);
            } elseif ($table == 'zhan') {
                $sql = "SELECT zid, zhan, ping, lng, lat FROM zhan";
                if ($where) {
                    $sql .= " WHERE {$where}";
                }
                $sql .= " ORDER BY zid DESC LIMIT {$offset}, {$pageSize}";
                $data = $db->execute($sql);
            }
            
            jsonReturn(0, 'success', [
                'data' => $data,
                'total' => $total,
                'totalPages' => $totalPages,
                'currentPage' => $page
            ]);
            break;
            
        default:
            jsonReturn(1, '未知操作');
            break;
    }
    
    exit;
}

// 引入公共头部
include_once './inc/head.php';

// 加载对应的模块文件
$file = './main/' . $do . '.php';
if (file_exists($file)) {
    include_once $file;
} else {
    include_once './main/main.php';
}

// 引入公共底部
include_once './inc/foot.php';
?>

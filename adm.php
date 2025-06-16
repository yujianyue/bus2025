<?php

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: adm.php
// 文件大小: 22364 字节

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: adm.php
// 文件大小: 22150 字节

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: adm.php
// 文件大小: 21936 字节
/**
 * 本文件功能: 管理员入口文件
 * 版权声明: 保留发行权和署名权
 * 作者信息: 15058593138@qq.com
 */

session_start();

// 引入配置文件
require_once './inc/conn.php';
require_once './inc/pubs.php';

// 检查是否安装
if (!file_exists('./inc/site.json.php') || !file_exists('./inc/user.json.php')) {
    header('Location: install.php');
    exit;
}

// 获取操作参数
$do = isset($_GET['do']) ? $_GET['do'] : 'login';
$act = isset($_GET['act']) ? $_GET['act'] : '';

// 读取用户配置
$userConfigFile = './inc/user.json.php';
if (file_exists($userConfigFile)) {
    $userConfig = json_decode(file_get_contents($userConfigFile), true);
} else {
    // 创建默认用户配置文件
    file_put_contents($userConfigFile, json_encode($defaultAdmin, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    $userConfig = $defaultAdmin;
}

// AJAX请求处理
if ($act) {
    require_once './inc/sqls.php';
    $db = new Sqls();
    
    switch ($act) {
        // 管理员登录
        case 'login':
            $username = isset($_POST['username']) ? safeFilter($_POST['username']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            
            if ($username == $userConfig['username'] && md5($password) == $userConfig['password']) {
                $_SESSION['admin'] = $username;
                // 更新最后登录时间
                $userConfig['lastLogin'] = date('Y-m-d H:i:s');
                file_put_contents($userConfigFile, json_encode($userConfig, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                jsonReturn(0, '登录成功');
            } else {
                jsonReturn(1, '用户名或密码错误');
            }
            break;
            
        // 修改密码
        case 'changepass':
            if (!isset($_SESSION['admin'])) {
                jsonReturn(1, '请先登录');
            }
            
            $oldpass = isset($_POST['oldpass']) ? $_POST['oldpass'] : '';
            $newpass = isset($_POST['newpass']) ? $_POST['newpass'] : '';
            $confirm = isset($_POST['confirm']) ? $_POST['confirm'] : '';
            
            if (md5($oldpass) != $userConfig['password']) {
                jsonReturn(1, '原密码错误');
            }
            
            if ($newpass != $confirm) {
                jsonReturn(1, '两次输入的新密码不一致');
            }
            
            // 更新密码
            $userConfig['password'] = md5($newpass);
            file_put_contents($userConfigFile, json_encode($userConfig, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            jsonReturn(0, '密码修改成功');
            break;
            
        // 系统设置
        case 'savesite':
            if (!isset($_SESSION['admin'])) {
                jsonReturn(1, '请先登录');
            }
            
            $siteName = isset($_POST['siteName']) ? safeFilter($_POST['siteName']) : '';
            $cityName = isset($_POST['cityName']) ? safeFilter($_POST['cityName']) : '';
            $pageSize = isset($_POST['pageSize']) ? intval($_POST['pageSize']) : 10;
            $mapKey = isset($_POST['mapKey']) ? safeFilter($_POST['mapKey']) : '';
            
            if (empty($siteName)) {
                jsonReturn(1, '网站名称不能为空');
            }
            
            if (empty($cityName)) {
                jsonReturn(1, '城市名称不能为空');
            }
            
            // 读取网站设置
            $siteConfigFile = './inc/site.json.php';
            if (file_exists($siteConfigFile)) {
                $siteConfig = json_decode(file_get_contents($siteConfigFile), true);
            } else {
                $siteConfig = [];
            }
            
            // 更新设置
            $siteConfig['siteName'] = $siteName;
            $siteConfig['cityName'] = $cityName;
            $siteConfig['pageSize'] = $pageSize;
            $siteConfig['mapKey'] = $mapKey;
            
            file_put_contents($siteConfigFile, json_encode($siteConfig, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            jsonReturn(0, '设置保存成功');
            break;
            
        // 添加站点
        case 'addzhan':
            if (!isset($_SESSION['admin'])) {
                jsonReturn(1, '请先登录');
            }
            
            $zhan = isset($_POST['zhan']) ? safeFilter($_POST['zhan']) : '';
            $lng = isset($_POST['lng']) ? safeFilter($_POST['lng']) : '';
            $lat = isset($_POST['lat']) ? safeFilter($_POST['lat']) : '';
            
            if (empty($zhan)) {
                jsonReturn(1, '站点名称不能为空');
            }
            
            // 检查站点是否已存在
            $exist = $db->getOne('zhan', 'zid', "zhan = '{$zhan}'");
            if ($exist) {
                jsonReturn(1, '站点已存在');
            }
            
            // 获取拼音
            $ping = getPinyin($zhan);
            
            // 添加站点
            $data = [
                'zhan' => $zhan,
                'ping' => $ping,
                'lng' => $lng,
                'lat' => $lat
            ];
            
            $id = $db->insert('zhan', $data);
            if ($id) {
                jsonReturn(0, '站点添加成功', ['id' => $id]);
            } else {
                jsonReturn(1, '站点添加失败');
            }
            break;
            
        // 修改站点
        case 'editzhan':
            if (!isset($_SESSION['admin'])) {
                jsonReturn(1, '请先登录');
            }
            
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $zhan = isset($_POST['zhan']) ? safeFilter($_POST['zhan']) : '';
            $lng = isset($_POST['lng']) ? safeFilter($_POST['lng']) : '';
            $lat = isset($_POST['lat']) ? safeFilter($_POST['lat']) : '';
            
            if (empty($id)) {
                jsonReturn(1, '站点ID不能为空');
            }
            
            if (empty($zhan)) {
                jsonReturn(1, '站点名称不能为空');
            }
            
            // 检查站点是否存在
            $exist = $db->getOne('zhan', 'zid', "zid = {$id}");
            if (!$exist) {
                jsonReturn(1, '站点不存在');
            }
            
            // 获取拼音
            $ping = getPinyin($zhan);
            
            // 更新站点
            $data = [
                'zhan' => $zhan,
                'ping' => $ping,
                'lng' => $lng,
                'lat' => $lat
            ];
            
            if ($db->update('zhan', $data, "zid = {$id}")) {
                jsonReturn(0, '站点修改成功');
            } else {
                jsonReturn(1, '站点修改失败');
            }
            break;
            
        // 删除站点
        case 'delzhan':
            if (!isset($_SESSION['admin'])) {
                jsonReturn(1, '请先登录');
            }
            
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            
            if (empty($id)) {
                jsonReturn(1, '站点ID不能为空');
            }
            
            // 检查站点是否存在
            $exist = $db->getOne('zhan', 'zid', "zid = {$id}");
            if (!$exist) {
                jsonReturn(1, '站点不存在');
            }
            
            // 检查站点是否被线路使用
            $sql = "SELECT id, name FROM line WHERE zlist LIKE '%-{$id}-%' OR flist LIKE '%-{$id}-%'";
            $usedLines = $db->execute($sql);
            
            if (!empty($usedLines)) {
                jsonReturn(1, '该站点已被线路使用，无法删除', ['lines' => $usedLines]);
            }
            
            // 删除站点
            if ($db->delete('zhan', "zid = {$id}")) {
                jsonReturn(0, '站点删除成功');
            } else {
                jsonReturn(1, '站点删除失败');
            }
            break;
            
        // 添加线路
        case 'addxian':
            if (!isset($_SESSION['admin'])) {
                jsonReturn(1, '请先登录');
            }
            
            $name = isset($_POST['name']) ? safeFilter($_POST['name']) : '';
            $type = isset($_POST['type']) ? safeFilter($_POST['type']) : '';
            $time = isset($_POST['time']) ? safeFilter($_POST['time']) : '';
            $start = isset($_POST['start']) ? safeFilter($_POST['start']) : '';
            $end = isset($_POST['end']) ? safeFilter($_POST['end']) : '';
            $comp = isset($_POST['comp']) ? safeFilter($_POST['comp']) : '';
            $fare = isset($_POST['fare']) ? safeFilter($_POST['fare']) : '';
            $note = isset($_POST['note']) ? safeFilter($_POST['note']) : '';
            $zlist = isset($_POST['zlist']) ? $_POST['zlist'] : '';
            $flist = isset($_POST['flist']) ? $_POST['flist'] : '';
            
            if (empty($name)) {
                jsonReturn(1, '线路名称不能为空');
            }
            
            // 检查线路是否已存在
            $exist = $db->getOne('line', 'id', "name = '{$name}'");
            if ($exist) {
                jsonReturn(1, '线路已存在');
            }
            
            // 处理正向站点列表
            $zStationIds = '';
            if (!empty($zlist)) {
                $zStations = explode("\n", trim($zlist));
                foreach ($zStations as $zStation) {
                    $zStation = trim($zStation);
                    if (!empty($zStation)) {
                        // 查找站点ID
                        $station = $db->getOne('zhan', 'zid', "zhan = '{$zStation}'");
                        if ($station) {
                            $zStationIds .= '-' . $station['zid'] . '-';
                        } else {
                            // 站点不存在，添加新站点
                            $ping = getPinyin($zStation);
                            $stationData = [
                                'zhan' => $zStation,
                                'ping' => $ping
                            ];
                            $stationId = $db->insert('zhan', $stationData);
                            $zStationIds .= '-' . $stationId . '-';
                        }
                    }
                }
            }
            
            // 处理反向站点列表
            $fStationIds = '';
            if (!empty($flist)) {
                $fStations = explode("\n", trim($flist));
                foreach ($fStations as $fStation) {
                    $fStation = trim($fStation);
                    if (!empty($fStation)) {
                        // 查找站点ID
                        $station = $db->getOne('zhan', 'zid', "zhan = '{$fStation}'");
                        if ($station) {
                            $fStationIds .= '-' . $station['zid'] . '-';
                        } else {
                            // 站点不存在，添加新站点
                            $ping = getPinyin($fStation);
                            $stationData = [
                                'zhan' => $fStation,
                                'ping' => $ping
                            ];
                            $stationId = $db->insert('zhan', $stationData);
                            $fStationIds .= '-' . $stationId . '-';
                        }
                    }
                }
            }
            
            // 添加线路
            $data = [
                'name' => $name,
                'type' => $type,
                'time' => $time,
                'gtime' => date('Y-m-d H:i:s'),
                'start' => $start,
                'end' => $end,
                'comp' => $comp,
                'note' => $note,
                'fare' => $fare,
                'zlist' => $zStationIds,
                'flist' => $fStationIds
            ];
            
            $id = $db->insert('line', $data);
            if ($id) {
                jsonReturn(0, '线路添加成功', ['id' => $id]);
            } else {
                jsonReturn(1, '线路添加失败');
            }
            break;
            
        // 修改线路
        case 'editxian':
            if (!isset($_SESSION['admin'])) {
                jsonReturn(1, '请先登录');
            }
            
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $name = isset($_POST['name']) ? safeFilter($_POST['name']) : '';
            $type = isset($_POST['type']) ? safeFilter($_POST['type']) : '';
            $time = isset($_POST['time']) ? safeFilter($_POST['time']) : '';
            $start = isset($_POST['start']) ? safeFilter($_POST['start']) : '';
            $end = isset($_POST['end']) ? safeFilter($_POST['end']) : '';
            $comp = isset($_POST['comp']) ? safeFilter($_POST['comp']) : '';
            $fare = isset($_POST['fare']) ? safeFilter($_POST['fare']) : '';
            $note = isset($_POST['note']) ? safeFilter($_POST['note']) : '';
            
            if (empty($id)) {
                jsonReturn(1, '线路ID不能为空');
            }
            
            if (empty($name)) {
                jsonReturn(1, '线路名称不能为空');
            }
            
            // 检查线路是否存在
            $exist = $db->getOne('line', 'id', "id = {$id}");
            if (!$exist) {
                jsonReturn(1, '线路不存在');
            }
            
            // 更新线路
            $data = [
                'name' => $name,
                'type' => $type,
                'time' => $time,
                'gtime' => date('Y-m-d H:i:s'),
                'start' => $start,
                'end' => $end,
                'comp' => $comp,
                'note' => $note,
                'fare' => $fare
            ];
            
            if ($db->update('line', $data, "id = {$id}")) {
                jsonReturn(0, '线路修改成功');
            } else {
                jsonReturn(1, '线路修改失败');
            }
            break;
            
        // 更新线路站点
        case 'updatestations':
            if (!isset($_SESSION['admin'])) {
                jsonReturn(1, '请先登录');
            }
            
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $type = isset($_POST['type']) ? safeFilter($_POST['type']) : '';
            $stations = isset($_POST['stations']) ? $_POST['stations'] : '';
            
            if (empty($id)) {
                jsonReturn(1, '线路ID不能为空');
            }
            
            if ($type != 'zlist' && $type != 'flist') {
                jsonReturn(1, '站点类型错误');
            }
            
            // 检查线路是否存在
            $exist = $db->getOne('line', 'id', "id = {$id}");
            if (!$exist) {
                jsonReturn(1, '线路不存在');
            }
            
            // 更新线路站点
            $data = [
                $type => $stations,
                'gtime' => date('Y-m-d H:i:s')
            ];
            
            if ($db->update('line', $data, "id = {$id}")) {
                jsonReturn(0, '站点更新成功');
            } else {
                jsonReturn(1, '站点更新失败');
            }
            break;
            
        // 添加站点到线路
        case 'addstation':
            if (!isset($_SESSION['admin'])) {
                jsonReturn(1, '请先登录');
            }
            
            $lineId = isset($_POST['lineId']) ? intval($_POST['lineId']) : 0;
            $type = isset($_POST['type']) ? safeFilter($_POST['type']) : '';
            $stationName = isset($_POST['stationName']) ? safeFilter($_POST['stationName']) : '';
            $position = isset($_POST['position']) ? intval($_POST['position']) : 0;
            
            if (empty($lineId)) {
                jsonReturn(1, '线路ID不能为空');
            }
            
            if ($type != 'zlist' && $type != 'flist') {
                jsonReturn(1, '站点类型错误');
            }
            
            if (empty($stationName)) {
                jsonReturn(1, '站点名称不能为空');
            }
            
            // 检查线路是否存在
            $line = $db->getOne('line', '*', "id = {$lineId}");
            if (!$line) {
                jsonReturn(1, '线路不存在');
            }
            
            // 查找站点ID
            $station = $db->getOne('zhan', 'zid', "zhan = '{$stationName}'");
            if (!$station) {
                // 站点不存在，添加新站点
                $ping = getPinyin($stationName);
                $stationData = [
                    'zhan' => $stationName,
                    'ping' => $ping
                ];
                $stationId = $db->insert('zhan', $stationData);
            } else {
                $stationId = $station['zid'];
            }
            
            // 获取线路站点列表
            $stationList = $line[$type];
            
            // 检查站点是否已存在
            if (strpos($stationList, '-' . $stationId . '-') !== false) {
                jsonReturn(1, '站点已存在于该线路');
            }
            
            // 添加站点到列表
            $stationIdList = explode('-', trim($stationList, '-'));
            
            if ($position <= 0) {
                // 添加到末尾
                $stationIdList[] = $stationId;
            } else if ($position > count($stationIdList)) {
                // 添加到末尾
                $stationIdList[] = $stationId;
            } else {
                // 插入到指定位置
                array_splice($stationIdList, $position - 1, 0, $stationId);
            }
            
            // 重建站点列表
            $newStationList = '-' . implode('-', $stationIdList) . '-';
            
            // 更新线路
            $data = [
                $type => $newStationList,
                'gtime' => date('Y-m-d H:i:s')
            ];
            
            if ($db->update('line', $data, "id = {$lineId}")) {
                jsonReturn(0, '站点添加成功', ['stationId' => $stationId]);
            } else {
                jsonReturn(1, '站点添加失败');
            }
            break;
            
        // 移除线路站点
        case 'removestation':
            if (!isset($_SESSION['admin'])) {
                jsonReturn(1, '请先登录');
            }
            
            $lineId = isset($_POST['lineId']) ? intval($_POST['lineId']) : 0;
            $type = isset($_POST['type']) ? safeFilter($_POST['type']) : '';
            $stationId = isset($_POST['stationId']) ? intval($_POST['stationId']) : 0;
            
            if (empty($lineId)) {
                jsonReturn(1, '线路ID不能为空');
            }
            
            if ($type != 'zlist' && $type != 'flist') {
                jsonReturn(1, '站点类型错误');
            }
            
            if (empty($stationId)) {
                jsonReturn(1, '站点ID不能为空');
            }
            
            // 检查线路是否存在
            $line = $db->getOne('line', '*', "id = {$lineId}");
            if (!$line) {
                jsonReturn(1, '线路不存在');
            }
            
            // 获取线路站点列表
            $stationList = $line[$type];
            
            // 移除站点
            $stationList = str_replace('-' . $stationId . '-', '-', $stationList);
            
            // 修复连续的减号
            $stationList = preg_replace('/-+/', '-', $stationList);
            
            // 确保开头和结尾有减号
            if (substr($stationList, 0, 1) != '-') {
                $stationList = '-' . $stationList;
            }
            if (substr($stationList, -1) != '-') {
                $stationList = $stationList . '-';
            }
            
            // 更新线路
            $data = [
                $type => $stationList,
                'gtime' => date('Y-m-d H:i:s')
            ];
            
            if ($db->update('line', $data, "id = {$lineId}")) {
                jsonReturn(0, '站点移除成功');
            } else {
                jsonReturn(1, '站点移除失败');
            }
            break;
            
        // 删除线路
        case 'delxian':
            if (!isset($_SESSION['admin'])) {
                jsonReturn(1, '请先登录');
            }
            
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            
            if (empty($id)) {
                jsonReturn(1, '线路ID不能为空');
            }
            
            // 检查线路是否存在
            $exist = $db->getOne('line', 'id', "id = {$id}");
            if (!$exist) {
                jsonReturn(1, '线路不存在');
            }
            
            // 删除线路
            if ($db->delete('line', "id = {$id}")) {
                jsonReturn(0, '线路删除成功');
            } else {
                jsonReturn(1, '线路删除失败');
            }
            break;
            
        default:
            jsonReturn(1, '未知操作');
            break;
    }
    
    exit;
}

// 检查登录状态（除了登录页）
if ($do != 'login' && $do != 'lgout' && !isset($_SESSION['admin'])) {
    header('Location: adm.php?do=login');
    exit;
}

// 加载对应的模块文件
$file = './adm/' . $do . '.php';
if (file_exists($file)) {
    include_once $file;
} else {
    // 默认加载登录页
    include_once './adm/login.php';
}
?>

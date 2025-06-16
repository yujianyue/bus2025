<?php

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: main/hshow.php
// 文件大小: 22385 字节

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: main/hshow.php
// 文件大小: 22164 字节
/**
 * 本文件功能: 换乘查询结果页面
 * 版权声明: 保留发行权和署名权
 * 作者信息: 15058593138@qq.com
 */

require_once './inc/sqls.php';

$db = new Sqls();
$start = isset($_GET['start']) ? safeFilter($_GET['start']) : '';
$end = isset($_GET['end']) ? safeFilter($_GET['end']) : '';

if (empty($start) || empty($end)) {
    echo '<script>location.href="index.php";</script>';
    exit;
}

// 查找起点站和终点站
$startStation = $db->getOne('zhan', '*', "zhan LIKE '%{$start}%'");
$endStation = $db->getOne('zhan', '*', "zhan LIKE '%{$end}%'");

if (!$startStation || !$endStation) {
    echo '<script>alert("找不到起点站或终点站，请重新输入！"); location.href="index.php";</script>';
    exit;
}

// 记录查询历史
logSearchHistory(3, $startStation['zhan'] . ' 到 ' . $endStation['zhan']);

// 查找直达线路
$directLines = [];
$sql = "SELECT id, name FROM line WHERE 
        (zlist LIKE '%-{$startStation['zid']}-%' AND zlist LIKE '%-{$endStation['zid']}-%') OR 
        (flist LIKE '%-{$startStation['zid']}-%' AND flist LIKE '%-{$endStation['zid']}-%')";
$directLines = $db->execute($sql);

// 查找经过起点的线路
$startLines = [];
$sql = "SELECT id, name FROM line WHERE zlist LIKE '%-{$startStation['zid']}-%' OR flist LIKE '%-{$startStation['zid']}-%'";
$startLines = $db->execute($sql);

// 查找经过终点的线路
$endLines = [];
$sql = "SELECT id, name FROM line WHERE zlist LIKE '%-{$endStation['zid']}-%' OR flist LIKE '%-{$endStation['zid']}-%'";
$endLines = $db->execute($sql);

// 计算一次换乘方案
$oneTransferPlans = [];

if (!empty($startLines) && !empty($endLines)) {
    // 找出所有可能的换乘站点
    foreach ($startLines as $startLine) {
        foreach ($endLines as $endLine) {
            if ($startLine['id'] == $endLine['id']) continue; // 跳过相同线路
            
            // 获取起点线路的所有站点
            $startLineInfo = $db->getOne('line', 'zlist, flist', "id = {$startLine['id']}");
            $startLineStations = [];
            
            if (!empty($startLineInfo['zlist'])) {
                $startLineStations = array_merge($startLineStations, explode('-', trim($startLineInfo['zlist'], '-')));
            }
            if (!empty($startLineInfo['flist'])) {
                $startLineStations = array_merge($startLineStations, explode('-', trim($startLineInfo['flist'], '-')));
            }
            $startLineStations = array_unique($startLineStations);
            
            // 获取终点线路的所有站点
            $endLineInfo = $db->getOne('line', 'zlist, flist', "id = {$endLine['id']}");
            $endLineStations = [];
            
            if (!empty($endLineInfo['zlist'])) {
                $endLineStations = array_merge($endLineStations, explode('-', trim($endLineInfo['zlist'], '-')));
            }
            if (!empty($endLineInfo['flist'])) {
                $endLineStations = array_merge($endLineStations, explode('-', trim($endLineInfo['flist'], '-')));
            }
            $endLineStations = array_unique($endLineStations);
            
            // 查找共同站点（换乘站）
            $transferStations = array_intersect($startLineStations, $endLineStations);
            
            foreach ($transferStations as $transferStationId) {
                $transferStation = $db->getOne('zhan', 'zid, zhan', "zid = {$transferStationId}");
                
                if ($transferStation) {
                    // 避免选择起点或终点作为换乘站
                    if ($transferStation['zid'] == $startStation['zid'] || $transferStation['zid'] == $endStation['zid']) {
                        continue;
                    }
                    
                    // 计算起点到换乘站的站数
                    $startToTransferStations = countStations($startLine['id'], $startStation['zid'], $transferStation['zid'], $db);
                    
                    // 计算换乘站到终点的站数
                    $transferToEndStations = countStations($endLine['id'], $transferStation['zid'], $endStation['zid'], $db);
                    
                    // 总站数
                    $totalStations = $startToTransferStations + $transferToEndStations;
                    
                    if ($totalStations > 0) {
                        $oneTransferPlans[] = [
                            'startLine' => $startLine['name'],
                            'startLineId' => $startLine['id'],
                            'transferStation' => $transferStation['zhan'],
                            'transferStationId' => $transferStation['zid'],
                            'endLine' => $endLine['name'],
                            'endLineId' => $endLine['id'],
                            'totalStations' => $totalStations,
                            'startToTransferStations' => $startToTransferStations,
                            'transferToEndStations' => $transferToEndStations
                        ];
                    }
                }
            }
        }
    }
}

// 计算二次换乘方案
$twoTransferPlans = [];

if (!empty($startLines)) {
    foreach ($startLines as $startLine) {
        // 获取起点线路的所有站点
        $startLineInfo = $db->getOne('line', 'zlist, flist', "id = {$startLine['id']}");
        $startLineStations = [];
        
        if (!empty($startLineInfo['zlist'])) {
            $startLineStations = array_merge($startLineStations, explode('-', trim($startLineInfo['zlist'], '-')));
        }
        if (!empty($startLineInfo['flist'])) {
            $startLineStations = array_merge($startLineStations, explode('-', trim($startLineInfo['flist'], '-')));
        }
        $startLineStations = array_unique($startLineStations);
        
        // 获取经过第一个换乘站的线路
        foreach ($startLineStations as $transferStationId1) {
            if ($transferStationId1 == $startStation['zid'] || $transferStationId1 == $endStation['zid']) {
                continue; // 跳过起点和终点
            }
            
            $transferStation1 = $db->getOne('zhan', 'zid, zhan', "zid = {$transferStationId1}");
            
            if (!$transferStation1) continue;
            
            // 查找经过第一换乘站的线路
            $sql = "SELECT id, name FROM line WHERE 
                    (zlist LIKE '%-{$transferStationId1}-%' OR flist LIKE '%-{$transferStationId1}-%') 
                    AND id != {$startLine['id']}";
            $middleLines = $db->execute($sql);
            
            foreach ($middleLines as $middleLine) {
                // 获取中间线路的所有站点
                $middleLineInfo = $db->getOne('line', 'zlist, flist', "id = {$middleLine['id']}");
                $middleLineStations = [];
                
                if (!empty($middleLineInfo['zlist'])) {
                    $middleLineStations = array_merge($middleLineStations, explode('-', trim($middleLineInfo['zlist'], '-')));
                }
                if (!empty($middleLineInfo['flist'])) {
                    $middleLineStations = array_merge($middleLineStations, explode('-', trim($middleLineInfo['flist'], '-')));
                }
                $middleLineStations = array_unique($middleLineStations);
                
                // 查找第二个换乘站
                foreach ($middleLineStations as $transferStationId2) {
                    if ($transferStationId2 == $startStation['zid'] || 
                        $transferStationId2 == $endStation['zid'] || 
                        $transferStationId2 == $transferStationId1) {
                        continue; // 跳过起点、终点和第一换乘站
                    }
                    
                    $transferStation2 = $db->getOne('zhan', 'zid, zhan', "zid = {$transferStationId2}");
                    
                    if (!$transferStation2) continue;
                    
                    // 查找经过第二换乘站和终点的线路
                    $sql = "SELECT id, name FROM line WHERE 
                            (zlist LIKE '%-{$transferStationId2}-%' AND zlist LIKE '%-{$endStation['zid']}-%') OR 
                            (flist LIKE '%-{$transferStationId2}-%' AND flist LIKE '%-{$endStation['zid']}-%') 
                            AND id != {$middleLine['id']} AND id != {$startLine['id']}";
                    $endLines2 = $db->execute($sql);
                    
                    foreach ($endLines2 as $endLine) {
                        // 计算站数
                        $startToTransfer1Stations = countStations($startLine['id'], $startStation['zid'], $transferStationId1, $db);
                        $transfer1ToTransfer2Stations = countStations($middleLine['id'], $transferStationId1, $transferStationId2, $db);
                        $transfer2ToEndStations = countStations($endLine['id'], $transferStationId2, $endStation['zid'], $db);
                        
                        $totalStations = $startToTransfer1Stations + $transfer1ToTransfer2Stations + $transfer2ToEndStations;
                        
                        if ($totalStations > 0) {
                            $twoTransferPlans[] = [
                                'startLine' => $startLine['name'],
                                'startLineId' => $startLine['id'],
                                'transferStation1' => $transferStation1['zhan'],
                                'transferStationId1' => $transferStation1['zid'],
                                'middleLine' => $middleLine['name'],
                                'middleLineId' => $middleLine['id'],
                                'transferStation2' => $transferStation2['zhan'],
                                'transferStationId2' => $transferStation2['zid'],
                                'endLine' => $endLine['name'],
                                'endLineId' => $endLine['id'],
                                'totalStations' => $totalStations,
                                'startToTransfer1Stations' => $startToTransfer1Stations,
                                'transfer1ToTransfer2Stations' => $transfer1ToTransfer2Stations,
                                'transfer2ToEndStations' => $transfer2ToEndStations
                            ];
                        }
                    }
                }
            }
        }
    }
}

// 对所有方案按总站数排序
$allPlans = [];

// 添加直达方案
foreach ($directLines as $line) {
    $stations = countStations($line['id'], $startStation['zid'], $endStation['zid'], $db);
    if ($stations > 0) {
        $allPlans[] = [
            'type' => 'direct',
            'line' => $line['name'],
            'lineId' => $line['id'],
            'totalStations' => $stations
        ];
    }
}

// 添加一次换乘方案
foreach ($oneTransferPlans as $plan) {
    $allPlans[] = [
        'type' => 'one_transfer',
        'plan' => $plan
    ];
}

// 添加二次换乘方案
foreach ($twoTransferPlans as $plan) {
    $allPlans[] = [
        'type' => 'two_transfer',
        'plan' => $plan
    ];
}

// 按总站数排序
usort($allPlans, function($a, $b) {
    $stationsA = ($a['type'] == 'direct') ? $a['totalStations'] : $a['plan']['totalStations'];
    $stationsB = ($b['type'] == 'direct') ? $b['totalStations'] : $b['plan']['totalStations'];
    return $stationsA - $stationsB;
});

// 最多显示前10个方案
$allPlans = array_slice($allPlans, 0, 10);

/**
 * 计算两个站点之间的站数
 * @param int $lineId 线路ID
 * @param int $startId 起点站ID
 * @param int $endId 终点站ID
 * @param Sqls $db 数据库对象
 * @return int 站数
 */
function countStations($lineId, $startId, $endId, $db) {
    $line = $db->getOne('line', 'zlist, flist', "id = {$lineId}");
    
    // 检查正向线路
    if (!empty($line['zlist'])) {
        $stations = explode('-', trim($line['zlist'], '-'));
        $startIndex = array_search($startId, $stations);
        $endIndex = array_search($endId, $stations);
        
        if ($startIndex !== false && $endIndex !== false) {
            return abs($endIndex - $startIndex);
        }
    }
    
    // 检查反向线路
    if (!empty($line['flist'])) {
        $stations = explode('-', trim($line['flist'], '-'));
        $startIndex = array_search($startId, $stations);
        $endIndex = array_search($endId, $stations);
        
        if ($startIndex !== false && $endIndex !== false) {
            return abs($endIndex - $startIndex);
        }
    }
    
    return 0;
}
?>

<div class="content-container">
    <div class="form-container">
        <div class="form-title">换乘查询结果</div>
        
        <div class="search-info">
            <div class="search-route">
                <span class="search-station start"><?php echo $startStation['zhan']; ?></span>
                <span class="search-arrow">→</span>
                <span class="search-station end"><?php echo $endStation['zhan']; ?></span>
            </div>
        </div>
    </div>
    
    <div class="form-container">
        <div class="form-title">换乘方案 (TOP <?php echo count($allPlans); ?>)</div>
        
        <?php if (!empty($allPlans)): ?>
        <div class="transfer-plans">
            <?php foreach ($allPlans as $index => $plan): ?>
            <div class="transfer-plan">
                <div class="plan-header">
                    <div class="plan-title">方案 <?php echo $index + 1; ?></div>
                    <div class="plan-stations">总站数: <?php echo ($plan['type'] == 'direct') ? $plan['totalStations'] : $plan['plan']['totalStations']; ?></div>
                </div>
                
                <div class="plan-content">
                    <?php if ($plan['type'] == 'direct'): ?>
                    <!-- 直达方案 -->
                    <div class="plan-step">
                        <div class="step-icon">→</div>
                        <div class="step-info">
                            <div class="step-line"><?php echo $plan['line']; ?></div>
                            <div class="step-stations">从 <?php echo $startStation['zhan']; ?> 乘坐到 <?php echo $endStation['zhan']; ?> (<?php echo $plan['totalStations']; ?>站)</div>
                            <div class="step-action">
                                <a href="index.php?do=xshow&id=<?php echo $plan['lineId']; ?>" class="action-btn">查看线路</a>
                            </div>
                        </div>
                    </div>
                    <?php elseif ($plan['type'] == 'one_transfer'): ?>
                    <!-- 一次换乘方案 -->
                    <div class="plan-step">
                        <div class="step-icon">1</div>
                        <div class="step-info">
                            <div class="step-line"><?php echo $plan['plan']['startLine']; ?></div>
                            <div class="step-stations">从 <?php echo $startStation['zhan']; ?> 乘坐到 <?php echo $plan['plan']['transferStation']; ?> (<?php echo $plan['plan']['startToTransferStations']; ?>站)</div>
                            <div class="step-action">
                                <a href="index.php?do=xshow&id=<?php echo $plan['plan']['startLineId']; ?>" class="action-btn">查看线路</a>
                            </div>
                        </div>
                    </div>
                    <div class="plan-step">
                        <div class="step-icon">2</div>
                        <div class="step-info">
                            <div class="step-line"><?php echo $plan['plan']['endLine']; ?></div>
                            <div class="step-stations">从 <?php echo $plan['plan']['transferStation']; ?> 乘坐到 <?php echo $endStation['zhan']; ?> (<?php echo $plan['plan']['transferToEndStations']; ?>站)</div>
                            <div class="step-action">
                                <a href="index.php?do=xshow&id=<?php echo $plan['plan']['endLineId']; ?>" class="action-btn">查看线路</a>
                            </div>
                        </div>
                    </div>
                    <?php elseif ($plan['type'] == 'two_transfer'): ?>
                    <!-- 二次换乘方案 -->
                    <div class="plan-step">
                        <div class="step-icon">1</div>
                        <div class="step-info">
                            <div class="step-line"><?php echo $plan['plan']['startLine']; ?></div>
                            <div class="step-stations">从 <?php echo $startStation['zhan']; ?> 乘坐到 <?php echo $plan['plan']['transferStation1']; ?> (<?php echo $plan['plan']['startToTransfer1Stations']; ?>站)</div>
                            <div class="step-action">
                                <a href="index.php?do=xshow&id=<?php echo $plan['plan']['startLineId']; ?>" class="action-btn">查看线路</a>
                            </div>
                        </div>
                    </div>
                    <div class="plan-step">
                        <div class="step-icon">2</div>
                        <div class="step-info">
                            <div class="step-line"><?php echo $plan['plan']['middleLine']; ?></div>
                            <div class="step-stations">从 <?php echo $plan['plan']['transferStation1']; ?> 乘坐到 <?php echo $plan['plan']['transferStation2']; ?> (<?php echo $plan['plan']['transfer1ToTransfer2Stations']; ?>站)</div>
                            <div class="step-action">
                                <a href="index.php?do=xshow&id=<?php echo $plan['plan']['middleLineId']; ?>" class="action-btn">查看线路</a>
                            </div>
                        </div>
                    </div>
                    <div class="plan-step">
                        <div class="step-icon">3</div>
                        <div class="step-info">
                            <div class="step-line"><?php echo $plan['plan']['endLine']; ?></div>
                            <div class="step-stations">从 <?php echo $plan['plan']['transferStation2']; ?> 乘坐到 <?php echo $endStation['zhan']; ?> (<?php echo $plan['plan']['transfer2ToEndStations']; ?>站)</div>
                            <div class="step-action">
                                <a href="index.php?do=xshow&id=<?php echo $plan['plan']['endLineId']; ?>" class="action-btn">查看线路</a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-data" style="padding: 30px; text-align: center; color: #999;">
            未找到合适的换乘方案
        </div>
        <?php endif; ?>
    </div>
    
    <div class="form-container">
        <div class="form-title">历史查询记录</div>
        
        <?php
        $history = getSearchHistory(3);
        if (!empty($history)):
        ?>
        <div class="history-list">
            <?php foreach ($history as $index => $item): ?>
                <?php if ($index < 10): // 只显示最近10条 ?>
                <div class="history-item">
                    <span class="history-keyword"><?php echo $item['keyword']; ?></span>
                    <span class="history-time"><?php echo $item['time']; ?></span>
                    <?php
                    $parts = explode(' 到 ', $item['keyword']);
                    if (count($parts) == 2) {
                        $historyStart = $parts[0];
                        $historyEnd = $parts[1];
                        echo '<a href="index.php?do=hshow&start=' . urlencode($historyStart) . '&end=' . urlencode($historyEnd) . '" class="history-action">查看</a>';
                    }
                    ?>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-data" style="padding: 15px; text-align: center; color: #999;">
            暂无查询记录
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.search-info {
    margin-bottom: 20px;
    padding: 15px;
    background-color: #f9f9f9;
    border-radius: 5px;
}

.search-route {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.search-station {
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: bold;
}

.search-station.start {
    background-color: #e3f2fd;
    color: #1e88e5;
}

.search-station.end {
    background-color: #e8f5e9;
    color: #43a047;
}

.search-arrow {
    margin: 0 15px;
    color: #666;
}

.transfer-plans {
    margin-top: 15px;
}

.transfer-plan {
    margin-bottom: 20px;
    border: 1px solid #eee;
    border-radius: 5px;
    overflow: hidden;
}

.plan-header {
    display: flex;
    justify-content: space-between;
    padding: 10px 15px;
    background-color: #f5f5f5;
    border-bottom: 1px solid #eee;
}

.plan-title {
    font-weight: bold;
}

.plan-stations {
    color: #666;
}

.plan-content {
    padding: 15px;
}

.plan-step {
    display: flex;
    margin-bottom: 15px;
}

.plan-step:last-child {
    margin-bottom: 0;
}

.step-icon {
    width: 24px;
    height: 24px;
    line-height: 24px;
    text-align: center;
    background-color: #1e88e5;
    color: #fff;
    border-radius: 50%;
    margin-right: 15px;
    flex-shrink: 0;
}

.step-info {
    flex: 1;
}

.step-line {
    font-weight: bold;
    margin-bottom: 5px;
}

.step-stations {
    margin-bottom: 5px;
    color: #666;
}

.history-list {
    margin-top: 10px;
}

.history-item {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    border-bottom: 1px solid #eee;
}

.history-keyword {
    font-weight: bold;
}

.history-time {
    color: #666;
}

.history-action {
    color: #1e88e5;
}
</style>

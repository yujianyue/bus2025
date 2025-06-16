<?php

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: main/xshow.php
// 文件大小: 10742 字节

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: main/xshow.php
// 文件大小: 10521 字节
/**
 * 本文件功能: 线路详情页面
 * 版权声明: 保留发行权和署名权
 * 作者信息: 15058593138@qq.com
 */

require_once './inc/sqls.php';

$db = new Sqls();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$keyword = isset($_GET['keyword']) ? safeFilter($_GET['keyword']) : '';

// 获取线路信息
$line = null;
if ($id) {
    $line = $db->getOne('line', '*', "id = {$id}");
} elseif ($keyword) {
    $line = $db->getOne('line', '*', "name LIKE '%{$keyword}%'");
    if ($line) {
        $id = $line['id'];
    }
}

// 如果找不到线路，返回线路列表
if (!$line) {
    echo '<script>location.href="index.php?do=xian";</script>';
    exit;
}

// 记录查询历史
logSearchHistory(1, $line['name']);

// 解析正向和反向站点
$zStations = [];
$fStations = [];
$stationCoords = []; // 存储站点坐标用于地图显示

if (!empty($line['zlist'])) {
    $zStationIds = explode('-', trim($line['zlist'], '-'));
    foreach ($zStationIds as $zid) {
        $station = $db->getOne('zhan', '*', "zid = {$zid}");
        if ($station) {
            $zStations[] = $station;
            if ($station['lng'] && $station['lat']) {
                $stationCoords[] = [
                    'name' => $station['zhan'],
                    'lng' => $station['lng'],
                    'lat' => $station['lat']
                ];
            }
        }
    }
}

if (!empty($line['flist'])) {
    $fStationIds = explode('-', trim($line['flist'], '-'));
    foreach ($fStationIds as $zid) {
        $station = $db->getOne('zhan', '*', "zid = {$zid}");
        if ($station) {
            $fStations[] = $station;
        }
    }
}
?>

<div class="content-container">
    <div class="tab-container">
        <div class="tab-header">
            <div class="tab active" data-target="forward-stations">正向站点</div>
            <div class="tab" data-target="backward-stations">反向站点</div>
            <div class="tab" data-target="info-stations">线路详情</div>
        </div>
        
        <div class="tab-content">
            <!-- 正向站点 -->
            <div class="tab-pane active" id="forward-stations">
                <?php if (!empty($zStations)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>序号</th>
                            <th>站点名称</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($zStations as $index => $station): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo $station['zhan']; ?></td>
                            <td>
                                <a href="index.php?do=zshow&id=<?php echo $station['zid']; ?>" class="action-btn">查看站点</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-data" style="padding: 20px; text-align: center; color: #999;">
                    暂无正向站点数据
                </div>
                <?php endif; ?>
            </div>
            
            <!-- 反向站点 -->
            <div class="tab-pane" id="backward-stations">
                <?php if (!empty($fStations)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>序号</th>
                            <th>站点名称</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fStations as $index => $station): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo $station['zhan']; ?></td>
                            <td>
                                <a href="index.php?do=zshow&id=<?php echo $station['zid']; ?>" class="action-btn">查看站点</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-data" style="padding: 20px; text-align: center; color: #999;">
                    暂无反向站点数据
                </div>
                <?php endif; ?>
            </div>
          
        <div  class="tab-pane" id="info-stations">        
        <div class="form-group">
            <div class="form-label">线路名称</div>
            <div class="form-value"><?php echo $line['name']; ?></div>
        </div>
        
        <div class="form-group">
            <div class="form-label">线路类型</div>
            <div class="form-value"><?php echo $line['type']; ?></div>
        </div>
        
        <div class="form-group">
            <div class="form-label">运行时间</div>
            <div class="form-value"><?php echo $line['time']; ?></div>
        </div>
        
        <div class="form-group">
            <div class="form-label">起始站</div>
            <div class="form-value"><?php echo $line['start']; ?></div>
        </div>
        
        <div class="form-group">
            <div class="form-label">终点站</div>
            <div class="form-value"><?php echo $line['end']; ?></div>
        </div>
        
        <div class="form-group">
            <div class="form-label">运营公司</div>
            <div class="form-value"><?php echo $line['comp']; ?></div>
        </div>
        
        <div class="form-group">
            <div class="form-label">票价</div>
            <div class="form-value"><?php echo $line['fare']; ?></div>
        </div>
        
        <div class="form-group">
            <div class="form-label">更新时间</div>
            <div class="form-value"><?php echo $line['gtime']; ?></div>
        </div>
        
        <div class="form-group">
            <div class="form-label">备注</div>
            <div class="form-value"><?php echo nl2br($line['note']); ?></div>
        </div>
    </div>
          
        </div>
    </div>
  
    <?php if (!empty($stationCoords)): ?>
    <div class="map-container" id="mapContainer">
        <!-- 百度地图容器 -->
    </div>
    <?php endif; ?>
  
    <div class="form-container">
        <div class="form-title">历史查询记录</div>
        
        <?php
        $history = getSearchHistory(1);
        if (!empty($history)):
        ?>
        <div class="history-list">
            <?php foreach ($history as $index => $item): ?>
                <?php if ($index < 10): // 只显示最近10条 ?>
                <div class="history-item">
                    <span class="history-keyword"><?php echo $item['keyword']; ?></span>
                    <span class="history-time"><?php echo $item['time']; ?></span>
                    <a href="index.php?do=xshow&keyword=<?php echo urlencode($item['keyword']); ?>" class="history-action">查看</a>
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

<?php if (!empty($stationCoords)): ?>
<script type="text/javascript" src="https://api.map.baidu.com/api?v=3.0&ak=<?php echo $siteConfig['mapKey']; ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 初始化百度地图
        var map = new BMap.Map("mapContainer");
        
        // 设置地图中心点为第一个站点
        var firstPoint = new BMap.Point(<?php echo $stationCoords[0]['lng']; ?>, <?php echo $stationCoords[0]['lat']; ?>);
        map.centerAndZoom(firstPoint, 13);
        map.enableScrollWheelZoom(true);
        
        // 添加站点标记
        var stations = <?php echo json_encode($stationCoords); ?>;
        var points = [];
        
        for (var i = 0; i < stations.length; i++) {
            var point = new BMap.Point(stations[i].lng, stations[i].lat);
            points.push(point);
            
            var marker = new BMap.Marker(point);
            map.addOverlay(marker);
            
            // 添加信息窗口
            var infoWindow = new BMap.InfoWindow(stations[i].name);
            (function(marker, infoWindow) {
                marker.addEventListener("click", function(){
                    this.openInfoWindow(infoWindow);
                });
            })(marker, infoWindow);
        }
        
        // 绘制线路
        var polyline = new BMap.Polyline(points, {
            strokeColor: "#1e88e5",
            strokeWeight: 3,
            strokeOpacity: 0.8
        });
        map.addOverlay(polyline);
        
        // 自动调整视野
        map.setViewport(points);
    });
</script>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 初始化标签页切换
        initTabs('tab-container');
        
        // 为标签添加点击事件
        var tabs = document.querySelectorAll('.tab-header .tab');
        tabs.forEach(function(tab) {
            tab.addEventListener('click', function() {
                var target = this.getAttribute('data-target');
                
                // 移除所有active类
                tabs.forEach(function(t) {
                    t.classList.remove('active');
                });
                
                document.querySelectorAll('.tab-content .tab-pane').forEach(function(pane) {
                    pane.classList.remove('active');
                });
                
                // 添加active类
                this.classList.add('active');
                document.getElementById(target).classList.add('active');
            });
        });
    });
</script>

<style>
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
.form-value {
    padding: 8px 0;
    color: #333;
}
</style>

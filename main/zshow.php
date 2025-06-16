<?php

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: main/zshow.php
// 文件大小: 5699 字节

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: main/zshow.php
// 文件大小: 5479 字节
/**
 * 本文件功能: 站点详情页面
 * 版权声明: 保留发行权和署名权
 * 作者信息: 15058593138@qq.com
 */

require_once './inc/sqls.php';

$db = new Sqls();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$keyword = isset($_GET['keyword']) ? safeFilter($_GET['keyword']) : '';

// 获取站点信息
$station = null;
if ($id) {
    $station = $db->getOne('zhan', '*', "zid = {$id}");
} elseif ($keyword) {
    $station = $db->getOne('zhan', '*', "zhan LIKE '%{$keyword}%'");
    if ($station) {
        $id = $station['zid'];
    }
}

// 如果找不到站点，返回站点列表
if (!$station) {
    echo '<script>location.href="index.php?do=zhan";</script>';
    exit;
}

// 记录查询历史
logSearchHistory(2, $station['zhan']);

// 查找经过该站点的线路
$passingLines = [];
$sql = "SELECT id, name, type, start, end FROM line WHERE zlist LIKE '%-{$id}-%' OR flist LIKE '%-{$id}-%'";
$passingLines = $db->execute($sql);
?>

<div class="content-container">
    <div class="form-container">
        <div class="form-title">站点详情</div>
        
        <div class="form-group">
            <div class="form-label">站点名称</div>
            <div class="form-value"><?php echo $station['zhan']; ?></div>
        </div>
        
        <div class="form-group">
            <div class="form-label">拼音</div>
            <div class="form-value"><?php echo $station['ping']; ?></div>
        </div>
        
        <?php if ($station['lng'] && $station['lat']): ?>
        <div class="form-group">
            <div class="form-label">地理坐标</div>
            <div class="form-value"><?php echo $station['lng'] . ', ' . $station['lat']; ?></div>
        </div>
        
        <div class="map-container" id="mapContainer">
            <!-- 百度地图容器 -->
        </div>
        <?php endif; ?>
    </div>
    
    <div class="table-container">
        <div class="table-header">
            <div class="table-title">经过此站点的线路</div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>线路名称</th>
                    <th>线路类型</th>
                    <th>起始站</th>
                    <th>终点站</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($passingLines)): ?>
                    <?php foreach ($passingLines as $line): ?>
                    <tr>
                        <td><?php echo $line['name']; ?></td>
                        <td><?php echo $line['type']; ?></td>
                        <td><?php echo $line['start']; ?></td>
                        <td><?php echo $line['end']; ?></td>
                        <td>
                            <a href="index.php?do=xshow&id=<?php echo $line['id']; ?>" class="action-btn">查看线路</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px 0;">暂无经过此站点的线路</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div class="form-container">
        <div class="form-title">历史查询记录</div>
        
        <?php
        $history = getSearchHistory(2);
        if (!empty($history)):
        ?>
        <div class="history-list">
            <?php foreach ($history as $index => $item): ?>
                <?php if ($index < 10): // 只显示最近10条 ?>
                <div class="history-item">
                    <span class="history-keyword"><?php echo $item['keyword']; ?></span>
                    <span class="history-time"><?php echo $item['time']; ?></span>
                    <a href="index.php?do=zshow&keyword=<?php echo urlencode($item['keyword']); ?>" class="history-action">查看</a>
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

<?php if ($station['lng'] && $station['lat']): ?>
<script type="text/javascript" src="https://api.map.baidu.com/api?v=3.0&ak=<?php echo $siteConfig['mapKey']; ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 初始化百度地图
        var map = new BMap.Map("mapContainer");
        var point = new BMap.Point(<?php echo $station['lng']; ?>, <?php echo $station['lat']; ?>);
        map.centerAndZoom(point, 15);
        map.enableScrollWheelZoom(true);
        
        // 添加标记
        var marker = new BMap.Marker(point);
        map.addOverlay(marker);
        
        // 添加信息窗口
        var infoWindow = new BMap.InfoWindow("<?php echo $station['zhan']; ?>");
        marker.addEventListener("click", function(){
            this.openInfoWindow(infoWindow);
        });
    });
</script>
<?php endif; ?>

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
</style>

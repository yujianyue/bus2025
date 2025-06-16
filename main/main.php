<?php

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: main/main.php
// 文件大小: 6850 字节

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: main/main.php
// 文件大小: 6631 字节
/**
 * 本文件功能: 首页主页面
 * 版权声明: 保留发行权和署名权
 * 作者信息: 15058593138@qq.com
 */

// 获取查询历史
$lineHistory = getSearchHistory(1);
$stationHistory = getSearchHistory(2);
$transferHistory = getSearchHistory(3);
?>

<div class="content-container">
    <div class="tab-container">
        <div class="tab-header">
            <div class="tab active" data-target="line-history">线路查询记录</div>
            <div class="tab" data-target="station-history">站点查询记录</div>
            <div class="tab" data-target="transfer-history">换乘查询记录</div>
        </div>
        
        <div class="tab-content">
            <!-- 线路查询记录 -->
            <div class="tab-pane active" id="line-history">
                <?php if (!empty($lineHistory)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>序号</th>
                            <th>查询线路</th>
                            <th>查询时间</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lineHistory as $index => $item): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo $item['keyword']; ?></td>
                            <td><?php echo $item['time']; ?></td>
                            <td>
                                <a href="index.php?do=xshow&keyword=<?php echo urlencode($item['keyword']); ?>" class="action-btn">查看</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-data" style="padding: 30px; text-align: center; color: #999;">
                    暂无查询记录
                </div>
                <?php endif; ?>
            </div>
            
            <!-- 站点查询记录 -->
            <div class="tab-pane" id="station-history">
                <?php if (!empty($stationHistory)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>序号</th>
                            <th>查询站点</th>
                            <th>查询时间</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stationHistory as $index => $item): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo $item['keyword']; ?></td>
                            <td><?php echo $item['time']; ?></td>
                            <td>
                                <a href="index.php?do=zshow&keyword=<?php echo urlencode($item['keyword']); ?>" class="action-btn">查看</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-data" style="padding: 30px; text-align: center; color: #999;">
                    暂无查询记录
                </div>
                <?php endif; ?>
            </div>
            
            <!-- 换乘查询记录 -->
            <div class="tab-pane" id="transfer-history">
                <?php if (!empty($transferHistory)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>序号</th>
                            <th>查询路线</th>
                            <th>查询时间</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transferHistory as $index => $item): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo $item['keyword']; ?></td>
                            <td><?php echo $item['time']; ?></td>
                            <td>
                                <?php
                                $parts = explode(' 到 ', $item['keyword']);
                                if (count($parts) == 2) {
                                    $start = $parts[0];
                                    $end = $parts[1];
                                    echo '<a href="index.php?do=hshow&start=' . urlencode($start) . '&end=' . urlencode($end) . '" class="action-btn">查看</a>';
                                }
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-data" style="padding: 30px; text-align: center; color: #999;">
                    暂无查询记录
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 初始化标签页切换
        initTabs('tab-container');
        
        // 手动实现标签切换逻辑以确保正常工作
        var tabs = document.querySelectorAll('.tab-header .tab');
        var tabPanes = document.querySelectorAll('.tab-content .tab-pane');
        
        tabs.forEach(function(tab, index) {
            tab.addEventListener('click', function() {
                // 移除所有active类
                tabs.forEach(function(t) {
                    t.classList.remove('active');
                });
                tabPanes.forEach(function(p) {
                    p.classList.remove('active');
                });
                
                // 添加active类
                this.classList.add('active');
                var targetId = this.getAttribute('data-target');
                var targetPane = document.getElementById(targetId);
                if (targetPane) {
                    targetPane.classList.add('active');
                }
            });
        });
        
        // 确保第一个标签是激活状态
        if (tabs.length > 0 && tabPanes.length > 0) {
            tabs[0].classList.add('active');
            tabPanes[0].classList.add('active');
        }
    });
</script>

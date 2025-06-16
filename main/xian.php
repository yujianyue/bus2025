<?php

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: main/xian.php
// 文件大小: 6786 字节

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: main/xian.php
// 文件大小: 6567 字节
/**
 * 本文件功能: 线路列表页面
 * 版权声明: 保留发行权和署名权
 * 作者信息: 15058593138@qq.com
 */

require_once './inc/sqls.php';

$db = new Sqls();
$keyword = isset($_GET['keyword']) ? safeFilter($_GET['keyword']) : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$pageSize = isset($siteConfig['pageSize']) ? intval($siteConfig['pageSize']) : 10;

// 构建查询条件
$where = '';
if ($keyword) {
    $where = "name LIKE '%{$keyword}%' OR type LIKE '%{$keyword}%' OR comp LIKE '%{$keyword}%'";
}

// 获取总记录数
$totalRecords = $db->getCount('line', $where);
$totalPages = ceil($totalRecords / $pageSize);

// 确保页码有效
if ($page < 1) $page = 1;
if ($page > $totalPages && $totalPages > 0) $page = $totalPages;

// 计算偏移量
$offset = ($page - 1) * $pageSize;

// 获取线路数据
$sql = "SELECT id, name, type, time, start, end, comp FROM line";
if ($where) {
    $sql .= " WHERE {$where}";
}
$sql .= " ORDER BY id DESC LIMIT {$offset}, {$pageSize}";
$lines = $db->execute($sql);

// 确保列表是数组形式
if (!is_array($lines)) {
    $lines = [];
}
?>

<div class="content-container">
    <div class="table-container">
        <div class="table-header">
            <div class="table-title">线路列表</div>
            <div class="table-actions">
                <div class="table-search">
                    <input type="text" id="keyword" placeholder="线路名称..." value="<?php echo $keyword; ?>">
                    <button id="searchBtn">查询</button>
                </div>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>线路名称</th>
                    <th>线路类型</th>
                    <!--th>运行时间</th-->
                    <th>起始站</th>
                    <th>终点站</th>
                    <!--th>运营公司</th-->
                    <th>操作</th>
                </tr>
            </thead>
            <tbody id="lineList">
                <?php if (!empty($lines)): ?>
                    <?php foreach ($lines as $line): ?>
                    <tr>
                        <td><?php echo $line['id']; ?></td>
                        <td><?php echo $line['name']; ?></td>
                        <td><?php echo $line['type']; ?></td>
                        <!--td><?php echo $line['time']; ?></td-->
                        <td><?php echo $line['start']; ?></td>
                        <td><?php echo $line['end']; ?></td>
                        <!--td><?php echo $line['comp']; ?></td-->
                        <td>
                            <a href="index.php?do=xshow&id=<?php echo $line['id']; ?>" class="action-btn">详情</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px 0;">暂无数据</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div class="pagination-container" id="paginationContainer">
            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                <a href="index.php?do=xian&page=1<?php echo $keyword ? '&keyword=' . urlencode($keyword) : ''; ?>" class="page-btn first">起始页</a>
                <a href="index.php?do=xian&page=<?php echo $page - 1; ?><?php echo $keyword ? '&keyword=' . urlencode($keyword) : ''; ?>" class="page-btn prev">上一页</a>
                <?php else: ?>
                <span class="page-btn first disabled">起始页</span>
                <span class="page-btn prev disabled">上一页</span>
                <?php endif; ?>
                
                <select class="page-select" onchange="location.href='index.php?do=xian&page='+this.value+'<?php echo $keyword ? '&keyword=' . urlencode($keyword) : ''; ?>'">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo $i == $page ? 'selected' : ''; ?>><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
                
                <?php if ($page < $totalPages): ?>
                <a href="index.php?do=xian&page=<?php echo $page + 1; ?><?php echo $keyword ? '&keyword=' . urlencode($keyword) : ''; ?>" class="page-btn next">下一页</a>
                <a href="index.php?do=xian&page=<?php echo $totalPages; ?><?php echo $keyword ? '&keyword=' . urlencode($keyword) : ''; ?>" class="page-btn last">最后页</a>
                <?php else: ?>
                <span class="page-btn next disabled">下一页</span>
                <span class="page-btn last disabled">最后页</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 搜索按钮点击事件
        document.getElementById('searchBtn').addEventListener('click', function() {
            var keyword = document.getElementById('keyword').value.trim();
            window.location.href = 'index.php?do=xian&keyword=' + encodeURIComponent(keyword);
        });

        // 回车查询
        document.getElementById('keyword').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                var keyword = this.value.trim();
                window.location.href = 'index.php?do=xian&keyword=' + encodeURIComponent(keyword);
            }
        });
        
        // 激活头部搜索栏
        var searchTabs = document.querySelectorAll('.search-tab');
        var searchPanes = document.querySelectorAll('.search-pane');
        
        // 激活线路查询选项卡
        for (var i = 0; i < searchTabs.length; i++) {
            if (searchTabs[i].getAttribute('data-target') === 'line-search') {
                searchTabs[i].classList.add('active');
            } else {
                searchTabs[i].classList.remove('active');
            }
        }
        
        // 激活线路查询表单
        for (var i = 0; i < searchPanes.length; i++) {
            if (searchPanes[i].id === 'line-search') {
                searchPanes[i].classList.add('active');
            } else {
                searchPanes[i].classList.remove('active');
            }
        }
    });
</script>

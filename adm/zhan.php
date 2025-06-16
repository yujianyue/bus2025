<?php

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: adm/zhan.php
// 文件大小: 16786 字节

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: adm/zhan.php
// 文件大小: 16567 字节
/**
 * 本文件功能: 站点管理页面
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
    $where = "zhan LIKE '%{$keyword}%' OR ping LIKE '%{$keyword}%'";
}

// 获取总记录数
$totalRecords = $db->getCount('zhan', $where);
$totalPages = ceil($totalRecords / $pageSize);

// 确保页码有效
if ($page < 1) $page = 1;
if ($page > $totalPages && $totalPages > 0) $page = $totalPages;

// 计算偏移量
$offset = ($page - 1) * $pageSize;

// 获取站点数据
$sql = "SELECT zid, zhan, ping, lng, lat FROM zhan";
if ($where) {
    $sql .= " WHERE {$where}";
}
$sql .= " ORDER BY zid DESC LIMIT {$offset}, {$pageSize}";
$stations = $db->execute($sql);
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>站点管理 - 公交查询系统</title>
    <link rel="stylesheet" href="./inc/pubs.css?v=<?php echo $version; ?>">
    <script src="./inc/js.js?v=<?php echo $version; ?>"></script>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="header-row">
                <div class="header-title">公交查询系统 - 管理后台</div>
                <button class="header-btn" onclick="location.href='adm.php?do=lgout'">退出</button>
            </div>
            
            <div class="admin-nav">
                <?php foreach ($adminMenuConfig as $key => $name): ?>
                <a href="adm.php?do=<?php echo $key; ?>" class="admin-nav-item <?php echo $do == $key ? 'active' : ''; ?>"><?php echo $name; ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="table-container">
            <div class="table-header">
                <div class="table-title">站点列表</div>
                <div class="table-actions">
                    <div class="table-search">
                        <input type="text" id="keyword" placeholder="站点名称..." value="<?php echo $keyword; ?>">
                        <button id="searchBtn">查询</button>
                    </div>
                    <button class="table-btn" id="addBtn">新增站点</button>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>站点名称</th>
                        <th>拼音</th>
                        <th>经度</th>
                        <th>纬度</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody id="stationList">
                    <?php if (!empty($stations)): ?>
                        <?php foreach ($stations as $station): ?>
                        <tr>
                            <td><?php echo $station['zid']; ?></td>
                            <td><?php echo $station['zhan']; ?></td>
                            <td><?php echo $station['ping']; ?></td>
                            <td><?php echo $station['lng']; ?></td>
                            <td><?php echo $station['lat']; ?></td>
                            <td>
                                <a href="javascript:void(0);" class="action-btn" onclick="editStation(<?php echo $station['zid']; ?>, '<?php echo $station['zhan']; ?>', '<?php echo $station['lng']; ?>', '<?php echo $station['lat']; ?>')">修改</a>
                                <a href="javascript:void(0);" class="action-btn delete" onclick="deleteStation(<?php echo $station['zid']; ?>, '<?php echo $station['zhan']; ?>')">删除</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 20px 0;">暂无数据</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <div class="pagination-container" id="paginationContainer">
                <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                    <a href="adm.php?do=zhan&page=1<?php echo $keyword ? '&keyword=' . urlencode($keyword) : ''; ?>" class="page-btn first">起始页</a>
                    <a href="adm.php?do=zhan&page=<?php echo $page - 1; ?><?php echo $keyword ? '&keyword=' . urlencode($keyword) : ''; ?>" class="page-btn prev">上一页</a>
                    <?php else: ?>
                    <span class="page-btn first disabled">起始页</span>
                    <span class="page-btn prev disabled">上一页</span>
                    <?php endif; ?>
                    
                    <select class="page-select" onchange="location.href='adm.php?do=zhan&page='+this.value+'<?php echo $keyword ? '&keyword=' . urlencode($keyword) : ''; ?>'">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo $i == $page ? 'selected' : ''; ?>><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                    
                    <?php if ($page < $totalPages): ?>
                    <a href="adm.php?do=zhan&page=<?php echo $page + 1; ?><?php echo $keyword ? '&keyword=' . urlencode($keyword) : ''; ?>" class="page-btn next">下一页</a>
                    <a href="adm.php?do=zhan&page=<?php echo $totalPages; ?><?php echo $keyword ? '&keyword=' . urlencode($keyword) : ''; ?>" class="page-btn last">最后页</a>
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
        // 搜索功能
        document.addEventListener('DOMContentLoaded', function() {
            // 搜索按钮点击事件
            document.getElementById('searchBtn').addEventListener('click', function() {
                var keyword = document.getElementById('keyword').value.trim();
                location.href = 'adm.php?do=zhan' + (keyword ? '&keyword=' + encodeURIComponent(keyword) : '');
            });
            
            // 回车键搜索
            document.getElementById('keyword').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    document.getElementById('searchBtn').click();
                }
            });
            
            // 新增站点按钮点击事件
            document.getElementById('addBtn').addEventListener('click', function() {
                showAddStationModal();
            });
        });
        
        // 显示新增站点模态框
        function showAddStationModal() {
            var content = `
                <div class="form-group">
                    <label class="form-label">站点名称</label>
                    <input type="text" class="form-input" id="add-zhan" required>
                </div>
                <div class="form-group">
                    <label class="form-label">经度</label>
                    <input type="text" class="form-input" id="add-lng">
                </div>
                <div class="form-group">
                    <label class="form-label">纬度</label>
                    <input type="text" class="form-input" id="add-lat">
                </div>
            `;
            
            var buttons = [
                {
                    text: '取消',
                    class: 'btn-secondary',
                    callback: function() {
                        hideModal();
                    }
                },
                {
                    text: '提交',
                    class: 'btn-primary',
                    callback: function() {
                        var zhan = document.getElementById('add-zhan').value.trim();
                        var lng = document.getElementById('add-lng').value.trim();
                        var lat = document.getElementById('add-lat').value.trim();
                        
                        if (!zhan) {
                            showToast('请输入站点名称');
                            return;
                        }
                        
                        ajaxRequest({
                            url: 'adm.php?act=addzhan',
                            data: {
                                zhan: zhan,
                                lng: lng,
                                lat: lat
                            },
                            success: function(res) {
                                if (res.code === 0) {
                                    showToast(res.msg);
                                    hideModal();
                                    setTimeout(function() {
                                        location.reload();
                                    }, 1000);
                                } else {
                                    showToast(res.msg);
                                }
                            },
                            error: function(msg) {
                                showToast('提交失败: ' + msg);
                            }
                        });
                    }
                }
            ];
            
            showModal('新增站点', content, buttons);
        }
        
        // 显示修改站点模态框
        function editStation(id, zhan, lng, lat) {
            var content = `
                <div class="form-group">
                    <label class="form-label">站点名称</label>
                    <input type="text" class="form-input" id="edit-zhan" value="${zhan}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">经度</label>
                    <input type="text" class="form-input" id="edit-lng" value="${lng}">
                </div>
                <div class="form-group">
                    <label class="form-label">纬度</label>
                    <input type="text" class="form-input" id="edit-lat" value="${lat}">
                </div>
            `;
            
            var buttons = [
                {
                    text: '取消',
                    class: 'btn-secondary',
                    callback: function() {
                        hideModal();
                    }
                },
                {
                    text: '提交',
                    class: 'btn-primary',
                    callback: function() {
                        var zhan = document.getElementById('edit-zhan').value.trim();
                        var lng = document.getElementById('edit-lng').value.trim();
                        var lat = document.getElementById('edit-lat').value.trim();
                        
                        if (!zhan) {
                            showToast('请输入站点名称');
                            return;
                        }
                        
                        ajaxRequest({
                            url: 'adm.php?act=editzhan',
                            data: {
                                id: id,
                                zhan: zhan,
                                lng: lng,
                                lat: lat
                            },
                            success: function(res) {
                                if (res.code === 0) {
                                    showToast(res.msg);
                                    hideModal();
                                    setTimeout(function() {
                                        location.reload();
                                    }, 1000);
                                } else {
                                    showToast(res.msg);
                                }
                            },
                            error: function(msg) {
                                showToast('提交失败: ' + msg);
                            }
                        });
                    }
                }
            ];
            
            showModal('修改站点', content, buttons);
        }
        
        // 删除站点
        function deleteStation(id, zhan) {
            var buttons = [
                {
                    text: '取消',
                    class: 'btn-secondary',
                    callback: function() {
                        hideModal();
                    }
                },
                {
                    text: '确认删除',
                    class: 'btn-danger',
                    callback: function() {
                        ajaxRequest({
                            url: 'adm.php?act=delzhan',
                            data: {
                                id: id
                            },
                            success: function(res) {
                                if (res.code === 0) {
                                    showToast(res.msg);
                                    hideModal();
                                    setTimeout(function() {
                                        location.reload();
                                    }, 1000);
                                } else {
                                    if (res.data && res.data.lines) {
                                        // 显示使用该站点的线路
                                        var lineList = '';
                                        res.data.lines.forEach(function(line) {
                                            lineList += `<li>${line.name}</li>`;
                                        });
                                        
                                        var content = `
                                            <div class="alert alert-warning">该站点已被以下线路使用，无法删除：</div>
                                            <ul>${lineList}</ul>
                                            <p>请先修改这些线路，移除该站点后再尝试删除。</p>
                                        `;
                                        
                                        showModal('无法删除站点', content, [{
                                            text: '确定',
                                            callback: function() {
                                                hideModal();
                                            }
                                        }]);
                                    } else {
                                        showToast(res.msg);
                                    }
                                }
                            },
                            error: function(msg) {
                                showToast('删除失败: ' + msg);
                            }
                        });
                    }
                }
            ];
            
            showModal('删除站点', `确定要删除站点 "${zhan}" 吗？`, buttons);
        }
    </script>
    
    <style>
    .admin-nav {
        display: flex;
        background-color: #f5f5f5;
        border-radius: 5px;
        margin-top: 10px;
        overflow: hidden;
    }
    
    .admin-nav-item {
        padding: 10px 15px;
        color: #333;
        text-decoration: none;
        border-right: 1px solid #ddd;
    }
    
    .admin-nav-item:last-child {
        border-right: none;
    }
    
    .admin-nav-item.active {
        background-color: #1e88e5;
        color: #fff;
    }
    
    .alert {
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 15px;
    }
    
    .alert-warning {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }
    </style>
</body>
</html>

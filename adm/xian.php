<?php

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: adm/xian.php
// 文件大小: 25158 字节

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: adm/xian.php
// 文件大小: 24939 字节
/**
 * 本文件功能: 线路管理页面
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
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>线路管理 - 公交查询系统</title>
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
                <div class="table-title">线路列表</div>
                <div class="table-actions">
                    <div class="table-search">
                        <input type="text" id="keyword" placeholder="线路名称..." value="<?php echo $keyword; ?>">
                        <button id="searchBtn">查询</button>
                    </div>
                    <button class="table-btn" id="addBtn">新增线路</button>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>线路名称</th>
                        <!--th>线路类型</th->
                        <!--th>运行时间</th-->
                        <th>起始站</th>
                        <th>终点站</th>
                        <th>运营公司</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody id="lineList">
                    <?php if (!empty($lines)): ?>
                        <?php foreach ($lines as $line): ?>
                        <tr>
                            <td><?php echo $line['id']; ?></td>
                            <td><?php echo $line['name']; ?></td>
                            <!--td><?php echo $line['type']; ?></td-->
                            <!--td><?php echo $line['time']; ?></td-->
                            <td><?php echo $line['start']; ?></td>
                            <td><?php echo $line['end']; ?></td>
                            <td><?php echo $line['comp']; ?></td>
                            <td>
                                <a href="adm.php?do=gaix&id=<?php echo $line['id']; ?>" class="action-btn">站点管理</a>
                                <a href="javascript:void(0);" class="action-btn" onclick="editLine(<?php echo $line['id']; ?>)">详情修改</a>
                                <a href="javascript:void(0);" class="action-btn delete" onclick="deleteLine(<?php echo $line['id']; ?>, '<?php echo $line['name']; ?>')">删除</a>
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
                    <a href="adm.php?do=xian&page=1<?php echo $keyword ? '&keyword=' . urlencode($keyword) : ''; ?>" class="page-btn first">起始页</a>
                    <a href="adm.php?do=xian&page=<?php echo $page - 1; ?><?php echo $keyword ? '&keyword=' . urlencode($keyword) : ''; ?>" class="page-btn prev">上一页</a>
                    <?php else: ?>
                    <span class="page-btn first disabled">起始页</span>
                    <span class="page-btn prev disabled">上一页</span>
                    <?php endif; ?>
                    
                    <select class="page-select" onchange="location.href='adm.php?do=xian&page='+this.value+'<?php echo $keyword ? '&keyword=' . urlencode($keyword) : ''; ?>'">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo $i == $page ? 'selected' : ''; ?>><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                    
                    <?php if ($page < $totalPages): ?>
                    <a href="adm.php?do=xian&page=<?php echo $page + 1; ?><?php echo $keyword ? '&keyword=' . urlencode($keyword) : ''; ?>" class="page-btn next">下一页</a>
                    <a href="adm.php?do=xian&page=<?php echo $totalPages; ?><?php echo $keyword ? '&keyword=' . urlencode($keyword) : ''; ?>" class="page-btn last">最后页</a>
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
        // 获取线路类型和运营公司列表（去重）
        var typeList = [];
        var compList = [];
        
        <?php
        // 获取所有线路类型
        $types = $db->execute("SELECT DISTINCT type FROM line WHERE type != '' ORDER BY type");
        if (!empty($types)) {
            echo "typeList = [";
            foreach ($types as $key => $type) {
                if ($key > 0) echo ", ";
                echo "'" . $type['type'] . "'";
            }
            echo "];";
        }
        
        // 获取所有运营公司
        $comps = $db->execute("SELECT DISTINCT comp FROM line WHERE comp != '' ORDER BY comp");
        if (!empty($comps)) {
            echo "compList = [";
            foreach ($comps as $key => $comp) {
                if ($key > 0) echo ", ";
                echo "'" . $comp['comp'] . "'";
            }
            echo "];";
        }
        ?>
        
        document.addEventListener('DOMContentLoaded', function() {
            // 搜索按钮点击事件
            document.getElementById('searchBtn').addEventListener('click', function() {
                var keyword = document.getElementById('keyword').value.trim();
                location.href = 'adm.php?do=xian' + (keyword ? '&keyword=' + encodeURIComponent(keyword) : '');
            });
            
            // 回车键搜索
            document.getElementById('keyword').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    document.getElementById('searchBtn').click();
                }
            });
            
            // 新增线路按钮点击事件
            document.getElementById('addBtn').addEventListener('click', function() {
                showAddLineModal();
            });
        });
        
        // 显示新增线路模态框
        function showAddLineModal() {
            var typeOptions = '';
            typeList.forEach(function(type) {
                typeOptions += `<option value="${type}">${type}</option>`;
            });
            
            var compOptions = '';
            compList.forEach(function(comp) {
                compOptions += `<option value="${comp}">${comp}</option>`;
            });
            
            var content = `
                <div class="form-group">
                    <label class="form-label">线路名称</label>
                    <input type="text" class="form-input" id="add-name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">线路类型</label>
                    <input type="text" class="form-input" id="add-type" list="type-list">
                    <datalist id="type-list">
                        ${typeOptions}
                    </datalist>
                </div>
                <div class="form-group">
                    <label class="form-label">运行时间</label>
                    <input type="text" class="form-input" id="add-time">
                </div>
                <div class="form-group">
                    <label class="form-label">起始站</label>
                    <input type="text" class="form-input" id="add-start">
                </div>
                <div class="form-group">
                    <label class="form-label">终点站</label>
                    <input type="text" class="form-input" id="add-end">
                </div>
                <div class="form-group">
                    <label class="form-label">运营公司</label>
                    <input type="text" class="form-input" id="add-comp" list="comp-list">
                    <datalist id="comp-list">
                        ${compOptions}
                    </datalist>
                </div>
                <div class="form-group">
                    <label class="form-label">票价</label>
                    <input type="text" class="form-input" id="add-fare">
                </div>
                <div class="form-group">
                    <label class="form-label">备注</label>
                    <textarea class="form-textarea" id="add-note"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">正向站点列表（每行一个站点）</label>
                    <textarea class="form-textarea" id="add-zlist" rows="8"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">反向站点列表（每行一个站点）</label>
                    <textarea class="form-textarea" id="add-flist" rows="8"></textarea>
                    <button type="button" class="form-btn" style="margin-top: 5px;" onclick="reverseStations()">从正向站点逆序生成</button>
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
                        var name = document.getElementById('add-name').value.trim();
                        var type = document.getElementById('add-type').value.trim();
                        var time = document.getElementById('add-time').value.trim();
                        var start = document.getElementById('add-start').value.trim();
                        var end = document.getElementById('add-end').value.trim();
                        var comp = document.getElementById('add-comp').value.trim();
                        var fare = document.getElementById('add-fare').value.trim();
                        var note = document.getElementById('add-note').value.trim();
                        var zlist = document.getElementById('add-zlist').value.trim();
                        var flist = document.getElementById('add-flist').value.trim();
                        
                        if (!name) {
                            showToast('请输入线路名称');
                            return;
                        }
                        
                        if (!zlist) {
                            showToast('请输入正向站点列表');
                            return;
                        }
                        
                        ajaxRequest({
                            url: 'adm.php?act=addxian',
                            data: {
                                name: name,
                                type: type,
                                time: time,
                                start: start,
                                end: end,
                                comp: comp,
                                fare: fare,
                                note: note,
                                zlist: zlist,
                                flist: flist
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
            
            showModal('新增线路', content, buttons);
        }
        
        // 从正向站点逆序生成反向站点
        function reverseStations() {
            var zlist = document.getElementById('add-zlist').value.trim();
            if (!zlist) {
                showToast('请先输入正向站点列表');
                return;
            }
            
            var stations = zlist.split('\n');
            var reversedStations = [];
            
            for (var i = stations.length - 1; i >= 0; i--) {
                if (stations[i].trim()) {
                    reversedStations.push(stations[i]);
                }
            }
            
            document.getElementById('add-flist').value = reversedStations.join('\n');
        }
        
        // 修改线路详情
        function editLine(id) {
            ajaxRequest({
                url: 'index.php?act=page',
                data: {
                    table: 'line',
                    page: 1,
                    pageSize: 1,
                    field: 'id',
                    keyword: id
                },
                success: function(res) {
                    if (res.code === 0 && res.data.data.length > 0) {
                        var line = res.data.data[0];
                        
                        var typeOptions = '';
                        typeList.forEach(function(type) {
                            typeOptions += `<option value="${type}" ${line.type === type ? 'selected' : ''}>${type}</option>`;
                        });
                        
                        var compOptions = '';
                        compList.forEach(function(comp) {
                            compOptions += `<option value="${comp}" ${line.comp === comp ? 'selected' : ''}>${comp}</option>`;
                        });
                        
                        var content = `
                            <div class="form-group">
                                <label class="form-label">线路名称</label>
                                <input type="text" class="form-input" id="edit-name" value="${line.name}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">线路类型</label>
                                <input type="text" class="form-input" id="edit-type" value="${line.type}" list="type-list">
                                <datalist id="type-list">
                                    ${typeOptions}
                                </datalist>
                            </div>
                            <div class="form-group">
                                <label class="form-label">运行时间</label>
                                <input type="text" class="form-input" id="edit-time" value="${line.time || ''}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">起始站</label>
                                <input type="text" class="form-input" id="edit-start" value="${line.start || ''}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">终点站</label>
                                <input type="text" class="form-input" id="edit-end" value="${line.end || ''}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">运营公司</label>
                                <input type="text" class="form-input" id="edit-comp" value="${line.comp || ''}" list="comp-list">
                                <datalist id="comp-list">
                                    ${compOptions}
                                </datalist>
                            </div>
                            <div class="form-group">
                                <label class="form-label">票价</label>
                                <input type="text" class="form-input" id="edit-fare" value="${line.fare || ''}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">备注</label>
                                <textarea class="form-textarea" id="edit-note">${line.note || ''}</textarea>
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
                                    var name = document.getElementById('edit-name').value.trim();
                                    var type = document.getElementById('edit-type').value.trim();
                                    var time = document.getElementById('edit-time').value.trim();
                                    var start = document.getElementById('edit-start').value.trim();
                                    var end = document.getElementById('edit-end').value.trim();
                                    var comp = document.getElementById('edit-comp').value.trim();
                                    var fare = document.getElementById('edit-fare').value.trim();
                                    var note = document.getElementById('edit-note').value.trim();
                                    
                                    if (!name) {
                                        showToast('请输入线路名称');
                                        return;
                                    }
                                    
                                    ajaxRequest({
                                        url: 'adm.php?act=editxian',
                                        data: {
                                            id: id,
                                            name: name,
                                            type: type,
                                            time: time,
                                            start: start,
                                            end: end,
                                            comp: comp,
                                            fare: fare,
                                            note: note
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
                        
                        showModal('修改线路详情', content, buttons);
                    } else {
                        showToast('获取线路信息失败');
                    }
                },
                error: function(msg) {
                    showToast('获取线路信息失败: ' + msg);
                }
            });
        }
        
        // 删除线路
        function deleteLine(id, name) {
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
                            url: 'adm.php?act=delxian',
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
                                    showToast(res.msg);
                                }
                            },
                            error: function(msg) {
                                showToast('删除失败: ' + msg);
                            }
                        });
                    }
                }
            ];
            
            showModal('删除线路', `确定要删除线路 "${name}" 吗？此操作不可恢复。`, buttons);
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
    
    .modal-content {
        max-width: 600px;
    }
    </style>
</body>
</html>

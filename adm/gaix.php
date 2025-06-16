<?php

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: adm/gaix.php
// 文件大小: 24155 字节

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: adm/gaix.php
// 文件大小: 23936 字节
/**
 * 本文件功能: 线路站点管理页面
 * 版权声明: 保留发行权和署名权
 * 作者信息: 15058593138@qq.com
 */

require_once './inc/sqls.php';

$db = new Sqls();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 获取线路信息
$line = $db->getOne('line', '*', "id = {$id}");
if (!$line) {
    echo '<script>alert("线路不存在"); location.href="adm.php?do=xian";</script>';
    exit;
}

// 解析正向和反向站点
$zStations = [];
$fStations = [];

if (!empty($line['zlist'])) {
    $zStationIds = explode('-', trim($line['zlist'], '-'));
    foreach ($zStationIds as $zid) {
        $station = $db->getOne('zhan', '*', "zid = {$zid}");
        if ($station) {
            $zStations[] = $station;
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

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>线路站点管理 - <?php echo $line['name']; ?> - 公交查询系统</title>
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
                <a href="adm.php?do=<?php echo $key; ?>" class="admin-nav-item <?php echo $key == 'xian' ? 'active' : ''; ?>"><?php echo $name; ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="line-info">
            <h2><?php echo $line['name']; ?> 站点管理</h2>
            <p><a href="adm.php?do=xian" class="back-link">← 返回线路列表</a></p>
        </div>
        
        <div class="tab-container">
            <div class="tab-header">
                <div class="tab active" data-target="forward-stations">正向站点</div>
                <div class="tab" data-target="backward-stations">反向站点</div>
            </div>
            
            <div class="tab-content">
                <!-- 正向站点 -->
                <div class="tab-pane active" id="forward-stations">
                    <div class="station-actions">
                        <button class="form-btn" onclick="addStation('zlist')">添加站点</button>
                        <button class="form-btn" onclick="editStationRaw('zlist', '<?php echo htmlspecialchars($line['zlist']); ?>')">批量编辑</button>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th width="60">序号</th>
                                <th>站点名称</th>
                                <th width="180">操作</th>
                            </tr>
                        </thead>
                        <tbody id="zStationList">
                            <?php if (!empty($zStations)): ?>
                                <?php foreach ($zStations as $index => $station): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo $station['zhan']; ?></td>
                                    <td>
                                        <a href="javascript:void(0);" class="action-btn" onclick="editStation('zlist', <?php echo $station['zid']; ?>, '<?php echo $station['zhan']; ?>')">修改</a>
                                        <a href="javascript:void(0);" class="action-btn delete" onclick="removeStation('zlist', <?php echo $station['zid']; ?>, '<?php echo $station['zhan']; ?>')">删除</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" style="text-align: center; padding: 20px 0;">暂无站点数据</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- 反向站点 -->
                <div class="tab-pane" id="backward-stations">
                    <div class="station-actions">
                        <button class="form-btn" onclick="addStation('flist')">添加站点</button>
                        <button class="form-btn" onclick="editStationRaw('flist', '<?php echo htmlspecialchars($line['flist']); ?>')">批量编辑</button>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th width="60">序号</th>
                                <th>站点名称</th>
                                <th width="180">操作</th>
                            </tr>
                        </thead>
                        <tbody id="fStationList">
                            <?php if (!empty($fStations)): ?>
                                <?php foreach ($fStations as $index => $station): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo $station['zhan']; ?></td>
                                    <td>
                                        <a href="javascript:void(0);" class="action-btn" onclick="editStation('flist', <?php echo $station['zid']; ?>, '<?php echo $station['zhan']; ?>')">修改</a>
                                        <a href="javascript:void(0);" class="action-btn delete" onclick="removeStation('flist', <?php echo $station['zid']; ?>, '<?php echo $station['zhan']; ?>')">删除</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" style="text-align: center; padding: 20px 0;">暂无站点数据</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
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
        
        // 添加站点
        function addStation(type) {
            var title = type === 'zlist' ? '添加正向站点' : '添加反向站点';
            
            var content = `
                <div class="form-group">
                    <label class="form-label">站点名称</label>
                    <input type="text" class="form-input" id="add-station-name" autocomplete="off">
                    <div id="station-suggestions" class="station-suggestions"></div>
                </div>
                <div class="form-group">
                    <label class="form-label">插入位置</label>
                    <select class="form-select" id="add-position">
                        <option value="0">添加到末尾</option>
                        <?php 
                        $stations = $type === 'zlist' ? $zStations : $fStations;
                        foreach ($stations as $index => $station): 
                        ?>
                        <option value="<?php echo $index + 1; ?>">插入到第 <?php echo $index + 1; ?> 位之前</option>
                        <?php endforeach; ?>
                    </select>
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
                        var stationName = document.getElementById('add-station-name').value.trim();
                        var position = document.getElementById('add-position').value;
                        
                        if (!stationName) {
                            showToast('请输入站点名称');
                            return;
                        }
                        
                        ajaxRequest({
                            url: 'adm.php?act=addstation',
                            data: {
                                lineId: <?php echo $id; ?>,
                                type: type,
                                stationName: stationName,
                                position: position
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
            
            showModal(title, content, buttons);
            
            // 站点输入提示
            var stationInput = document.getElementById('add-station-name');
            var stationSuggestions = document.getElementById('station-suggestions');
            var timer = null;
            
            stationInput.addEventListener('input', function() {
                var keyword = this.value.trim();
                
                if (!keyword) {
                    stationSuggestions.innerHTML = '';
                    return;
                }
                
                clearTimeout(timer);
                
                timer = setTimeout(function() {
                    ajaxRequest({
                        url: 'index.php?act=suggest',
                        data: {
                            keyword: keyword,
                            type: 'station'
                        },
                        success: function(res) {
                            if (res.code === 0 && res.data && res.data.length > 0) {
                                var html = '';
                                res.data.forEach(function(item) {
                                    html += `<div class="suggestion-item" onclick="selectStation('${item.zhan}')">${item.zhan}</div>`;
                                });
                                stationSuggestions.innerHTML = html;
                                stationSuggestions.style.display = 'block';
                            } else {
                                stationSuggestions.innerHTML = '';
                                stationSuggestions.style.display = 'none';
                            }
                        }
                    });
                }, 500);
            });
            
            // 点击页面其他区域关闭提示
            document.addEventListener('click', function(e) {
                if (e.target !== stationInput && !stationSuggestions.contains(e.target)) {
                    stationSuggestions.style.display = 'none';
                }
            });
        }
        
        // 选择站点
        function selectStation(name) {
            document.getElementById('add-station-name').value = name;
            document.getElementById('station-suggestions').style.display = 'none';
        }
        
        // 修改站点
        function editStation(type, stationId, stationName) {
            var title = '修改站点';
            
            var content = `
                <div class="form-group">
                    <label class="form-label">站点名称</label>
                    <input type="text" class="form-input" id="edit-station-name" value="${stationName}" autocomplete="off">
                    <div id="station-suggestions" class="station-suggestions"></div>
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
                        var newStationName = document.getElementById('edit-station-name').value.trim();
                        
                        if (!newStationName) {
                            showToast('请输入站点名称');
                            return;
                        }
                        
                        ajaxRequest({
                            url: 'adm.php?act=addstation',
                            data: {
                                lineId: <?php echo $id; ?>,
                                type: type,
                                stationName: newStationName,
                                position: 0, // 添加到末尾
                                replaceId: stationId
                            },
                            success: function(res) {
                                if (res.code === 0) {
                                    showToast('站点修改成功');
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
            
            showModal(title, content, buttons);
            
            // 站点输入提示
            var stationInput = document.getElementById('edit-station-name');
            var stationSuggestions = document.getElementById('station-suggestions');
            var timer = null;
            
            stationInput.addEventListener('input', function() {
                var keyword = this.value.trim();
                
                if (!keyword) {
                    stationSuggestions.innerHTML = '';
                    return;
                }
                
                clearTimeout(timer);
                
                timer = setTimeout(function() {
                    ajaxRequest({
                        url: 'index.php?act=suggest',
                        data: {
                            keyword: keyword,
                            type: 'station'
                        },
                        success: function(res) {
                            if (res.code === 0 && res.data && res.data.length > 0) {
                                var html = '';
                                res.data.forEach(function(item) {
                                    html += `<div class="suggestion-item" onclick="selectEditStation('${item.zhan}')">${item.zhan}</div>`;
                                });
                                stationSuggestions.innerHTML = html;
                                stationSuggestions.style.display = 'block';
                            } else {
                                stationSuggestions.innerHTML = '';
                                stationSuggestions.style.display = 'none';
                            }
                        }
                    });
                }, 500);
            });
            
            // 点击页面其他区域关闭提示
            document.addEventListener('click', function(e) {
                if (e.target !== stationInput && !stationSuggestions.contains(e.target)) {
                    stationSuggestions.style.display = 'none';
                }
            });
        }
        
        // 选择修改站点
        function selectEditStation(name) {
            document.getElementById('edit-station-name').value = name;
            document.getElementById('station-suggestions').style.display = 'none';
        }
        
        // 移除站点
        function removeStation(type, stationId, stationName) {
            var title = '删除站点';
            var content = `确定要从${type === 'zlist' ? '正向' : '反向'}站点列表中删除站点 "${stationName}" 吗？`;
            
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
                            url: 'adm.php?act=removestation',
                            data: {
                                lineId: <?php echo $id; ?>,
                                type: type,
                                stationId: stationId
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
            
            showModal(title, content, buttons);
        }
        
        // 批量编辑站点（原始值）
        function editStationRaw(type, rawValue) {
            var title = type === 'zlist' ? '批量编辑正向站点' : '批量编辑反向站点';
            
            var content = `
                <div class="form-group">
                    <label class="form-label">站点列表原始值</label>
                    <p class="form-tip">直接编辑下面的值，格式为: -站点ID-站点ID-站点ID- 的形式</p>
                    <textarea class="form-textarea" id="edit-stations-raw" rows="10">${rawValue}</textarea>
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
                        var rawStations = document.getElementById('edit-stations-raw').value.trim();
                        
                        ajaxRequest({
                            url: 'adm.php?act=updatestations',
                            data: {
                                id: <?php echo $id; ?>,
                                type: type,
                                stations: rawStations
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
            
            showModal(title, content, buttons);
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
    
    .line-info {
        margin: 20px 0;
    }
    
    .back-link {
        color: #1e88e5;
        text-decoration: none;
        font-size: 14px;
    }
    
    .back-link:hover {
        text-decoration: underline;
    }
    
    .station-actions {
        margin-bottom: 15px;
    }
    
    .station-suggestions {
        position: absolute;
        background: white;
        border: 1px solid #ddd;
        border-radius: 3px;
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
        z-index: 100;
        display: none;
    }
    
    .suggestion-item {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
    }
    
    .suggestion-item:hover {
        background-color: #f5f5f5;
    }
    
    .form-tip {
        margin-top: 5px;
        font-size: 12px;
        color: #666;
    }
    </style>
</body>
</html>

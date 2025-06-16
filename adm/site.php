<?php

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: adm/site.php
// 文件大小: 5357 字节

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: adm/site.php
// 文件大小: 5139 字节
/**
 * 本文件功能: 系统设置页面
 * 版权声明: 保留发行权和署名权
 * 作者信息: 15058593138@qq.com
 */

// 读取网站设置
$siteConfigFile = './inc/site.json.php';
if (file_exists($siteConfigFile)) {
    $siteConfig = json_decode(file_get_contents($siteConfigFile), true);
} else {
    // 创建默认配置文件
    file_put_contents($siteConfigFile, json_encode($siteConfig, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>系统设置 - 公交查询系统</title>
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
        <div class="form-container">
            <div class="form-title">系统设置</div>
            
            <form id="siteForm" action="javascript:void(0);">
                <div class="form-group">
                    <label class="form-label">网站名称</label>
                    <input type="text" class="form-input" name="siteName" id="siteName" value="<?php echo $siteConfig['siteName']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">城市名称</label>
                    <input type="text" class="form-input" name="cityName" id="cityName" value="<?php echo $siteConfig['cityName']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">每页显示记录数</label>
                    <input type="number" class="form-input" name="pageSize" id="pageSize" value="<?php echo $siteConfig['pageSize']; ?>" min="5" max="50" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">百度地图API密钥</label>
                    <input type="text" class="form-input" name="mapKey" id="mapKey" value="<?php echo $siteConfig['mapKey']; ?>">
                    <p class="form-tip" style="margin-top: 5px; color: #666; font-size: 12px;">可从百度地图开放平台获取: <a href="https://lbsyun.baidu.com/" target="_blank">https://lbsyun.baidu.com/</a></p>
                </div>
                
                <div class="form-group" style="margin-top: 20px;">
                    <button type="submit" class="form-btn">保存设置</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('siteForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                var siteName = document.getElementById('siteName').value.trim();
                var cityName = document.getElementById('cityName').value.trim();
                var pageSize = document.getElementById('pageSize').value.trim();
                var mapKey = document.getElementById('mapKey').value.trim();
                
                if (!siteName) {
                    showToast('请输入网站名称');
                    return;
                }
                
                if (!cityName) {
                    showToast('请输入城市名称');
                    return;
                }
                
                if (!pageSize || pageSize < 5 || pageSize > 50) {
                    showToast('每页显示记录数必须在5-50之间');
                    return;
                }
                
                ajaxRequest({
                    url: 'adm.php?act=savesite',
                    data: {
                        siteName: siteName,
                        cityName: cityName,
                        pageSize: pageSize,
                        mapKey: mapKey
                    },
                    success: function(res) {
                        if (res.code === 0) {
                            showToast(res.msg);
                        } else {
                            showToast(res.msg);
                        }
                    },
                    error: function(msg) {
                        showToast('保存失败: ' + msg);
                    }
                });
            });
        });
    </script>
</body>
</html>

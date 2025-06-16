<?php

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: adm/pass.php
// 文件大小: 4417 字节

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: adm/pass.php
// 文件大小: 4199 字节
/**
 * 本文件功能: 修改密码页面
 * 版权声明: 保留发行权和署名权
 * 作者信息: 15058593138@qq.com
 */
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>修改密码 - 公交查询系统</title>
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
            <div class="form-title">修改密码</div>
            
            <form id="passForm" action="javascript:void(0);">
                <div class="form-group">
                    <label class="form-label">原密码</label>
                    <input type="password" class="form-input" name="oldpass" id="oldpass" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">新密码</label>
                    <input type="password" class="form-input" name="newpass" id="newpass" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">确认新密码</label>
                    <input type="password" class="form-input" name="confirm" id="confirm" required>
                </div>
                
                <div class="form-group" style="margin-top: 20px;">
                    <button type="submit" class="form-btn">修改密码</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('passForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                var oldpass = document.getElementById('oldpass').value;
                var newpass = document.getElementById('newpass').value;
                var confirm = document.getElementById('confirm').value;
                
                if (!oldpass) {
                    showToast('请输入原密码');
                    return;
                }
                
                if (!newpass) {
                    showToast('请输入新密码');
                    return;
                }
                
                if (newpass.length < 6) {
                    showToast('新密码长度不能少于6位');
                    return;
                }
                
                if (newpass !== confirm) {
                    showToast('两次输入的新密码不一致');
                    return;
                }
                
                ajaxRequest({
                    url: 'adm.php?act=changepass',
                    data: {
                        oldpass: oldpass,
                        newpass: newpass,
                        confirm: confirm
                    },
                    success: function(res) {
                        if (res.code === 0) {
                            showToast(res.msg);
                            document.getElementById('passForm').reset();
                        } else {
                            showToast(res.msg);
                        }
                    },
                    error: function(msg) {
                        showToast('修改失败: ' + msg);
                    }
                });
            });
        });
    </script>
</body>
</html>

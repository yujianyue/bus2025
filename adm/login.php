<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员登录 - 公交查询系统</title>
    <link rel="stylesheet" href="./inc/pubs.css?v=<?php echo $version; ?>">
    <script src="./inc/js.js?v=<?php echo $version; ?>"></script>
</head>
<body>
    <div class="container" style="max-width: 400px; margin: 100px auto;">
        <div class="form-container">
            <div class="form-title" style="text-align: center;">管理员登录</div>
            
            <form id="loginForm" action="javascript:void(0);">
                <div class="form-group">
                    <label class="form-label">用户名</label>
                    <input type="text" class="form-input" name="username" id="username" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">密码</label>
                    <input type="password" class="form-input" name="password" id="password" required>
                </div>
                
                <div class="form-group" style="margin-top: 20px;">
                    <button type="submit" class="form-btn" style="width: 100%;">登录</button>
                </div>
                
                <div class="form-group" style="margin-top: 15px; text-align: center;">
                    <a href="index.php">返回首页</a>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('loginForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                var username = document.getElementById('username').value.trim();
                var password = document.getElementById('password').value;
                
                if (!username) {
                    showToast('请输入用户名');
                    return;
                }
                
                if (!password) {
                    showToast('请输入密码');
                    return;
                }
                
                ajaxRequest({
                    url: 'adm.php?act=login',
                    data: {
                        username: username,
                        password: password
                    },
                    success: function(res) {
                        if (res.code === 0) {
                            showToast('登录成功，正在跳转...');
                            setTimeout(function() {
                                location.href = 'adm.php?do=site';
                            }, 1000);
                        } else {
                            showToast(res.msg);
                        }
                    },
                    error: function(msg) {
                        showToast('登录失败: ' + msg);
                    }
                });
            });
        });
    </script>
</body>
</html>

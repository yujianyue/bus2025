<?php

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: install.php
// 文件大小: 20773 字节

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: install.php
// 文件大小: 20555 字节

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: install.php
// 文件大小: 20337 字节
/**
 * 本文件功能: 系统安装程序
 * 版权声明: 保留发行权和署名权
 * 作者信息: 15058593138@qq.com
 */

// 检查是否已安装
if (file_exists('./inc/site.json.php') && file_exists('./inc/user.json.php')) {
    // 已经安装过，跳转到首页
    header('Location: index.php');
    exit;
}

// 引入配置文件
require_once './inc/conn.php';

// 处理安装请求
$act = isset($_GET['act']) ? $_GET['act'] : '';

if ($act == 'install') {
    // 创建数据库表
    $conn = @mysqli_connect($dbConfig['host'], $dbConfig['user'], $dbConfig['pass'], '', $dbConfig['port']);
    
    if (!$conn) {
        echo json_encode(['code' => 1, 'msg' => '数据库连接失败: ' . mysqli_connect_error()]);
        exit;
    }
    
    // 检查数据库是否存在，如果不存在则创建
    $sql = "CREATE DATABASE IF NOT EXISTS `{$dbConfig['name']}` DEFAULT CHARACTER SET {$dbConfig['charset']} COLLATE {$dbConfig['charset']}_general_ci";
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        echo json_encode(['code' => 1, 'msg' => '创建数据库失败: ' . mysqli_error($conn)]);
        exit;
    }
    
    // 选择数据库
    mysqli_select_db($conn, $dbConfig['name']);
    
    // 设置字符集
    mysqli_set_charset($conn, $dbConfig['charset']);
    
    // 创建线路表
    $sql = "CREATE TABLE IF NOT EXISTS `line` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(100) NOT NULL COMMENT '线路名称',
      `type` varchar(100) DEFAULT NULL COMMENT '线路类别',
      `time` varchar(1024) DEFAULT NULL COMMENT '运行时间',
      `gtime` varchar(50) DEFAULT NULL COMMENT '最后更新时间',
      `start` varchar(100) DEFAULT NULL COMMENT '起始站点',
      `end` varchar(100) DEFAULT NULL COMMENT '终点站',
      `comp` varchar(100) DEFAULT NULL COMMENT '运营公司',
      `note` text DEFAULT NULL COMMENT '备注',
      `fare` varchar(256) DEFAULT NULL COMMENT '费用',
      `zlist` text DEFAULT NULL COMMENT '站点正向(升序排列，-号开头结尾分隔)',
      `flist` text DEFAULT NULL COMMENT '站点反程(升序排列，-号开头结尾分隔)',
      PRIMARY KEY (`id`),
      KEY `idx_name` (`name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公交线路表'";
    
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        echo json_encode(['code' => 1, 'msg' => '创建线路表失败: ' . mysqli_error($conn)]);
        exit;
    }
    
    // 创建站点表
    $sql = "CREATE TABLE IF NOT EXISTS `zhan` (
      `zid` int(11) NOT NULL AUTO_INCREMENT,
      `zhan` varchar(100) NOT NULL COMMENT '站点名称',
      `ping` varchar(100) DEFAULT NULL COMMENT '站点拼音',
      `lng` varchar(16) DEFAULT NULL COMMENT '经度',
      `lat` varchar(16) DEFAULT NULL COMMENT '纬度',
      PRIMARY KEY (`zid`),
      KEY `idx_zhan` (`zhan`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公交站点表'";
    
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        echo json_encode(['code' => 1, 'msg' => '创建站点表失败: ' . mysqli_error($conn)]);
        exit;
    }

    // 安装演示数据
    $demoData = isset($_POST['demoData']) ? intval($_POST['demoData']) : 0;
    if ($demoData) {
        // 插入演示站点数据
        $demoStations = [
            ['zhan' => '火车东站', 'ping' => 'huochedongzhan', 'lng' => '120.2158', 'lat' => '30.2028'],
            ['zhan' => '城站火车站', 'ping' => 'chengzhanhuochezhan', 'lng' => '120.1675', 'lat' => '30.2425'],
            ['zhan' => '武林广场', 'ping' => 'wulinguangchang', 'lng' => '120.1699', 'lat' => '30.2765'],
            ['zhan' => '西湖文化广场', 'ping' => 'xihuwenhuaguangchang', 'lng' => '120.1542', 'lat' => '30.2621'],
            ['zhan' => '凤起路', 'ping' => 'fengqilu', 'lng' => '120.1888', 'lat' => '30.2595'],
            ['zhan' => '龙翔桥', 'ping' => 'longxiangqiao', 'lng' => '120.1686', 'lat' => '30.2541'],
            ['zhan' => '浙大玉泉', 'ping' => 'zhedayuquan', 'lng' => '120.1279', 'lat' => '30.2675'],
            ['zhan' => '西溪湿地', 'ping' => 'xixishidi', 'lng' => '120.0711', 'lat' => '30.2650'],
            ['zhan' => '黄龙', 'ping' => 'huanglong', 'lng' => '120.1335', 'lat' => '30.2786'],
            ['zhan' => '杭州汽车东站', 'ping' => 'hangzhouqichedongzhan', 'lng' => '120.2158', 'lat' => '30.2023'],
            ['zhan' => '景芳', 'ping' => 'jingfang', 'lng' => '120.1944', 'lat' => '30.2419'],
            ['zhan' => '钱江路', 'ping' => 'qianjianlu', 'lng' => '120.2125', 'lat' => '30.2297'],
            ['zhan' => '萧山国际机场', 'ping' => 'xiaoshanguojijichang', 'lng' => '120.4323', 'lat' => '30.2350'],
            ['zhan' => '婺江路', 'ping' => 'wujianglu', 'lng' => '120.1520', 'lat' => '30.3022'],
            ['zhan' => '滨江', 'ping' => 'binjiang', 'lng' => '120.2118', 'lat' => '30.2095'],
            ['zhan' => '西兴', 'ping' => 'xixing', 'lng' => '120.1877', 'lat' => '30.1841'],
            ['zhan' => '江南大道', 'ping' => 'jiangnadao', 'lng' => '120.2070', 'lat' => '30.1939'],
            ['zhan' => '文泽路', 'ping' => 'wenzelu', 'lng' => '120.1742', 'lat' => '30.2869'],
            ['zhan' => '杭州师范大学', 'ping' => 'hangzhoudaxue', 'lng' => '120.0280', 'lat' => '30.3186'],
            ['zhan' => '下沙高教园区', 'ping' => 'xiashagaojiaoyuanqu', 'lng' => '120.3456', 'lat' => '30.3081']
        ];
        
        foreach ($demoStations as $station) {
            $sql = "INSERT INTO `zhan` (`zhan`, `ping`, `lng`, `lat`) VALUES ('{$station['zhan']}', '{$station['ping']}', '{$station['lng']}', '{$station['lat']}')";
            mysqli_query($conn, $sql);
        }
        
        // 获取所有站点ID
        $stationMap = [];
        $result = mysqli_query($conn, "SELECT zid, zhan FROM zhan");
        while ($row = mysqli_fetch_assoc($result)) {
            $stationMap[$row['zhan']] = $row['zid'];
        }
        
        // 插入演示线路数据
        $demoLines = [
            [
                'name' => '1路公交车',
                'type' => '公交线路',
                'time' => '6:00-22:00',
                'gtime' => date('Y-m-d H:i:s'),
                'start' => '火车东站',
                'end' => '西溪湿地',
                'comp' => '杭州公交集团',
                'note' => '杭州市区主要公交线路之一',
                'fare' => '2元',
                'zStations' => ['火车东站', '杭州汽车东站', '景芳', '钱江路', '凤起路', '武林广场', '西湖文化广场', '黄龙', '浙大玉泉', '西溪湿地'],
                'fStations' => ['西溪湿地', '浙大玉泉', '黄龙', '西湖文化广场', '武林广场', '凤起路', '钱江路', '景芳', '杭州汽车东站', '火车东站']
            ],
            [
                'name' => '2路公交车',
                'type' => '公交线路',
                'time' => '5:30-21:30',
                'gtime' => date('Y-m-d H:i:s'),
                'start' => '城站火车站',
                'end' => '滨江',
                'comp' => '杭州公交集团',
                'note' => '连接主城区和滨江区的公交线路',
                'fare' => '2元',
                'zStations' => ['城站火车站', '龙翔桥', '武林广场', '文泽路', '黄龙', '西湖文化广场', '江南大道', '滨江'],
                'fStations' => ['滨江', '江南大道', '西湖文化广场', '黄龙', '文泽路', '武林广场', '龙翔桥', '城站火车站']
            ],
            [
                'name' => '机场快线',
                'type' => '快速公交',
                'time' => '6:00-22:30',
                'gtime' => date('Y-m-d H:i:s'),
                'start' => '城站火车站',
                'end' => '萧山国际机场',
                'comp' => '杭州公交集团',
                'note' => '从市区到萧山机场的快速公交线路',
                'fare' => '20元',
                'zStations' => ['城站火车站', '武林广场', '凤起路', '钱江路', '滨江', '西兴', '萧山国际机场'],
                'fStations' => ['萧山国际机场', '西兴', '滨江', '钱江路', '凤起路', '武林广场', '城站火车站']
            ]
        ];
        
        foreach ($demoLines as $line) {
            $zlist = '';
            $flist = '';
            
            // 构建正向站点列表
            foreach ($line['zStations'] as $stationName) {
                if (isset($stationMap[$stationName])) {
                    $zlist .= '-' . $stationMap[$stationName] . '-';
                }
            }
            
            // 构建反向站点列表
            foreach ($line['fStations'] as $stationName) {
                if (isset($stationMap[$stationName])) {
                    $flist .= '-' . $stationMap[$stationName] . '-';
                }
            }
            
            $sql = "INSERT INTO `line` (`name`, `type`, `time`, `gtime`, `start`, `end`, `comp`, `note`, `fare`, `zlist`, `flist`) 
                    VALUES ('{$line['name']}', '{$line['type']}', '{$line['time']}', '{$line['gtime']}', '{$line['start']}', '{$line['end']}', 
                    '{$line['comp']}', '{$line['note']}', '{$line['fare']}', '{$zlist}', '{$flist}')";
            mysqli_query($conn, $sql);
        }
    }
    
    mysqli_close($conn);
    
    echo json_encode(['code' => 0, 'msg' => '安装成功，点击确定进入系统']);
    exit;
}

    
    // 创建默认配置文件
    if (!file_exists('./inc/site.json.php')) {
        file_put_contents('./inc/site.json.php', json_encode($siteConfig, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
    
    if (!file_exists('./inc/user.json.php')) {
        file_put_contents('./inc/user.json.php', json_encode($defaultAdmin, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
    
    // 创建查询记录文件
    file_put_contents('./inc/type1.json', json_encode([], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    file_put_contents('./inc/type2.json', json_encode([], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    file_put_contents('./inc/type3.json', json_encode([], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>安装 - 公交查询系统</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: "Microsoft YaHei", Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
            background-color: #f5f5f5;
        }
        
        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #666;
        }
        
        .section {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 5px;
        }
        
        .section h2 {
            font-size: 18px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-checkbox {
            margin: 15px 0;
        }
        
        .form-checkbox label {
            display: inline;
            margin-left: 5px;
            font-weight: normal;
        }
        
        .form-btn {
            background-color: #1e88e5;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .form-btn:hover {
            background-color: #1976d2;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 3px;
        }
        
        .alert-success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }
        
        .alert-danger {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #eee;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: bold;
        }
        
        .info-value {
            color: #666;
        }
        
        .info-success {
            color: #4caf50;
        }
        
        .info-error {
            color: #f44336;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>公交查询系统安装向导</h1>
            <p>按照向导完成系统安装</p>
        </div>
        
        <div class="section">
            <h2>环境检查</h2>
            
            <div class="info-item">
                <span class="info-label">PHP 版本</span>
                <span class="info-value <?php echo version_compare(PHP_VERSION, '7.0.0', '>=') ? 'info-success' : 'info-error'; ?>">
                    <?php echo PHP_VERSION; ?> 
                    <?php echo version_compare(PHP_VERSION, '7.0.0', '>=') ? '(符合要求)' : '(需要 PHP 7.0 以上版本)'; ?>
                </span>
            </div>
            
            <div class="info-item">
                <span class="info-label">MySQLi 扩展</span>
                <span class="info-value <?php echo extension_loaded('mysqli') ? 'info-success' : 'info-error'; ?>">
                    <?php echo extension_loaded('mysqli') ? '已安装' : '未安装'; ?>
                </span>
            </div>
            
            <div class="info-item">
                <span class="info-label">JSON 扩展</span>
                <span class="info-value <?php echo extension_loaded('json') ? 'info-success' : 'info-error'; ?>">
                    <?php echo extension_loaded('json') ? '已安装' : '未安装'; ?>
                </span>
            </div>
            
            <div class="info-item">
                <span class="info-label">目录写入权限</span>
                <span class="info-value <?php echo is_writable('./inc') ? 'info-success' : 'info-error'; ?>">
                    <?php echo is_writable('./inc') ? '正常' : '无写入权限'; ?>
                </span>
            </div>
        </div>
        
        <div class="section">
            <h2>数据库配置</h2>
            
            <div class="info-item">
                <span class="info-label">数据库主机</span>
                <span class="info-value"><?php echo $dbConfig['host']; ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">数据库端口</span>
                <span class="info-value"><?php echo $dbConfig['port']; ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">数据库用户名</span>
                <span class="info-value"><?php echo $dbConfig['user']; ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">数据库密码</span>
                <span class="info-value">******</span>
            </div>
            
            <div class="info-item">
                <span class="info-label">数据库名称</span>
                <span class="info-value"><?php echo $dbConfig['name']; ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">数据库编码</span>
                <span class="info-value"><?php echo $dbConfig['charset']; ?></span>
            </div>
            
            <p style="margin-top: 15px; color: #666;">
                注意：数据库配置信息存储在 ./inc/conn.php 文件中，如需修改请编辑该文件。
            </p>
        </div>
        
        <div class="section">
            <h2>管理员信息</h2>
            
            <div class="info-item">
                <span class="info-label">管理员账号</span>
                <span class="info-value"><?php echo $defaultAdmin['username']; ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">管理员密码</span>
                <span class="info-value">admin123</span>
            </div>
            
            <p style="margin-top: 15px; color: #666;">
                注意：请在安装完成后及时修改默认密码。
            </p>
        </div>
        
        <div class="section">
            <h2>开始安装</h2>
            
            <div id="install-result"></div>
            
            <div class="form-checkbox">
                <input type="checkbox" id="demoData" checked>
                <label for="demoData">安装演示数据</label>
            </div>
            
            <button id="installBtn" class="form-btn">立即安装</button>
        </div>
    </div>
    
    <script>
        document.getElementById('installBtn').addEventListener('click', function() {
            var btn = this;
            var resultBox = document.getElementById('install-result');
            var demoData = document.getElementById('demoData').checked ? 1 : 0;
            
            // 禁用按钮
            btn.disabled = true;
            btn.textContent = '安装中...';
            
            // 显示加载状态
            resultBox.innerHTML = '<div class="alert alert-info">正在安装，请稍候...</div>';
            
            // 发送安装请求
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'install.php?act=install', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        try {
                            var res = JSON.parse(xhr.responseText);
                            if (res.code === 0) {
                                resultBox.innerHTML = '<div class="alert alert-success">' + res.msg + '</div>';
                                setTimeout(function() {
                                    location.href = 'index.php';
                                }, 1500);
                            } else {
                                resultBox.innerHTML = '<div class="alert alert-danger">' + res.msg + '</div>';
                                btn.disabled = false;
                                btn.textContent = '重新安装';
                            }
                        } catch (e) {
                            resultBox.innerHTML = '<div class="alert alert-danger">安装失败，返回数据格式错误</div>';
                            btn.disabled = false;
                            btn.textContent = '重新安装';
                        }
                    } else {
                        resultBox.innerHTML = '<div class="alert alert-danger">安装失败，HTTP 状态码: ' + xhr.status + '</div>';
                        btn.disabled = false;
                        btn.textContent = '重新安装';
                    }
                }
            };
            
            xhr.send('demoData=' + demoData);
        });
    </script>
</body>
</html>

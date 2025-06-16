<?php

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: inc/pubs.php
// 文件大小: 7812 字节

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: inc/pubs.php
// 文件大小: 7594 字节
/**
 * 本文件功能: 公共PHP函数
 * 版权声明: 保留发行权和署名权
 * 作者信息: 15058593138@qq.com
 */

// 引入数据库连接
require_once 'conn.php';

/**
 * JSON格式返回函数
 * @param int $code 状态码 0成功 其他失败
 * @param string $msg 提示信息
 * @param array $data 返回数据
 */
function jsonReturn($code, $msg, $data = []) {
    $result = [
        'code' => $code,
        'msg' => $msg,
        'data' => $data
    ];
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * 安全过滤函数
 * @param string $str 需要过滤的字符串
 * @return string 过滤后的字符串
 */
function safeFilter($str) {
    if (!$str) return '';
    $str = trim($str);
    $str = htmlspecialchars($str, ENT_QUOTES);
    return $str;
}

/**
 * 获取汉字拼音
 * @param string $str 中文字符串
 * @return string 拼音字符串
 */
function getPinyin($str) {
    $py_arr = array(
        'a'=>'啊阿锕',
        'b'=>'巴八白百北摆报保暴爆被备背',
        'c'=>'擦采藏操曹测策层叉差产长常场厂车',
        'd'=>'打大代带单当到道的得地点电调丢东',
        'e'=>'额恶恩而儿二',
        'f'=>'发法反方房放飞非',
        'g'=>'改该甘刚高个各给跟根公狗够',
        'h'=>'还海好喝和河合何黑很红后',
        'i'=>'一以伊衣医依义乙亿',
        'j'=>'极加家间将江讲交角接街',
        'k'=>'卡看靠科口',
        'l'=>'拉来蓝浪老乐类了累厉力历离',
        'm'=>'马吗买卖满猫毛没每美妹们门',
        'n'=>'那南难男内能你年',
        'o'=>'哦',
        'p'=>'怕爬拍牌盘胖跑朋篷片漂',
        'q'=>'七期起气汽前钱强桥巧',
        'r'=>'然让日肉如入',
        's'=>'三色森杀山上少社谁申生师十时',
        't'=>'他塔台太弹堂套特疼踢提',
        'u'=>'乌污无',
        'v'=>'',
        'w'=>'外弯完玩晚万王望为位文我问',
        'x'=>'西息希吸系洗喜戏下夏鲜现',
        'y'=>'压牙亚眼燕样养要也一以义',
        'z'=>'杂在咱早造怎增',
    );
    
    $result = '';
    $str = trim($str);
    $len = mb_strlen($str, 'UTF-8');
    
    for($i = 0; $i < $len; $i++) {
        $char = mb_substr($str, $i, 1, 'UTF-8');
        $found = false;
        
        foreach($py_arr as $py => $chars) {
            if(mb_strpos($chars, $char, 0, 'UTF-8') !== false) {
                $result .= $py;
                $found = true;
                break;
            }
        }
        
        if(!$found) {
            // 如果不在预定义列表中，检查是否是英文字母或数字
            if(preg_match('/^[a-zA-Z0-9]$/', $char)) {
                $result .= strtolower($char);
            }
        }
    }
    
    return $result;
}

/**
 * 导入CSV数据到数据库
 * @param string $file CSV文件路径
 * @param string $table 目标数据表
 * @param array $fields 字段映射
 * @return array 导入结果
 */
function importCSV($file, $table, $fields) {
    $conn = connectDB();
    $result = ['success' => 0, 'fail' => 0, 'errors' => []];
    
    if (($handle = fopen($file, "r")) !== FALSE) {
        // 跳过第一行（标题行）
        fgetcsv($handle, 1000, ",");
        
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $values = [];
            foreach($fields as $index => $field) {
                if(isset($data[$index])) {
                    $values[$field] = "'" . mysqli_real_escape_string($conn, $data[$index]) . "'";
                }
            }
            
            if(count($values) > 0) {
                $sql = "INSERT INTO `{$table}` (" . implode(',', array_keys($values)) . ") VALUES (" . implode(',', $values) . ")";
                if(mysqli_query($conn, $sql)) {
                    $result['success']++;
                } else {
                    $result['fail']++;
                    $result['errors'][] = mysqli_error($conn);
                }
            }
        }
        fclose($handle);
    }
    
    mysqli_close($conn);
    return $result;
}

/**
 * 新增站点函数
 * @param string $name 站点名称
 * @param string $lng 经度（可选）
 * @param string $lat 纬度（可选）
 * @return int 新增站点ID
 */
function addStation($name, $lng = '', $lat = '') {
    $conn = connectDB();
    $name = mysqli_real_escape_string($conn, $name);
    $ping = getPinyin($name);
    
    // 检查站点是否已存在
    $sql = "SELECT zid FROM zhan WHERE zhan = '{$name}'";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_close($conn);
        return $row['zid'];
    }
    
    // 站点不存在，新增
    $lng = mysqli_real_escape_string($conn, $lng);
    $lat = mysqli_real_escape_string($conn, $lat);
    
    $sql = "INSERT INTO zhan (zhan, ping, lng, lat) VALUES ('{$name}', '{$ping}', '{$lng}', '{$lat}')";
    if(mysqli_query($conn, $sql)) {
        $id = mysqli_insert_id($conn);
        mysqli_close($conn);
        return $id;
    }
    
    mysqli_close($conn);
    return 0;
}

/**
 * 读取站点信息
 * @param int $id 站点ID
 * @return array 站点信息
 */
function getStationById($id) {
    $conn = connectDB();
    $id = intval($id);
    
    $sql = "SELECT * FROM zhan WHERE zid = {$id}";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_close($conn);
        return $row;
    }
    
    mysqli_close($conn);
    return [];
}

/**
 * 读取配置文件
 * @param string $file 配置文件路径
 * @return array 配置数组
 */
function readJsonConfig($file) {
    if(file_exists($file)) {
        $content = file_get_contents($file);
        return json_decode($content, true);
    }
    return [];
}

/**
 * 写入配置文件
 * @param string $file 配置文件路径
 * @param array $data 配置数据
 * @return bool 是否成功
 */
function writeJsonConfig($file, $data) {
    $content = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    return file_put_contents($file, $content) !== false;
}

/**
 * 记录查询历史
 * @param int $type 查询类型：1线路查询 2站点查询 3换乘查询
 * @param string $keyword 查询关键词
 */
function logSearchHistory($type, $keyword) {
    $type = intval($type);
    if($type < 1 || $type > 3) return;
    
    $file = __DIR__ . "/type{$type}.json";
    $history = readJsonConfig($file);
    if(!is_array($history)) $history = [];
    
    // 检查是否已存在该记录
    foreach($history as $key => $item) {
        if($item['keyword'] == $keyword) {
            // 已存在，将其移动到最前面
            unset($history[$key]);
            break;
        }
    }
    
    // 添加新记录
    array_unshift($history, [
        'keyword' => $keyword,
        'time' => date('Y-m-d H:i:s')
    ]);
    
    // 限制记录数量为最近30条
    if(count($history) > 30) {
        $history = array_slice($history, 0, 30);
    }
    
    writeJsonConfig($file, $history);
}

/**
 * 获取查询历史
 * @param int $type 查询类型：1线路查询 2站点查询 3换乘查询
 * @return array 查询历史
 */
function getSearchHistory($type) {
    $type = intval($type);
    if($type < 1 || $type > 3) return [];
    
    $file = __DIR__ . "/type{$type}.json";
    $history = readJsonConfig($file);
    if(!is_array($history)) return [];
    
    return $history;
}
?>

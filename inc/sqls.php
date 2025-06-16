<?php

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: inc/sqls.php
// 文件大小: 5717 字节

// PHP7+MySQL5.6 查立得轻量级公交查询系统 V2025.06.01
// 演示地址: bus2025.chalide.cn 
// 开源更新: /chalide/chalidebus 
// 文件路径: inc/sqls.php
// 文件大小: 5499 字节
/**
 * 本文件功能: 数据库增删改查mysqli类
 * 版权声明: 保留发行权和署名权
 * 作者信息: 15058593138@qq.com
 */

require_once 'conn.php';

class Sqls {
    private $conn;
    
    /**
     * 构造函数，创建数据库连接
     */
    public function __construct() {
        $this->conn = connectDB();
    }
    
    /**
     * 析构函数，关闭数据库连接
     */
    public function __destruct() {
        if($this->conn) {
            mysqli_close($this->conn);
        }
    }
    
    /**
     * 执行SQL查询
     * @param string $sql SQL语句
     * @return mixed 查询结果
     */
    public function query($sql) {
        return mysqli_query($this->conn, $sql);
    }
    
    /**
     * 获取单条记录
     * @param string $table 表名
     * @param string $fields 字段名，默认*
     * @param string $where 条件
     * @return array 结果数组
     */
    public function getOne($table, $fields = '*', $where = '') {
        $sql = "SELECT {$fields} FROM {$table}";
        if($where) {
            $sql .= " WHERE {$where}";
        }
        $sql .= " LIMIT 1";
        
        $result = $this->query($sql);
        if($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
        return [];
    }
    
    /**
     * 获取多条记录
     * @param string $table 表名
     * @param string $fields 字段名，默认*
     * @param string $where 条件
     * @param string $orderby 排序
     * @param string $limit 限制
     * @return array 结果数组
     */
    public function getAll($table, $fields = '*', $where = '', $orderby = '', $limit = '') {
        $sql = "SELECT {$fields} FROM {$table}";
        if($where) {
            $sql .= " WHERE {$where}";
        }
        if($orderby) {
            $sql .= " ORDER BY {$orderby}";
        }
        if($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        $result = $this->query($sql);
        $data = [];
        if($result && mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        return $data;
    }
    
    /**
     * 获取记录总数
     * @param string $table 表名
     * @param string $where 条件
     * @return int 总数
     */
    public function getCount($table, $where = '') {
        $sql = "SELECT COUNT(*) as total FROM {$table}";
        if($where) {
            $sql .= " WHERE {$where}";
        }
        
        $result = $this->query($sql);
        if($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return intval($row['total']);
        }
        return 0;
    }
    
    /**
     * 插入数据
     * @param string $table 表名
     * @param array $data 数据数组
     * @return int 插入ID
     */
    public function insert($table, $data) {
        $fields = [];
        $values = [];
        
        foreach($data as $field => $value) {
            $fields[] = "`{$field}`";
            $values[] = "'" . mysqli_real_escape_string($this->conn, $value) . "'";
        }
        
        $sql = "INSERT INTO {$table} (" . implode(',', $fields) . ") VALUES (" . implode(',', $values) . ")";
        if($this->query($sql)) {
            return mysqli_insert_id($this->conn);
        }
        return 0;
    }
    
    /**
     * 更新数据
     * @param string $table 表名
     * @param array $data 数据数组
     * @param string $where 条件
     * @return bool 是否成功
     */
    public function update($table, $data, $where) {
        $sets = [];
        
        foreach($data as $field => $value) {
            $sets[] = "`{$field}` = '" . mysqli_real_escape_string($this->conn, $value) . "'";
        }
        
        $sql = "UPDATE {$table} SET " . implode(',', $sets);
        if($where) {
            $sql .= " WHERE {$where}";
        }
        
        return $this->query($sql) ? true : false;
    }
    
    /**
     * 删除数据
     * @param string $table 表名
     * @param string $where 条件
     * @return bool 是否成功
     */
    public function delete($table, $where) {
        if(!$where) return false; // 防止误删除全表
        
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return $this->query($sql) ? true : false;
    }
    
    /**
     * 执行自定义SQL语句
     * @param string $sql SQL语句
     * @return array 结果数组
     */
    public function execute($sql) {
        $result = $this->query($sql);
        $data = [];
        
        if($result && is_object($result)) {
            while($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
            return $data;
        }
        
        return $result;
    }
    
    /**
     * 获取最后一次操作的错误信息
     * @return string 错误信息
     */
    public function getError() {
        return mysqli_error($this->conn);
    }
    
    /**
     * 开启事务
     */
    public function beginTransaction() {
        mysqli_autocommit($this->conn, false);
    }
    
    /**
     * 提交事务
     */
    public function commit() {
        mysqli_commit($this->conn);
        mysqli_autocommit($this->conn, true);
    }
    
    /**
     * 回滚事务
     */
    public function rollback() {
        mysqli_rollback($this->conn);
        mysqli_autocommit($this->conn, true);
    }
}
?>

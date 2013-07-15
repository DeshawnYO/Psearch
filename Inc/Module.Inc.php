<?php

namespace Psearch\Inc;

class Module {

    var $db;

    var $index;

    var $method;

    var $key;

    var $field;

    var $limit;

    var $order;

    var $keyword;

    var $condition;

    var $sphinx;

    public function __construct($param) {
        $this->db = new \Psearch\Inc\DbMysql(\Psearch\Inc\Config::$db);
        list($this->index, $this->method, $this->key, $this->field, $this->limit, $this->order, $this->keyword, $this->condition) = $param;
        $this->method();
    }

    public function method() {
        switch($this->method) {
            case 'save':
                $this->insert();
                break;
            case 'update':
                $this->update();
                break;
            case 'delete':
                $this->delete();
                break;
            case 'find':
                $this->find();
                break;
            default:
                \Psearch\Inc\Error::showError('Method Empty Or Wrong', 1002);
        }
    }

    public function insert() {
        $table = $this->table();
        $fields = $this->field($table);
        $field = $value = array();
        $field[] = 'id';
        $value[] = $this->key;
        foreach($fields as $k => $v) {
            $field[] = '`'.$k.'`';
            $value[] = "'".mysql_real_escape_string($v)."'";
        }
        $sql = "REPLACE INTO `{$table}`(".implode(",", $field).") VALUES(".implode(',', $value).")";
        $rst = $this->db->execute($sql);
        if(1 === $rst) {
            \Psearch\Inc\View::output(true, 'Data Insert Succeed');
        } elseif(2 === $rst) {
            \Psearch\Inc\View::output(true, 'Data Update Succeed');
        } else {
            \Psearch\Inc\Error::showError('Data Insert Error', 1004);
        }
    }

    public function update() {
        $table = $this->table();
        $fields = $this->field($table);
        $dots = '';
        $sql = "UPDATE `{$table}` SET ";
        foreach($fields as $key => $val) {
            $sql .= $dots ."`{$key}` = '".mysql_real_escape_string($val)."'";
            $dots = ',';
        }
        $sql .= " WHERE `id` = {$this->key}";
        $rst = $this->db->execute($sql);
        if(1 === $rst) {
            \Psearch\Inc\View::output(true, 'Data Update Succeed');
        } elseif(0 === $rst) {
            \Psearch\Inc\View::output(true, 'Data Not Changed');
        } else {
            \Psearch\Inc\Error::showError('Data Insert Error', 1005);
        }
    }

    public function delete() {
        $table = $this->table();
        $sql = "DELETE FROM `{$table}` WHERE `id` = {$this->key}";
        $rst = $this->db->execute($sql);
        if(1 === $rst) {
            \Psearch\Inc\View::output(true, 'Data Delete Succeed');
        } elseif(0 === $rst) {
            \Psearch\Inc\View::output(true, 'No Data Delete');
        }
    }

    public function find() {
        $this->sphinx = new \Psearch\Inc\Sphinx();
        if(empty($this->keyword)) {
            $this->sphinx->setMatchMode(1);
        } else {
            $this->sphinx->setMatchMode(2);
        }
        $this->condition();
        $order = explode('|', $this->order);
        if(2 === count($order)) {
            $this->sphinx->setSortMode($order[0], $order[1]);
        }
        $limit = explode('|', $this->limit);
        $this->sphinx->setLimits((isset($limit[0]) ? $limit[0]: 1),(isset($limit[1]) ? $limit[1] : 20));
        $rest = $this->sphinx->query($this->keyword, $this->index);
        unset($rest['error'], $rest['warning'], $rest['status'], $rest['fields'], $rest['attrs'], $rest['time']);
        \Psearch\Inc\View::output($rest);
    }

    public function table() {
        $sql = "SHOW TABLES LIKE '{$this->index}'";
        $rst = $this->db->queryFirst($sql);
        if(!$rst || empty($this->index)) {
            \Psearch\Inc\Error::showError('Index Table Not Exists', 1003);
        }
        return $this->index;
    }

    public function field($table) {
        if(empty($this->field)) {
            \Psearch\Inc\Error::showError('Field Empty', 1005);
        }
        $newField = array();
        $index = \Psearch\Inc\Config::$index;
        $cfield = $index[$table];
        foreach($this->field as $k => $v) {
            if(isset($cfield[$k])) {
                $newField[$k] = $v;
            }
        }
        return $newField;
    }

    public function condition() {
        if(is_array($this->condition)) {
            foreach($this->condition as $val) {
                $condition = explode('|', $val);
                if(3 !== count($condition)){
                    continue;
                }
                $op = array_shift($condition);
                $field = array_shift($condition);
                $value = array_shift($condition);
                switch($op) {
                    case 'filter':
                        $this->sphinx->setFilter($field, array($value));
                        break;
                }
            }
        }
    }
}

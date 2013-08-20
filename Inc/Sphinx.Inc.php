<?php

/*
 * Psearch [A journey always starts with the first step]
 *
 * @copyright Copyright (C) 2013 wine.cn All rights reserved.
 * @license http://www.apache.org/licenses/LICENSE-2.0.txt
 */

//----------------------------------------------------------------

/**
 * Sphinx操作类
 *
 * @author   <mengfk@eswine.com>
 * @since    1.0
 */

namespace Psearch\Inc;

class Sphinx {

    static $sphinx;

    public function __construct() {
        if(!class_exists('SphinxClient')) {
            \Psearch\Error::showError("SphinxClient Not Exists", 3001);
        }
        self::$sphinx = new \SphinxClient();
        self::$sphinx->SetServer(\Psearch\Inc\Config::$sphinx['host'], \Psearch\Inc\Config::$sphinx['port']);
        if(!self::$sphinx->open() ) {
            \Psearch\Inc\Error::showError('Connect to sphinx server failed.', 3002);
        }
        self::$sphinx->SetRankingMode(SPH_RANK_BM25);
        self::$sphinx->SetArrayResult(true);
    }

    public function setMatchMode($mode = 1) {
        switch($mode) {
            case 1:
                $rst = self::$sphinx->setMatchMode(SPH_MATCH_ALL);
                break;
            case 2:
                $rst = self::$sphinx->setMatchMode(SPH_MATCH_PHRASE);
                break;
            default:
                $rst = false;
        }
        return $rst;
    }

    public function setSortMode($field, $order) {
        $rst = false;
        if('asc' === $order) {
            $rst = self::$sphinx->setSortMode(SPH_SORT_ATTR_ASC, $field);
        } elseif('desc' === $order) {
            $rst = self::$sphinx->setSortMode(SPH_SORT_ATTR_DESC, $field);
        }
        return $rst;
    }

    public function setLimits($page,$prpage) {
        $page = max(1, intval($page));
        $offset = ($page - 1) * $prpage;
        $match = $offset + $prpage;
        return self::$sphinx->setLimits($offset, $prpage, $match);
    }

    public function setFilter($attr, $value) {
        return self::$sphinx->setFilter($attr, $value);
    }

    public function query($keyword, $index) {
        if($error = $this->error()) {
            \Psearch\Inc\Error::showError($error, 3003);
        }
        $index = empty($index) ? '*' : $index;
        return self::$sphinx->query($keyword, $index);
    }

    public function error() {
        return self::$sphinx->getLastError();
    }
}

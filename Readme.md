#Psearch - 简单分类搜索搭建程序
基于此程序可快速搭建分类搜索引擎，能基本满足中小网站搜索需求，本程序目前为测试版本，仅供开发参考，请误用于生产环境。

更新时间:2013.07.14
最新版本:1.0_Alpha

#***环境搭建***

1.coreseek/sphinx

2.Nginx/PHP

3.MySQL

#***部署程序***

1.将源码包拷贝到Web目录中，如:

    ROOT : /home/www/webroot/Psearch
    URL  : http://localhost/Psearch/
    其中Data, SphinxCfg需要可写权限

2.编写配置文件

    vim ./Inc/Config.Inc.php

    1)static $db:     数据库配置信息,需手动建立数据库
    2)static $sphinx: sphinx searchd服务器信息
    3)static $gzip:   是否启用Gzip压缩
    4)static $index:  索引核心配置信息,用来生成索引临时表和生成sphinx参考配置片段
        参考实例:
            static $index = array(
                //酒庄索引
                'winery' => array(
                    //酒庄中文名,全文检索属性
                    'cname' => 'varchar(200)|sql_field_string',        
                    //酒庄外文名,全文检索属性
                    'fname' => 'varchar(200)|sql_field_string',        
                    //葡萄品种
                    'grape_id' => 'varchar(100)|sql_attr_multi|uint grape_id from field'
                    //酒庄分级
                    'honor' => 'varchar(255)|sql_attr_string',        
                    //所属国家
                    'country' => 'varchar(255)|sql_attr_string',        
                    //所属产区
                    'region' => 'varchar(255)|sql_attr_string',        
                ),        
            );
    5) static $charsetDictpath: mmseg中文分词词典所在的目录,默认为{$MMSEG_PATH}/etc

3.执行初始化程序，用于生成数据库和生成简单的Sphinx配置文件片段

    1)生成索引存储表
        {$PHP_BIN}/php -f {$ROOT}/cli.php init winery 
        其中winery应在配置文件Inc/Config.Inc.php的$index中存在,否则会报错
        若索引存储表已存在,则可在命令行最后添加 force 强制重建,此操作会删除源表中所有数据

    2)根据生成的MySQL表生成sphinx配置文件片段
        {$PHP_BIN}/php -f {$ROOT}/cli.php sphinx winery
        生成的配置文件片段存在与SphinxCfg/sphinx.winery.conf中,需手动加入到coreseek/sphinx的配置中去

4.接口使用

    1)接口规范
        请求方式: HTTP/POST
        HEADER头: Content-Type:application/json
        数据请求格式: json
        数据返回格式: json

    2)保存数据接口(save)
        请求JSON
            {
                "method":"save",
                "index":"winery",
                "key":12,
                "field":{
                            "cname":"酒庄中文名",
                            "fname":"Fname",
                            "grape_id":"12,32,124,321,122",
                            "country":"法国",
                            "region":"波尔多"
                        }
            }
        说明: method,执行方法(必须)
              index,索引名称(必须)
              key,主键ID,必须是正整数,且不能重复(必须)
              field,需要保存到索引数据库中的数据(可省略部分字段)

    3)更新数据接口(update)
        请求JSON
            {
                "method":"update",
                "index":"winery",
                "key":12,
                "field":{
                            "cname":"酒庄新中文名",
                            "country":"新国家"
                        }
            }
        说明: method,执行方法(必须)
              index,索引名称(必须)
              key,主键(必须)
              field,需要更新的具体字段(只提交需要更新字段)

    4)删除数据接口(delete)
        请求JSON
            {
                "method":"delete",
                "index":"winery",
                "key":12
            }
        说明: method,执行方法(必须)
              index,索引名称(必须)
              key,主键,数据将根据主键删除(必须)

    5)数据读取接口(find)
        请求JSON
            {
                "method":"find",
                "index":"winery",
                "keyword":"葡萄酒",
                "limit":"1|2",
                "order":"uid|asc",
                "condition":[
                                "filter|country_id|87",
                                "filter|region_id|12"
                            ]
            }
        说明: method,执行方法(必须)
              index,索引名称(必须)
              keyword,检索关键字(必须,可为空,为空则检索全部)
              limit, [当前页码]|[每页获取数据] (可选,忽略该字段后程序默认为 1|20)
              order, [排序字段名]|[asc or desc](可选)
              condition,按条件查找数据,目前只支持正整数字段值过滤,支持多重过滤,如查找uid为10001的数据则写为"filter|uid|10001",

#***待完善***

1.sphinx索引需自行添加到系统计划任务执行更新

2.未作增量索引分离,目前仅适用于百万以内数据存储索引使用

3.未实现读取缓存功能

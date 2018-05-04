<?php
/**
 * 用户映射分组模块数据处理
 * 
 * @copyright 广州酷狗科技有限公司版权所有 Copyright (c) 2004-2014 (http://www.kugou.com)
 * @author 都要（zhangdongyao@kugou.net）
 * @date 2014-5-21
 *
 */
namespace DB;

use DB\PdoOperateBase;


class user_hash_id extends PdoOperateBase
{
      protected static $SCHEMA = array(
			'DBNAME'=>'big_data',
			'TABLENAME'=>'user_hash_id',
	        'PARTITION'=> array(
	          'suffix' => '_',// 要分表的后缀比如 tablename_中的'_'
	          'field' => 'hash',// 要分表的字段 通常数据会根据某个字段的值按照规则进行分表,我们这里按照用户的id进行分表
	          'type' => 'md5',// 分表的规则 包括id year mod md5 函数 和首字母，此处选择mod（求余）的方式
	          'num' => '128',// 分表的数目 可选 实际分表的数量，在建表阶段就要确定好数量，后期不能增减表的数量
	          'default' => 1,// 默认下标
	        ),
	    );

}
<?php 
/**
*   数组工具类
*
*   @author      WangYongJun
*   @version      V 1.1
*   @date        2015-12-21
*/
namespace Utils;

class ArrayUtil
{	
	/**
	 * 获取数组某列的值，去重并返回
	 * @author WangYongJun<598393636@qq.com>
	 * @date   2016-07-15
	 * @param  [type]     $arr [description]
	 * @param  [type]     $key [description]
	 * @return [type]          [description]
	 */
	public static function getArrayColumn($arr, $key, $isUnique=true) 
	{
		$return = array();
		if (is_array($key)) {
		    foreach ($key as $k) {
		        $return[$k] = array();
		    }
		    foreach ($arr as $v) {
		        foreach ($key as $k) {
		            if (isset($v[$k])) {
		                array_push($return[$k], $v[$k]);
		            }
		        }
		    }
		    if ($isUnique) {
		        foreach ($return as $k=>$v) {
		            $return[$k] = array_unique($v);
		        }
		    }
		    return $return;
		} else {
		   foreach ($arr as $k => $v) {
    			if (isset($v[$key])) {
    				array_push($return, $v[$key]);
    			}
    		}
    		if ($isUnique) {
    			return array_unique($return);
    		}
    		return $return; 
		}
	}

	/**
	 * 一维数组排序-指定元素顺序
	 * 如：
	 * $rule = array(3,2,1,4);
	 * $data = array(2,3,1);
	 * $arr = sortArrayByAppointValueSort($data, $rule);
	 * 结果：
	 * array(3,2,1);
     */	
	public static function sortArrayByAppointValueSort($data, $sortRuleArr) {
	    $compare = function ($a, $b) use ($sortRuleArr) {
	        $indexA = array_search($a, $sortRuleArr);
	        $indexB = array_search($b, $sortRuleArr);
	        return ( $indexA < $indexB) ? -1 : 1;
	    };
	    usort($data, $compare);
	    return $data;
	}

	/**
	 * 二维数组排序-指定字段排序顺序
	 * 如：
	 * $arr = array(
     *	   array('platformId'=>1,'name'=>'5sing'),
     *	   array('platformId'=>2,'name'=>'qq'),
     *	   array('platformId'=>3,'name'=>'kugou'),
     *	   array('platformId'=>4,'name'=>'kuwo'),
     *	);
	 * $arr = sortByAppointValueSort($arr, "platformId", array(3,2,4,1));
	 * 结果：
	 * array(
	 * 	   array('platformId'=>3,'name'=>'kugou'),
	 * 	   array('platformId'=>2,'name'=>'qq'),
     *	   array('platformId'=>4,'name'=>'kuwo'),
     *	   array('platformId'=>1,'name'=>'5sing'),
     *	);
	 * 
	 * @author WangYongJun@kugou.net
	 * @date   2017-07-06
	 * @param  [type]     $data        [description]
	 * @param  [type]     $sortKey         [description]
	 * @param  [type]     $sortRuleArr [description]
	 * @return [type]                  [description]
	 */
	public static function sortArraysByAppointValueSort($data, $sortKey, $sortRuleArr) {
	    $compare = function ($a, $b) use ($sortRuleArr, $sortKey) {
	        $indexA = array_search($a[$sortKey], $sortRuleArr);
	        $indexB = array_search($b[$sortKey], $sortRuleArr);
	        return ( $indexA < $indexB) ? -1 : 1;
	    };
	    usort($data, $compare);
	    return $data;
	}
	
	/**
	 * 二维数组排序-指定key排序顺序
	 * 如：
	 * $arr = array(
     *	   '1' => array('platformId'=>1,'name'=>'5sing'),
     *	   '2' => array('platformId'=>2,'name'=>'qq'),
     *	   '3' => array('platformId'=>3,'name'=>'kugou'),
     *	   '4' => array('platformId'=>4,'name'=>'kuwo'),
     *	);
	 * $arr = sortByAppointKeySort($arr, array(3,2,4,1));
	 * 结果：
	 * array(
	 * 	   '3' => array('platformId'=>3,'name'=>'kugou'),
	 * 	   '2' => array('platformId'=>2,'name'=>'qq'),
     *	   '4' => array('platformId'=>4,'name'=>'kuwo'),
     *	   '1' => array('platformId'=>1,'name'=>'5sing'),
     *	);
	 * 
	 * @author WangYongJun@kugou.net
	 * @date   2017-07-06
	 * @param  [type]     $data        [description]
	 * @param  [type]     $sortKey         [description]
	 * @param  [type]     $sortRuleArr [description]
	 * @return [type]                  [description]
	 */	
	public static function sortArraysByAppointKeySort($data, $sortRuleArr) {
	    $compare = function ($a, $b) use ($sortRuleArr) {
	        $indexA = array_search($a, $sortRuleArr);
	        $indexB = array_search($b, $sortRuleArr);
	        return ( $indexA < $indexB) ? -1 : 1;
	    };
	    uksort($data, $compare);
	    return $data;
	}
}

 ?>
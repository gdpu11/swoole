<?php
namespace Test;

class ApiBase
{
	/**
	 * [getPage 获取分页信息]
	 * @param  [type] $page     [当前页]
	 * @param  [type] $page_num [总共页数]
	 * @return [type]           [description]
	 */
	public static function getPage($page,$page_num,$page_url='/'){
		$page_head = '';
		$page_tail = '';
		$page_before = $page<=1?'<a href="'.$page_url.'?p=1">首页</a>':'<a href="'.$page_url.'?p=1">首页</a><a href="'.$page_url.'?p='.($page-1).'">上一页</a>';
		$page_next = $page<$page_num?'<a href="'.$page_url.'?p='.($page+1).'">下一页</a><a href="'.$page_url.'?p='.$page_num.'">尾页</a>':'<a href="'.$page_url.'?p='.$page_num.'">尾页</a>';
		$page_body = '';
		for ($i=$page<3?1:(($page>($page_num-3)?$page_num-2:$page)-2),$j=0; $j < 5 ; $i++,$j++) {
			if ($i == $page) {
				$page_body .= '<span class="current">'.$i.'</span>';
			}else{
				$page_body .= '<a href="'.$page_url.'?p='.$i.'">'.$i.'</a>';
			}
		}
		return $page_head.$page_before.$page_body.$page_next.$page_tail;
	}
}
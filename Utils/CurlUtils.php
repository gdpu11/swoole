<?php
namespace Utils;
/**
 * curl封装
 * @author mmfei<wlfkongl@163.com>
 */
class CurlUtils
{
	private static $_curlInfo = null;

	/**
	 * 获取请求结果信息
	 * @author WangYongJun@kugou.net
	 * @date   2016-12-05
	 * @return [type]     [description]
	 */
	public static function getCurlInfo() {
		return self::$_curlInfo;
	}
	private static $cookie_arr = array(
		'__utma'=>'51854390.1971512820.1495441849.1495441849.1495441849.1',
		'__utmv'=>'51854390.100--|2=registration_date=20160119=1^3=entry_date=20160119=1',
		'__utmz'=>'51854390.1495441849.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none)',
		'_xsrf'=>'57cddfd9-b094-4f1b-9fa8-0e8acb701af5',
		'_zap'=>'ba4a1bac-da22-4a73-a0d3-0066ea5ad593',
		'_zap'=>'d05786b1-9bff-48c7-9052-02aa12e1f98f',
		'aliyungf_tc'=>'AQAAABszHH+cEA8Au+wSDsZWhoivpj60',
		'cap_id'=>'"NjlkYWRlY2U2MDZiNGNlZGEzMjdkYWQxOWNmZmU2MzA=|1499246736|95322a8496f2419ca418ee064bb73b6528fa5528"',
		'd_c0'=>'"ACDCfpjlywuPTvAisBdyKpWvI1SCtrWgt8k=|1495441847"',
		'q_c1'=>'ffd7639888a442649d6221875dd243da|1494571745000|1494571745000',
		'q_c1'=>'ffd7639888a442649d6221875dd243da|1497423203000|1494571745000',
		'r_cap_id'=>'"N2RhMmZmNTlkZGZlNGEzYmJhMDBiNDBmYzM0MDU1Mzg=|1499246736|209ee0d5c97684d447b8ab797a0c1c6f3cbadb71"',
		

		// 'z_c0'=>'',		
		/*'_zap'=>'d05786b1-9bff-48c7-9052-02aa12e1f98f',
		'q_c1'=>'ffd7639888a442649d6221875dd243da|1494571745000|1494571745000',
		'd_c0'=>'"ACDCfpjlywuPTvAisBdyKpWvI1SCtrWgt8k=|1495441847"',
		'_zap'=>'ba4a1bac-da22-4a73-a0d3-0066ea5ad593',
		'__utma'=>'51854390.1971512820.1495441849.1495441849.1495441849.1',
		'__utmz'=>'51854390.1495441849.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none)',
		'__utmv'=>'51854390.100--|2=registration_date=20160119=1^3=entry_date=20160119=1',
		'q_c1'=>'ffd7639888a442649d6221875dd243da|1497423203000|1494571745000',
		'aliyungf_tc'=>'AQAAABszHH+cEA8Au+wSDsZWhoivpj60',
		'r_cap_id'=>'"N2RhMmZmNTlkZGZlNGEzYmJhMDBiNDBmYzM0MDU1Mzg=|1499246736|209ee0d5c97684d447b8ab797a0c1c6f3cbadb71"',
		'cap_id'=>'"NjlkYWRlY2U2MDZiNGNlZGEzMjdkYWQxOWNmZmU2MzA=|1499246736|95322a8496f2419ca418ee064bb73b6528fa5528"',
		'z_c0'=>'Mi4wQUJES2wxb1VWZ2tBSU1KLW1PWExDeGNBQUFCaEFsVk5tRDJFV1FENnB3YXF2QWJXMk1BQ2VKZEo5Zk1PSU5fOWxR|1499246744|4ad2fd5b847d30b5a594919f5a3b3c0ebeea0f1d',
		'_xsrf'=>'57cddfd9-b094-4f1b-9fa8-0e8acb701af5',
		*/
		/*57cddfd9-b094-4f1b-9fa8-0e8acb701af5
		'_utma'=>'51854390.1081609616.1491554927.1491554927.1494485214.2',
		'__utmc'=>'51854390',
		'__utmv'=>'51854390.100--|2=registration_date=20160119=1^3=entry_date=20160119=1',
		'__utmz'=>'51854390.1494485214.2.2.utmcsr=zhihu.com|utmccn=(referral)|utmcmd=referral|utmcct=/',
		'_xsrf'=>'d92dda0a2597a8dc99a8dbf6dfef6dc1',
		'_zap'=>'bfac6e93-86f9-438a-a8b1-7af0f57d99a9',
		'acw_tc'=>'AQAAACpKmiEF+gAAxKzXDmw3Q5LnJnzT',
		'aliyungf_tc'=>'AQAAAGoxITgcogcAu+wSDnMGiPbFskVB',
		'cap_id'=>'"MzVkOTJiOTUyYmVhNDA0Yjk2ZDQzOWVmMzkzMjVmZDk=|1494485183|4feb381cc9106b3bce6578d13c6c3eb03c0e3cb8"',
		'd_c0'=>'AABCT-sdhguPTp0HENc3BtMzgZfbe9xoJw0=|1490758991',
		'l_n_c'=>'1',
		'q_c1'=>'32fa6ca9dd034413a9449f5090359780|1493975504000|1490758991000',
		'r_cap_id'=>'"NDAzN2M0NDk4Y2Y3NGFiYmFiODJkZjA0NjE5ZTYyYjg=|1494485183|e065d07edefce46cdbcc6ea2c2934e1e6730416d"',
		'z_c0'=>'Mi4wQUJES2wxb1VWZ2tBQUVKUDZ4MkdDeGNBQUFCaEFsVk55WlU3V1FDX1dsOV9GNVpDd3RjZ',*/
	);
	private static function genCookie11() {
		$cookie = '';
		foreach (self::$cookie_arr as $key => $value) {
			if($key != 'z_c0')
				$cookie .= $key . '=' . $value . ';';
			else
				$cookie .= $key . '=' . $value;
		}

		return $cookie;
	}

	private static function genCookie22() {
		$z_c0 = array(
		'"QUlCQ1QzQ3hCUXdYQUFBQVlRSlZUZWRlaFZtZlVaYTNVbUdyNDlzNHBMQS1tUjk0VjAyMFl3PT0=|1499320807|b2faa05970e65d4f38330e5e8476a93fd749dc8e"',
		'Mi4wQUlCQ1QzQ3hCUXdBTUFLRTMtMi1DeGNBQUFCaEFsVk5kbDZGV1FCdzFMTGhSTjNrQ3E5Mnk4TGtUUFg4bElXb1h3|1499320694|1fa80b6b492ed89185227273e6d17975b13c7b02',
		'Mi4wQUlCQ1QzQ3hCUXdBVUFJZmhMUUZEQmNBQUFCaEFsVk4zbUNGV1FBekZvdWZ3NnMwTWZPTTg1V0xmQnkxbUU4NWxR|1499321310|6a754f4f5733552734d4be7f2ec9ba639d3bb884',
		'Mi4wQUlCQ1QzQ3hCUXdBSUFLajRMVUZEQmNBQUFCaEFsVk5QbUtGV1FBeDhOMUdZS1FBMTdUM1dFbHp1cGlrYi1BdFFR|1499321662|b249afc00d55f6a9318ebe7f40bd6c84e8146d77',
		'Mi4wQUlCQ1QzQ3hCUXdBZ0lMRzc3WUZEQmNBQUFCaEFsVk5VR09GV1FET2R3cGdoazZ3MG9SUGlUQUZTNTZlQ092c3hR|1499321937|d4bc14e15874eda7c2e42acd84a3087b9b7368df',
		'Mi4wQUlCQ1QzQ3hCUXdBSUFMY1BiY0ZEQmNBQUFCaEFsVk5vR09GV1FEb1VTN2d1b1ZwSm5ZTnBudEJLSVVyQzZzVG1n|1499322016|c37118444a1f41bc182c41f745f515acf3a2db09',
		'Mi4wQUlCQ1QzQ3hCUXdBa01LM2FyY0ZEQmNBQUFCaEFsVk56Mk9GV1FCb0JSVW5HTjVDUzBVNEJISE9fdTVWRUFWekFB|1499322063|868439ee3eb34d4c742104277376de9837c72666',
		'Mi4wQUlCQ1QzQ3hCUXdBWUFLQm43Y0ZEQmNBQUFCaEFsVk5CR1NGV1FEeHdlNDRUM1lPT29LY1J2eHRTM2Fsd01XY2VB|1499322117|17680eac44c47e98e7b5d34757fbc7cad56dc052',
		);
		$randarr= mt_rand(0,count($z_c0)-1);
		$cookie = '';
		foreach (self::$cookie_arr as $key => $value) {
			$cookie .= $key . '=' . $value . ';';
		}
		$cookie .='z_c0=' . $z_c0[$randarr];
		return $cookie;
	}
	private static function genCookie() {
		// $cookie ='';
		// $cookie ='';
		$cookie ='YF-Ugrow-G0=5b31332af1361e117ff29bb32e4d8439; login_sid_t=8bc76b153e8419e2795fce8ca738843e; YF-V5-G0=5f9bd778c31f9e6f413e97a1d464047a; WBStorage=28a7a732670d7678|undefined; _s_tentry=-; Apache=9386359956115.484.1499396230374; SINAGLOBAL=9386359956115.484.1499396230374; ULV=1499396230383:1:1:1:9386359956115.484.1499396230374:; YF-Page-G0=04608cddd2bbca9a376ef2efa085a43b; UOR=,,login.sina.com.cn; crossidccode=CODE-gz-1DtjwS-fYJVF-xYrSfoDDq8nrFr76c7956; WBtopGlobal_register_version=85d55bc0e4930702; SCF=Al4VIhWRikQ_pF8ROr4MVBS4w4C_EaNOpe5MrF4DAe8nXPK_yA0mLr_xXpyqcLc80lS0euPRSUdc3QZ3bidIcYI.; SUB=_2A250WomJDeRhGeRP7FAU9ifPwz-IHXVXEfxBrDV8PUNbmtBeLXXHkW-FCFttl-SMWprWFXGRsTbQymDVww..; SUBP=0033WrSXqPxfM725Ws9jqgMF55529P9D9WW8RL5kPyoQX_3dg7PXzR555JpX5o275NHD95QEeKMESKq4e0n0Ws4Dqcj8gFfAIg-t; SUHB=0RybsSXogaVWfz; ALF=1500001370; SSOLoginState=1499396569; un=455019211@qq.com; wvr=6';
		// $cookie ='_zap=df8b68aa-bd8b-45c2-900a-9b7781a4893e; aliyungf_tc=AQAAALp3DRYjEAAAxKzXDrTma44mbMpt; anc_cap_id=1d1393e6579649dcad2694569d9a05b7; l_n_c=1; q_c1=28d62b19c92c40728f52173ad8b236d5|1499327448000|1499327448000; r_cap_id="NmFkZjkxYTNmYTI5NDA3MWIzNTRmYjA3ZDA2NDAyN2E=|1499327448|5849917506a1125d1bfbfb844bf75d6cee7022eb"; cap_id="NjgyZjUzN2FmNDAzNDAyOThiM2I1MzBkYTljODhmZmM=|1499327448|3a40ca197e419c01f840b2e8d15a1ee09dca448f"; l_cap_id="YzU0YWZmNmRhNDY3NGEwZWI4MjNiZWRmODk4YzlkNTA=|1499327448|4ff3e3c8a7abb75bd37e6551a094a1385027e4ec"; d_c0="ABCCaP3LBQyPTu9hrsuOVwESYFbvHsxrQu4=|1499327449"; n_c=1; _zap=33cc4424-d0eb-4689-ae40-cab891b37f5d; __utma=51854390.55227478.1499327462.1499327462.1499327462.1; __utmc=51854390; __utmz=51854390.1499327462.1.1.utmcsr=zhihu.com|utmccn=(referral)|utmcmd=referral|utmcct=/account/unhuman; __utmv=51854390.000--|3=entry_date=20170706=1; capsion_ticket="2|1:0|10:1499327502|14:capsion_ticket|44:YWIyNzE3MTc2ZmUwNDRlNGI3OTBiM2U3NjljMTU5Mzk=|a9e76a03fa83997eaf6c9b2529d3afca13dc45f1ada2c74bbc1a0c56a81c80d1"; _xsrf=dc50d013-7f80-4589-a7d0-809505d4f0d1';
		
		return $cookie;
	}

	private static  function get_rand_ip(){
	  $arr_1 = array("218","218","66","66","218","218","60","60","202","204","66","66","66","59","61","60","222","221","66","59","60","60","66","218","218","62","63","64","66","66","122","211");
	  $randarr= mt_rand(0,count($arr_1)-1);
	  $ip1id = $arr_1[$randarr];
	  $ip2id=  round(rand(600000,  2550000)  /  10000);
	  $ip3id=  round(rand(600000,  2550000)  /  10000);
	  $ip4id=  round(rand(600000,  2550000)  /  10000);
	  return  $ip1id . "." . $ip2id . "." . $ip3id . "." . $ip4id;
	}

	public static function aliCurl($url) {
		$cookie = 'JSESSIONID=9L78l9qw1-dN1Ys1U4W2Dl9kRgPA-eM0kMQQ-nyOL; cna=5uTkEdtdcxACAQ4S7LvKDQbv; cookie1=BdM0yO4M2ve2%2F6GBip3tA89bd6%2BmJx%2FVgCZmCbXANS4%3D; cookie2=1c0f323d2e4bef75f7c8bfc201b45ebe; cookie17=Uoe0bUt%2F%2FrCnPw%3D%3D; uss=UR2NjczBazSjTiff9ZPW0axmpnw46Nz%2BcI3Ftxe3INNM9hGEZcXM%2Bp1a3w%3D%3D; t=a02544b73a9fd263e867b8638e17a6f4; _tb_token_=e671eefb3bb3e; sg=157; __cn_logon__=true; __cn_logon_id__=gdpu11; ali_apache_track="c_ms=1|c_mid=b2b-1600327515|c_lid=gdpu11"; ali_apache_tracktmp="c_w_signed=Y"; cn_tmp="Z28mC+GqtZ1waru4w6kjau/aRCobqNdabaWTDqYYOYjx3BJZf9BR447G9TsJcO8n+elQ6kMpFECVhh8zoVBIg69y/rSD9ppJ5s4rM6nX098I0hqsYx2ZnU+Ti/4INuzXqlfNtz4ZlhpNWFXblkqsCnJ5uOy8BKO3X2IiO+k9/wt836lf2VgRNb2bnC2ggVF3uFBb59wFm28cvRKfVupE98ifIp41Eyo1zyDQNMk6XE4="; _cn_slid_=vbQEJ44hHm; tbsnid=OM7YsQqTUl9t9vV%2FsSRU8mgfXErszIa%2BlysU6PGaSIc6sOlEpJKl9g%3D%3D; LoginUmid="8Sqcaf5aU%2FD8dlhkB5NTCPI%2FbyJwsOAvccw36LNHa49TG4BC5yy9jw%3D%3D"; userID="%2BTZ1ATiQ%2BU6K%2FhvPk1RWIUYghLzxWWn%2B%2BsGi33baZlM6sOlEpJKl9g%3D%3D"; last_mid=b2b-1600327515; unb=1600327515; __last_loginid__=gdpu11; login="kFeyVBJLQQI%3D"; _csrf_token=1500961647943; _is_show_loginId_change_block_=b2b-1600327515_false; _show_force_unbind_div_=b2b-1600327515_false; _show_sys_unbind_div_=b2b-1600327515_false; _show_user_unbind_div_=b2b-1600327515_false; UM_distinctid=15d7855b21f3ee-094d01276b6842-1571466f-1fa400-15d7855b220586; ali_ab=14.215.172.196.1500962471158.5; alicnweb=lastlogonid%3Dgdpu11%7Ctouch_tb_at%3D1500965244177; userIDNum=lDtWkCp106nFSJDXntqVGw%3D%3D; _nk_=rS6M4t5tu9E%3D; __rn_alert__=false; _tmp_ck_0="t9wOA%2BIRt2AxvGEoshUuDHE3zL3BBA5JHVWfr9%2BYLulZRQikiDdDioqsJUIBqjD%2BVsnuvIu7QQvNB4Wt%2BPBNuyQkX9XRHmZMYmtdz71hS9Q9vE5Toe%2FuxWSoXRxDdSvWnyBbGWFjuuLosXr2CnBl3UXIqx02ruSZ9J3JbnZsV%2BKEN6yQTBopJykS0Kq6KRkBePW4MVOQqS1kr6lBJfwMy%2F7aC33%2BlOuRjB%2BZGfKQ%2FBOp1d1l5FYtP8%2FE2U%2FdIqqddAHXcUej4%2Fk06YcE0zkf8YDTqDUy%2BadQUabsh2lZdk6EXC%2Byk5d%2BkCVMr%2BWwrMQiqOXr2L%2FJVhJ8vaG6Sq2N1oDQRm42ahtsvxyNstwFOXmdGrlHXRb5dZ9yfyhKnsL7OiWKaARm4e8QE6QQCDbZw%2FW0wBkYkhN3fj4fOekX2%2Bh4c3cPFBEl1DCdKZPwPzWyeZXxtny9aDcIDxWZOBjqiepHW81Sx9JvQuscviifi2FzIFrPPHBQPtmVCqQqAcv7OM3ylBRDTNa0hhT1CqJL9%2FeXeksFz2xAhlpX5mO8PcY%3D"; isg=AqqqAbeuhxOi6gt_v4viSi-n9RCMs550Y7E6HDRjVv2IZ0ohHKt-hfAVg4wf';

		$ip = self::get_rand_ip();
		// $header = array(
		// 	'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
		// 	'Accept-Encoding:gzip,deflate,sdch',
		// 	'Accept-Language:zh-CN,zh;q=0.8',
		// 	'Cache-Control:max-age=0',
		// 	'Cookie:YF-Ugrow-G0=5b31332af1361e117ff29bb32e4d8439; login_sid_t=8bc76b153e8419e2795fce8ca738843e; YF-V5-G0=5f9bd778c31f9e6f413e97a1d464047a; WBStorage=28a7a732670d7678|undefined; _s_tentry=-; Apache=9386359956115.484.1499396230374; SINAGLOBAL=9386359956115.484.1499396230374; ULV=1499396230383:1:1:1:9386359956115.484.1499396230374:; YF-Page-G0=04608cddd2bbca9a376ef2efa085a43b; UOR=,,login.sina.com.cn; crossidccode=CODE-gz-1DtjwS-fYJVF-xYrSfoDDq8nrFr76c7956; WBtopGlobal_register_version=85d55bc0e4930702; SCF=Al4VIhWRikQ_pF8ROr4MVBS4w4C_EaNOpe5MrF4DAe8nXPK_yA0mLr_xXpyqcLc80lS0euPRSUdc3QZ3bidIcYI.; SUB=_2A250WomJDeRhGeRP7FAU9ifPwz-IHXVXEfxBrDV8PUNbmtBeLXXHkW-FCFttl-SMWprWFXGRsTbQymDVww..; SUBP=0033WrSXqPxfM725Ws9jqgMF55529P9D9WW8RL5kPyoQX_3dg7PXzR555JpX5o275NHD95QEeKMESKq4e0n0Ws4Dqcj8gFfAIg-t; SUHB=0RybsSXogaVWfz; ALF=1500001370; SSOLoginState=1499396569; un=455019211@qq.com; wvr=6',
		// 	// 'Host:weibo.com',
		// 	'Proxy-Connection:keep-alive',
		// 	// 'Referer:http://weibo.com/2172569383/profile?topnav=1&wvr=6&is_all=1',
		// 	// 'Referer:http://weibo.com/6089568504/follow',
		// 	'User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.122 Safari/537.36 SE 2.X MetaSr 1.0',
		// 	'X-FORWARDED-FOR:'.$ip,
		// 	'CLIENT-IP:'.$ip,
		// 	// '',
		// 	);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
	    // curl_setopt($ch, CURLOPT_HTTPHEADER, $header);  //构造IP  
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:'.$ip, 'CLIENT-IP:'.$ip));  //构造IP  
	    curl_setopt($ch, CURLOPT_REFERER, $url);  

		// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书

		// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
		curl_setopt($ch, CURLOPT_COOKIE, self::genCookie());
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.130 Safari/537.36');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		$str = curl_exec($ch); 
		// self::$_curlInfo = curl_getinfo($ch);
	    curl_close($ch);  
	    return $str; 
	}

	public static function weiboCurl($url) {
		$ip = self::get_rand_ip();
		// $header = array(
		// 	'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
		// 	'Accept-Encoding:gzip,deflate,sdch',
		// 	'Accept-Language:zh-CN,zh;q=0.8',
		// 	'Cache-Control:max-age=0',
		// 	'Cookie:YF-Ugrow-G0=5b31332af1361e117ff29bb32e4d8439; login_sid_t=8bc76b153e8419e2795fce8ca738843e; YF-V5-G0=5f9bd778c31f9e6f413e97a1d464047a; WBStorage=28a7a732670d7678|undefined; _s_tentry=-; Apache=9386359956115.484.1499396230374; SINAGLOBAL=9386359956115.484.1499396230374; ULV=1499396230383:1:1:1:9386359956115.484.1499396230374:; YF-Page-G0=04608cddd2bbca9a376ef2efa085a43b; UOR=,,login.sina.com.cn; crossidccode=CODE-gz-1DtjwS-fYJVF-xYrSfoDDq8nrFr76c7956; WBtopGlobal_register_version=85d55bc0e4930702; SCF=Al4VIhWRikQ_pF8ROr4MVBS4w4C_EaNOpe5MrF4DAe8nXPK_yA0mLr_xXpyqcLc80lS0euPRSUdc3QZ3bidIcYI.; SUB=_2A250WomJDeRhGeRP7FAU9ifPwz-IHXVXEfxBrDV8PUNbmtBeLXXHkW-FCFttl-SMWprWFXGRsTbQymDVww..; SUBP=0033WrSXqPxfM725Ws9jqgMF55529P9D9WW8RL5kPyoQX_3dg7PXzR555JpX5o275NHD95QEeKMESKq4e0n0Ws4Dqcj8gFfAIg-t; SUHB=0RybsSXogaVWfz; ALF=1500001370; SSOLoginState=1499396569; un=455019211@qq.com; wvr=6',
		// 	// 'Host:weibo.com',
		// 	'Proxy-Connection:keep-alive',
		// 	// 'Referer:http://weibo.com/2172569383/profile?topnav=1&wvr=6&is_all=1',
		// 	// 'Referer:http://weibo.com/6089568504/follow',
		// 	'User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.122 Safari/537.36 SE 2.X MetaSr 1.0',
		// 	'X-FORWARDED-FOR:'.$ip,
		// 	'CLIENT-IP:'.$ip,
		// 	// '',
		// 	);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
	    // curl_setopt($ch, CURLOPT_HTTPHEADER, $header);  //构造IP  
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:'.$ip, 'CLIENT-IP:'.$ip));  //构造IP  
	    curl_setopt($ch, CURLOPT_REFERER, $url);  

		// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书

		// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
		curl_setopt($ch, CURLOPT_COOKIE, self::genCookie());
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.130 Safari/537.36');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		$str = curl_exec($ch); 
		// self::$_curlInfo = curl_getinfo($ch);
	    curl_close($ch);  
	    return $str; 
	}
	public static function zhihuCurl($url) {

		$ip = self::get_rand_ip();
		$header = array(
			'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
			'Accept-Encoding:gzip,deflate,sdch',
			'Accept-Language:zh-CN,zh;q=0.8',
			'Cache-Control:max-age=0',
			'Cookie:YF-Ugrow-G0=5b31332af1361e117ff29bb32e4d8439; login_sid_t=8bc76b153e8419e2795fce8ca738843e; YF-V5-G0=5f9bd778c31f9e6f413e97a1d464047a; WBStorage=28a7a732670d7678|undefined; _s_tentry=-; Apache=9386359956115.484.1499396230374; SINAGLOBAL=9386359956115.484.1499396230374; ULV=1499396230383:1:1:1:9386359956115.484.1499396230374:; YF-Page-G0=04608cddd2bbca9a376ef2efa085a43b; UOR=,,login.sina.com.cn; crossidccode=CODE-gz-1DtjwS-fYJVF-xYrSfoDDq8nrFr76c7956; WBtopGlobal_register_version=85d55bc0e4930702; SCF=Al4VIhWRikQ_pF8ROr4MVBS4w4C_EaNOpe5MrF4DAe8nXPK_yA0mLr_xXpyqcLc80lS0euPRSUdc3QZ3bidIcYI.; SUB=_2A250WomJDeRhGeRP7FAU9ifPwz-IHXVXEfxBrDV8PUNbmtBeLXXHkW-FCFttl-SMWprWFXGRsTbQymDVww..; SUBP=0033WrSXqPxfM725Ws9jqgMF55529P9D9WW8RL5kPyoQX_3dg7PXzR555JpX5o275NHD95QEeKMESKq4e0n0Ws4Dqcj8gFfAIg-t; SUHB=0RybsSXogaVWfz; ALF=1500001370; SSOLoginState=1499396569; un=455019211@qq.com; wvr=6',
			'Host:weibo.com',
			'Proxy-Connection:keep-alive',
			// 'Referer:http://weibo.com/2172569383/profile?topnav=1&wvr=6&is_all=1',
			// 'Referer:http://weibo.com/?topnav=1&mod=logo',
			'User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.122 Safari/537.36 SE 2.X MetaSr 1.0',
			'X-FORWARDED-FOR:'.$ip,
			'CLIENT-IP:'.$ip,
			// '',
			);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
	    // curl_setopt($ch, CURLOPT_HTTPHEADER, $header);  //构造IP  

	    // curl_setopt($ch, CURLOPT_REFERER, 'https://www.zhihu.com');  

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书

		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
		curl_setopt($ch, CURLOPT_COOKIE, self::genCookie());
		// curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.130 Safari/537.36');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		$str = curl_exec($ch); 
		// self::$_curlInfo = curl_getinfo($ch);
	    curl_close($ch);  
	    return $str; 
	}

	public static function zhihuMultiCurl($url_array, $wait_usec = 0)
	{
	    if (!is_array($url_array))
	        return false;
	    $wait_usec = intval($wait_usec);
	    $data    = array();
	    $handle  = array();
	    $running = 0;
	    $mh = curl_multi_init(); // multi curl handler
	    $i = 0;
	    foreach($url_array as $url) {
	    	$ip = self::get_rand_ip();

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
		    curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:'.$ip, 'CLIENT-IP:'.$ip));  //构造IP  

		    curl_setopt($ch, CURLOPT_REFERER, 'https://www.zhihu.com');  

			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书

			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
			curl_setopt($ch, CURLOPT_COOKIE, self::genCookie());
			// curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.130 Safari/537.36');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	        curl_multi_add_handle($mh, $ch); // 把 curl resource 放进 multi curl handler 里
	        $handle[$i++] = $ch;
	    }
	    /* 执行 */
	    do {
	        curl_multi_exec($mh, $running);
	        if ($wait_usec > 0) /* 每个 connect 要间隔多久 */
	            usleep($wait_usec); // 250000 = 0.25 sec
	    } while ($running > 0);
	    /* 读取资料 */
	    foreach($handle as $i => $ch) {
	        $content  = curl_multi_getcontent($ch);
	        $data[$i] = (curl_errno($ch) == 0) ? $content : false;
	    }
	    /* 移除 handle*/
	    foreach($handle as $ch) {
	        curl_multi_remove_handle($mh, $ch);
	    }
	    curl_multi_close($mh);
	    return $data;
	}

	/**
	 * 发送post请求
	 * @param string $url
	 * @param $param
	 * @param string $cookieFile
	 * @param string $error			错误内容 - 回调返回
	 * @return string
	 */
	public static function sendPost($url , $data = array() , $headers = array(),$status = false)
	{
		//  初始化
	    $ch = curl_init();
	    //CURLOPT_URL 是指提交到哪里？相当于表单里的“action”指定的路径
	    //  设置变量
	    curl_setopt($ch, CURLOPT_URL, $url);
	    
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//执行结果是否被返回，0是返回，1是不返回
	    // curl_setopt($ch, CURLOPT_HEADER, 0);//参数设置，是否显示头部信息，1为显示，0为不显示
	    // 伪造网页来源地址,伪造来自百度的表单提交
	    // curl_setopt($ch, CURLOPT_REFERER, "http://www.baidu.com");
	    if ($headers) {
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
		}

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    //表单数据，是正规的表单设置值为非0
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 50);//设置curl执行超时时间最大是多少
	    //使用数组提供post数据时，CURL组件大概是为了兼容@filename这种上传文件的写法，
	    //默认把content_type设为了multipart/form-data。虽然对于大多数web服务器并
	    //没有影响，但是还是有少部分服务器不兼容。本文得出的结论是，在没有需要上传文件的
	    //情况下，尽量对post提交的数据进行http_build_query，然后发送出去，能实现更好的兼容性，更小的请求数据包。
	    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	    //   执行并获取结果
	    $output = curl_exec($ch);
        
		if ($output === FALSE)
		{
			throw new \Exception(curl_error($ch), 1);
		}
		if ($status===true) {
			// 获得响应结果里的：头大小
			$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			// 根据头大小去获取头信息内容
			$header = substr($output, 0, $headerSize);
			curl_close($ch);
			return array($output,$header);
		}
		curl_close($ch);
		return $output;
	}
	/**
	 * 发送get请求
	 * @param string $url
	 * @param $param
	 * @param string $error			错误内容 - 回调返回
	 * @return string
	 */
	public static function sendGet($url , $param = array() , &$error = null)
	{
		if($param)
		{
			if(false === strpos($url, '?'))
			{
				$url .= "?".http_build_query($param);
			}
			else
			{
				$url = rtrim($url , '&');
				if(!preg_match("/\\?$/", $url))
					$url .= "&";
				$url .= http_build_query($param);
			}
		}
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		//添加https不验证证书
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		$output = curl_exec($ch);
		if ($output === FALSE)
		{
			\Fx\DAL\FxLog\FxLog::insertLog($url,'sendGet');
			throw new \Exception(curl_error($ch), 1);
		}
		self::$_curlInfo = curl_getinfo($ch);
		curl_close($ch);
		return $output;
	}

}
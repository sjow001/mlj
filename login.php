<?php
/**************************************
* Project Name:盛传移动商务平台
* Time:2016-03-22
* Author:MarkingChanning QQ:380992882
**************************************/
set_time_limit(0);
header("Content-Type: text/html;charset=utf-8");
include_once("curlapi.class.php");
$curl = new curlapi();
if($_GET['action'] == "code"){//获取验证码
	$curl -> url = "http://vip8.sentree.com.cn/shair/vc";
	echo $curl -> get_code();
}else if($_GET['action'] == "login"){
	$access_token = '670c27a802473c547c4262c66952de40';
	$curl -> url = "https://saas.mljia.cn/customer/card/list?shop_sid=105471&card_flag=0&custom_id=706393&page=1&access_token=670c27a802473c547c4262c66952de40";
	$rs = $curl -> curl();
	echo "<pre>";
	print_r($rs);
	echo "</pre>";
	exit;

	$login = urlencode($_POST['login']);
	$passwd = $_POST['passwd'];
	$rand = $_POST['rand'];
	$params = "phone_number=13423803031&user_password=hjr13423803031";
	$curl -> url = "https://saas.mljia.cn/saas.shop/login";
	$curl -> params = $params;
	$result = $curl -> login();
	$result = json_decode($result,true);
	if($result['code'] == 4){
		echo "验证码错误！";
	}else if($result['code'] == 5){
		echo "不存在的账号！";
	}else if($result['code'] == 6){
		echo "密码错误！";
	}else if($result['role']){
		echo 1;
	}
}else if($_GET['action'] == 'curlmember'){
	$pages = 38;
	$access_token = 'b86094ef923e3c81e4924b1ff011d1da';
	$shopname = '超人气';
	$shop_sid = 111044;
	$data = array();
	for($i=1; $i<=$pages; $i++){
		//获取员工列表
		$curl -> url = "https://saas.mljia.cn/customer/info/list?shop_sid=$shop_sid&sex=&custom_type=0&day=&agent_type_flag=&start_date=&end_date=&custom_level_id=-1&custom_status=0&left_money_min=&left_money_max=&left_count_min=&left_count_max=&key_words=&note_words=&birthday_remind_flag=&phone_flag=&birthday_flag=&sort=customTotalMoney&sort_type=0&page=$i&access_token=$access_token";
		$pagesData = $curl -> curl();
		$pagesData = json_decode($pagesData,true);
		$content =  base64_decode($pagesData['content']);
		$content = json_decode($content,true);
		foreach($content as $v){
			$data[] = $v;
		}
	};
    if($data == '') {
        header('Location: index.php');
    }

	$curl -> downMembersCvs($data, $shopname, $access_token);
}else if($_GET['action'] == 'curlpackage'){
    $shopname = $_REQUEST['shopname'];
    $data = '';

    //获取总数
    $curl -> url = "http://vip8.sentree.com.cn/shair/timesItem!initTreat.action?set=cash";
    $rs = $curl -> curl();
    preg_match('/共(.*)条/isU', $rs, $totals);
    $totals = isset($totals[1])?$totals[1]:100;

	//总页数
    $pages = ceil($totals/100);
    for($i=1; $i<=$pages; $i++){
        $params = "page.currNum=$i&page.rpp=100&set=cash&r=0.3421386775783387";
        $curl -> params = $params;
        $curl -> url = "http://vip8.sentree.com.cn/shair/timesItem!initTreat.action";
        $pagesData = $curl -> getPackagePage();
        $data .= $curl ->getPackageInfo($pagesData, $i);
    };
    if($data == '') {
        header('Location: index.php');
    }
    $curl -> downPackageCvs($data, $shopname);
}else if($_GET['action'] == 'curlstaff'){
	$shopname = $_REQUEST['shopname'];
	$data = '';

	//获取员工数据
	$curl -> url = "http://vip8.sentree.com.cn/shair/employee!employeeInfo.action?set=manage&r=0.5704847458180489";
	$rs = $curl -> curl();

	$rsBlank = preg_replace("/\s\n\t/","",$rs);
	//$rsBlank = str_replace(' ', '', $rsBlank);
	preg_match_all("/table_fixed_head.*>(.*)<\/form>/isU", $rsBlank ,$tables);

    if(count($tables[0]) == 0) {
        header('Location: index.php');
    }
	$curl -> downStaffCvs($tables[1][0], $shopname);
}
?>
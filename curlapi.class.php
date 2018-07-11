<?php
/**************************************
* Project Name:盛传移动商务平台
* Time:2016-03-22
* Author:MarkingChanning QQ:380992882
**************************************/
error_reporting(0);
require 'querylist/phpQuery.php';
require 'querylist/QueryList.php';
use QL\QueryList;

class curlapi{
    public $url; //提交地址
    public $params; //登入的post数据
    public $cookies=""; //cookie
    public $referer=""; //http referer
    
    /*
        获取验证码
    */
    public function get_code(){
        $ch = curl_init($this -> url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        preg_match("/Set-Cookie:(.*);/siU", $output, $arr);
        $cookies = $arr[1];
        //cookies存SESSION
        session_start();
        $_SESSION['cookies'] = $cookies;
        //截取GIF二进制图片
        $explode = explode("HttpOnly",$output);
        return $explode = trim($explode[1]);
    }
    
    /*
        模拟登陆
    */
    public function login(){
        session_start();
        $ch=curl_init();

        $cacert = getcwd() . '/cacert.pem'; //CA根证书
        $headers = array();
        $headers[] = 'X-Apple-Tz: 0';
        $headers[] = 'X-Apple-Store-Front: 143444,12';
        $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $headers[] = 'Accept-Encoding: gzip, deflate';
        $headers[] = 'Accept-Language: en-US,en;q=0.5';
        $headers[] = 'Cache-Control: no-cache';
        $headers[] = 'Content-Type: application/json; charset=utf-8';
        $headers[] = 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0';
        $headers[] = 'X-MicrosoftAjax: Delta=true';
        //$headers[] = 'Content-Length';

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_URL,$this -> url);
        curl_setopt($ch, CURLOPT_HEADER,1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //curl_setopt($ch,CURLOPT_COOKIE,$_SESSION['cookies']);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$this -> params);
        curl_setopt ($ch, CURLOPT_REFERER,$this -> url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);   // 只信任CA颁布的证书
        curl_setopt($ch, CURLOPT_CAINFO, $cacert); // CA根证书（用来验证的网站证书是否是CA颁布）
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 检查证书中是否设置域名，并且是否与提供的主机名匹配

//      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
//      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
        $cookie_file = tempnam('./temp','cookie');

        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        $_SESSION['cookie_file'] = $cookie_file;

        $result=curl_exec($ch);
        preg_match_all("/Set-Cookie:(.*);/siU", $result, $arr);
        $arr[1][0] = explode('token=', $arr[1][0]);
        $arr[1][1] = explode('_shopid=', $arr[1][1]);
        $_shopid = explode('|', $arr[1][1][1]);
        $_SESSION['shopid'] = ceil($_shopid[0]);
        $_SESSION['cookies'] = array(
            'token' => $arr[1][0][1],
            '_shopid' => $arr[1][0][1],
        );

        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == '200') {
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($result, 0, $headerSize);
            $body = substr($result, $headerSize);
        }

        echo "<pre>";
        print_r($result);
        echo "</pre>";
        exit;
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == '200') {
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($result, 0, $headerSize);
            $body = substr($result, $headerSize);
        }

        curl_close($ch);
        return $body;
    }
    
    /*
        curl模拟采集数据
    */
    public function curl(){
        session_start();
        $cacert = getcwd() . '/cacert.pem'; //CA根证书
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL,$this -> url);
        curl_setopt($ch, CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch,CURLOPT_COOKIE,$_SESSION['cookies']);
        curl_setopt ($ch, CURLOPT_REFERER,$this -> referer);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);   // 只信任CA颁布的证书
        curl_setopt($ch, CURLOPT_CAINFO, $cacert); // CA根证书（用来验证的网站证书是否是CA颁布）
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 检查证书中是否设置域名，并且是否与提供的主机名匹配
        $result=curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /*
    curl模拟采集数据，会员数据
    */
    public function getMembersPage(){
        session_start();
        $cacert = getcwd() . '/cacert.pem'; //CA根证书
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL,$this -> url);
        curl_setopt($ch, CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_COOKIE,$_SESSION['cookies']);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$this -> params);
        curl_setopt ($ch, CURLOPT_REFERER,$this -> url);
        curl_setopt ($ch, CURLOPT_REFERER,$this -> referer);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);   // 只信任CA颁布的证书
        curl_setopt($ch, CURLOPT_CAINFO, $cacert); // CA根证书（用来验证的网站证书是否是CA颁布）
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 检查证书中是否设置域名，并且是否与提供的主机名匹配
        $result=curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /*
    curl模拟采集数据，会员一些详细数据
    */
    public function getMembersInfos(){
        session_start();
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL,$this -> url);
        curl_setopt($ch, CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_COOKIE,$_SESSION['cookies']);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$this -> params);
        curl_setopt ($ch, CURLOPT_REFERER,$this -> url);
        $result=curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**分析会员数据
     * @param $rs
     * @param $page
     * @return mixed|string
     */
    public function getMembersInfo($rs, $page){
        $rsBlank = preg_replace("/\s\n\t/","",$rs);
        //$rsBlank = str_replace(' ', '', $rsBlank);
        preg_match_all("/delForm.*>(.*)<\/form>/isU", $rsBlank ,$tables);
        if(isset($tables[1][0])) {
            if($page>1) {
                return preg_replace("/<thead[^>]*>.*<\/thead>/isU", '', $tables[1][0]);
            } else {
                return $tables[1][0];
            }
        } else {
            return '';
        }
        return $tables[1][0];
    }

    /**
     * 获取会员信息下载到CVS
     * @param $html
     * @param $shopname
     */
    public function downMembersCvs($data, $shopname, $access_token){
        $k = 0;
        foreach ($data as &$item) {
            //会员信息
            $custom_id = $item['custom_id'];
            $shop_sid = $item['SHOP_ID'];
            $this -> url = "https://saas.mljia.cn/customer/info/get?custom_id=$custom_id&shop_sid=$shop_sid&access_token=$access_token";
            $rs = $this -> curl();
            $memberData = json_decode($rs,true);
            $memberData =  base64_decode($memberData['content']);
            $memberData = json_decode($memberData,true);

            //会员卡信息
            $this -> url = "https://saas.mljia.cn/customer/card/list?shop_sid=$shop_sid&card_flag=0&custom_id=$custom_id&page=1&access_token=$access_token";
            $rs = $this -> curl();
            $cardData = json_decode($rs,true);
            $cardData =  base64_decode($cardData['content']);
            $cardData = json_decode($cardData,true);

            if(isset($cardData[0]) && count($cardData[0]) > 0){
                foreach($cardData as $card){
                    $card = $card;
                    //卡号
                    $other = $item;
                    $newdata[$k][0] = "\t".$other['custom_member_id']; //卡号
                    $newdata[$k][1] = str_replace($other['custom_member_id'], '', $other['custom_name']); //姓名
                    $newdata[$k][2] = $other['custom_mobile']; //手机号
                    $newdata[$k][3] = $other['custom_sex']; //性别

                    //卡类型
                    $newdata[$k][4] = $card['card_name']; //卡类型

                    $newdata[$k][5] = '10'; //折扣

                    //卡金余额信息,
                    $newdata[$k][6] = $card['card_info_list'][0]['left_not_given_money']; //卡余额
                    $newdata[$k][12] = 0; //欠款
                    $newdata[$k][7] = $other['custom_total_money']; //充值总额
                    $newdata[$k][9] = '0'; //消费总额
                    $newdata[$k][10] = $card['card_info_list'][0]['left_given_money']; //赠送金
                    $newdata[$k][8] = 0; //消费次数
                    $newdata[$k][11] = 0; //积分
                    $newdata[$k][13] = $card['card_open_date']; //开卡时间

                    $newdata[$k][14] = ''; //最后消费时间
                    $newdata[$k][15] = $memberData['birthday']; //生日
                    $newdata[$k][16] = $memberData['birthday_remind_flag']=='1'?1:0; //生日类型（1阳历 公里，0阴历 农历）
                    $newdata[$k][17] = $memberData['note']; //会员备注
                    ksort($newdata[$k]);
                    $k++;
                }
            } else {
                $other = $item;
                $newdata[$k][0] = "\t".$other['custom_member_id']; //卡号
                $newdata[$k][1] = str_replace($other['custom_member_id'], '', $other['custom_name']); //姓名
                $newdata[$k][2] = $other['custom_mobile']; //手机号
                $newdata[$k][3] = $other['custom_sex']; //性别

                //卡类型
                $newdata[$k][4] = ''; //卡类型

                $newdata[$k][5] = '10'; //折扣

                //卡金余额信息,
                $newdata[$k][6] = 0; //卡余额
                $newdata[$k][12] = 0; //欠款
                $newdata[$k][7] = $other['custom_total_money']; //充值总额
                $newdata[$k][9] =  0; //消费总额
                $newdata[$k][10] = 0; //赠送金
                $newdata[$k][8] = 0; //消费次数
                $newdata[$k][11] = 0; //积分
                $newdata[$k][13] = ''; //开卡时间

                $newdata[$k][14] = ''; //最后消费时间
                $newdata[$k][15] = $memberData['birthday']; //生日
                $newdata[$k][16] = $memberData['birthday_remind_flag']=='1'?1:0; //生日类型（1阳历 公里，0阴历 农历）
                $newdata[$k][17] = $memberData['note']; //会员备注
                ksort($newdata[$k]);
                $k++;
            }
            $k++;
        }

        //导出CVS
        $cvsstr = "卡号(必填[唯一]),姓名(必填),手机号(必填[唯一]),性别(必填[“0”代表男，“1”代表女]),卡类型(必填[系统编号]),折扣(必填),卡金余额(必填),充值总额,消费次数,消费总额,赠送金,积分,欠款,开卡时间(格式：YYYY-mm-dd),最后消费时间(格式：YYYY-mm-dd),生日(格式：YYYY-mm-dd),生日类型（1阳历，0阴历）,会员备注\n";
        $filename = $shopname.'_会员信息.csv';
        $cvsstr = iconv('utf-8','gb2312//ignore',$cvsstr);

        foreach($newdata as &$v){
            foreach($v as $k=>&$v1){
                //转码
                $cvsdata = iconv('utf-8','gb2312//ignore',$v1);
                $cvsstr .= $cvsdata; //用引文逗号分开
                if($k < 19) {
                    $cvsstr .= ","; //用引文逗号分开
                }
            }
            $cvsstr .= "\n";
        }
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=".$filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo $cvsstr;
    }
    /*
    curl模拟采集数据，会员套餐数据
    */
    public function getPackagePage(){
        session_start();
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL,$this -> url);
        curl_setopt($ch, CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_COOKIE,$_SESSION['cookies']);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$this -> params);
        curl_setopt ($ch, CURLOPT_REFERER,$this -> url);
        curl_setopt ($ch, CURLOPT_REFERER,$this -> referer);
        $result=curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     *获取套餐页面数据
     */
    public function getPackageInfo($rs, $page){
        $rsBlank = preg_replace("/\s\n\t/","",$rs);
        //$rsBlank = str_replace(' ', '', $rsBlank);
        preg_match_all("/table-responsive.*>(.*)<\/form>/isU", $rsBlank ,$tables);
        if(isset($tables[1][0])) {
            if($page>1) {
                return preg_replace("/<thead[^>]*>.*<\/thead>/isU", '', $tables[1][0]);
            } else {
                return $tables[1][0];
            }
        } else {
            return '';
        }
        return $tables[1][0];
    }

    /**
     * 获取会员套餐信息下载到CVS
     * @param $html
     * @param $shopname
     */
    public function downPackageCvs($html,$shopname){
        $rules = array(
            //采集tr中的纯文本内容
            'other' => array('tr','html'),
        );
        $newdata = array();
        $data = QueryList::Query($html, $rules)->data;
        foreach ($data as $k=>&$item) {
            $other = explode('</td>', $item['other']);
            if(count($other) > 8) {
                //unset($other[0]);//去掉第一空白项
                $item['other'] = $other;
                foreach ($other as $k1 => &$v1) {
                    $v1 = strip_tags($v1);;
                    $v1 = preg_replace("/\s\n\t/","",$v1);
                    $v1 = str_replace(' ', '', $v1);
                    $v1= trim(str_replace(PHP_EOL, '', $v1));
                    if($k1 == 5) {
                        $v1 = trim(str_replace(',', '，', $v1));
                        $v1 = explode('项目编号:', $v1);
                        unset($v1[0]);
                    }
                }

                foreach($other[5] as $k2=>$v2) {
                    $newA[0] = $other[0]; //手机号
                    $newA[1] = "\t".$other[1]; //卡号
                    $newA[2] = $other[2]; //姓名
                    $newA[3] = $other[3]; //卡名称
                    $newA[4] = $other[4]; //卡类型

                    $v2 .= "#";
                    //获取项目套餐信息
                    preg_match('/(.*)，项目名称/isU', $v2, $p1);  //项目编号
                    preg_match('/项目名称:(.*)，/isU', $v2, $p2);  //项目名称
                    preg_match('/总次数:(.*)，/isU', $v2, $p3);  //总次数
                    preg_match('/剩余次数:(.*)，/isU', $v2, $p4);  //剩余次数
                    preg_match('/单次消费金额:(.*)，/isU', $v2, $p5);  //单次消费金额
                    preg_match('/剩余金额:(.*)#/isU', $v2, $p6);  //剩余金额
                    if(!isset($p6[1])) {
                        preg_match('/剩余金额:(.*)，/isU', $v2, $p6);  //剩余金额
                    }
                    preg_match('/失效日期：(.*)#/isU', $v2, $p7);  //失效日期
                    $newA[5] = isset($p1[1])?$p1[1]:' ';//项目编号
                    $newA[6] = isset($p2[1])?$p2[1]:' ';//项目名称
                    $newA[7] = isset($p3[1])?$p3[1]:' ';//总次数
                    $newA[8] = isset($p4[1])?$p4[1]:' ';//剩余次数
                    $newA[9] = isset($p5[1])?$p5[1]:' '; //单次消费金额
                    $newA[10] = isset($p6[1])?$p6[1]:' '; //剩余金额
                    $newA[11] = isset($p7[1])?$p7[1]:' ';//失效日期

                    $newA[12] = $newA[8];//总剩余次数
                    $newA[13] = $newA[10]; //总剩余金额
                    $newA[14] = $other[8];
                    $newdata[] = $newA;
                }
            }
        }
        //导出CVS
        $cvsstr = "手机号,卡号,姓名,卡名称,卡类型,项目编号,项目名称,总次数,剩余次数,单次消费金额,剩余金额,失效日期,总剩余次数,总剩余金额\n";
        $filename = $shopname.'_会员套餐信息.csv';
        $cvsstr = iconv('utf-8','gb2312//ignore',$cvsstr);
        foreach($newdata as &$v){
            foreach($v as $k=>&$v1){
                //时间转换
                if($k == 5 || $k == 19) {
                    //$v1 = strtotime($v1);
                }
                //转码
                $cvsdata = iconv('utf-8','gb2312//ignore',$v1);
                $cvsstr .= $cvsdata; //用引文逗号分开
                if($k < 14) {
                    $cvsstr .= ","; //用引文逗号分开
                }
            }
            $cvsstr .= "\n";
        }
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=".$filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo $cvsstr;
    }


    /**
     * 获取次卡会员
     * @param $html
     * @param $shopname
     */
    public function downPackageCvsNew($data, $shopname, $access_token){
        foreach ($data as &$item) {
            //会员信息
            $custom_id = $item['custom_id'];
            $shop_sid = $item['SHOP_ID'];
            // $this -> url = "https://saas.mljia.cn/customer/info/get?custom_id=$custom_id&shop_sid=$shop_sid&access_token=$access_token";
            // $rs = $this -> curl();
            // $memberData = json_decode($rs,true);
            // $memberData =  base64_decode($memberData['content']);
            // $memberData = json_decode($memberData,true);

            //会员卡信息
            $this -> url = "https://saas.mljia.cn/customer/card/list?shop_sid=$shop_sid&card_flag=0&custom_id=$custom_id&page=1&access_token=$access_token";
            $rs = $this -> curl();
            $cardData = json_decode($rs,true);
            $cardData =  base64_decode($cardData['content']);
            $cardData = json_decode($cardData,true);

            $newdata = array();
            if(isset($cardData[0]) && count($cardData[0]) > 0){
                foreach($cardData as $card){
                    if( isset($card['card_info_list'][0]['item_left_num']) && isset($card['card_info_list'][0]['item_total_num']) ) {
                        $other = $item;

                        $item_total_num = $card['card_info_list'][0]['item_total_num'];//总次数
                        $item_left_num = $card['card_info_list'][0]['item_left_num'];//剩余次数

                        $newA[0] = $other['custom_mobile']; //手机号
                        $newA[1] = "\t".$other['custom_member_id']; //卡号
                        $newA[2] = str_replace($other['custom_member_id'], '', $other['custom_name']); //姓名
                        $newA[3] = $card['card_name']; //卡名称
                        $newA[4] = $card['card_name']; //卡类型

                        $newA[5] = $card['card_name'];//项目编号
                        $newA[6] = $card['card_name'];//项目名称
                        $newA[7] = $item_total_num;//总次数
                        $newA[8] = $item_left_num;//剩余次数
                        $newA[9] = $card['card_price']/$item_total_num; //单次消费金额
                        $newA[10] = $item_left_num*$newA[9]; //剩余金额
                        $newA[11] = '永久有效';//失效日期

                        $newA[12] = $newA[8];//总剩余次数
                        $newA[13] = $newA[10]; //总剩余金额
                        $newA[14] = $other[8];
                        $newdata[] = $newA;

                    }
                }
            }
        }

        //导出CVS
        $cvsstr = "手机号,卡号,姓名,卡名称,卡类型,项目编号,项目名称,总次数,剩余次数,单次消费金额,剩余金额,失效日期,总剩余次数,总剩余金额\n";
        $filename = $shopname.'_会员次卡信息.csv';
        $cvsstr = iconv('utf-8','gb2312//ignore',$cvsstr);
        foreach($newdata as &$v){
            foreach($v as $k=>&$v1){
                //时间转换
                if($k == 5 || $k == 19) {
                    //$v1 = strtotime($v1);
                }
                //转码
                $cvsdata = iconv('utf-8','gb2312//ignore',$v1);
                $cvsstr .= $cvsdata; //用引文逗号分开
                if($k < 14) {
                    $cvsstr .= ","; //用引文逗号分开
                }
            }
            $cvsstr .= "\n";
        }
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=".$filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo $cvsstr;
    }

    /**
     * 获取员工信息下载到CVS
     * @param $html
     * @param $shopname
     */
    public function downStaffCvs($html,$shopname){
        $rules = array(
            //采集tr中的纯文本内容
            'other' => array('tr','html'),
        );
        $newdata = array();
        $data = QueryList::Query($html, $rules)->data;
        foreach ($data as $k=>&$item) {
            $other = explode('</td>', $item['other']);
            if(count($other) > 8) {
                //unset($other[0]);//去掉第一空白项
                $item['other'] = $other;
                foreach ($other as $k1 => &$v1) {
                    $v1 = strip_tags($v1);;
                    $v1 = preg_replace("/\s\n\t/","",$v1);
                    $v1 = str_replace(' ', '', $v1);
                    $v1= trim(str_replace(PHP_EOL, '', $v1));
                }

                $date1 = substr($other[11], 0, 3).' '.substr($other[11], 3, 3).' '.substr($other[11], 19, 4);
                $date1 = date('Y-m-d', strtotime($date1));
                $newdata[$k][0] = "\t".$other[1];
                $newdata[$k][1] = $other[2];
                $newdata[$k][2] = $other[3];
                $newdata[$k][3] = preg_match('/男/', $other[4])?0:1;
                $newdata[$k][4] = $other[9];
                $newdata[$k][5] = str_replace('阴', '', $other[10]);
                $newdata[$k][5] = str_replace('阳', '', $newdata[$k][5]);
                $newdata[$k][5] = str_replace('"', '', $newdata[$k][5]);
                $newdata[$k][6] = $date1;
                $newdata[$k][7] = $other[8];
                $newdata[$k][8] = '';

                //日期格式含有1900，设置为空
                if(preg_match("/1900/isU", $newdata[$k][5])) {
                    $newdata[$k][5] = '';
                }
            }
        }
        unset($newdata[count($newdata)]);
        unset($newdata[count($newdata)]);

        //导出CVS
        $cvsstr = "编号(必填[唯一]),姓名(必填),级别(必填),性别,手机号码,生日,入职时间,身份证号,银行账号\n";
        $filename = $shopname.'_员工信息.csv';
        $cvsstr = iconv('utf-8','gb2312//ignore',$cvsstr);

        foreach($newdata as &$v){
            foreach($v as $k=>&$v1){
                //转码
                $cvsdata = iconv('utf-8','gb2312//ignore',$v1);
                $cvsstr .= $cvsdata; //用引文逗号分开
                if($k < 8) {
                    $cvsstr .= ","; //用引文逗号分开
                }
            }
            $cvsstr .= "\n";
        }
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=".$filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo $cvsstr;
    }
}

?>
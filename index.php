<?php
/*$cc_uri = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : ($_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']);
$arr_uri = array("/h5/index/userInfo","/h5/api/gameImage","/h5/ad/check.html","/service/count/credit.html","/api/tt/getInfoHigh");

if($_SERVER['HTTP_HOST']=="www.501wan.com" && !in_array($cc_uri, $arr_uri)  && strpos($cc_uri,"/api/tt/getInfoHigh")!=0) {
    $agent = $_SERVER['HTTP_USER_AGENT'];
    if(empty($cc_uri)){
        $cc_uri = "index.php";
    }
    $cc3 = md5($cc_uri . $agent. $cc_uri. $cc_uri);
    $real_ip = $_SERVER['REMOTE_ADDR'];

    if(@$_COOKIE[$cc3]==1){
        cc_log($real_ip, $real_ip, 'cc_log3.txt', $cc_uri."-".$_SERVER['HTTP_USER_AGENT']."\n");
        exit('系统繁忙，请稍后访问');
    }


    if (empty($agent)) {
        exit('系统繁忙，请稍后访问');
    }

    $cc = md5($cc_uri . $agent);
    $cc2 = md5($cc_uri . $agent. $cc_uri);

    if (LRedis::redis()->get($cc3) == 1) {
        if(!setcookie($cc3,1, time()+60)){
            exit('系统繁忙，请稍后访问');
        }
        //cc_log($real_ip, $real_ip, 'cc_log2.txt', $cc_uri."-".$_SERVER['HTTP_USER_AGENT']."\n");
        exit('系统繁忙，请稍后访问');
    }

    if(LRedis::redis()->get($cc)){
        $i = LRedis::redis()->get($cc2) + 1;
        if($i>=7){
            LRedis::redis()->setex($cc3, 30, 1);
            if(!setcookie($cc3,1, time()+60)){
                exit('系统繁忙，请稍后访问');
            }
            //cc_log($real_ip, $real_ip, 'cc_log.txt', $cc_uri."-".$_SERVER['HTTP_USER_AGENT']."\n");
            exit('系统繁忙，请稍后访问');
        }
        LRedis::redis()->setex($cc2, 3, $i);
    }else{
        LRedis::redis()->setex($cc, 1, 1);
    }
}
//记录cc日志
function cc_log($client_ip, $real_ip, $cc_log, $cc_uri){
    $temp_time = date("Y-m-d H:i:s", time() + 3600*8);

    $temp_result = "[".$temp_time."] [client ".$client_ip."] ";
    if($real_ip) $temp_result .= " [real ".$real_ip."] ";
    $temp_result .= $cc_uri . "rn";

    $handle = fopen ("$cc_log", "rb");
    $oldcontent = fread($handle,filesize("$cc_log"));
    fclose($handle);

    $newcontent = $temp_result . $oldcontent;
    $fhandle=fopen("$cc_log", "wb");
    fwrite($fhandle,$newcontent,strlen($newcontent));
    fclose($fhandle);
}

class LRedis
{
    private static $_redis = null;

    public static function redis(){
        if($_SERVER['HTTP_HOST']=="www.501wan.com" || $_SERVER['HTTP_HOST']=="t.501wan.com"){
            $host = "192.168.1.39";
            $redis_psd ="501wan_redis";
        }else{
            $host = "192.168.1.39";
            $redis_psd ="501wan_redis";
        }
        if(!self::$_redis){
            self::$_redis = new Redis();
            self::$_redis->connect($host, 6379);
            self::$_redis->auth($redis_psd);
            self::$_redis->select(1);
        }
        return self::$_redis;
    }
    public static function instance(){
        return self::redis();
    }

    public static function get($key,$callback=null,$expire=-1)
    {
        $result = LRedis::instance()->get($key);
        if(empty($result) && !empty($callback)){
            if(is_callable($callback)){
                $result  = $callback();
            }elseif(is_string($callback)){
                $result = $callback;
            }elseif(is_object($callback)){
                $result = CJSON::encode($callback);
            }
            if($expire==-1){
                self::set($key,$result);
            }else{
                self::setex($key,$result,$expire);
            }
        }
        return $result;
    }

    public static function setex($key,$value,$expire=7200)
    {
        return LRedis::instance()->setex($key,$expire,$value);
    }

    public static function set($key,$value)
    {
        return LRedis::instance()->set($key,$value);
    }


    public static function delete($id)
    {
        LRedis::instance()->delete($id);
    }
}*/

Session_start();
header("Access-Control-Allow-Credentials: true");

if(empty($_SERVER['HTTP_REFERER'])){
    $host = "http://www.501wan.com";
}else{
    /*$host = "http:".explode(":",$_SERVER['HTTP_REFERER'])[1];
    if($host == "http://www.501wan.com/"){
        $host = "http://www.501wan.com";
    }*/
    $host = "http://www.501wan.com";
}
if(strpos($host,"8080")>0){
    header("Access-Control-Allow-Origin: $host:8080");
}else{
    header("Access-Control-Allow-Origin: $host");
}

header("Access-Control-Allow-Methods:GET, POST");
header("Access-Control-Allow-Headers:DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");

if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
    header("HTTP/1.1 200 OK");
    exit();
}



/*$cc_min_nums = '3';                    //次，刷新次数
$cc_url_time = '5';                    //秒，延迟时间
$cc_log = 'cc_log.txt';                //启用本行为记录日志
$cc_forward = 'http://localhost';    //释放到URL
$cc_uri = $_SERVER['REQUEST_URI']?$_SERVER['REQUEST_URI']:($_SERVER['PHP_SELF']?$_SERVER['PHP_SELF']:$_SERVER['SCRIPT_NAME']);
$arr_uri = array("/h5/index/userInfo","/h5/api/gameImage");
//--------------------------------------------
if($_SERVER['HTTP_HOST']=="www.501wan.com" && !in_array($cc_uri, $arr_uri)){
    //返回URL
    $site_url = 'http://'.$_SERVER ['HTTP_HOST'].$cc_uri;

//启用session

    $_SESSION["visiter"] = true;
    if ($_SESSION["visiter"] <> true){
        printf('您的刷新过快，请稍后。');
        //echo "<script>setTimeout(\"window.location.href ='$cc_forward';\", 1);</script>";
        //header("Location: ".$cc_forward);
        exit;
    }

    $timestamp = time();
    $cc_nowtime = $timestamp ;
    if (isset($_SESSION['cc_lasttime'])){
        $cc_lasttime = $_SESSION['cc_lasttime'];
        $cc_times = $_SESSION['cc_times'] + 1;
        $_SESSION['cc_times'] = $cc_times;
    }else{
        $cc_lasttime = $cc_nowtime;
        $cc_times = 1;
        $_SESSION['cc_times'] = $cc_times;
        $_SESSION['cc_lasttime'] = $cc_lasttime;
    }

//获取真实IP
    if (isset($_SERVER)){
        $real_ip = $_SERVER['REMOTE_ADDR'];
    }else{
        $real_ip = getenv("REMOTE_ADDR");
    }

//print_r($_SESSION);

//释放IP
    if (($cc_nowtime - $cc_lasttime)<=0){
        if ($cc_times>=$cc_min_nums){
            $_SESSION[md5($real_ip.$cc_uri.$_SERVER['HTTP_USER_AGENT'])] = md5($real_ip.$cc_uri.$_SERVER['HTTP_USER_AGENT'])+1;
            if(!empty($cc_log))    cc_log(get_ip(), $real_ip, $cc_log, $cc_uri."\r");    //产生log
            //echo "您的刷新过快，系统再次连接中!<script>setTimeout(\"window.location.href ='$site_url';\", 3000);</script>";
            //printf('您的刷新过快，请稍后。');
            echo "刷新过快，请稍后再试";
            //header("Location: ".$site_url);
            exit;
        }
    }else{
        $cc_times = 0;
        $_SESSION['cc_lasttime'] = $cc_nowtime;
        $_SESSION['cc_times'] = $cc_times;
    }
}*/


//防止快速刷新

/*$seconds = '3'; //时间段[秒]
$refresh = '5'; //刷新次数
//设置监控变量
$cur_time = time();

if(isset($_SESSION['last_time'])){
    $_SESSION['refresh_times'] += 1;
}else{
    $_SESSION['refresh_times'] = 1;
    $_SESSION['last_time'] = $cur_time;
}
//处理监控结果
if($cur_time - $_SESSION['last_time'] < $seconds){
    if($_SESSION['refresh_times'] >= $refresh){
        exit('Access Denied');
    }
}else{
    $_SESSION['refresh_times'] = 0;
    $_SESSION['last_time'] = $cur_time;
}*/

error_reporting(0);

//error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

//bind domain

define('ROOT', dirname(dirname(__FILE__)));
define('WEB', dirname(__FILE__));

//初始化系统
include ROOT . '/config/init.php';

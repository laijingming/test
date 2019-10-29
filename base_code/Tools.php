<?php

/**
 * Class Tools
 * 常用方法
 */
class Tools{

    /**
     * func 验证中文姓名 2~5个汉字
     * @param $name
     * @return bool
     */
    static function isChineseName($name){
        if (preg_match('/^([\xe4-\xe9][\x80-\xbf]{2}){2,5}$/', $name)) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * func 判断生日日期格式是否正确
     * @param $birth_time 生日字符串
     * @return bool
    */
    static function isBirthTime($birth_time){
        if(preg_match('/(^\d{4}年\d{2}月\d{2}日$)|(^\d{4}年\d{2}月\d{2}日 \d{2}时$)/s',$birth_time)){
            return true;
        } else {
            return false;
        }
    }

    /**
     * @throws CHttpException
     * 抛出异常
     */
    static function toError($code=404,$message="此页面不存在"){
        throw new CHttpException($code,$message);die();
    }
    /**
     * 脚本执行时间调试
     */
    static function getmicrotime(){
        list($usec,$sec) = explode(" ",microtime());
        $num = ((float)$usec+(float)$sec);
        return sprintf("%.4f",$num);
    }
    /**
     * @param int $code 返回码
     * @param string $msg 返回信息
     * @param string $info 需要返回的数据
     * @return json_encode
     */
    static function jsonReturn($code = 200,$msg = '',$info = '',$unicode=""){
        $res = [
            'code' => $code,
            'msg'  => $msg,
            'info' => $info
        ];
        if($unicode){
            $res = json_encode($res,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
        }else{
            $res = json_encode($res);
        }

        // 返回JSON数据格式到客户端 包含状态信息
        header('Content-Type:application/json; charset=utf-8');
        exit($res);
    }

    /**
     * 校验手机号码
     */
    static function checkTelphone($phonenumber){
        if(preg_match("/^1[3456789]{1}\d{9}$/",$phonenumber)){
            return true;
        }else{
            return false;
        }
    }

    /**
    判断是否合法车牌号
     *
    @name isCarLicense
    @author furong
    @param $license
    @return bool
    @since 2016年12月24日 11:51:22
    @abstract
    2017年4月7日 14:06:17 增加对 特种车牌，武警车牌,军牌的校验
    2018年3月5日 13:32:18 增加对 6位新能源车牌的校验
     */
    static function isCarLicense($license)
    {

        if (empty($license)) {
            return false;
        }
        #匹配民用车牌和使馆车牌
        # 判断标准
        # 1，第一位为汉字省份缩写
        # 2，第二位为大写字母城市编码
        # 3，后面是5位仅含字母和数字的组合
        {
            $regular = "/[京津冀晋蒙辽吉黑沪苏浙皖闽赣鲁豫鄂湘粤桂琼川贵云渝藏陕甘青宁新使]{1}[A-Z]{1}[0-9a-zA-Z]{5}$/u";
            preg_match($regular, $license, $match);
            if (isset($match[0])) {
                return true;
            }
        }

        #匹配特种车牌(挂,警,学,领,港,澳)
        #参考 https://wenku.baidu.com/view/4573909a964bcf84b9d57bc5.html
        {
            $regular = '/[京津冀晋蒙辽吉黑沪苏浙皖闽赣鲁豫鄂湘粤桂琼川贵云渝藏陕甘青宁新]{1}[A-Z]{1}[0-9a-zA-Z]{4}[挂警学领港澳]{1}$/u';
            preg_match($regular, $license, $match);
            if (isset($match[0])) {
                return true;
            }
        }

        #匹配武警车牌
        #参考 https://wenku.baidu.com/view/7fe0b333aaea998fcc220e48.html
        {
            $regular = '/^WJ[京津冀晋蒙辽吉黑沪苏浙皖闽赣鲁豫鄂湘粤桂琼川贵云渝藏陕甘青宁新]?[0-9a-zA-Z]{5}$/ui';
            preg_match($regular, $license, $match);
            if (isset($match[0])) {
                return true;
            }
        }

        #匹配军牌
        #参考 http://auto.sina.com.cn/service/2013-05-03/18111149551.shtml
        {
            $regular = "/[A-Z]{2}[0-9]{5}$/";
            preg_match($regular, $license, $match);
            if (isset($match[0])) {
                return true;
            }
        }
        #匹配新能源车辆6位车牌
        #参考 https://baike.baidu.com/item/%E6%96%B0%E8%83%BD%E6%BA%90%E6%B1%BD%E8%BD%A6%E4%B8%93%E7%94%A8%E5%8F%B7%E7%89%8C
        {
            #小型新能源车
            $regular = "/[京津冀晋蒙辽吉黑沪苏浙皖闽赣鲁豫鄂湘粤桂琼川贵云渝藏陕甘青宁新]{1}[A-Z]{1}[DF]{1}[0-9a-zA-Z]{5}$/u";
            preg_match($regular, $license, $match);
            if (isset($match[0])) {
                return true;
            }
            #大型新能源车
            $regular = "/[京津冀晋蒙辽吉黑沪苏浙皖闽赣鲁豫鄂湘粤桂琼川贵云渝藏陕甘青宁新]{1}[A-Z]{1}[0-9a-zA-Z]{5}[DF]{1}$/u";
            preg_match($regular, $license, $match);
            if (isset($match[0])) {
                return true;
            }
        }
        return false;
    }

    /**
     *Utf-8、gb2312都支持的汉字截取函数
     *cut_str(字符串, 截取长度, 开始长度, 末尾字符代替,编码);
     *编码默认为 utf-8
     *开始长度默认为 0
     *末尾字符代替默认为 空
    */
    static function cut_str($string, $sublen, $start = 0,$msg = '', $code = 'UTF-8')
    {
        if($code == 'UTF-8')
        {
            $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
            preg_match_all($pa, $string, $t_string);
            if(count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen))."...";
            return join('', array_slice($t_string[0], $start, $sublen));
        } else {
            $start = $start*2;
            $sublen = $sublen*2;
            $strlen = strlen($string);
            $tmpstr = '';
            for($i=0; $i< $strlen; $i++)
            {
                if($i>=$start && $i< ($start+$sublen))
                {
                    if(ord(substr($string, $i, 1))>129)
                    {
                        $tmpstr.= substr($string, $i, 2);
                    } else {
                        $tmpstr.= substr($string, $i, 1);
                    }
                }
                if(ord(substr($string, $i, 1))>129) $i++;
            }
            if(strlen($tmpstr)< $strlen ) $tmpstr.= $msg;
            return $tmpstr;
        }
    }
    /**
     * @return string
     * 获取客户端ip
     */
    static function getIp(){
        if ($_SERVER["HTTP_X_FORWARDED_FOR"])
        {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        elseif ($_SERVER["HTTP_CLIENT_IP"])
        {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        }
        elseif ($_SERVER["REMOTE_ADDR"])
        {
            $ip = $_SERVER["REMOTE_ADDR"];
        }
        elseif (getenv("HTTP_X_FORWARDED_FOR"))
        {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        }
        elseif (getenv("HTTP_CLIENT_IP"))
        {
            $ip = getenv("HTTP_CLIENT_IP");
        }
        elseif (getenv("REMOTE_ADDR"))
        {
            $ip = getenv("REMOTE_ADDR");
        }
        else
        {
            $ip = "Unknown";
        }
        return $ip ;
    }
    /**
     * @param $filename
     * @param $content
     * @param $dir
     * 日志存入 (可写入数组)
     * 默认存入根目录log目录
     */
    static function writeLog($filename,$content,$dir){
        //保证目录有效
        if(!$dir){
            $dir = $_SERVER['DOCUMENT_ROOT']."/log";
        }
        if(!is_dir($dir)){
            if(!mkdir($dir, 0755, true)){
                exit("无法创建目录$dir，可能没有权限");
            }
        }
        $file_path = $dir.'/'.$filename;
        if(is_array($content)){
            $content = var_export($content,true);
        }
        if($f = file_put_contents($file_path, $content.PHP_EOL,FILE_APPEND)){//这个函数支持版本(PHP 5)
            return true;
        }
    }
    /**
     * 下载远程图片
     * @param string $url 图片的绝对url
     * @param string $filepath 文件的完整路径（例如/www/images/test） ，此函数会自动根据图片url和http头信息确定图片的后缀名
     * @param string $filename 要保存的文件名(不含扩展名)
     * @return mixed 下载成功返回一个描述图片信息的数组，下载失败则返回false
     */
    static public function downloadImage($url, $filepath, $filename) {
        //服务器返回的头信息
        $responseHeaders = array();
        //原始图片名
        $originalfilename = '';
        //图片的后缀名
        $ext = '';
        $ch = curl_init(); //初始化curl
        curl_setopt($ch, CURLOPT_URL, $url);//设置链接
        curl_setopt($ch, CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//https
        $html = curl_exec($ch);//接收返回信息
        //获取此次抓取的相关信息
        $httpinfo = curl_getinfo($ch);
        curl_close($ch);
        if ($html !== false) {
            //分离response的header和body，由于服务器可能使用了302跳转，所以此处需要将字符串分离为 2+跳转次数 个子串
            $httpArr = explode("\r\n\r\n", $html, 2 + $httpinfo['redirect_count']);
            //倒数第二段是服务器最后一次response的http头
            $header = $httpArr[count($httpArr) - 2];
            //倒数第一段是服务器最后一次response的内容
            $body = $httpArr[count($httpArr) - 1];
            $header.="\r\n";
            //获取最后一次response的header信息
            preg_match_all('/([a-z0-9-_]+):\s*([^\r\n]+)\r\n/i', $header, $matches);
            if (!empty($matches) && count($matches) == 3 && !empty($matches[1]) && !empty($matches[1])) {
                for ($i = 0; $i < count($matches[1]); $i++) {
                    if (array_key_exists($i, $matches[2])) {
                        $responseHeaders[$matches[1][$i]] = $matches[2][$i];
                    }
                }
            }
            //获取图片后缀名
            if (0 < preg_match('{(?:[^\/\\\\]+)\.(jpg|jpeg|gif|png|bmp)$}i', $url, $matches)) {
                $originalfilename = $matches[0];
                $ext = $matches[1];
            } else {
                if (array_key_exists('Content-Type', $responseHeaders)) {
                    if (0 < preg_match('{image/(\w+)}i', $responseHeaders['Content-Type'], $extmatches)) {
                        $ext = $extmatches[1];
                    }
                }
            }
            //保存文件
            if (!empty($ext)) {
                //如果目录不存在，则先要创建目录
                if(!is_dir($filepath)){
                    mkdir($filepath, 0777, true);
                }
                $filepath .= '/'.$filename.".$ext";
                $local_file = fopen($filepath, 'w');
                if (false !== $local_file) {
                    if (false !== fwrite($local_file, $body)) {
                        fclose($local_file);
                        $sizeinfo = getimagesize($filepath);
                        return array('filepath' => realpath($filepath), 'width' => $sizeinfo[0], 'height' => $sizeinfo[1], 'orginalfilename' => $originalfilename, 'filename' => pathinfo($filepath, PATHINFO_BASENAME));
                    }
                }
            }
        }
        return false;
    }

    /**
     * 对象 转 数组
     *
     * @param object $obj 对象
     * @return array
     */
    static public function object_to_array($obj) {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)self::object_to_array($v);
            }
        }

        return $obj;
    }

    /**
     * @param $dir
     * 删除指定文件夹以及文件夹下的所有文件
     */
    static public function deldir($dir) {
        //先删除目录下的文件：
        $dh = opendir($dir);
        while ($file=readdir($dh)) {
            if($file!="." && $file!="..") {
                $fullpath=$dir."/".$file;
                if(!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    deldir($fullpath);
                }
            }
        }
    }

    /**
     * @param $object
     * @return array
     * 对象转数组
     */
    static public function object2array($object){
        $array = array();
        foreach($object as $key=> $item){
            $array[$key] = $item?$item:'';
        }
        return $array;
    }
    /**
     * @param $url
     * @param $str
     * @return mixed
     * post 请求数据
     */
    static public function get_api($url,$str){
        $res = self::getapis($url,$str);
        $res = json_decode($res,true);
        return $res;
    }
    /**
     * @param $url
     * @param $str
     * @return mixed
     * post curl请求
     */
    static public function getapis($url,$str){
        $ch = curl_init(); //初始化curl
        curl_setopt($ch, CURLOPT_URL, $url);//设置链接
        curl_setopt($ch, CURLOPT_HEADER,1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息
        curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $str);//POST数据
        $response = curl_exec($ch);//接收返回信息
        curl_close($ch);
        return $response;
    }

    /**
     * @param $url
     * @param $str
     * @return mixed
     * post curl请求https
     */
    static public function getapi_https($url,$str){
        if (empty($url) || empty($str)) {
            return false;
        }
        $ch = curl_init(); //初始化curl
        curl_setopt($ch, CURLOPT_URL, $url);//设置链接
        curl_setopt($ch, CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息
        curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $str);//POST数据
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//https
        $response = curl_exec($ch);//接收返回信息
        curl_close($ch);
        return $response;
    }

    /**
     * 返回数组中指定的一列
     * @param $input            需要取出数组列的多维数组（或结果集）
     * @param $columnKey        需要返回值的列，它可以是索引数组的列索引，或者是关联数组的列的键。 也可以是NULL，此时将返回整个数组（配合index_key参数来重置数组键的时候，非常管用）
     * @param null $indexKey    作为返回数组的索引/键的列，它可以是该列的整数索引，或者字符串键值。
     * @return array            返回值
     */
    static public function _array_column($input, $columnKey, $indexKey = null)
    {
        if (!function_exists('array_column')) {
            $columnKeyIsNumber = (is_numeric($columnKey)) ? true : false;
            $indexKeyIsNull = (is_null($indexKey)) ? true : false;
            $indexKeyIsNumber = (is_numeric($indexKey)) ? true : false;
            $result = array();
            foreach ((array)$input as $key => $row) {
                if ($columnKeyIsNumber) {
                    $tmp = array_slice($row, $columnKey, 1);
                    $tmp = (is_array($tmp) && !empty($tmp)) ? current($tmp) : null;
                } else {
                    $tmp = isset($row[$columnKey]) ? $row[$columnKey] : null;
                }
                if (!$indexKeyIsNull) {
                    if ($indexKeyIsNumber) {
                        $key = array_slice($row, $indexKey, 1);
                        $key = (is_array($key) && !empty($key)) ? current($key) : null;
                        $key = is_null($key) ? 0 : $key;
                    } else {
                        $key = isset($row[$indexKey]) ? $row[$indexKey] : 0;
                    }
                }
                $result[$key] = $tmp;
            }
            return $result;
        } else {
            return array_column($input, $columnKey, $indexKey);
        }
    }

    /*
     * 根据二维数组某个字段的值查找数组
    */
    function filter_by_value($array, $index, $value){
        if(is_array($array) && count($array)>0)
        {
            foreach(array_keys($array) as $key){
                $temp[$key] = $array[$key][$index];

                if ($temp[$key] == $value){
                    $newarray[$key] = $array[$key];
                }
            }
        }
        return $newarray;
    }

    /**
     * @return string
     * 生成唯一HASH值
     * 例：1a4e1d61ab87e80da18e7f5fc16e00a05757fc0d
     */
    static function get_hash(){
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()+-';
        $random = $chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)];//Random 5 times
        $content = uniqid().$random;
        return sha1($content);
    }

    /**
     * @param $length
     * @return string
     * 生成随机字符串
     */
    static function randomkeys($length=4) {
        $returnStr='';
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        for($i = 0; $i < $length; $i ++) {
            $returnStr .= $pattern {mt_rand ( 0, 61 )}; //生成php随机数
        }
        return $returnStr;
    }

    /**
     * 获取当前页面完整URL地址
     */
    static function get_url() {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? self::safe_replace($_SERVER['PHP_SELF']) : self::safe_replace($_SERVER['SCRIPT_NAME']);
        $path_info = isset($_SERVER['PATH_INFO']) ? self::safe_replace($_SERVER['PATH_INFO']) : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? self::safe_replace($_SERVER['REQUEST_URI']) : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.self::safe_replace($_SERVER['QUERY_STRING']) : $path_info);
        return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
    }

    /**
     * 安全过滤函数
     *
     * @param $string
     * @return string
     */
    static function safe_replace($string) {
        $string = str_replace('%20','',$string);
        $string = str_replace('%27','',$string);
        $string = str_replace('%2527','',$string);
        $string = str_replace('*','',$string);
        $string = str_replace('"','&quot;',$string);
        $string = str_replace("'",'',$string);
        $string = str_replace('"','',$string);
        //$string = str_replace(';','',$string);
        $string = str_replace('<','&lt;',$string);
        $string = str_replace('>','&gt;',$string);
        $string = str_replace("{",'',$string);
        $string = str_replace('}','',$string);
        $string = str_replace('\\','',$string);
        return $string;
    }

    /**
     * xss过滤函数
     *
     * @param $string
     * @return string
     */
    static function remove_xss($string) {
        $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $string);

        $parm1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');

        $parm2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');

        $parm = array_merge($parm1, $parm2);

        for ($i = 0; $i < sizeof($parm); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($parm[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[x|X]0([9][a][b]);?)?';
                    $pattern .= '|(&#0([9][10][13]);?)?';
                    $pattern .= ')?';
                }
                $pattern .= $parm[$i][$j];
            }
            $pattern .= '/i';
            $string = preg_replace($pattern, ' ', $string);
        }
        return $string;
    }

}
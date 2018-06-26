<?php
/**
 * Created by PhpStorm.
 * User: luowei
 * Date: 2018/6/26
 * Time: 11:58
 */
namespace shangxin\yii_alipay\lib;
class Tools{
    /**
     * 除去数组中的空值和签名参数,但保留sign_type
     * @param array $para 签名参数组
     * @return mixed 去掉空值与签名参数后的新签名参数组
     */
    public function paraFilterNew($para) {
        $para_filter = array();
        while (list ($key, $val) = each ($para)) {
            if($key == "sign" || $val == "")continue;
            else	$para_filter[$key] = $para[$key];
        }
        return $para_filter;
    }

    /**
     * 对数组排序
     * @param array $para 排序前的数组
     * @return mixed 排序后的数组
     */
    public function argSort($para) {
        ksort($para);
        reset($para);
        return $para;
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param array $para 需要拼接的数组
     * @return mixed 拼接完成以后的字符串
     */
    public function createLinkstring($para) {
        $arg  = "";
        while (list ($key, $val) = each ($para)) {
            $arg.=$key."=".$val."&";
        }
        //去掉最后一个&字符
        $arg = substr($arg,0,count($arg)-2);

        //如果存在转义字符，那么去掉转义
        if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}

        return $arg;
    }

    /**
     * RSA签名
     * @param array $data 待签名数据
     * @param string $private_key_path 商户私钥文件路径
     * @return mixed 签名结果
     */
    public function rsaSign($data, $private_key_path) {
        $priKey = file_get_contents($private_key_path);
        $res = openssl_get_privatekey($priKey);
        openssl_sign($data, $sign, $res);
        openssl_free_key($res);
        //base64编码
        $sign = base64_encode($sign);
        return $sign;
    }

}
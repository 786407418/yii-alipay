<?php
namespace shangxin\yii_alipay;
use shangxin\yii_alipay\lib\Tools;
use yii\base\Component;
class Ali extends Component {
    /**
     * @var string
     * 支付宝分配给开发者的应用ID
     */
    public $app_id;

    /**
     * @var h5支付接口名称
     * 接口名称
     */
    public $h5_pay_method = "alipay.trade.wap.pay";

    /**
     * @var string
     * 请求使用的编码格式，如utf-8,gbk,gb2312等
     */
    public $charset = "utf-8";

    /**
     * @var string
     * 商户生成签名字符串所使用的签名算法类型，目前支持RSA2和RSA，推荐使用RSA2
     */
    public $sign_type = "RSA";

    /**
     * @var string
     * h5支付调用的接口版本，固定为：1.0
     */
    public $h5_pay_version = "1.0";

    /**
     * @var string
     * 支付宝毁掉地址
     */
    public $notify_url;

    public $biz_content;

    /**
     * @var array 
     * 公共请求参数
     */
    public $common_params = [];
    public $practiceParam = [];
    public $request_params;

    public $h5_pay_request_url = "https://openapi.alipay.com/gateway.do";

    public $h5_pay_public_key_path;
    public $h5_pay_private_key_path;
    public $h5_pay_notify_url;

    /**
     * @var Tools Tools
     */
    public $tools;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->tools = new Tools();
        $this->common_params["timestamp"] = date("Y-m-d H:i:s",time());
        $this->common_params["app_id"] = $this->app_id;
        $this->common_params["charset"] = $this->charset;
        $this->common_params["sign_type"] = $this->sign_type;

    }


    /**
     * @param $body 对一笔交易的具体描述信息。如果是多种商品，请将商品描述字符串累加传给body。
     * @param $subject  商品的标题/交易标题/订单标题/订单关键字等。
     * @param $out_trade_no 商户网站唯一订单号
     * @param $total_amount 订单总金额，单位为元，精确到小数点后两位，取值范围[0.01,100000000]
     * @param $passback_params  公用回传参数，如果请求时传递了该参数，则返回给商户时会回传该参数。支付宝会在异步通知时将该参数原样返回。本参数必须进行UrlEncode之后才可以发送给支付宝
     * @param string $product_code  销售产品码，商家和支付宝签约的产品码。该产品请填写固定值：QUICK_WAP_WAY
     */
    public function h5_pay($body,$subject,$out_trade_no,$total_amount,$passback_params="",$product_code="QUICK_WAP_WAY"){

        $this->common_params["method"] = $this->h5_pay_method;
        $this->common_params["version"] = $this->h5_pay_version;
        $this->common_params["notify_url"] = $this->h5_pay_notify_url;
        $this->practiceParam = [
            "subject"=>$subject,
            "out_trade_no"=>$out_trade_no,
            "total_amount"=>$total_amount,
            "product_code"=>$product_code
        ];

        if(trim($body)!=''){
            $this->practiceParam['body']=$body;
        }
        $this->common_params["biz_content"] = json_encode($this->practiceParam,JSON_UNESCAPED_UNICODE);
        $para_filter=$this->tools->paraFilterNew($this->common_params);
        $sort=$this->tools->argSort($para_filter);
        $preSignStr=$this->tools->createLinkstring($sort);
        $this->common_params['sign'] = $this->tools->rsaSign($preSignStr, $this->h5_pay_private_key_path);
        $request_url = $this->h5_pay_request_url."?".$preSignStr."&sign=".urlencode($this->common_params["sign"]);
        return $request_url;

    }



}
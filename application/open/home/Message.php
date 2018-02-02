<?php
/**
 * Created by PhpStorm.
 * User: 15390
 * Date: 2017/9/5
 * Time: 11:01
 */

namespace app\open\home;

use think\Db;
use think\Controller;

class Message extends Controller
{

    /**
     * 验证码
     * 参数  ：
     *      mobile 手机号
     */
    public function send_message()
    {
        $data = input();
        $list =  Db::name('phone')->field('name,phone')->where('is_open = 1')->select();
        $data1['name'] = '开奖异常短信发送';
        $data1['create_time'] = time();
        $code = $data['msg'];
        $time = date('Y/m/d H:i:s', time());
        if(!empty($list)){
            foreach($list as $k => $v){
                $chars = "/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$|17[0-9]{1}[0-9]{8}$/";
                if (preg_match($chars, $v['phone'])) {
                    $appkey = "23354381";
                    $secret = "bc984175691b66ce317a9618eef32092";
                    vendor('alidayu.TopSdk');
                    $c = new \TopClient;
                    $c->appkey = $appkey;
                    $c->secretKey = $secret;
                    $c->format = "json";
                    $req = new \AlibabaAliqinFcSmsNumSendRequest;
                    $req->setSmsType("normal");
                    $req->setSmsFreeSignName("易客网络");
                    $req->setSmsParam("{'name':'" . $code . "','time':'" . $time . "'}");
                    $req->setRecNum("{$v['phone']}");
                    $req->setSmsTemplateCode("SMS_124430011");
                    $resp = $c->execute($req);
                    if (isset($resp->result)) {
                        if ($resp->result->success) {
                            $data1['is_ok'] = 1;
                            $data1['reason'] = $data['msg'].'开奖失败';
                        } else {
                            $data1['is_ok'] = 0;
                            $da = $this->verification_code($resp);
                            $data1['reason'] = $da['Error_Description'];
                        };
                    } else {
                        $data1['is_ok'] = 0;
                        $da = $this->verification_code($resp);
                        $data1['reason'] = $da['Error_Description'];
                    }
                } else {
                    $data1['is_ok'] = 0;
                    $data1['reason'] = $v['name'].'手机号错误';
                }
                Db::name('log')->insert($data1);
            }
        }else {
            $data1['is_ok'] = 0;
            $data1['reason'] = '无可用手机号';
            Db::name('log')->insert($data1);
        }
    }

    /**
     * 验证码返回错误信息
     * @param $data
     * @return array
     */
    private static function verification_code($data)
    {
        switch ($data->sub_code) {
            case 'isv.OUT_OF_SERVICE':
                $Error_Description = '业务停机';
                $solution          = '登陆www.alidayu.com充值';
                break;
            case 'isv.PRODUCT_UNSUBSCRIBE':
                $Error_Description = '产品服务未开通';
                $solution          = '登陆www.alidayu.com开通相应的产品服务';
                break;
            case 'isv.ACCOUNT_NOT_EXISTS':
                $Error_Description = '账户信息不存在';
                $solution          = '登陆www.alidayu.com完成入驻';
                break;
            case 'isv.ACCOUNT_ABNORMAL':
                $Error_Description = '账户信息异常';
                $solution          = '联系技术支持';
                break;
            case 'isv.SMS_TEMPLATE_ILLEGAL':
                $Error_Description = '模板不合法';
                $solution          = '登陆www.alidayu.com查询审核通过短信模板使用';
                break;
            case 'isv.SMS_SIGNATURE_ILLEGAL':
                $Error_Description = '签名不合法';
                $solution          = '登陆www.alidayu.com查询审核通过的签名使用';
                break;
            case 'isv.MOBILE_NUMBER_ILLEGAL':
                $Error_Description = '手机号码格式错误';
                $solution          = '使用合法的手机号码';
                break;
            case 'isv.MOBILE_COUNT_OVER_LIMIT':
                $Error_Description = '手机号码数量超过限制';
                $solution          = '批量发送，手机号码以英文逗号分隔，不超过200个号码';
                break;
            case 'isv.TEMPLATE_MISSING_PARAMETERS':
                $Error_Description = '短信模板变量缺少参数';
                $solution          = '确认短信模板中变量个数，变量名，检查传参是否遗漏';
                break;
            case 'isv.INVALID_PARAMETERS':
                $Error_Description = '参数异常';
                $solution          = '检查参数是否合法';
                break;
            case 'isv.BUSINESS_LIMIT_CONTROL':
                $Error_Description = '触发业务流控限制';
                $solution          = '短信验证码，使用同一个签名，对同一个手机号码发送短信验证码，支持1条/分钟，5条/小时，10条/天。一个手机号码通过阿里大于平台只能收到40条/天。 短信通知，使用同一签名、同一模板，对同一手机号发送短信通知，允许每天50条（自然日）。';
                break;
            case 'isv.INVALID_JSON_PARAM':
                $Error_Description = 'JSON参数不合法';
                $solution          = 'JSON参数接受字符串值。例如{"code":"123456"}，不接收{"code":123456}';
                break;
            case 'isv.SYSTEM_ERROR':
                $Error_Description = '-';
                $solution          = '-';
                break;
            case 'isv.BLACK_KEY_CONTROL_LIMIT':
                $Error_Description = '模板变量中存在黑名单关键字。如：阿里大鱼';
                $solution          = '黑名单关键字禁止在模板变量中使用，若业务确实需要使用，建议将关键字放到模板中，进行审核。';
                break;
            case 'isv.PARAM_NOT_SUPPORT_URL':
                $Error_Description = '不支持url为变量';
                $solution          = '域名和ip请固化到模板申请中';
                break;
            case 'isv.PARAM_LENGTH_LIMIT':
                $Error_Description = '变量长度受限';
                $solution          = '变量长度受限 请尽量固化变量中固定部分';
                break;
            case 'isv.AMOUNT_NOT_ENOUGH':
                $Error_Description = '余额不足';
                $solution          = '因余额不足未能发送成功，请登录管理中心充值后重新发送';
                break;
            default:
                break;
        }
        return [
            'Error_Description' => $Error_Description,
            'solution'          => $solution
        ];
    }
}
<?php

namespace Liujinyong\LaravelDingding\Handler\robot;

use AllowDynamicProperties;
use GuzzleHttp\Exception\GuzzleException;
use Liujinyong\LaravelDingding\Exceptions\ParamMissingException;
use Liujinyong\LaravelDingding\Exceptions\SystemWrongException;

#[AllowDynamicProperties]
class RobotController
{
    protected $messageUrl = [
        /**
         * 自定义机器人发送消息
         * https://open.dingtalk.com/document/development/custom-robots-send-group-messages
         */
        "robot_send" => "https://oapi.dingtalk.com/robot/send",
    ];

    public function __construct($config = [])
    {
        //属性化参数
        $this->config     = $config;
        $this->httpClient = new \GuzzleHttp\Client();
        $this->timeStamp  = round(microtime(true) * 1000);
    }

    /**
     * @param $type
     * @param $header
     * @param $body
     * @param $sign
     * @return mixed
     * @throws GuzzleException
     * @throws ParamMissingException
     * @throws SystemWrongException
     */
    public function send($type, $header = [], $body = [], $sign = false)
    {
        if (!in_array($type, array_keys($this->messageUrl))) {
            throw new SystemWrongException('不存在的机器人类型');
        }

        if ($header == [] || $body == []) {
            throw new ParamMissingException("请求头/请求体缺失了哦");
        }
        try {
            if ($sign) {
                $header = array_merge($header, ['sign' => $this->getRobotSendSign(), 'timestamp' => $this->timeStamp]);
            }
            $queryStr = http_build_query($header);
            $url = $this->messageUrl[$type] . "?" . $queryStr;

            $res = $this->httpClient->post($url, ['json' => $body]);
        } catch (\Exception $e) {
            throw new SystemWrongException($e->getMessage(), $e->getCode());
        }
        $res = json_decode($res->getBody()->getContents(), true);

        if ($res['errcode'] != 0) {
            throw new SystemWrongException($res['errmsg'], $res['errcode']);
        }

        return $res;
    }

    /**
     * @return string
     * @throws SystemWrongException
     * 自定义机器人生成sign
     */
    public function getRobotSendSign()
    {
        try {
            $timestamp = $this->timeStamp; // 获取毫秒时间戳
            $secret = $this->config['secret'];
            $stringToSign = $timestamp . "\n" . $secret;
            // 计算 HMAC-SHA256 签名
            $signData = hash_hmac('sha256', $stringToSign, $secret, true);
            // Base64 编码
            $base64Sign = base64_encode($signData);
            // URL 编码
            $sign = urlencode($base64Sign);
        } catch (\Exception $e) {
            throw new SystemWrongException($e->getMessage());
        }
        return $sign;
    }

}
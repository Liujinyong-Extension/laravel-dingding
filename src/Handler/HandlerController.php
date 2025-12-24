<?php

namespace Liujinyong\LaravelDingding\Handler;

use Liujinyong\LaravelDingding\Exceptions\ParamMissingException;
use Liujinyong\LaravelDingding\Exceptions\SystemWrongException;

class HandlerController
{
    public $configInstance = null;

    public $httpClient = null;

    public $accessToken = null;

    public $expires = null;

    private $gettokenUrl = 'https://oapi.dingtalk.com/gettoken';

    public function __construct($ClientId, $ClientSecret, $AgentId)
    {

        if (empty($ClientId) || empty($ClientSecret) || empty($AgentId)) {
            throw new ParamMissingException("参数缺失了哦");
        }
        $this->configInstance = ConfigController::getInstance($ClientId, $ClientSecret, $AgentId);
        $this->httpClient     = new \GuzzleHttp\Client();

    }

    public function getAccessToken()
    {


        if (  time() > $this->expires) {
            $params   = [
                'appkey'    => $this->configInstance->getAttribute('client_id'),
                'appsecret' => $this->configInstance->getAttribute('client_secret')
            ];
            $queryStr = http_build_query($params);
            $response = $this->httpClient->get($this->gettokenUrl . '?' . $queryStr);
            $response = json_decode($response->getBody()->getContents(), true);

            if (isset($response['errcode']) && $response['errcode'] == 0) {
                $this->accessToken = $response['access_token'];
                $this->expires     = time() + $response['expires_in'] - 10;
            } else {
                throw new SystemWrongException($response['errmsg'], $response['errcode']);
            }

        }
        return $this->accessToken;
    }
}
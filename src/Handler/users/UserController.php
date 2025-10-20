<?php

namespace Liujinyong\LaravelDingding\Handler\users;

use GuzzleHttp\Exception\GuzzleException;
use Liujinyong\LaravelDingding\Exceptions\SystemWrongException;
use Liujinyong\LaravelDingding\Handler\HandlerController;

class UserController extends HandlerController
{

    /**
     * @var string 根据手机号获取用户id的url
     * https://open.dingtalk.com/document/development/query-users-by-phone-number
     */
    private $getbymoboleUrl = 'https://oapi.dingtalk.com/topapi/v2/user/getbymobile';

    /**
     * @var string 根据用户id获取用户详细信息
     * https://open.dingtalk.com/document/development/query-user-details
     */
    private $getuerinfoUrl = 'https://oapi.dingtalk.com/topapi/v2/user/get';

    /**
     * 获取用户的userid
     * 权限【根据手机号获取成员基本信息权限】
     * @param $mobile string
     * @return string|SystemWrongException
     * @throws SystemWrongException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUserIdByMobile($mobile): string|SystemWrongException|GuzzleException
    {

        $token = $this->getAccessToken();


        try {
            $res = $this->httpClient->post($this->getbymoboleUrl . '?access_token=' . $token, ['json' => ["mobile" => $mobile]]);
        } catch (\Exception $e) {
            throw new SystemWrongException($e->getMessage(), $e->getCode());
        }
        $res = json_decode($res->getBody()->getContents(), true);

        if ($res['errcode'] != 0) {
            throw new SystemWrongException($res['errmsg'], $res['errcode']);
        }
        return $res['result']['userid'];

    }

    /**
     * 根据用户ID获取用户信息
     * 权限【成员信息读权限】
     * @param $userId string 用户ID
     * @param $language string 语言
     * @return array|SystemWrongException|GuzzleException
     * @throws GuzzleException
     * @throws SystemWrongException
     */
    public function getUserInfo($userId, $language = 'zh_CN'): array|SystemWrongException|GuzzleException
    {
        $token = $this->getAccessToken();
        try {
            $res = $this->httpClient->post($this->getuerinfoUrl . '?access_token=' . $token, ['json' => ["userid" => $userId, 'language' => $language]]);
        } catch (\Exception $e) {
            throw new SystemWrongException($e->getMessage(), $e->getCode());
        }
        $res = json_decode($res->getBody()->getContents(), true);


        if ($res['errcode'] != 0) {
            throw new SystemWrongException($res['errmsg'], $res['errcode']);
        }
        return $res['result'];
    }

}
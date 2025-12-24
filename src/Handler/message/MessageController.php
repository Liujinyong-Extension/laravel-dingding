<?php

namespace Liujinyong\LaravelDingding\Handler\message;

use GuzzleHttp\Exception\GuzzleException;
use Liujinyong\LaravelDingding\Exceptions\ParamMissingException;
use Liujinyong\LaravelDingding\Exceptions\SystemWrongException;
use Liujinyong\LaravelDingding\Handler\HandlerController;

class MessageController extends HandlerController
{
    /**
     * @var string 发送工作通知
     * https://open.dingtalk.com/document/development/asynchronous-sending-of-enterprise-session-messages
     */
    private $worknotificationUrl = 'https://oapi.dingtalk.com/topapi/message/corpconversation/asyncsend_v2';

    /**
     * @var string 获取工作通知的结果
     * https://open.dingtalk.com/document/development/gets-the-result-of-sending-messages-asynchronously-to-the-enterprise
     */
    private $getnotificationresultUrl = 'https://oapi.dingtalk.com/topapi/message/corpconversation/getsendresult';


    /**
     *  发送工作消息通知
     * 权限【无需申请】
     * @param $msg string 发送消息类型
     * @param $userIdList string 用户列
     * @param $deptIdList string 部门列
     * @param $toAllUser boolean 是否发送全员
     * @return array|ParamMissingException|SystemWrongException|GuzzleException
     * @throws GuzzleException
     * @throws ParamMissingException
     * @throws SystemWrongException
     */
    public function workNotification($msg, $userIdList = "", $deptIdList = "", $toAllUser = false): array|ParamMissingException|SystemWrongException|GuzzleException
    {
        $token = $this->getAccessToken();
        $jsonData = [];
        if (!$toAllUser && $userIdList == "" && $deptIdList == "") {
            throw new ParamMissingException("userid_list、dept_id_list不能全为空");
        }
        if (!$toAllUser) {
            $jsonData['to_all_user'] = $toAllUser;
        }
        if ($userIdList != "") {
            $jsonData['userid_list'] = $userIdList;
        }
        if ($deptIdList != "") {
            $jsonData['dept_id_list'] = $deptIdList;
        }
        $jsonData['msg'] = $msg;
        $jsonData['agent_id'] = $this->configInstance->getAttribute("agent_id");
        try {
            $res = $this->httpClient->post($this->worknotificationUrl . '?access_token=' . $token, ['json' => $jsonData]);
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
     * @param $taskId
     * @return mixed
     * @throws GuzzleException
     * @throws ParamMissingException
     * @throws SystemWrongException
     * 获取工作通知消息的发送结果
     */
    public function getNotificationResult($taskId = "")
    {
        $token = $this->getAccessToken();
        $jsonData = [];
        if ( $taskId == "") {
            throw new ParamMissingException("task_id不能全为空");
        }

        $jsonData['agent_id'] = $this->configInstance->getAttribute('agent_id');
        $jsonData['task_id'] = trim($taskId);
        try {
            $res = $this->httpClient->post($this->getnotificationresultUrl . '?access_token=' . $token, ['json' => $jsonData]);
        } catch (\Exception $e) {
            throw new SystemWrongException($e->getMessage(), $e->getCode());
        }
        $res = json_decode($res->getBody()->getContents(), true);
var_dump($res);die();
        if ($res['errcode'] != 0) {
            throw new SystemWrongException($res['errmsg'], $res['errcode']);
        }
        return $res;
    }
}
<?php

namespace Liujinyong\LaravelDingding\Handler;

class ConfigController
{
    /**
     * @var null 实例化对象
     */
    private static $instance = null;
    /**
     * @var string|null appkey
     */
    private $client_id = null;
    /**
     * @var string|null  appsecret
     */
    private $client_secret = null;
    /**
     * @var string|null agentID
     */
    private $agent_id = null;

    /**
     * @param $ClientId string
     * @param $ClientSecret string
     * @param $AgentId string
     */
    private function __construct($ClientId = null, $ClientSecret = null,$AgentId = null){
        $this->client_id = $ClientId;
        $this->client_secret = $ClientSecret;
        $this->agent_id = $AgentId;
    }

    /**
     * @param $ClientId
     * @param $ClientSecret
     * @param $AgentId
     * @param $residentMemory
     * @return self|null
     */
    public static function getInstance($ClientId, $ClientSecret,$AgentId,$residentMemory = false){
        //是否常驻内存判断 不常驻内存返回新的实例
        if ($residentMemory == false){

            return new self($ClientId, $ClientSecret,$AgentId);
        }

        if (self::$instance == null) {
            self::$instance =  new self($ClientId, $ClientSecret,$AgentId);
        }
        return self::$instance;
    }

    /**
     * @param $attribute string 获取属性
     * @return mixed
     */
    public function getAttribute($attribute){
        return $this->$attribute;
    }
    private function __clone() {}


}
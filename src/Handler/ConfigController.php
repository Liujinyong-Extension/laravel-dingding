<?php

namespace Liujinyong\LaravelDingding\Handler;

use Liujinyong\LaravelDingding\Exceptions\ParamMissingException;

class ConfigController
{
    private static $instance = null;

    private $client_id = null;
    private $client_secret = null;
    private $agent_id = null;
    private function __construct($ClientId = null, $ClientSecret = null,$AgentId = null){

        if(!$ClientId || !$ClientSecret || !$AgentId){
            return new ParamMissingException("参数缺失了哦");
        }

        $this->client_id = $ClientId;
        $this->client_secret = $ClientSecret;
        $this->agent_id = $AgentId;
    }
    public static function getInstance($ClientId, $ClientSecret,$AgentId){
        if (self::$instance == null) {
            self::$instance =  new self($ClientId, $ClientSecret,$AgentId);
        }
        return self::$instance;
    }
    public function getAttribute($attribute){
        return $this->$attribute;
    }
    private function __clone() {}
    private function __wakeup() {}


}
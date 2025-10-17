<?php

namespace Liujinyong\LaravelDingding\Handler;

class HandlerController
{
    public $configInstance = null;
    public function __construct($ClientId, $ClientSecret,$AgentId){
        $this->configInstance = ConfigController::getInstance($ClientId, $ClientSecret,$AgentId);
    }
    public function getAccessToken(){

    }
}
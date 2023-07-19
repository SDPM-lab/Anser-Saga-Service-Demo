<?php

namespace App\Controllers;

use App\Anser\Orchestrators\CreateOrder;
use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use SDPMlab\Anser\Orchestration\Saga\Restarter\Restarter;
use SDPMlab\Anser\Orchestration\Saga\Cache\CacheFactory;

class OrderRestarter extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        CacheFactory::initCacheDriver('redis', 'tcp://10.1.1.6:6379');
    }

    public function restartCreateOrderOrchestratorByServerName()
    {
        $userOrchRestarter = new Restarter();

        $result = $userOrchRestarter->reStartOrchestratorsByServer(CreateOrder::class, 'Anser_1');

        return $this->respond([
            "result" => $result
        ]);
    }

    public function restartCreateOrderOrchestratorByClassName()
    {
        $userOrchRestarter = new Restarter();

        $result = $userOrchRestarter->reStartOrchestratorsByClass(CreateOrder::class);

        return $this->respond([
            "result" => $result
        ]);
    }

    public function restartCreateOrderOrchestratorByServerNameAndNeedRestart()
    {
        $userOrchRestarter = new Restarter();

        $result = $userOrchRestarter->reStartOrchestratorsByServer(CreateOrder::class, 'Anser_1', true);

        return $this->respond([
            "result" => $result
        ]);
    }

    public function restartCreateOrderOrchestratorByClassNameAndNeedRestart()
    {
        $userOrchRestarter = new Restarter();

        $result = $userOrchRestarter->reStartOrchestratorsByClass(CreateOrder::class, true);

        return $this->respond([
            "result" => $result
        ]);
    }
}

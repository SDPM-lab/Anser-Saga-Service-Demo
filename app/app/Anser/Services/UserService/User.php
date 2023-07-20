<?php

namespace App\Anser\Services\UserService;

use SDPMlab\Anser\Service\SimpleService;
use SDPMlab\Anser\Service\Action;
use SDPMlab\Anser\Exception\ActionException;
use Psr\Http\Message\ResponseInterface;
use SDPMlab\Anser\Service\ActionInterface;

class User extends SimpleService
{

    protected $serviceName = "user_service";
    protected $retry      = 0;
    protected $retryDelay = 0.2;
    protected $timeout    = 6000.0;

    /**
     * 使用者驗證 Action
     * @param integer $userKey
     *
     * @return ActionInterface $action
     */
    public function userValidation(int $userKey): ActionInterface 
    {
        $action = $this->getAction("GET", "/api/v1/wallet");
        $action->addOption("headers", [
            "X-User-key" => $userKey
        ])
        ->doneHandler(function (
            ResponseInterface $response,
            Action $action
        ){
            $resBody = $response->getBody()->getContents();
            $data    = json_decode($resBody, true);
            $action->setMeaningData($data["data"]);
        })
        ->failHandler(function (
            ActionException $e
        ){
            $errorResult = $e->getResponse()->getBody();
            $data = json_decode($errorResult, true);
            if ($e->isServerError()) {
                log_message("error", $e->getMessage());
                $e->getAction()->setMeaningData([]);
            }

            if ($e->isClientError()) {
                $errorResult = $errorResult->getContents();
                $data = json_decode($errorResult, true);
                log_message("notice", $e->getMessage());
                $e->getAction()->setMeaningData([]);
            }

            if ($e->isConnectError()) {
                log_message("critical", $e->getMessage());
                $e->getAction()->setMeaningData([]);
            }
        });
        return $action;
    }

    /**
     * 使用者錢包儲值 Action
     *
     * @param integer $addAmount
     * @param integer $userKey
     * @return ActionInterface $action
     */
    public function topUpUserWallet(
        int $addAmount,
        int $userKey
    ): ActionInterface {
        $action = $this->getAction("POST", "/api/v1/wallet")
            ->addOption("form_params", [
                "addAmount" => $addAmount,
            ])
            ->addOption("headers", [
                "X-User-key" => $userKey
            ])
            ->doneHandler(function (
                ResponseInterface $response,
                Action $action
            ){
                $resBody = $response->getBody()->getContents();
                $data    = json_decode($resBody, true);
                $action->setMeaningData($data);
            })
            ->failHandler(function (
                ActionException $e
            ){
                $errorResult = $e->getResponse()->getBody();
                $data = json_decode($errorResult, true);
                if ($e->isServerError()) {
                    log_message("error", $e->getMessage());
                    $e->getAction()->setMeaningData([]);
                }

                if ($e->isClientError()) {
                    $errorResult = $errorResult->getContents();
                    $data = json_decode($errorResult, true);
                    log_message("notice", $e->getMessage());
                    $e->getAction()->setMeaningData([]);
                }

                if ($e->isConnectError()) {
                    log_message("critical", $e->getMessage());
                    $e->getAction()->setMeaningData([]);
                }
            });
        return $action;
    }

    /**
     * 使用者錢包儲值 Action
     *
     * @param integer $addAmount
     * @param integer $userKey
     * @return ActionInterface $action
     */
    public function chargeOrder(
        string $o_key,
        int $total,
        int $userKey
    ): ActionInterface {
        $action = $this->getAction("POST", "/api/v1/wallet/charge")
            ->addOption("form_params", [
                "total" => $total,
                "o_key" => $o_key,
            ])
            ->addOption("headers", [
                "X-User-key" => $userKey
            ])
            ->doneHandler(function (
                ResponseInterface $response,
                Action $action
            ) {
                $resBody = $response->getBody()->getContents();
                $data    = json_decode($resBody, true);
                $action->setMeaningData($data);
            })
            ->failHandler(function (
                ActionException $e
            ) {
                $errorResult = $e->getResponse()->getBody();
                $data = json_decode($errorResult, true);
                if ($e->isServerError()) {
                    log_message("error", $e->getMessage());
                    $e->getAction()->setMeaningData([]);
                }

                if ($e->isClientError()) {
                    $errorResult = $errorResult->getContents();
                    $data = json_decode($errorResult, true);
                    log_message("notice", $e->getMessage());
                    $e->getAction()->setMeaningData([]);
                }

                if ($e->isConnectError()) {
                    log_message("critical", $e->getMessage());
                    $e->getAction()->setMeaningData([]);
                }
            });
        return $action;
    }

    /**
     * 使用者錢包補償 Action
     *
     * @param integer $addAmount
     * @param integer $userKey
     * @param string $o_key
     * @return ActionInterface $action
     */
    public function walletCompensation(
        int $addAmount,
        int $userKey,
        string $o_key
    ): ActionInterface {
        $action = $this->getAction("POST", "/api/v1/wallet/compensate")
            ->addOption("form_params", [
                "addAmount" => $addAmount,
                "o_key" => $o_key,
            ])
            ->addOption("headers", [
                "X-User-key" => $userKey
            ])
            ->doneHandler(function (
                ResponseInterface $response,
                Action $action
            ) {
                $resBody = $response->getBody()->getContents();
                $data    = json_decode($resBody, true);
                $action->setMeaningData($data);
            })
            ->failHandler(function (
                ActionException $e
            ) {
                $errorResult = $e->getResponse()->getBody();
                $data = json_decode($errorResult, true);
                if ($e->isServerError()) {
                    log_message("error", $e->getMessage());
                    $e->getAction()->setMeaningData([]);
                }

                if ($e->isClientError()) {
                    $errorResult = $errorResult->getContents();
                    $data = json_decode($errorResult, true);
                    log_message("notice", $e->getMessage());
                    $e->getAction()->setMeaningData([]);
                }

                if ($e->isConnectError()) {
                    log_message("critical", $e->getMessage());
                    $e->getAction()->setMeaningData([]);
                }
            });
        return $action;
    }
}

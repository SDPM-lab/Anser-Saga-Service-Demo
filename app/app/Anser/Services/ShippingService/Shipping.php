<?php

namespace App\Anser\Services\ShippingService;

use SDPMlab\Anser\Service\SimpleService;
use SDPMlab\Anser\Service\Action;
use SDPMlab\Anser\Exception\ActionException;
use Psr\Http\Message\ResponseInterface;
use SDPMlab\Anser\Service\ActionInterface;

class Shipping extends SimpleService
{
    protected $serviceName = "shipping_service";
    protected $retry      = 0;
    protected $retryDelay = 0.2;
    protected $timeout    = 6000.0;

    /**
     * 取得使用者運送資訊清單 Action
     *
     * @param integer $userKey
     * @return ActionInterface $action
     */
    public function getShippingList(int $userKey): ActionInterface
    {
        $action = $this->getAction("GET", "/api/v1/shipping");
        $action->addOption("headers", [
            "X-User-key" => $userKey
        ])
        ->doneHandler(function (
            ResponseInterface $response,
            Action $action
        ) {
            $resBody = $response->getBody()->getContents();
            $data    = json_decode($resBody, true);
            $action->setMeaningData($data["data"]);
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
     * 取得使用者單筆運送訂單 Action
     *
     * @param string $orderKey
     * @param int $userKey
     * @return ActionInterface $action
     */
    public function getShipping(string $orderKey, int $userKey): ActionInterface
    {
        $action = $this->getAction("GET", "/api/v1/shipping/{$orderKey}")
            ->addOption("headers", [
                "X-User-key" => $userKey
            ])
            ->doneHandler(function (
                ResponseInterface $response,
                Action $action
            ) {
                $resBody = $response->getBody()->getContents();
                $data = json_decode($resBody, true);
                $action->setMeaningData($data["data"]);
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
     * 新增訂單運送資訊 Action
     *
     * @param string $orderKey
     * @param integer $userKey
     * @return ActionInterface $action
     */
    public function createShipping(
        string $orderKey,
        int $userKey
    ): ActionInterface {
        $action = $this->getAction("POST", "/api/v1/shipping")
            ->addOption("json", [
                "o_key"      => $orderKey
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
     * 取得訂單運送資訊 Action
     *
     * @param string $orderKey
     * @param string $status
     * @return ActionInterface $action
     */
    public function updateShipping(
        string $orderKey,
        string $status,
    ): ActionInterface {
        $action = $this->getAction("PUT", "/api/v1/shipping")
            ->addOption("json", [
                "o_key"  => $orderKey,
                "status" => $status
            ])
            ->addOption("headers", [
                "X-User-key" => 1
            ])
            ->doneHandler(function (
                ResponseInterface $response,
                Action $action
            ) {
                $resBody = $response->getBody()->getContents();
                $data    = json_decode($resBody, true);
                $action->setMeaningData($data["data"]);
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
                    $errorResult = $e->getResponse()->getBody()->getContents();
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
     * 取得刪除訂單運送資訊 Action
     *
     * @param string $orderKey
     * @return ActionInterface $action
     */
    public function deleteShipping(string $orderKey, int $userKey): ActionInterface
    {
        $action = $this->getAction("DELETE", "/api/v1/shipping/{$orderKey}")
            ->addOption("headers", [
                "X-User-key" => $userKey
            ])
            ->doneHandler(function (
                ResponseInterface $response,
                Action $action
            ) {
                $resBody = $response->getBody()->getContents();
                $data = json_decode($resBody, true);
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

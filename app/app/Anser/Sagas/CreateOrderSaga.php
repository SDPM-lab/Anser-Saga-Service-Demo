<?php

namespace App\Anser\Sagas;

use App\Anser\Services\OrderService\Order;
use App\Anser\Services\PaymentService\v2\Payment;
use App\Anser\Services\UserService\User;
use App\Anser\Services\ShippingService\Shipping;
use SDPMlab\Anser\Orchestration\Saga\SimpleSaga;
use App\Anser\Services\ProductService\Inventory;
use SDPMlab\Anser\Service\ConcurrentAction;

class CreateOrderSaga extends SimpleSaga
{
    /**
     * 新增訂單補償
     *
     * @return void
     */
    public function orderCompensation()
    {
        $order    = new Order();
        $orderKey = $this->getOrchestrator()->orderKey;
        $userKey  = $this->getOrchestrator()->userKey;

        $order->deleteOrder($orderKey, $userKey)->do();
    }

    /**
     * 商品庫存補償
     *
     * @return void
     */
    public function productInventoryCompensateion()
    {
        $productInvArr    = $this->getOrchestrator()->productInvArr;
        $concurrentAction = new ConcurrentAction();
        $inventory = new Inventory();
        $orderKey  = $this->getOrchestrator()->orderKey;

        foreach ($productInvArr as $actionName => $productKey) {
            if ($this->getOrchestrator()->getStepAction($actionName)->isSuccess()) {
                $concurrentAction->addAction(
                    $actionName,
                    $inventory->addInventory(
                        $productKey,
                        $orderKey,
                        1,
                        'compensate'
                    )
                );
            }
        }

        $concurrentAction->send();
    }

    /**
     * 付款補償
     *
     * @return void
     */
    public function paymentCompensation()
    {
        // $createPaymentAction = $this->getOrchestrator()->getStepAction('createPayment');
        // $error    = $createPaymentAction->getMeaningData();
        $orderKey = $this->getOrchestrator()->orderKey;
        $userKey  = $this->getOrchestrator()->userKey;

        $payment = new Payment();

        // $total = $this->getOrchestrator()->getStepAction('createOrder')->getMeaningData()['total'];
        $payment->deletePayment($orderKey, $userKey)->do();
    }

    /**
     * 付款補償
     *
     * @return void
     */
    public function walletCompensation()
    {
        // $chargeOrderAction = $this->getOrchestrator()->getStepAction('chargeOrder');
        // $error    = $chargeOrderAction->getMeaningData();
        $orderKey = $this->getOrchestrator()->orderKey;
        $userKey  = $this->getOrchestrator()->userKey;

        $user = new User();

        $total = $this->getOrchestrator()->getStepAction('createOrder')->getMeaningData()['total'];
        $user->walletCompensation($total, $userKey, $orderKey)->do();
    }

    /**
     * 運送補償
     *
     * @return void
     */
    public function shippingCompensation()
    {
        // $createShippingAction = $this->getOrchestrator()->getStepAction('createShipping');
        // $error    = $createShippingAction->getMeaningData();
        $orderKey = $this->getOrchestrator()->orderKey;
        $userKey  = $this->getOrchestrator()->userKey;

        $shipping = new Shipping();

        $shipping->deleteShipping($orderKey, $userKey)->do();
    }
}

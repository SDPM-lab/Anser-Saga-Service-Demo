<?php

namespace App\Anser\Orchestrators;

use SDPMlab\Anser\Orchestration\Orchestrator;
use App\Anser\Sagas\CreateOrderSaga;
use App\Anser\Services\OrderService\Order;
use App\Anser\Services\PaymentService\Payment;
use App\Anser\Services\ProductService\Product;
use App\Anser\Services\ProductService\Inventory;

class CreateOrder extends Orchestrator
{
    /**
     * Order service instance.
     *
     * @var Order
     */
    protected $order;

    /**
     * Payment service instance.
     *
     * @var [type]
     */
    protected $payment;

    /**
     * Product service instance.
     *
     * @var [type]
     */
    protected $product;

    /**
     * Inventory service instance.
     *
     * @var [type]
     */
    protected $inventory;

    /**
     * Order key
     *
     * @var string
     */
    public $orderKey;

    /**
     * Mock user key
     *
     * @var integer
     */
    public $userKey = 1;

    /**
     * 商品庫存名稱陣列
     *
     * @var array<string,string>
     */
    public $productInvArr = [];

    /**
     * product action 名稱
     * @var array<string>
     */
    protected $productActions = [];

    public function __construct()
    {
        $this->order   = new Order();
        $this->payment = new Payment();
        $this->product = new Product();
        $this->inventory = new Inventory();
    }

    protected function definition(array $products = [], int $userKey = 1)
    {
        //初始化所需資訊
        $order = $this->order;
        $this->userKey = $userKey;
        $payment = $this->payment;
        $inventory = $this->inventory;

        $str = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
        str_shuffle($str);
        $randomStr = substr(str_shuffle($str), 0, strlen($str) - 1);

        $productActions = &$this->productActions;
        $this->orderKey = $orderKey =  sha1(serialize($products) . $userKey . uniqid() . $randomStr);

        $step1 = $this->setStep();
        foreach ($products as $key => $productKey) {
            $actionName = "product{$productKey}";
            $productActions[] = $actionName;
            $step1->addAction($actionName, $this->product->getProduct($productKey));
        }

        $this->transStart(CreateOrderSaga::class);

        $this->setStep()
            ->setCompensationMethod('orderCompensation')
            ->addAction("createOrder", function (Orchestrator $runtimeOrch) use ($order, $orderKey, $productActions, $userKey) {
                $products = [];
                foreach ($productActions as $actionName) {
                    $products[] = $runtimeOrch->getStepAction($actionName)->getMeaningData();
                }
                return $order->createOrder($orderKey, 0, $products, $userKey);
            });

        $actionName = "product{$key}";

        $step3 = $this->setStep();

        foreach ($products as $key => $productKey) {
            $actionName = "productInv{$productKey}";

            $step3->setCompensationMethod('productInventoryCompensateion');
            $step3->addAction($actionName, $inventory->reduceInventory($productKey, $orderKey, 1));

            $this->productInvArr[$actionName] = $productKey;
        }

        $this->setStep()
            ->setCompensationMethod('paymentCompensation')
            ->addAction("createPayment", function (Orchestrator $runtimeOrch) use ($payment, $orderKey, $userKey) {
                $total = $runtimeOrch->getStepAction('createOrder')->getMeaningData()['total'];
                return $payment->createPayment($orderKey, $total, $userKey);
            });

        $this->transEnd();
    }

    protected function defineResult()
    {
        if ($this->isSuccess()) {
            $data = [
                "orderKey" => $this->orderKey,
            ];
            return $data;
        }
    }
}

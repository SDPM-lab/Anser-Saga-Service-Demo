<?php

namespace App\Anser\Orchestrators;

use SDPMlab\Anser\Orchestration\Orchestrator;
use App\Anser\Sagas\CreateOrderSaga;
use App\Anser\Services\OrderService\Order;
use App\Anser\Services\UserService\User;
use App\Anser\Services\ShippingService\Shipping;
use App\Anser\Services\PaymentService\V2\Payment;
use App\Anser\Services\ProductService\Product;
use App\Anser\Services\ProductService\Inventory;
use SDPMlab\Anser\Orchestration\OrchestratorInterface;
use SDPMlab\Anser\Orchestration\Saga\Cache\CacheFactory;

class ComplexCreateOrder extends Orchestrator
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
     * @var Payment
     */
    protected $payment;

    /**
     * Product service instance.
     *
     * @var Product
     */
    protected $product;

    /**
     * Inventory service instance.
     *
     * @var Inventory
     */
    protected $inventory;

    /**
     * User service instance.
     *
     * @var User
     */
    protected $user;

    /**
     * Shipping service instance.
     *
     * @var Shipping
     */
    protected $shipping;

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
        $this->user    = new User();
        $this->shipping  = new Shipping();
        $this->inventory = new Inventory();
    }

    protected function definition(array $products = [], int $userKey = 1)
    {
        // CacheFactory::initCacheDriver('redis', 'tcp://' . env("REDIS_IP") . ':' . env("REDIS_PORT"));

        // $this->setServerName("Anser_1");

        // Init the properties.
        $this->userKey = $userKey;
        $inventory     = $this->inventory;

        $str = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";

        str_shuffle($str);

        $randomStr = substr(str_shuffle($str), 0, strlen($str) - 1);

        $productActions = &$this->productActions;

        $this->orderKey = $orderKey =  sha1(
            serialize($products) .
            $userKey .
            uniqid("", true) .
            $randomStr
        );

        $step1 = $this->setStep();

        foreach ($products as $key => $productKey) {
            $actionName = "product{$productKey}";
            $productActions[] = $actionName;
            $step1->addAction($actionName, $this->product->getProduct($productKey));
        }

        $step2 = $this->setStep()
            ->addAction("userValidation", $this->user->userValidation($userKey));

        $this->transStart(CreateOrderSaga::class);

        $step3Closure = static function (
            OrchestratorInterface $runtimeOrch
        ) use (
            $orderKey,
            $productActions,
            $userKey
        ) {
            $products = [];

            foreach ($productActions as $actionName) {
                $products[] = $runtimeOrch->getStepAction($actionName)->getMeaningData();
            }

            return $runtimeOrch->order->createOrder($orderKey, 0, $products, $userKey);
        };

        $step3 = $this->setStep()
            ->setCompensationMethod('orderCompensation')
            ->addAction("createOrder", $step3Closure);

        $step4Clousre = static function (
            OrchestratorInterface $runtimeOrch
        ) use (
            $orderKey,
            $userKey
        ) {
            $total = $runtimeOrch->getStepAction('createOrder')
                ->getMeaningData()['total'];

            return $runtimeOrch->payment->createPayment($orderKey, $total, $userKey);
        };

        $step4 = $this->setStep()
            ->setCompensationMethod('paymentCompensation')
            ->addAction("createPayment", $step4Clousre);

        $step5 = $this->setStep()
            ->setCompensationMethod('shippingCompensation')
            ->addAction("createShipping", $this->shipping->createShipping($orderKey, $userKey));

        $actionName = "product{$key}";

        $step6 = $this->setStep();

        foreach ($products as $key => $productKey) {
            $actionName = "productInv{$productKey}";

            $step6->setCompensationMethod('productInventoryCompensateion');
            $step6->addAction(
                $actionName,
                $inventory->reduceInventory($productKey, $orderKey, 1)
            );

            $this->productInvArr[$actionName] = $productKey;
        }

        $step7Closure = static function (
            OrchestratorInterface $runtimeOrch
        ) use (
            $orderKey,
            $userKey
        ) {
            $total = $runtimeOrch->getStepAction('createOrder')
                                ->getMeaningData()['total'];

            return $runtimeOrch->user->chargeOrder($orderKey, $total, $userKey);
        };

        $step7 = $this->setStep()
            ->setCompensationMethod('walletCompensation')
            ->addAction("chargeOrder", $step7Closure);

        $this->transEnd();
    }

    protected function defineResult()
    {
        $data = [
            "status"    => $this->isSuccess(),
            "orderKey"  => $this->orderKey
        ];

        if ($this->isSuccess() === false) {
            $data["fail"]["isCompensationSuccess"] = $this->isCompensationSuccess();
            $data["fail"]["FailAction"] = $this->getFailActions();
        }

        return $data;
    }
}

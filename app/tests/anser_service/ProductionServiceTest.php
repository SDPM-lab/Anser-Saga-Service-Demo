<?php

require_once APPPATH . 'Anser/ServiceConfig.php';

use App\Anser\Orchestrators\CreateOrder;
use \CodeIgniter\Test\CIUnitTestCase;
use App\Anser\Services\ProductService\Product;

class ProductionServiceTest extends CIUnitTestCase
{
    protected Product $productService;
    protected int $canGetOrderKey    = 3;
    protected int $cannotGetOrderKey = 50000;

    public function setUp(): void
    {
        parent::setUp();
        $this->productService = new Product();
    }

    /**
     * @group test
     */
    // public function testGetProduct()
    // {
    //     $action = $this->productService->getProduct($this->canGetOrderKey);
    //     $data = $action->do()->getMeaningData();
    //     var_dump($data);
    // }

    public function testOrder()
    {
		$createOrder = new CreateOrder();
		$data = $createOrder->build([1,2,3], 1);
        var_dump($data);
    }
}

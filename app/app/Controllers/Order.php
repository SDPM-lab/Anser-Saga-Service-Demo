<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Anser\Orchestrators\CreateOrder;
use App\Anser\Orchestrators\ComplexCreateOrder;

class Order extends BaseController
{
	use ResponseTrait;

	public function createOrder()
	{
		$jsonData  = $this->request->getJSON(true);

		$memberKey = $jsonData["memberKey"];
		$products  = $jsonData["products"];

		$createOrder = new CreateOrder();

		$data = $createOrder->build($products, $memberKey);
		
		return $this->respond($data ?? ["order_key" => $createOrder->orderKey]);
	}

	public function complexCreateOrder()
	{
		$jsonData  = $this->request->getJSON(true);

		$memberKey = $jsonData["memberKey"];
		$products  = $jsonData["products"];

		$createOrder = new ComplexCreateOrder();

		$data = $createOrder->build($products, $memberKey);

		return $this->respond($data ?? ["order_key" => $createOrder->orderKey]);
	}

}

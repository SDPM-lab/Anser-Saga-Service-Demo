<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Anser\Orchestrators\CreateOrder;

class Order extends BaseController
{
	use ResponseTrait;

	public function createOrder()
	{
		$jsonData = $this->request->getJSON(true);
		$memberKey = $jsonData["memberKey"];
		$products = $jsonData["products"];
		$createOrder = new CreateOrder();
		$data = $createOrder->build($products, $memberKey);
		return $this->respond($data ?? ["order_key" => $createOrder->orderKey]);
	}

}

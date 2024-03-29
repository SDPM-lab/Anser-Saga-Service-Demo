<?php

namespace App\Anser;

use SDPMlab\Anser\Service\ServiceList;

ServiceList::addLocalService("order_service", env("ORDER_SERVICE_IP"), env("ORDER_SERVICE_PORT"), false);
ServiceList::addLocalService("product_service", env("PRODUCT_SERVICE_IP"), env("PRODUCT_SERVICE_PORT"), false);
ServiceList::addLocalService("payment_service", env("PAYMENT_SERVICE_IP"), env("PAYMENT_SERVICE_PORT"), false);
ServiceList::addLocalService("user_service", env("USER_SERVICE_IP"), env("USER_SERVICE_PORT"), false);
ServiceList::addLocalService("shipping_service", env("SHIPPING_SERVICE_IP"), env("SHIPPING_SERVICE_PORT"), false);

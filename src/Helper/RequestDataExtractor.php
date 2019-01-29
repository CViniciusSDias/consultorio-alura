<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\Request;

class RequestDataExtractor
{
    public function getFilterAndOrderData(Request $request)
    {
        $queryData = $request->query->all();
        $orderData = array_key_exists('order', $queryData) ? $queryData['order'] : [];
        unset($queryData['order']);

        return [$queryData, $orderData];
    }

    public function getOrderData(Request $request)
    {
        [, $orderData] = $this->getFilterAndOrderData($request);
        return $orderData;
    }
}

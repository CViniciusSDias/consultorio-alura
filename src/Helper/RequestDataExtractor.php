<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\Request;

class RequestDataExtractor
{
    private function getUrlData(Request $request)
    {
        $queryData = $request->query->all();
        $orderData = array_key_exists('sort', $queryData) ? $queryData['sort'] : [];
        unset($queryData['sort']);
        $paginationData = array_key_exists('page', $queryData) ? $queryData['page'] : 1;
        unset($queryData['page']);

        return [$queryData, $orderData, $paginationData];
    }

    public function getFilterData(Request $request)
    {
        [$filterData, , ] = $this->getUrlData($request);
        return $filterData;
    }

    public function getOrderData(Request $request)
    {
        [, $orderData, ] = $this->getUrlData($request);
        return $orderData;
    }

    public function getPaginationData(Request $request)
    {
        [, , $paginationData] = $this->getUrlData($request);
        return $paginationData;
    }
}

<?php

namespace App\Controllers\Api;
use App\Controllers\BaseController;
class Hardware extends BaseController
{
    public function getHardware()
    {
        $client = \Config\Services::curlrequest();

        try {
            $response = $client->get(
                'http://192.168.52.251:9997/api/v1/hardware',
                [
                    'query' => [
                        'assigned_to'   => 437,
                        'assigned_type' => 'App\Models\User',
                        'search'        => '',
                        'sort'          => 'name',
                        'order'         => 'asc',
                        'offset'        => 0,
                        'limit'         => 20,
                    ],
                    'headers' => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNTgzYjk5ZmNhMzgyNWFmM2JlZDNmZTY5Nzc3ZWRiZTNlMzkwMWU4YWEyMDg2OTExY2QzOGEyYWZlZDQ4Y2Y2ZmNmOGM0NjY2MjdiMTE1YjgiLCJpYXQiOjE3ODE1MzY5ODQuMDg0MDczLCJuYmYiOjE3ODE1MzY5ODQuMDg0MDc0LCJleHAiOjI0MTI2ODg5ODQuMDc3Njk2LCJzdWIiOiIxNTQwIiwic2NvcGVzIjpbXX0.iwzrg5ubVj8telzoRQ2c9Q9ZlNa_RxPZMtYkBDLWA7B18-V3Ds9ckP7Pyy2QF8C4umxI-u54wPnOryCsdgq1SB_DbMP8HvjsZqAJyAsKsG1fmCW9qPumaDmfXxnFdcqKh3iNuzb8FxREYzjamWHbuMgp3pYAsgndpHo3o6A7s58Lo78QcWfdhYfTomKyD4ItALO6FZg8iT8VAaVCpoE9CAvsscAwoxp6ykiJKU9PfigE1jXfIKcoJygVvIn1pRi35N8yZru6HS0y9a2XPyvrWtSsXVz1jrVW_FntlcO9uJk3rmX2SsUp6d6HqGMxPwqdpiEVJBPNcs7NtEKFdFJJLz1P0_Z8HsFRX31IeUEbXK2Xg8AWjK1IodKb7vocN3WfwRuIflxwhpMBBZ0JxG3ovEglgjIdkIs85IJNgi2XNsRx4Xw5tpnfkKjoSn1VTF7xWRoiNndbydNMQrKUQRKQH5L24_cTHxjm2tFxtnPyqK9bVEEp89CYj-IJypuggyRHxJp0SHVhAg3nilCs7tbuUhPGELTsULSwtoKHr-wlkkwTxuByPFLbBgNHEVTDLVafD-xCn8F7oU_2vKfchsylhryVKP8EbJKPaXilOavkub-9fjnQmXOjYE9RGT-VX2R2IjrdqOxH6Ncz2Eua3n_vZwmea0auf1-yrXik18iMbz0'
                    ]
                ]
            );

            $result = json_decode($response->getBody(), true);

            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
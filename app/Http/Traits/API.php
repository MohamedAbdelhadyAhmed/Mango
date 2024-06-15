<?php

namespace App\Http\Traits;

trait Api
{
    function data(array $data = [], string $msg = '', $code = 200)
    {
        $respons = [
            'status' => $code,
            'message' => $msg,
            'data' => $data,

        ];
        return response()->json($respons, $code);
    }
    function success_message(string $msg = '', array $data = [], $code = 200)
    {
        $respons = [
            'status' => $code,
            'message' => $msg,
            'data' => $data,

        ];
        return response()->json($respons, $code);
    }
    function error_message(string $msg = '', array $data = [], $code = 200)
    {
        $respons = [
            'status' => $code,
            'message' => $msg,
            'data' => $data,
        ];
        return response()->json($respons, $code);
    }
}

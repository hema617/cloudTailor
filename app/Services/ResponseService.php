<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
class ResponseService 
{
    protected string $message = '';
    protected int $statusCode = 200;
    protected array|Collection $data = [];
    protected array $errors  = [];


    public function response()
    {
        return response()->json([
            'status' => $this->statusCode == 200 ? true : false,
            'message' => $this->message,
            'error' => $this->errors,
            'data' => $this->data,
        ], $this->statusCode);
    }
    public function success($message, $data = [], $statusCode = 200, $status=true)
    {
        return response()->json([
            'status' => $status,
            'status_code'=>$statusCode,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public function error($message, $statusCode = 400,$data= [])
    {
        return response()->json([
            'status' => false,
            'status_code'=>$statusCode,
            'message' => $message,
            'data' =>$data
        ], $statusCode);
    }
}

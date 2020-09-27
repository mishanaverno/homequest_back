<?php

namespace App\Lib;

use App\Model;
use Exception;
use stdClass;

class APIResponse
{
    const CODE_SUCCESS = 200;
    const CODE_VALUES_NOT_PASSED = 400;
    const CODE_NOT_PERMISSIONED = 402;
    const CODE_NOT_AUTH = 403;
    const CODE_NOT_FOUND = 404;
    const CODE_INVALID_STATE = 405;
    const CODE_INVALID_DATA = 401;
    const CODE_NON_UNIQ_VALUE = 406;

    protected $code;
    protected $msg;
    protected $data;

    /**
     * Undocumented function
     *
     * @param integer $code
     * @return APIResponse
     */
    public static function make(int $code) : APIResponse
    {
        return new self($code);
    }

    public function __construct(int $code)
    {
        $this->code = $code;
    }

    public function setMsg(string $msg) : APIResponse
    {
        $this->msg = $msg;
        return $this;
    }

    public function setData($data) : APIResponse
    {
        if($data instanceof Model){
            $data = $data->toArray();
        }
        $this->data = $data;
        return $this;
    }

    public function complete($data = null){
        if ($data) $this->setData($data);
        header('Content-Type: application/json');
        echo json_encode($this->shalowCopy());
    }

    public static function found(Model $model){
        return self::make(self::CODE_SUCCESS)->setMsg( class_basename($model) . " element found!")->complete($model);
    }


    public static function fail(Exception $e){
        return self::make((int) $e->getCode())->setMsg($e->getMessage())->complete();
    }

    private function shalowCopy() : stdClass
    {
        $obj = new stdClass;
        foreach($this as $key => $value){
            $obj->{$key} = $value;
        }
        return $obj;
    }

}

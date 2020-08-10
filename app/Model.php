<?php

namespace App;

use App\Lib\APIResponse;
use Exception;
use Illuminate\Support\Facades\DB;
use stdClass;

abstract class Model
{
    protected $id;
    protected $table;
    protected $columns;

    protected function __construct($id = null)
    {
        $this->id = intval($id);
    }

    /**
     * Find by id
     *
     * @param int $id
     * @return Model
     */
    public static function find($id = 0)
    {
        try{
            $model = static::make($id);
            $result = (array) DB::table($model->table)->find($id);
        }catch (Exception $e){
            throw new Exception($e->getMessage(), $e->getCode());
        }
        if ($result) {
            $model->setBulk($result);
            return $model;
        } else { 
            throw new Exception(static::class." : Element not found", 404);
        }
    }

    /**
     * Undocumented function
     *
     * @param int $id
     * @return Model
     */
    public static function make($id = null)
    {
        return new static($id);
    }

    /**
     * Bulk set values
     *
     * @param array $values
     * @return Model
     */
    public function setBulk(array $values) : Model
    {
        if(is_array($values)){
            foreach($values as $column => $value){
                $this->set($column, $value);
            }
        }
        return $this;
    }

    /**
     * Set single value if column exists in $columns
     *
     * @param string $column
     * @param string $value
     * @return void
     */
    public function set(string $column, $value)
    {
        if (array_key_exists($column, $this->columns) && $value){
            $this->{$column} = $this->getTyped($column,$value);
        }
        return $this;
    }

    public function save()
    {
        $values = [];
        foreach($this->columns as $column => $type){
            if (isset($this->{$column})){
                $values[$column] = $this->getTyped($column,$this->{$column});
            }
        }
        if(empty($values)) throw new Exception("Empty values", APIResponse::CODE_VALUES_NOT_PASSED);

        $values['updated_at'] = date('Y-m-d H:i:s');
        try{
            if($this->isFound()){
                DB::table($this->table)->where('id', $this->id)->update($values);
            } else {
                $values['created_at'] = date('Y-m-d H:i:s');
                $this->id = DB::table($this->table)->insertGetId($values);
            }
        }catch (Exception $e){
            throw new Exception($e->getMessage(), $e->getCode());
        }
        return $this;
    }

    public function isFound() : Bool
    {
        return $this->id !== 0;
    }

    public function getId()
    {
        return $this->id;
    }

    private function getTyped($column, $value){
        switch ($this->columns[$column]) {
            case 'int':
                $value = intval($value);
                break;
        }
        return $value;
    }
    public function __call($name, $arguments)
    {
        
    }
}

<?php

namespace App;

use App\Lib\APIResponse;
use Exception;
use Illuminate\Support\Facades\DB;
use stdClass;

abstract class Model
{
    const COLUMN_VIRTUAL = 'virtual';
    const COLUMN_IMMUTABLE = 'immutable';
    const COLUMN_SIMPLE = '';

    protected $id;
    protected $table;
    protected $columns = [];
    protected $hidden = [];

    protected function __construct($id = null)
    {
        $this->id = intval($id);
    }

    /**
     * Find by id
     *
     * @param int $id
     * @return 
     */
    public static function find($value = 0, $column = 'id')
    {
        try{
            $model = static::make(0);
            $result = $model->_get($value, $column);
        }catch (Exception $e){
            throw $e;
        }
        if ($result) {
            $model->id = $result['id'];
            $model->setFromDB($result);
            return $model;
        } else { 
            throw new Exception(static::class." : Element not found", APIResponse::CODE_NOT_FOUND);
        }
    }

    protected function _get($value, $column) : array
    {   
        try{
            return (array) DB::table($this->table)->where($column, $value)->get()->first();
        } catch (Exception $e){
            throw $e;
        }
    }

    /**
     * Undocumented function
     *
     * @param int $id
     * @return 
     */
    public static function make($id = null)
    {
        return new static($id);
    }

    protected function setFromDB(array $values) 
    {
        if(is_array($values)){
            foreach($values as $column => $value){
                if ((array_key_exists($column, $this->columns) || $column == 'id') && $value){
                    $this->{$column} = $value;
                }
            }
        }
        return $this;
    }
    /**
     * Bulk set values
     *
     * @param array $values
     * @return 
     */
    public function setBulk(array $values)
    {
        if(is_array($values)){
            foreach($values as $column => $value){
                if ($this->isImmutableColumn($column)) continue;
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
        if ($value && array_key_exists($column, $this->columns)){
            $this->{$column} = $value;
        }
        return $this;
    }

    public function save()
    {
        $values = [];
        foreach($this->columns as $column => $type){
            if (isset($this->{$column})){
                if ($this->isVirtualColumn($column)) continue;
                $values[$column] = $this->{$column};
            }
        }
        $values = $this->_morph($values);
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
            throw $e;
        }
        return $this;
    }
    protected function _morph($values){
        return $values;
    }
    public function isFound() : Bool
    {
        return $this->id !== 0;
    }

    public function getId()
    {
        return $this->id;
    }
    private function isVirtualColumn($column){
        return isset($this->columns[$column]) && $this->columns[$column] == Model::COLUMN_VIRTUAL;
    }
    private function isImmutableColumn($column){
        return isset($this->columns[$column]) && $this->columns[$column] == Model::COLUMN_IMMUTABLE;
    }
    
    public function toArray() : array
    {
        $array = [];
        foreach ($this as $column => $value){
            if(!in_array($column, ['columns', 'table', 'hidden']) && !in_array($column, $this->hidden)) $array[$column] = $value;
        }
        return $array;
    }

}

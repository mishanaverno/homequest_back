<?php

namespace App;

use Exception;
use Illuminate\Support\Facades\DB;

class Hero extends Model
{
    protected $table = 'hero';
    protected $columns = [
        'name' => 'string',
        'login' => 'string',
        'avatar' => 'string'
    ];

    /**
     * save hero 
     *
     * @param int $id
     * @return Hero
     */
    public function saveToGang(int $id) : Hero
    {   
        try{
            DB::beginTransaction();
            $this->save();
            DB::table('gang_hero')->insert([
                'gang_id' => $id,
                'hero_id' => $this->id
            ]);
            DB::commit();
        }catch (Exception $e){
            DB::rollBack();
            throw new Exception($e->getMessage(), $e->getCode());
        }
        return $this;
    }
}

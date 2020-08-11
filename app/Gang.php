<?php

namespace App;

use App\Lib\APIResponse;
use Exception;
use Illuminate\Support\Facades\DB;

class Gang extends Model
{
    protected $table = 'gang';
    protected $columns = [
        'name' => 'string'
    ];

    public function setCreator($hero_id)
    {
        $this->_joinHero($hero_id, 1);
    }

    public function joinHero($hero_id)
    {
        $this->_joinHero($hero_id);
    }

    private function _joinHero($hero_id, $creator = 0)
    {
        try{
            $exists = (bool) DB::table('gang_hero')
                ->where('gang_id', $this->id)
                ->where('hero_id', $hero_id)
                ->first();
            if ($exists) throw new Exception("Hero already joined", APIResponse::CODE_INVALID_DATA);
            DB::table('gang_hero')->insert([
                'gang_id' => $this->id,
                'hero_id' => $hero_id,
                'creator' => $creator
            ]);
        } catch (Exception $e){
            throw $e;
        }
    }
}

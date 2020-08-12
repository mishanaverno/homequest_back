<?php

namespace App;

use App\Lib\APIResponse;
use Exception;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\AssignOp\Mod;

class Gang extends Model
{
    protected $table = 'gang';
    protected $columns = [
        'name' => Model::COLUMN_SIMPLE,
        'creator' => Model::COLUMN_IMMUTABLE
    ];

    public function setCreator($hero_id)
    {
        $this->creator = $hero_id;
    }

    public function joinHero($hero_id)
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
            ]);
        } catch (Exception $e){
            throw $e;
        }
    }

    public function createInvite() : int
    {
        $code = random_int(100000,999999);
        DB::table('invite')->insert([
            'gang_id' => $this->id,
            'code' => $code
        ]);
        return $code;
    }
    public static function findByInviteCode($code) : Gang
    {
        $model = Gang::make();
        $result = (array) DB::table('invite')
            ->where('code', $code)
            ->leftJoin('gang','gang.id','invite.gang_id')
            ->get(['gang.*'])->first();
        if (empty($result)) throw new Exception("Gang : element no found", APIResponse::CODE_NOT_FOUND);
        $model->setFromDB($result);
        DB::table('invite')->where('code', $code)->delete();
        return $model;
    }
}

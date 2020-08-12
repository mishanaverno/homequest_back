<?php

namespace App;

use App\Lib\APIResponse;
use Exception;
use Illuminate\Support\Facades\DB;

class Hero extends Model
{
    const TOKEN_LENGTH = 60;

    protected $table = 'hero';
    protected $columns = [
        'name' => Model::COLUMN_SIMPLE,
        'login' => Model::COLUMN_SIMPLE,
        'avatar' => Model::COLUMN_SIMPLE,
        'gang' => Model::COLUMN_VIRTUAL,
        'password' => Model::COLUMN_IMMUTABLE,
        'api_token' => Model::COLUMN_IMMUTABLE,
        'avaliable_quests' => Model::COLUMN_VIRTUAL,
        'created_quests' => Model::COLUMN_VIRTUAL,
        'gangs' => Model::COLUMN_VIRTUAL,
        'style_points' => Model::COLUMN_VIRTUAL
    ];

    protected $hidden = [
        'password'
    ];

    protected function _get($value, $column): array
    {
        try{
            
            $result = (array) DB::table('hero')
                ->where("hero.{$column}", $value)
                ->get(['hero.*'])
                ->first();

            $result['style_points'] =  DB::table('quest')
                ->selectRaw('SUM(reward) as style')
                ->where('quest.state','complete')
                ->where('quest.performer_id', $result['id'])
                ->pluck('style')->first();

            $result['gangs'] = DB::table('gang_hero')
                ->leftJoin('gang','gang_hero.gang_id','gang.id')
                ->where('gang_hero.hero_id', $result['id'])
                ->get(['gang.id','gang.name'])->all();
            return $result;

        } catch (Exception $e){
            throw $e;
        }
    }
    public static function findByApiToken($apiToken) : Hero
    {
            return self::find($apiToken, 'api_token');
    }

    public static function findByLogin($login) : Hero
    {
        return self::find($login, 'login');
    }
    
    public function setPassword($password) : Hero
    {
        if($password){
            $this->set('password', password_hash($password, PASSWORD_DEFAULT));
        }
        return $this;
    }

    public function generateApiToken() : Hero
    {
        $retries = 100;
        do {
            $token = substr(md5(uniqid($this->login, true)), 0, Hero::TOKEN_LENGTH);
            $retries--;
        } while ($this->checkToken($token) && $retries > 0);
        $this->set('api_token', $token);
        return $this;
    }

    private function checkToken($token){
        return (bool) DB::table($this->table)->where('api_token',$token)->first();
    }
    public function getQuests() : Hero
    {
        try{
            $res = DB::table('quest')
                ->whereIn('quest.customer_id',function($query){
                    $query->select('gang_hero.hero_id')
                        ->from('gang_hero')
                        ->whereIn('gang_hero.gang_id', function($subquery){
                            $subquery->select('gang_hero.gang_id')
                                ->from('gang_hero')
                                ->where('gang_hero.hero_id', $this->id);
                        });
                })
                ->leftJoin('hero as customer', 'customer.id','quest.customer_id')
                ->leftJoin('hero as performer', 'performer.id','quest.performer_id')
                ->get([
                    
                    'quest.*',
                    'customer.id as customer_id',
                    'customer.name as customer_name',
                    'customer.avatar as customer_avatar',
                    'performer.id as performer_id',
                    'performer.name as performer_name',
                    'performer.avatar as performer_avatar',
                    
                ])
                ->all();
            $performer = [];
            $customer = [];
            foreach($res as $quest){
                if ($quest->customer_id == $this->id){
                    $customer[] = $quest;
                } else {
                    $performer[] = $quest;
                }
            }

            $this->avaliable_quests = $performer;
            $this->created_quests = $customer;
        } catch (Exception $e){
            throw $e;
        }
        return $this;
    }

    public function inGang($gang_id) : bool
    {
        foreach($this->gangs as $gang){
            if($gang->id == $gang_id) return true;
        }
        return false;
    }
}

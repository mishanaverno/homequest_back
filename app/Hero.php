<?php

namespace App;

use App\Lib\APIResponse;
use App\Lib\Avatar\Gravatar;
use Exception;
use Illuminate\Support\Facades\DB;
use stdClass;

class Hero extends Model
{
    const TOKEN_LENGTH = 60;

    protected $table = 'hero';
    protected $avatarGenerator;
    protected $columns = [
        'name' => Model::COLUMN_SIMPLE,
        'login' => Model::COLUMN_SIMPLE,
        'avatar' => Model::COLUMN_IMMUTABLE,
        'gang' => Model::COLUMN_VIRTUAL,
        'password' => Model::COLUMN_IMMUTABLE,
        'api_token' => Model::COLUMN_IMMUTABLE,
        'gangs' => Model::COLUMN_VIRTUAL,
        'style' => Model::COLUMN_IMMUTABLE
    ];

    protected $hidden = [
        'password',
        'login',
        'api_token'
    ];
    public function __construct($id)
    {
        parent::__construct($id);
        $this->avatarGenerator = new Gravatar();
    }
    public function generateAvatar() : Hero
    {
        $this->avatar = $this->avatarGenerator->generate($this->login);
        return $this;
    }
    protected function _getGangs(){

    }
    protected function _get($value, $column): array
    {
        try{
            $result = (array) DB::table('hero')
                ->where("hero.{$column}", $value)
                ->get(['hero.*'])
                ->first();
            $gangs = DB::table('gang_hero')
                ->leftJoin('gang','gang_hero.gang_id','gang.id')
                ->where('gang_hero.hero_id', $result['id'])
                ->get(['gang.id','gang.name', 'gang.completed'])->all();
            foreach($gangs as $gang){
                $gang->reward = Gang::calcBaseReward($gang->completed);
                unset($gang->completed);
            }
            $result['gangs'] = $gangs;
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
    public function getApiToken() : string 
    {
        return $this->api_token;
    }
    private function checkToken($token){
        return (bool) DB::table($this->table)->where('api_token',$token)->first();
    }
    public function getDashboard() : Hero
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
                })->where('quest.state','<>','complete')
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
                    
                ])->sortBy('quest.created_at')
                ->all();
            
            foreach($res as $quest){
                if ($gang = $this->_findGang($quest->gang_id)) {
                    $gang->quests[] = $quest;
                }
            }
        } catch (Exception $e){
            throw $e;
        }
        return $this;
    }
    public function getProfile() : Hero
    {
        try{
            $res = DB::table('quest')
                ->where('quest.performer_id',$this->id)
                ->where('quest.state','complete')
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
                    
                ])->sortBy('quest.created_at')
                ->all();
            
            foreach($res as $quest){
                if ($gang = $this->_findGang($quest->gang_id)) {
                    $gang->quests[] = $quest;
                }
            }
        } catch (Exception $e){
            throw $e;
        }
        return $this;
    }
    private function _findGang($id)
    {
        if (isset($this->gangs) && is_array($this->gangs)){
            foreach ($this->gangs as $gang){
                if ($gang->id === $id) 
                    return $gang;
            }
        }
        return null;
    }

    public function inGang($gang_id) : bool
    {
        foreach($this->gangs as $gang){
            if($gang->id == $gang_id) return true;
        }
        return false;
    }

    public function addStyle(int $style) : Hero
    {
        $this->style += $style;
        return $this;
    }

    public function removeStyle(int $style) : Hero
    {
        if ((int) $this->style < $style) throw new Exception("Not enought style", APIResponse::CODE_INVALID_DATA);
        $this->style -= $style;
        return $this;
    }
    public function getAccountInformation() : object
    {
        $obj = new stdClass;
        $obj->id = $this->id;
        $obj->login = $this->login;
        $obj->name = $this->name;
        return $obj;
    }
}

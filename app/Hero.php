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
        'api_token' => Model::COLUMN_IMMUTABLE
    ];

    protected $hidden = [
        'password'
    ];

    public static function findByApiToken($apiToken){
        try{
            $hero = self::make();
            $hero->setFromDB((array) DB::table($hero->table)->where('api_token', $apiToken)->first());
            if(!$hero->isFound()) throw new Exception("Hero : element not found", APIResponse::CODE_NOT_FOUND);
            return $hero;
        } catch(Exception $e){
            throw $e;
        }
    }

    public static function findByLogin($login){
        try{
            $hero = self::make();
            $hero->setFromDB((array) DB::table($hero->table)->where('login', $login)->first());
            if(!$hero->isFound()) throw new Exception("Hero : element not found", APIResponse::CODE_NOT_FOUND);
            return $hero;
        } catch(Exception $e){
            throw $e;
        }
    }
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
            DB::table('gang_hero')->insert([
                'gang_id' => $id,
                'hero_id' => $this->id
            ]);
            DB::commit();
        }catch (Exception $e){
            DB::rollBack();
            throw $e;
        }
        return $this;
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

    protected function _get() : array
    {
        try{
            $res =  (array) DB::table('hero')->where('hero.id', $this->id)->leftJoin('gang','gang.id','=','hero.id')->first(['hero.*','gang.name as gang']);
            return $res;
        } catch (Exception $e){
            throw $e;
        }
    }
}

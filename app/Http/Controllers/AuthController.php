<?php

namespace App\Http\Controllers;

use App\Hero;
use App\Lib\APIResponse;
use App\Lib\Token;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(Request $request)
    {   
        try{
            $login = $request->post('login');
            $password = $request->post('password');
            
            $hero = Hero::findByLogin($login);
            if (password_verify($password, $hero->password)){
                $hero->generateApiToken()->save();
                APIResponse::make(APIResponse::CODE_SUCCESS)->setMsg('Login success')->complete($hero->getApiToken());
            } else {
                throw new Exception("Login failed", APIResponse::CODE_NOT_AUTH);
            }
        } catch (Exception $e){
            APIResponse::fail($e);
        }

    }

    public function logout(Request $request)
    {
        try{
            Hero::findByApiToken(Token::get($request))->generateApiToken()->save();
            APIResponse::make(200)->setMsg('Logout complete')->complete();
        } catch (Exception $e){
            APIResponse::fail($e);
        }
    }
    public function info(Request $request)
    {
        try{
            $hero = Hero::findByApiToken(Token::get($request));
            APIResponse::make(200)->setMsg('Hero found complete')->complete($hero->getAccountInformation());
        } catch (Exception $e){
            APIResponse::fail($e);
        }
    }
}

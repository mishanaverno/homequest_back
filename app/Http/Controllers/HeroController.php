<?php

namespace App\Http\Controllers;

use App\Gang;
use App\Hero;
use App\Lib\APIResponse;
use App\Lib\Token;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HeroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $hero = Hero::make()
                ->setBulk($request->except('password'))
                ->generateAvatar()
                ->setPassword($request->get('password'))
                ->generateApiToken()
                ->save();
            APIResponse::make(APIResponse::CODE_SUCCESS)->setMsg('Hero created')->complete($hero->getApiToken());
        }catch (Exception $e){
            APIResponse::fail($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $hero = Hero::find($id);
            APIResponse::found($hero);
        } catch (Exception $e){
            APIResponse::fail($e);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showSelf(Request $request)
    {
        try{
            $hero = Hero::findByApiToken(Token::get($request))->getQuests();
            APIResponse::found($hero);
        } catch (Exception $e){
            APIResponse::fail($e);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try{
            $hero = Hero::findByApiToken(Token::get($request));
            $hero->setBulk($request->all())->save();
            APIResponse::make(APIResponse::CODE_SUCCESS)->setMsg("Hero updated")->complete($hero);
        } catch (Exception $e){
            APIResponse::fail($e);
        }
    }
}

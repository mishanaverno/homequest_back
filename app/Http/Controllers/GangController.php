<?php

namespace App\Http\Controllers;

use App\Gang;
use App\Hero;
use App\Lib\APIResponse;
use App\Lib\Token;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\MockObject\Api;

class GangController extends Controller
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
            $hero = Hero::findByApiToken(Token::get($request));
            DB::beginTransaction();
            $gang = Gang::make()->setBulk($request->all())->setCreator($hero->getId())->save()->joinHero($hero->getId());
            DB::commit();
            APIResponse::make(APIResponse::CODE_SUCCESS)->setMsg("Gang : element created")->complete($gang);
        }catch(Exception $e){
            DB::rollBack();
            APIResponse::fail($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try{
            $hero = Hero::findByApiToken(Token::get($request));
            $gang = Gang::find($id)->getHeroes();
            APIResponse::found($gang);
        } catch (Exception $e) {
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
    public function update(Request $request, $id)
    {
        try{
            $hero = Hero::findByApiToken(Token::get($request));
            $gang = Gang::find($id);
            if ($hero->getId() != $gang->creator) throw new Exception("Hero is not creator", APIResponse::CODE_NOT_PERMISSIONED);
            $gang->setBulk($request->all())->save();
            APIResponse::make(APIResponse::CODE_SUCCESS)->setMsg("Gang : element updated")->complete($gang);
        } catch (Exception $e){
            APIResponse::fail($e);
        }
    }

    public function join(Request $request)
    {
        try{
            $hero = Hero::findByApiToken(Token::get($request));
            $gang = Gang::findByInviteCode($request->get('code'))->joinHero($hero->getId());
            APIResponse::make(APIResponse::CODE_SUCCESS)->setMsg("Hero @{$hero->login} Joined")->complete($gang);
        } catch (Exception $e){
            APIResponse::fail($e);
        }
    }

    public function invite(Request $request, $id)
    {
        try{
            $hero = Hero::findByApiToken(Token::get($request));
            if (!$hero->inGang($id)) throw new Exception("Hero not joined to this gang", APIResponse::CODE_INVALID_DATA);
            $code = Gang::find($id)->createInvite();
            APIResponse::make(APIResponse::CODE_SUCCESS)->setMsg("Invite created by @{$hero->login}")->complete($code);
        } catch (Exception $e) {
            APIResponse::fail($e);
        }
    }
}

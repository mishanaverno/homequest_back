<?php

namespace App\Http\Controllers;

use App\Gang;
use App\Hero;
use App\Lib\APIResponse;
use App\Lib\Token;
use App\Quest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestController extends Controller
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
        try {
            $hero = Hero::findByApiToken(Token::get($request));
            $gang_id = $request->get('gang_id');
            if(!$hero->inGang($gang_id)) throw new Exception("Hero not joined into defined gang", APIResponse::CODE_INVALID_DATA);
            $gang = Gang::find($gang_id);
            $bonus_reward = (int) $request->get('bonus_reward');
            DB::beginTransaction();
            $hero->removeStyle($bonus_reward)->save();
            $quest = Quest::make()->setBulk($request->all())->create($hero->getId(), $gang->getId());
            DB::commit();
            APIResponse::make(APIResponse::CODE_SUCCESS)->setMsg('Quest created')->complete($quest);
        } catch (Exception $e){
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
        try {
            $hero = Hero::findByApiToken(Token::get($request));
            $quest = Quest::find($id);
            APIResponse::found($quest);
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
    public function update(Request $request, $id)
    {   
        try {
            $hero = Hero::findByApiToken(Token::get($request));
            $quest = Quest::find($id);
            if ($hero->getId() !== $quest->customer_id) throw new Exception("Hero is not customer of this quest", APIResponse::CODE_NOT_PERMISSIONED);
            
            $quest->setBulk($request->all())->save();
            APIResponse::make(APIResponse::CODE_SUCCESS)->setMsg("Quest updated")->complete($quest);
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
    public function progress(Request $request, $id)
    {
        try{
            $hero = Hero::findByApiToken(Token::get($request));
            $quest = Quest::find($id);
            $quest->progress($hero->getId());
            APIResponse::make(APIResponse::CODE_SUCCESS)->setMsg("Quest started by @{$hero->login}")->complete($quest);
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
    public function pending(Request $request, $id)
    {
        try{
            $hero = Hero::findByApiToken(Token::get($request));
            $quest = Quest::find($id);
            $quest->pending($hero->getId());
            APIResponse::make(APIResponse::CODE_SUCCESS)->setMsg("Quest pending by @{$hero->login}")->complete($quest);
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
    public function complete(Request $request, $id)
    {
        try{
            $hero = Hero::findByApiToken(Token::get($request));
            $quest = Quest::find($id);
            $gang = Gang::find($quest->getGangId());
            DB::beginTransaction();
            $quest->complete($hero->getId());
            $hero->addStyle($quest->base_reward + $quest->bonus_reward)->save();
            $gang->incCompleted()->save();
            DB::commit();
            APIResponse::make(APIResponse::CODE_SUCCESS)->setMsg("Quest complete by @{$hero->login}")->complete($quest);
        } catch (Exception $e){
            DB::rollBack();
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
    public function decline(Request $request, $id)
    {
        try{
            $hero = Hero::findByApiToken(Token::get($request));
            $quest = Quest::find($id);
            $quest->decline($hero->getId());
            APIResponse::make(APIResponse::CODE_SUCCESS)->setMsg("Quest declined by @{$hero->login}")->complete($quest);
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
    public function reopen(Request $request, $id)
    {
        try{
            $hero = Hero::findByApiToken(Token::get($request));
            $quest = Quest::find($id);
            $quest->reopen($hero->getId());
            APIResponse::make(APIResponse::CODE_SUCCESS)->setMsg("Quest reopened by @{$hero->login}")->complete($quest);
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
    public function delete(Request $request, $id)
    {
        try{
            $hero = Hero::findByApiToken(Token::get($request));
            $quest = Quest::find($id);
            DB::beginTransaction();
            $hero->addStyle($quest->bonus_reward)->save();
            $quest->delete($hero->getId());
            DB::commit();
            APIResponse::make(APIResponse::CODE_SUCCESS)->setMsg("Quest delited by @{$hero->login}")->complete();
        } catch (Exception $e){
            DB::rollBack();
            APIResponse::fail($e);
        }
    }

}

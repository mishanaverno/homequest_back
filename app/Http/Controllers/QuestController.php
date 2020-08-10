<?php

namespace App\Http\Controllers;

use App\Hero;
use App\Lib\APIResponse;
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
            $heroId = Hero::find($request->post('heroId'))->getId();
            $quest = Quest::make()->setBulk($request->all())->saveByHero();
            APIResponse::make(APIResponse::CODE_SUCCESS)->setMsg('Quest created')->complete($quest);
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
    public function show($id)
    {
        try {
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
            $quest = Quest::find($id);
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
            $quest = Quest::find($id);
            $hero = Hero::find($request->get('heroId'));
            $quest->progress($hero->getId());
            APIResponse::make(APIResponse::CODE_SUCCESS)->setMsg("Quest started by @{$hero->login}")->complete($quest);
        } catch (Exception $e){
            APIResponse::fail($e);
        }
    }

}

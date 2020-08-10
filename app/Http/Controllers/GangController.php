<?php

namespace App\Http\Controllers;

use App\Gang;
use App\Lib\APIResponse;
use Exception;
use Illuminate\Http\Request;
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
            $gang = Gang::make()->setBulk($request->all())->save();
            APIResponse::make(APIResponse::CODE_SUCCESS)->setMsg("Gang : element created")->complete($gang);
        }catch(Exception $e){
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
            $gang = Gang::find($id);
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
            $gang = Gang::find($id);
            $gang->setBulk($request->all())->save();
            APIResponse::make(APIResponse::CODE_SUCCESS)->setMsg( "Gang : element updated")->complete($gang);
        } catch (Exception $e){
            APIResponse::fail($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function heroes($id)
    {
        try{
            $gang = Gang::find($id);
            $result = DB::table('gang_hero')
                ->join('hero', 'hero.id', '=', 'gang_hero.id')
                ->select([
                    'hero.id', 'hero.login', 'hero.name', 'hero.avatar'
                ])->where('gang_id',$gang->getId())->get();
            APIResponse::make(APIResponse::CODE_SUCCESS)->setMsg("Heroes found")->complete($result);
        }catch (Exception $e){
            APIResponse::fail($e);
        }
    }

}

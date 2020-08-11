<?php

namespace App;

use App\Lib\APIResponse;
use Exception;
use Illuminate\Support\Facades\DB;

class Quest extends Model
{
    const TYPE_PERFORMER = 'performer';
    const TYPE_CUSTOMER = 'customer';

    const STATE_OPEN = 'open';
    const STATE_PENDING = 'pending';
    const STATE_PROGRESS = 'progress';
    const STATE_COMPLETE = 'complete';
    const STATE_DECLINED = 'declined';

    protected $table = 'quest';
    protected $columns = [
        'title' => Model::COLUMN_STRING,
        'description' => Model::COLUMN_STRING,
        'reward' => Model::COLUMN_INT,
        'state' => Model::COLUMN_IMMUTABLE,
        'customer' => Model::COLUMN_VIRTUAL,
        'performer' => Model::COLUMN_VIRTUAL,
    ];

    public function saveByHero($id) : Quest
    {
        try{
            DB::beginTransaction();
            $this->save();
            DB::table('quest_hero')->insert([
                'quest_id' => $id,
                'hero_id' => $this->id,
                'type' => Quest::TYPE_CUSTOMER
            ]);
            DB::commit();
        }catch (Exception $e){
            DB::rollBack();
            throw $e;
        }
        return $this;
    }

    public function progress(int $heroId) : Quest
    {   
        if ($this->isOpen()){
            try {
                DB::beginTransaction();
                $this->setState(Quest::STATE_PROGRESS)->save();
                DB::table('quest_hero')->insert([
                    'hero_id' => $heroId,
                    'quest_id' => $this->id,
                    'type' => Quest::TYPE_PERFORMER
                    ]);
                DB::commit();
            } catch (Exception $e){
                DB::rollBack();
                throw $e;
            }
        } else {
            throw new Exception('Quest are not open', APIResponse::CODE_INVALID_STATE);
        }
        return $this;
    }

    public function pending() : Quest
    {
        if ($this->isProgress()){
            try {
                DB::beginTransaction();
                $this->setState(Quest::STATE_PENDING)->save();
                DB::commit();
            } catch (Exception $e){
                DB::rollBack();
                throw $e;
            }
        } else {
            throw new Exception("Quest are not in progress", APIResponse::CODE_INVALID_STATE);
        }
        return $this;
    }

    public function complete() : Quest
    {
        if ($this->isPanding()){
            try {
                DB::beginTransaction();
                $this->setState(Quest::STATE_COMPLETE)->save();
                DB::commit();
            } catch (Exception $e){
                DB::rollBack();
                throw $e;
            }
        } else {
            throw new Exception("Quest are not pending", APIResponse::CODE_INVALID_STATE);
        }
        return $this;
    }

    public function reopen() : Quest
    {
        if ($this->isPanding()){
            try {
                DB::beginTransaction();
                $this->setState(Quest::STATE_OPEN)->save();
                DB::commit();
            } catch (Exception $e){
                DB::rollBack();
                throw $e;
            }
        } else {
            throw new Exception("Quest are not pending", APIResponse::CODE_INVALID_STATE);
        }
        return $this;
    }

    public function decline() : Quest
    {
        if ($this->isOpen()){
            try {
                DB::beginTransaction();
                $this->setState(Quest::STATE_DECLINED)->save();
                DB::commit();
            } catch (Exception $e){
                DB::rollBack();
                throw $e;
            }
        } else {
            throw new Exception("Quest are not open", APIResponse::CODE_INVALID_STATE);
        }
        return $this;
    }
    private function setState($state) : Quest
    {
        $this->state = $state;
        return $this;
    }
    private function isOpen(){
        return isset($this->state) && $this->state == Quest::STATE_OPEN;
    }
    private function isProgress(){
        return isset($this->state) && $this->state == Quest::STATE_PROGRESS;
    }
    private function isPanding(){
        return isset($this->state) && $this->state == Quest::STATE_PENDING;
    }

    private function setCustomer($hero){
        $this->customer = $hero;
    }
    private function setPerformer($hero){
        $this->performer = $hero;
    }
}

<?php

namespace App;

use App\Lib\APIResponse;
use Exception;
use Illuminate\Support\Facades\DB;

class Quest extends Model
{
    const STATE_OPEN = 'open';
    const STATE_PENDING = 'pending';
    const STATE_PROGRESS = 'progress';
    const STATE_COMPLETE = 'complete';

    protected $table = 'quest';

    protected $columns = [
        'title' => Model::COLUMN_SIMPLE,
        'description' => Model::COLUMN_SIMPLE,
        'base_reward' => Model::COLUMN_SIMPLE,
        'bonus_reward' => Model::COLUMN_SIMPLE,
        'state' => Model::COLUMN_IMMUTABLE,
        'performer_id' => Model::COLUMN_IMMUTABLE,
        'customer_id' => Model::COLUMN_IMMUTABLE,
        'gang_id' => Model::COLUMN_IMMUTABLE,
        'customer' => Model::COLUMN_VIRTUAL,
        'performer' => Model::COLUMN_VIRTUAL,
    ];

    /**
     * Create new quest
     *
     * @param int $id - hero_id
     * @return Quest
     */
    public function create($hero_id, $gang_id) : Quest
    {
        $this->setCustomerId($hero_id)->setGang($gang_id)->save();
        return $this;
    }

    public function progress(int $heroId) : Quest
    {   
        if (!$this->isOpen()) throw new Exception('Quest are not open', APIResponse::CODE_INVALID_STATE);
        if ($this->customer_id == $heroId) throw new Exception("Hero is the customer of this quest", APIResponse::CODE_NOT_PERMISSIONED);
        $this->setPerformerId($heroId)
            ->setState(Quest::STATE_PROGRESS)
            ->save();
        return $this;
    }

    public function pending(int $heroId) : Quest
    {
        if (!$this->isProgress()) throw new Exception("Quest are not in progress", APIResponse::CODE_INVALID_STATE);
        if ($this->performer_id !== $heroId) throw new Exception("Hero is not a performer of this quest", APIResponse::CODE_NOT_PERMISSIONED);
        $this->setState(Quest::STATE_PENDING)->save();
        return $this;
    }

    public function complete(int $heroId) : Quest
    {
        if (!$this->isPending()) throw new Exception("Quest are not pending", APIResponse::CODE_INVALID_STATE);
        if ($this->customer_id !== $heroId) throw new Exception("Hero is not a customer of this quest", APIResponse::CODE_NOT_PERMISSIONED);
        $this->setState(Quest::STATE_COMPLETE)->save();
        return $this;
    }

    public function reopen(int $heroId) : Quest
    {
        if (!$this->isPending()) throw new Exception("Quest are not pending", APIResponse::CODE_INVALID_STATE);
        if ($this->customer_id !== $heroId) throw new Exception("Hero is not a customer of this quest", APIResponse::CODE_NOT_PERMISSIONED);
        $this->setPerformerId(0)->setState(Quest::STATE_OPEN)->save();
        return $this;
    }

    public function decline(int $heroId) : Quest
    {
        if (!$this->isProgress()) throw new Exception('Quest are not open', APIResponse::CODE_INVALID_STATE);
        if ($this->performer_id !== $heroId) throw new Exception("Hero is not a customer of this quest", APIResponse::CODE_NOT_PERMISSIONED);
        $this->setState(Quest::STATE_OPEN)->setPerformerId(0)->save();
        return $this;
    }

    public function delete(int $heroId)
    {
        if (!$this->isOpen()) throw new Exception('Quest are not open', APIResponse::CODE_INVALID_STATE);
        if ($this->customer_id !== $heroId) throw new Exception("Hero is not a customer of this quest", APIResponse::CODE_NOT_PERMISSIONED);
        DB::table('quest')->where('id', $this->id)->delete();
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
    private function isPending(){
        return isset($this->state) && $this->state == Quest::STATE_PENDING;
    }

    private function setCustomerId($hero_id) : Quest
    {
        $this->set('customer_id', $hero_id);
        return $this;
    }
    private function setPerformerId($hero_id) : Quest
    {
        $this->set('performer_id', $hero_id);
        return $this;
    }
    private function setCustomer($hero) : Quest
    {
        $this->customer = $hero;
        return $this;
    }
    private function setPerformer($hero) : Quest
    {
        $this->performer = $hero;
        return $this;
    }
    private function setGang($gang) : Quest
    {
        $this->set('gang_id', $gang);
        return $this;
    }
    public function getGangId()
    {
        return $this->gang_id;
    } 
}

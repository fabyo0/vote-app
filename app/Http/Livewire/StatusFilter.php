<?php

namespace App\Http\Livewire;

use App\Models\Status;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class StatusFilter extends Component
{
    public $status;

    public $statusCount;

    public function setStatus($newStatus)
    {
        $this->status = $newStatus;

        //TODO: Emit queryStringStatus

        $this->emit('queryStringUpdatedStatus', $this->status);

        if ($this->getPreviousRouteName() == 'idea.show') {
            return redirect()->route('idea.index', [
                'status' => $this->status,
            ]);
        }

        /*return redirect()->route('idea.index', [
            'status' => $this->status
        ]);*/
    }

    public function mount()
    {
        // Status count
        $this->statusCount = Status::getCount();
        $this->status = request()->status ?? 'All';

        //TODO: Mevcut url idea.show ise
        if (Route::currentRouteName() === 'idea.show') {
            $this->status = null;
            // $this->queryString = [];
        }
    }

    private function getPreviousRouteName()
    {
        return app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();
    }

    public function render()
    {
        return view('livewire.status-filter');
    }
}

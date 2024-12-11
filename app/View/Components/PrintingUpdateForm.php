<?php

namespace App\View\Components;

use App\Models\PrintingMachine;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PrintingUpdateForm extends Component
{
    /**
     * Create a new component instance.
     */
    public $printingMachineList;
    public function __construct()
    {
        $this->printingMachineList = PrintingMachine::where("lock_status",false)->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.printing-update-form');
    }
}

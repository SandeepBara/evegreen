<?php

namespace App\View\Components;

use App\Models\BagType;
use App\Models\ClientDetail;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RollBooking extends Component
{
    /**
     * Create a new component instance.
     */
    public $clientList;
    public $bagType;
    public function __construct()
    {
        $this->clientList = (new ClientDetail())->getClientListOrm()->get();
        $this->bagType = (new BagType())->getBagListOrm()->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.roll-booking');
    }
}

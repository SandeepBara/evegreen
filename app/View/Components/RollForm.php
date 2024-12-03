<?php

namespace App\View\Components;

use App\Models\BagType;
use App\Models\ClientDetail;
use App\Models\VendorDetail;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RollForm extends Component
{
    /**
     * Create a new component instance.
     */
    public $vendorList;
    public $clientList;
    public $bagType;
    public function __construct()
    {
        $this->vendorList = (new VendorDetail())->getVenderListOrm()->get();
        $this->clientList = (new ClientDetail())->getClientListOrm()->get();
        $this->bagType = (new BagType())->getBagListOrm()->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.roll-form');
    }
}

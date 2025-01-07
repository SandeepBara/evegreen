<?php

namespace App\View\Components;

use App\Models\Sector;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ClientForm extends Component
{
    /**
     * Create a new component instance.
     */
    public $sector;
    public function __construct()
    {
        //
        $this->sector = (new Sector())->getSectorListOrm()->orderBy("id","ASC")->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.client-form');
    }
}

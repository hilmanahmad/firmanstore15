<?php

namespace App\Livewire;

use Log;
use Livewire\Component;
use App\Models\Master\Pool;
use App\Models\Master\Vehicle;
use App\Models\Master\Customer;
use App\Models\Master\Employee;
use App\Models\VendorManagement\Vendor;

class Dashboard extends Component
{

    public function render()
    {
        return view('livewire.dashboard', ['title' => 'Dashboard'])
            ->extends('layout.template')
            ->section('container');
    }

    public function changeData()
    {

        $this->dispatch('dashboardDataUpdated', [])->self();
    }
}

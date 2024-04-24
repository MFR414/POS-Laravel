<?php

namespace App\Http\Controllers\Application\Web;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $vehicleNames = [];
        $vehicleOrderCount = [];
        // $vehicles = Vehicle::withCount('orderVehicles')->get();

        // foreach ($vehicles as $vehicle) {
        //     $vehicleNames[] = $vehicle->registration_no.' - '.$vehicle->name;
        //     $vehicleOrderCount[] = $vehicle->order_vehicles_count;
        // }

        return view('application.dashboard',[
            //'vehicle chart data
            'vehicleNames' => json_encode($vehicleNames),
            'vehicleOrderCount' => json_encode($vehicleOrderCount),
            'active_page' => 'dashboard',
        ]);
    }
}

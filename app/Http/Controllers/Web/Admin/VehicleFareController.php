<?php

namespace App\Http\Controllers\Web\Admin;

use App\Base\Constants\Masters\zoneRideType;
use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Zone\AssignZoneTypeRequest;
use App\Models\Admin\VehicleType;
use App\Models\Admin\Zone;
use App\Models\Admin\ZoneTypePrice;
use Illuminate\Http\Request;

class VehicleFareController extends Controller
{
    public function index()
    {
        $page = trans('pages_names.vehicle-fare');
        $main_menu = 'vehicle-fare';
        $sub_menu = '';

        return view('admin.vehicle_fare.index', compact('page', 'main_menu', 'sub_menu'));
    }

    public function fetchFareList(QueryFilterContract $queryFilter)
    {
        $query = ZoneTypePrice::latest();

        $results = $queryFilter->builder($query)->customFilter(new CommonMasterFilter)->paginate();

        return view('admin.vehicle_fare._fare_list', compact('results'));
    }

    public function create() 
    {
        $zones = Zone::active()->get();
        $page = trans('pages_names.add_vehicle_fare');
        $main_menu = 'vehicle-fare';
        $sub_menu = '';

        return view('admin.vehicle_fare.create', compact('page', 'main_menu', 'sub_menu', 'zones'));
    }

    public function fetchVehiclesByZone(Request $request)
    {
        $zone = Zone::whereId($request->_zone)->first();
        $ids = $zone->zoneType()->pluck('type_id')->toArray();

        $types = VehicleType::whereNotIn('id', $ids)->active()->get();

        return response()->json(['success' => true, 'data' => $types]);
    }

    public function store(AssignZoneTypeRequest $request)
    {
        $zone  = Zone::whereId($request->zone)->first();
        $payment = implode(',', $request->payment_type);

        // To save default type
        if ($zone->default_vehicle_type == null) {
            $zone->default_vehicle_type = $request->type;
            $zone->save();
        }

        $zoneType = $zone->zoneType()->create([
            'type_id' => $request->type,
            'payment_type' => $payment,
            'bill_status' => true
        ]);

        $zoneType->zoneTypePrice()->create([
            'price_type' => zoneRideType::RIDENOW,
            'base_price' => $request->ride_now_base_price,
            'price_per_distance' => $request->ride_now_price_per_distance,
            'cancellation_fee' => $request->ride_now_cancellation_fee,
            'base_distance' => $request->ride_now_base_distance ? $request->ride_now_base_distance : 0,
            'price_per_time' => $request->ride_now_price_per_time ? $request->ride_now_price_per_time : 0.00,
        ]);

        $zoneType->zoneTypePrice()->create([
            'price_type' => zoneRideType::RIDELATER,
            'base_price' => $request->ride_later_base_price,
            'price_per_distance' => $request->ride_later_price_per_distance,
            'cancellation_fee' => $request->ride_later_cancellation_fee,
            'base_distance' => $request->ride_later_base_distance ? $request->ride_later_base_distance : 0,
            'price_per_time' => $request->ride_later_price_per_time ? $request->ride_later_price_per_time : 0.00,
        ]);

        $message = trans('succes_messages.type_assigned_succesfully');

        return redirect('vehicle_fare')->with('success', $message);
    }

    public function getById(ZoneTypePrice $zone_price)
    {
        $page = trans('pages_names.edit_vehicle_fare');
        $main_menu = 'vehicle-fare';
        $sub_menu = '';

        return view('admin.vehicle_fare.edit', compact('page', 'main_menu', 'sub_menu', 'zone_price'));
    }

    public function update(Request $request,ZoneTypePrice $zone_price)
    {
        $zone_price->zoneType()->update([
            'payment_type' => implode(',', $request->payment_type)
        ]);

        $zone_price->update([
            'base_price' => $request->ride_now_base_price,
            'price_per_distance' => $request->ride_now_price_per_distance,
            'cancellation_fee' => $request->ride_now_cancellation_fee,
            'base_distance' => $request->ride_now_base_distance ? $request->ride_now_base_distance : 0,
            'price_per_time' => $request->ride_now_price_per_time ? $request->ride_now_price_per_time : 0.00,
        ]);

        $message = trans('succes_messages.type_fare_updated_succesfully');

        return redirect('vehicle_fare')->with('success', $message);
    }

    public function toggleStatus(ZoneTypePrice $zone_price) {
        $status = $zone_price->zoneType->isActive() ? false : true;
        $zone_price->zoneType->update(['active' => $status]);

        $message = trans('succes_messages.type_fare_status_updated_succesfully');

        return redirect('vehicle_fare')->with('success', $message);
    }
}

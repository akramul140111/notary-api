<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\GlobalHelper;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    //

    public function __construct(private readonly GlobalHelper $globalHelper)
    {
        
    }

    public function serviceList()
    {
        $allServices = $this->globalHelper->getServiceList(65) ?? [];
        $applicationCounts = Application::select('service_id', DB::raw('count(*) as count'))
            ->whereIn('service_id', array_column($allServices, 'sid'))
            ->groupBy('service_id')
            ->get()
            ->keyBy('service_id')
            ->toArray();
        
        foreach ($allServices as &$service) {
            $service['application_count'] = $applicationCounts[$service['sid']]['count'] ?? 0;
        }

        return response()->json([
            'services' => $allServices,
        ], 200);

    }
}

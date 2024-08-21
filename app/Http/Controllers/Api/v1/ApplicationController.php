<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\GlobalHelper;
use App\Http\Requests\ApplicationRequest;
use App\Http\Resources\ApplicationResource;
use App\Http\Resources\ApplicationResourceCollection;
use App\Models\Application;
use App\Models\User;
use App\Services\ApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApplicationController extends Controller
{
    //

    public function __construct(private readonly GlobalHelper $globalHelper)
    {
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($service_id)
    {
        $allServices    = $this->globalHelper->getServiceList(65) ?? [];
        $application = new ApplicationResourceCollection(Application::where('service_id',$service_id)->get());
        return response()->json(['status' => true, 'data' => $application, 'message' => 'Successfully get Applications', 'services' => $allServices], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ApplicationRequest $request
     * @return JsonResponse
     */
    public function store(ApplicationRequest $request) : JsonResponse
    {
        try {
            $application = (new ApplicationService())->store($request);
            $applications = new ApplicationResourceCollection(Application::where('service_id',$application->service_id)->get());

            $forNotary = [
                'application'   => $application,
                'user_info'     => User::where('id', $request->userId)->first()
            ];

                $third_party = config('services.app.third_party_api');

                try{
                    $sync = Http::post($third_party."/api/notary-application", $forNotary);

                    if($sync) {
                        Application::where('id', $application->id)->update(['is_sync' => true]);
                    }

                }catch(\Exception $e) {
                    return response()->json(['status' => false, 'data' => '', 'message' => $e->getMessage()], 422);
                }

            return response()->json(['status' => true, 'application' => $application, 'data' => $applications, 'message' => 'Successfully created an application'], 201);
        } catch(\Throwable $t) {
            return response()->json(['status' => false, 'data' => '', 'message' => $t->getMessage()], 422);
        }
    }

    
    /**
     * Display the specified resource.
     *
     * @param Application $application
     * @return JsonResponse
     */
    public function show(Application $application) : JsonResponse
    {
        try{
            $application = new ApplicationResource($application);
            return response()->json(['status' => true, 'data' => $application, 'message' => "Successfully get Application"], 200);
        } catch (\Throwable $t) {
            return response()->json(['status' => false, 'data' => '', 'message' => $t->getMessage()], 422);
        }
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param Application $application
     * @return JsonResponse
     */
    public function edit(Application $application) : JsonResponse
    {
        try{
            $application = new ApplicationResource($application);
            return response()->json(['status' => true, 'data' => $application, 'message' => "Successfully get Application"], 200);
        } catch (\Throwable $t) {
            return response()->json(['status' => false, 'data' => '', 'message' => $t->getMessage()], 422);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ApplicationRequest $request
     * @param Application $application
     * @return JsonResponse
     */
    public function update(ApplicationRequest $request, Application $application) : JsonResponse
    {
        try{
            $application->update($request->validated());
            return response()->json(['status' => true, 'data' => $application, 'message' => "Application update Successfully "], 200);
        } catch (\Throwable $t) {
            return response()->json(['status' => false, 'data' => '', 'message' => $t->getMessage()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Application $application
     * @return JsonResponse
     */
    public function destroy(Application $application) : JsonResponse
    {
        try {
            $application->delete();
            return response()->json(['status' => true, 'data' => $application, 'message' => 'Application deleted successfully']);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'data' => '', 'message' => $th->getMessage()], 422);
        }
    }
}

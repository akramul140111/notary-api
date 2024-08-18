<?php

namespace App\Http\Helpers;

use App\Models\Application;
use App\Models\ApplicationAssignment;
use App\Models\NothiApplication;
use App\Models\SubDivision;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Optional;

class GlobalHelper
{
    public function getApiToken(): void
    {
        $loginResponse = Http::withHeaders([
            'Accept' => 'application/json',
            'api-key' => config('services.app.kong_api_key'),
        ])->withOptions(['verify' => false])->post(config('services.app.token_url'), [
            'email' => config('services.app.token_email'),
            'password' => config('services.app.token_password'),
        ]);
        $loginToken = $loginResponse->json()['token'];
        session()->put('api_access_token', $loginToken);
    }

    /**
     * @return array|JsonResponse|mixed|string
     */
    public function getServiceList(int $offie_id): mixed
    {
        // $data = [['sid'=>'BDGS-1623308572', 'name'=>'অত্যাবশ্যকীয় পণ্যের ডিলিং লাইসেন্স (স্বর্ণ) নবায়নের আবেদনপত্র']];
        // return $data;
        try {
            $apiAccessToken = session('api_access_token');
            if (empty($apiAccessToken)) {
                $this->getApiToken();
                $apiAccessToken = session('api_access_token');
            }

            //TODO: Here will be added caching(Redis) process[Currently Laravel Default Cache]
            $cacheKey = 'service_list_from_api'.auth()->user()->mygov_office_id;
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'api-key' => config('services.app.kong_api_key'),

                'Authorization' => 'Bearer '.$apiAccessToken,
                ])->withOptions(['verify' => false])->get(config('services.app.service_url').auth()->user()->mygov_office_id);

                // 'Authorization' => 'Bearer ' . $apiAccessToken
                // ])->withOptions(["verify" => false])->get(config('services.app.service_url') . auth()->user()->mygov_office_id);


                if ($response->successful()) {
                    $data = $response->json();
                    // dd($data);
                    // Cache::put($cacheKey, $data, now()->addMinute(config('services.app.service_cache_time')));

                    return $data;
            } else {
                return response()->json(['error' => 'API call failed'], $response->status());
            }
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function getServiceTitleById(string $serviceId): string
    {
        try {
            $services = $this->getServiceList(auth()->user()->office_id);
            $serviceName = collect($services)->where('sid', $serviceId)->pluck('name')->first();

            if ($serviceName === null) {
                return 'Service not found for the given ID';
            }

            return $serviceName;
        } catch (\Exception $e) {
            \Log::error($e->getMessage());

            return false;
        }
    }

    public function getCountBySidAndStatus(string $serviceId, int $applicationStatus, string $userType = 'du'): mixed
    {
        return Application::where([
            'application_status' => $applicationStatus,
            'service_id' => $serviceId,
            $userType.'_receiver' => auth()->user()->id,
        ])->count();
    }

    /**
     * @return mixed
     */
    // public function getCountByNothiNumberAndStatus(string $nothiNumber, int $applicationStatus)
    // {
    //     return NothiApplication::where(['nothi_number' => $nothiNumber, 'status' => $applicationStatus, 'user_id' => auth()->user()->id])->count();
    // }

    /**
     * @return mixed
     */
    public function oppkhoManCountBySid(string $serviceId): int
    {
        return $this->getCountBySidAndStatus($serviceId, 1, 'fu') ?? 0;
    }

    /**
     * @return int|mixed
     */
    public function dakCountBySidForFu(string $serviceId): mixed
    {
        return $this->getCountBySidAndStatus($serviceId, 2, 'fu') ?? 0;
    }

    /**
     * @return int|mixed
     */
    public function dakCountBySid(string $serviceId): mixed
    {
        return $this->getCountBySidAndStatus($serviceId, 2, 'du') ?? 0;
    }

    /**
     * @return int|mixed
     */
    public function nothiCountBySid(string $serviceId): mixed
    {
        return $this->getCountBySidAndStatus($serviceId, 3, 'nu') ?? 0;
    }

    /**
     * @return int|mixed
     */
    public function nothiCountBySidForFu(string $serviceId): mixed
    {
        return $this->getCountBySidAndStatus($serviceId, 3, 'fu') ?? 0;
    }

    /**
     * @return int|mixed
     */
    public function nispottiCountBySid(string $serviceId): mixed
    {
        return $this->getCountBySidAndStatus($serviceId, 4, 'nu') ?? 0;
    }

    /**
     * @return int|mixed
     */
    public function nispottiCountBySidForFu(string $serviceId): mixed
    {
        return $this->getCountBySidAndStatus($serviceId, 4, 'fu') ?? 0;
    }

    public function getCountForAccepted($nothiNumber)
    {
        return $this->getCountByNothiNumberAndStatus($nothiNumber, 3) ?? 0;
    }

    public function getCountForCompleted($nothiNumber)
    {
        return $this->getCountByNothiNumberAndStatus($nothiNumber, 4) ?? 0;
    }

    /**
     * @param  $serviceId
     */
    public function getServiceCounts($serviceIdOrNothiNumber): array
    {
        return [
            'dakForFu' => $this->dakCountBySidForFu($serviceIdOrNothiNumber),
            'nothiForFu' => $this->nothiCountBySidForFu($serviceIdOrNothiNumber),
            'nispottiForFu' => $this->nispottiCountBySidForFu($serviceIdOrNothiNumber),
            'totalForFu' => $this->oppkhoManCountBySid($serviceIdOrNothiNumber) + $this->dakCountBySidForFu($serviceIdOrNothiNumber) + $this->nothiCountBySidForFu($serviceIdOrNothiNumber) + $this->nispottiCountBySidForFu($serviceIdOrNothiNumber),

            'oppkhoMan' => $this->oppkhoManCountBySid($serviceIdOrNothiNumber),
            'dak' => $this->dakCountBySid($serviceIdOrNothiNumber),
            'nothi' => $this->nothiCountBySid($serviceIdOrNothiNumber),
            'nispotti' => $this->nispottiCountBySid($serviceIdOrNothiNumber),
            'total' => $this->oppkhoManCountBySid($serviceIdOrNothiNumber) + $this->dakCountBySid($serviceIdOrNothiNumber) + $this->nothiCountBySid($serviceIdOrNothiNumber) + $this->nispottiCountBySid($serviceIdOrNothiNumber),
            'dakTotal' => $this->dakCountBySid($serviceIdOrNothiNumber) + $this->nothiCountBySid($serviceIdOrNothiNumber) + $this->nispottiCountBySid($serviceIdOrNothiNumber),

            'nothiAccepted' => $this->getCountForAccepted($serviceIdOrNothiNumber), // Nothi Number
            'nothiCompleted' => $this->getCountForCompleted($serviceIdOrNothiNumber), // Nothi Number
            'nothiTotal' => $this->getCountForCompleted($serviceIdOrNothiNumber) + $this->getCountForAccepted($serviceIdOrNothiNumber), // Nothi Number
        ];
    }

    /**
     * @return Optional|mixed|string
     */
    public function officerInformation($userId): mixed
    {
        $user = User::leftJoin('profiles', 'users.id', '=', 'profiles.user_id')
            ->select('profiles.name_bn', 'profiles.designation_bn', 'profiles.office_name_bn')
            ->find($userId);

        return optional($user, function ($user) {
            return implode(', ', [$user->name_bn, $user->designation_bn, $user->office_name_bn]);
        }) ?? '';
    }

    public function UReceiverNameDesignationSection($receiverId): string
    {
        return $this->officerInformation($receiverId);
    }

    // public function FuReceiverCompletedDateTime($receiverId, $applicationId)
    // {
    //     return ApplicationAssignment::select('assignment_date')->where(['previous_assignee_id' => $receiverId, 'application_id' => $applicationId])->first();
    // }

    // public function DuReceiverCompletedDateTime($receiverId, $applicationId)
    // {
    //     return ApplicationAssignment::select('assignment_date')->where(['previous_assignee_id' => $receiverId, 'application_id' => $applicationId])->first();
    // }

    // public function DeskWiseUserInformation($applicationId, $deskType)
    // {
    //     $logs = ApplicationAssignment::join('profiles', 'application_assignments.assignee_id', '=', 'profiles.user_id')
    //         ->where(['application_assignments.application_id' => $applicationId, 'application_assignments.application_desk' => $deskType])
    //         ->select('application_assignments.*', 'profiles.name_bn', 'profiles.designation_bn', 'profiles.office_name_bn', 'application_assignments.assignment_date') // Add other fields as needed
    //         ->orderBy('id','ASC')
    //         ->get();

    //     return $logs;
    // }

    // public function DakWiseUserInformation($applicationId)
    // {
    //     return ApplicationAssignment::join('profiles', 'application_assignments.assignee_id', '=', 'profiles.user_id')
    //         ->where(['application_assignments.application_id' => $applicationId, 'application_assignments.application_desk' => 2])
    //         ->select('application_assignments.*', 'profiles.name_bn', 'profiles.designation_bn', 'profiles.office_name_bn', 'application_assignments.assignment_date') // Add other fields as needed
    //         ->get();
    // }

    /**
     * @param  $applicationId
     *                        TODO: Need to merge with Desk wise user information
     */
    public function CompletedUserInformation($applicationId)
    {
        $logs = DB::table('applications')
            ->leftJoin('application_assignments', 'applications.application_id', '=', 'application_assignments.application_id')
            ->leftJoin('profiles', 'profiles.user_id', '=', 'application_assignments.assignee_id')
            ->select('profiles.name_bn', 'profiles.designation_bn', 'profiles.office_name_bn', 'application_assignments.assignment_date')
            ->where(['application_assignments.application_desk' => 4, 'application_assignments.application_id' => $applicationId])
            ->first();

        return $logs;
    }

    // public function getSubDivision($id)
    // {
    //     return SubDivision::select('name')->where('id',$id)->first();
    // }

    public function engToBngNum($num): array|string
    {
        $num = str_replace('0', '০', $num);
        $num = str_replace('1', '১', $num);
        $num = str_replace('2', '২', $num);
        $num = str_replace('3', '৩', $num);
        $num = str_replace('4', '৪', $num);
        $num = str_replace('5', '৫', $num);
        $num = str_replace('6', '৬', $num);
        $num = str_replace('7', '৭', $num);
        $num = str_replace('8', '৮', $num);
        $num = str_replace('9', '৯', $num);

        return $num;
    }

    public function getMyGovFormAttachment($service_id)
    {
        try {
            $apiAccessToken = session('api_access_token');

            if (empty($apiAccessToken)) {
                $this->getApiToken();
                $apiAccessToken = session('api_access_token');
            }

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'api-key' => config('services.app.kong_api_key'),
                'Authorization' => 'Bearer '.$apiAccessToken,
            ])->withOptions(['verify' => false])->post(config('services.app.mygov_form_attachment_url'), [
                'service_id' => $service_id,
            ]);
            if ($response->successful()) {
                return $response->json('data');
            } else {
                return response()->json(['error' => 'API call failed'], $response->status());
            }
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function getIdFromSid($sid)
    {
        $services = $this->getServiceList(auth()->user()->office_id);

        return collect($services)->where('sid', $sid)->first();
    }

    /**
     * @return array|mixed|string
     */
    public function sendApplicationDataToMyGov($application): mixed
    {
        $apiAccessToken = session('api_access_token');

        if (empty($apiAccessToken)) {
            $this->getApiToken();
            $apiAccessToken = session('api_access_token');
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'api-key' => config('services.app.kong_api_key'),
                'Authorization' => 'Bearer '.$apiAccessToken,
            ])->withOptions(['verify' => false])->post(config('services.app.mygov_external_application_url'), [
                'applicant_mobile' => $application->applicant_mobile,
                'application_date' => $application->application_date,
                'service_id' => $application->service_id,
                'data' => $application,
                'status' => '1',
                'details_external_url' => 'https://qr-application.dev.mygov.bd',
            ]);

            return $response->json();
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }

    }

    public function GetDecisionByStatus($status)
    {
        $decision = [
            '20' => 'অনুমোদিত',
            '17' => 'না-মঞ্জুর যোগ্য',
            '11' => 'তদন্তের জন্য পাঠানো হয়েছে',
            '24' => 'পেমেন্ট করুন',
            '12' => 'তদন্ত রিপোর্ট গৃহীত',
            '8' => 'পেমেন্ট গৃহীত',
        ];
        return $decision[$status] ?? 'Status code not valid';
    }
}

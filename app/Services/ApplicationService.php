<?php 
namespace App\Services;

use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\ScanCopy;

class ApplicationService {
    public function store($request) 
    {
        $application                    = new Application();
        $application->name              = $request->name;
        $application->mobile            = $request->mobile;
        $application->gender            = $request->gender;
        $application->email             = $request->email;
        $application->office_id         = $request->office_id ? $request->office_id : 4;
        $application->service_main_id   = $request->service_main_id;
        $application->service_id        = $request->service_id;
        $application->service_name      = $request->service_name;
        $application->save();

        if ($request->has('scan_copy')) {
            foreach ($request->input('scan_copy') as $index => $scan) {
                $scan_file                  = new ScanCopy();
                $scan_file->application_id  = $application->id;
                $scan_file->title           = $scan['title'] ?? 'No Title';
    
                if ($request->hasFile('scan_copy.' . $index . '.appImg')) {
                    $file                   = $request->file('scan_copy.' . $index . '.appImg');
                    $scan_file->scan_copy   = $file->store('scan_copies', 'public');
                }
    
                $scan_file->save();
            }
        }

        return new ApplicationResource($application);
    }
}

?>
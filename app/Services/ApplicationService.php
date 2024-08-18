<?php 
namespace App\Services;

use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\ScanCopy;

class ApplicationService {
    public function store($request) 
    {
        $application                = new Application();
        $application->name          = $request->name;
        $application->mobile        = $request->mobile;
        $application->gender        = $request->gender;
        $application->email         = $request->email;
        // if($request->hasFile('scan_copy')) 
        // {
        //     $application->scan_copy = $request->file('scan_copy')->store('scan_copies', 'public');
        // }
        if($request->hasFile('scan_copies')) 
        {
            foreach($request->scan_copies as $s_copy)
            {
                $scan_file                  = new ScanCopy();
                $scan_file->application_id  = $application->id;
                $scan_file->title           = $s_copy->title?$s_copy->title:"No Title";
                $scan_file->scan_copy       = $s_copy->file('scan_copy')->store('scan_copies', 'public');
                $scan_file->save();
            }

        }
        $application->save();

        return new ApplicationResource($application);
    }
}

?>
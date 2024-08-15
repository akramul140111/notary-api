<?php 
namespace App\Services;

use App\Http\Resources\ApplicationResource;
use App\Models\Application;

class ApplicationService {
    public function store($request) 
    {
        $application                = new Application();
        $application->name          = $request->name;
        $application->mobile        = $request->mobile;
        $application->gender        = $request->gender;
        $application->email         = $request->email;
        $application->scan_copy     = $request->scan_copy;
        // if($request->hasFile('scan_copy')) 
        // {
        //     $application->scan_copy = $request->file('scan_copy')->store('scan_copies', 'public');
        // }
        $application->save();

        return new ApplicationResource($application);
    }
}

?>
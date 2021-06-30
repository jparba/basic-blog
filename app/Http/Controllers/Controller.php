<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Increment filename if exist.
     *
     * @param  path
     * @return filename string
     */
    public function incrementFileName($path) {
        $filename = pathinfo($path, PATHINFO_FILENAME);
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        if(file_exists($path)) {
            $x = 1;
            $new_path = $path;

            while (file_exists($new_path)) {
                $newfilename = $filename . '-' . $x . '.' .$extension;
                $new_path = 'image/cover/'.$newfilename;
                $x++;
            }
            return $newfilename;
        }else{
            return $filename.'.'.$extension;
        }
    }
}

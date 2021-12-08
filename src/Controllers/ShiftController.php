<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;
use App\Models\Shift;

class ShiftController extends Controller
{
    public function getAll($request, $response, $args)
    {
        $shifts = Shift::all();
        
        $data = json_encode($shifts, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE);

        return $response->withStatus(200)
                ->withHeader("Content-Type", "application/json")
                ->write($data);
    }
    
    public function getById($request, $response, $args)
    {
        $shifts = Shift::where('loginname', $args['loginname'])
                    ->get(['loginname', 'name', 'entryposition'])
                    ->first();
                    
        $data = json_encode($shifts, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE);

        return $response->withStatus(200)
                ->withHeader("Content-Type", "application/json")
                ->write($data);
    }
}

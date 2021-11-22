<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;
use App\Models\Building;

class BuildingController extends Controller
{
    public function getAll($request, $response, $args)
    {
        $buildings = Building::all();

        return $response->withStatus(200)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($buildings, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
    }
    
    public function getById($request, $response, $args)
    {
        $building = Building::where('id', $args['id'])->first();

        return $response->withStatus(200)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($building, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
    }
    
    public function getBuildingWards($request, $response, $args)
    {
        $sortBy = $request->getQueryParam('sort');

        $building = Building::where('id', $args['id'])
                        ->when($sortBy, function($sub) use ($sortBy) {
                            $sub->with(['wards' => function($q) use ($sortBy) {
                                $q->orderBy($sortBy);
                            }]);
                        }, function($sub) {
                            $sub->with(['wards' => function($q) {
                                $q->orderBy('ward_no');
                            }]);
                        })
                        ->first();

        return $response->withStatus(200)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($building, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
    }

    public function store($request, $response, $args)
    {
        $post = (array)$request->getParsedBody();

        $building = new Building;
        $building->building_no = $post['building_no'];
        $building->building_name = $post['building_name'];
        
        if($building->save()) {
            return $response->withStatus(200)
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode($building, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
        }                    
    }

    public function update($request, $response, $args)
    {
        $post = (array)$request->getParsedBody();

        $building = Building::where('building_id', $args['id'])->first();
        $building->building_no = $post['building_no'];
        $building->building_name = $post['building_name'];
        
        if($building->save()) {
            return $response->withStatus(200)
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode($building, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
        }
    }

    public function delete($request, $response, $args)
    {
        $building = Building::where('building_id', $args['id'])->first();
        
        if($building->delete()) {    
            return $response->withStatus(200)
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode($building, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
        }
    }
}

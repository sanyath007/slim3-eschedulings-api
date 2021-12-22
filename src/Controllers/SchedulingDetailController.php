<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;
use App\Models\Scheduling;
use App\Models\SchedulingDetail;
use App\Models\Person;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Division;
use App\Models\MemberOf;
use App\Models\Shift;
use App\Models\ShiftSwapping;
use App\Models\Holiday;

class SchedulingDetailController extends Controller
{
    public function getAll($req, $res, $args)
    {
        return $res->withJson([
            'details'   => SchedulingDetail::where('scheduling_id', $args['scheduleId'])
                            ->with('person')
                            ->with('person.prefix','person.position')
                            ->with('scheduling','scheduling.division','scheduling.controller')
                            ->get()
        ]);
    }

    public function getById($req, $res, $args)
    {
        $detail = SchedulingDetail::where('id', $args['id'])
                        ->with('person')
                        ->with('person.prefix','person.position')
                        ->with('scheduling','scheduling.division','scheduling.controller')
                        ->first();

        return $res
                ->withStatus(200)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode([
                    'detail'    => $detail
                ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
    }

    public function update($req, $res, $args)
    {
        try {
            $post = (array)$req->getParsedBody();

            $detail = SchedulingDetail::find($args['id']);
            $detail->scheduling_id  = $post['scheduling_id'];
            $detail->person_id      = $post['person_id'];
            $detail->shifts         = $post['shifts'];

            if($detail->save()) {
                /** 
                 * To manipulate total_persons and total_shifts of schedulings data 
                 * on scheduling_detail is updated 
                */
                // $scheduling = Scheduling::find($post['scheduling_id']);
                // $scheduling->total_persons  = $post['total_persons'];
                // $scheduling->total_shifts   = $post['total_shifts'];
                // $scheduling->save();

                return $res
                        ->withStatus(200)
                        ->withHeader("Content-Type", "application/json")
                        ->write(json_encode([
                            'status'    => 1,
                            'message'   => 'Updating successfully',
                            'detail'    => $detail
                        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
            } else {
                return $res
                    ->withStatus(500)
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode([
                        'status'    => 0,
                        'message'   => 'Something went wrong!!'
                    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
            }
        } catch (\Exception $ex) {
            return $res
                    ->withStatus(500)
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode([
                        'status'    => 0,
                        'message'   => $ex->getMessage()
                    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
        }
    }

    public function delete($req, $res, $args)
    {
        try {
            $scheduling = Scheduling::find($args['id']);

            if($scheduling->delete()) {
                /** TODO: To manipulate scheduling_detail data on scheduling is deleted */
                $deletedDetail = SchedulingDetail::where('scheduling_id', $args['id'])->delete();

                return $res
                        ->withStatus(200)
                        ->withHeader("Content-Type", "application/json")
                        ->write(json_encode([
                            'status'    => 1,
                            'message'   => 'Deleting successfully',
                            'id'        => $args['id']
                        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
            } else {
                return $res
                    ->withStatus(500)
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode([
                        'status'    => 0,
                        'message'   => 'Something went wrong!!'
                    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
            }
        } catch (\Exception $ex) {
            return $res
                    ->withStatus(500)
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode([
                        'status'    => 0,
                        'message'   => $ex->getMessage()
                    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
        }
    }

    public function swap($req, $res, $args)
    {
        try {
            $post = (array)$req->getParsedBody();

            $detail = SchedulingDetail::find($args['id']);
            $detail->shifts = $post['shifts'];

            if($detail->save()) {
                /** To add new ShiftSwapping record */
                $swap = new ShiftSwapping;
                $swap->scheduling_detail_id = $post['scheduling_detail_id'];
                $swap->request_date         = date('Y-m-d');
                $swap->owner                = $post['owner'];
                $swap->delegator            = $post['delegator'];
                $swap->reason               = $post['reason'];
                $swap->swap_date            = $post['swap_date'];
                $swap->swap_shift           = $post['swap_shift'];
                $swap->represent_date       = $post['represent_date'];
                $swap->represent_shift      = $post['represent_shift'];
                $swap->save();

                return $res
                        ->withStatus(200)
                        ->withHeader("Content-Type", "application/json")
                        ->write(json_encode([
                            'status'    => 1,
                            'message'   => 'Updating successfully',
                            'swap'      => $swap
                        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
            } else {
                return $res
                    ->withStatus(500)
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode([
                        'status'    => 0,
                        'message'   => 'Something went wrong!!'
                    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
            }
        } catch (\Exception $ex) {
            return $res
                    ->withStatus(500)
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode([
                        'status'    => 0,
                        'message'   => $ex->getMessage()
                    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
        }
    }
}

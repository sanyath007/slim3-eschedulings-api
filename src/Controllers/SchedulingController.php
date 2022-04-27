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
use App\Models\Holiday;

class SchedulingController extends Controller
{
    public function getAll($req, $res, $args)
    {
        $depart     = $req->getQueryParam('depart');
        $division   = $req->getQueryParam('division');
        $month      = $req->getQueryParam('month');
        $sdate      = $month. '-01';
        $edate      = date('Y-m-t', strtotime($sdate));

        return $res->withJson([
            'schedulings'   => Scheduling::with('shifts','division')
                                ->with('shifts.person')
                                ->with('shifts.person.prefix','shifts.person.position')
                                ->when($month != '', function($q) use ($month) {
                                    $q->where('month', $month);
                                })
                                ->get(),
            'memberOfDep'   => Person::join('level', 'level.person_id', '=', 'personal.person_id')
                                ->where([
                                    'level.faction_id'    => '5',
                                    'level.depart_id'     => $depart,
                                ])
                                ->where('person_state', '1')
                                ->get()
        ]);
    }

    public function getById($req, $res, $args)
    {
        $scheduling = Scheduling::where('id', $args['id'])
                        ->with('shifts','division','controller')
                        ->with('shifts.person')
                        ->with('shifts.person.prefix','shifts.person.position')
                        ->first();

        return $res
                ->withStatus(200)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode([
                    'scheduling'    => $scheduling
                ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
    }

    public function initForm($req, $res, $args)
    {
        return $res->withJson([
            'factions'      => Faction::all(),
            'departs'       => Depart::all(),
            'divisions'     => Division::all(),
            'shifts'        => Shift::all(),
            'holidays'      => Holiday::all(),
        ]);
    }

    public function getMemberOfDivision($req, $res, $args)
    {
        $members = Person::join('level', 'level.person_id', '=', 'personal.person_id')
                    ->where(['level.ward_id'     => $args['division']])
                    ->where('person_state', '1')
                    ->get();

        return $res->withJson($members);
    }

    public function getMemberOfDepart($req, $res, $args)
    {
        $members = Person::join('level', 'level.person_id', '=', 'personal.person_id')
                    ->where(['level.depart_id'     => $args['depart']])
                    ->with('prefix','position')
                    ->where('person_state', '1')
                    ->get();

        return $res->withJson($members);
    }

    public function store($req, $res, $args)
    {
        try {
            $post = (array)$req->getParsedBody();

            $scheduling = new Scheduling;
            $scheduling->division_id    = $post['division'];
            $scheduling->month          = $post['month'];            
            $scheduling->year           = $post['year'];
            $scheduling->controller     = $post['controller'];
            $scheduling->total_persons  = $post['total_persons'];
            $scheduling->total_shifts   = $post['total_shifts'];
            $scheduling->remark         = $post['remark'];

            if($scheduling->save()) {
                $schedulingId = $scheduling->id;

                foreach($post['person_shifts'] as $ps) {
                    $shiftsText = implode(',', $ps['shifts']);

                    $detail = new SchedulingDetail;
                    $detail->scheduling_id  = $schedulingId;
                    $detail->person_id      = $ps['person']['person_id'];
                    $detail->shifts         = $shiftsText;
                    $detail->total_shift    = $ps['total_shift'];
                    $detail->working        = 0;
                    $detail->ot             = 0;
                    $detail->save();
                }

                return $res
                        ->withStatus(200)
                        ->withHeader("Content-Type", "application/json")
                        ->write(json_encode([
                            'status' => 1,
                            'message' => 'Inserting successfully',
                            'scheduling' => $scheduling
                        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
            } else {
                return $res
                    ->withStatus(500)
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode([
                        'status' => 0,
                        'message' => 'Something went wrong!!'
                    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
            }
        } catch (\Exception $ex) {
            return $res
                    ->withStatus(500)
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode([
                        'status' => 0,
                        'message' => $ex->getMessage()
                    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
        }
    }

    public function update($req, $res, $args)
    {
        try {
            $post = (array)$req->getParsedBody();

            $scheduling = Scheduling::find($args['id']);
            $scheduling->division_id    = $post['division'];
            $scheduling->month          = $post['month'];            
            $scheduling->year           = $post['year'];
            $scheduling->controller     = $post['controller'];
            $scheduling->total_persons  = $post['total_persons'];
            $scheduling->total_shifts   = $post['total_shifts'];
            $scheduling->remark         = $post['remark'];

            if($scheduling->save()) {
                /** TODO: To manipulate scheduling_detail data on scheduling is updated */
                $oldDetail = SchedulingDetail::where('scheduling_id', $args['id'])->delete();

                foreach($post['person_shifts'] as $ps) {
                    $shiftsText = implode(',', $ps['shifts']);

                    $detail = new SchedulingDetail;
                    $detail->scheduling_id  = $args['id'];
                    $detail->person_id      = $ps['person']['person_id'];
                    $detail->shifts         = $shiftsText;
                    $detail->save();
                }

                return $res
                        ->withStatus(200)
                        ->withHeader("Content-Type", "application/json")
                        ->write(json_encode([
                            'status' => 1,
                            'message' => 'Updating successfully',
                            'scheduling' => $scheduling
                        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
            } else {
                return $res
                    ->withStatus(500)
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode([
                        'status' => 0,
                        'message' => 'Something went wrong!!'
                    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
            }
        } catch (\Exception $ex) {
            return $res
                    ->withStatus(500)
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode([
                        'status' => 0,
                        'message' => $ex->getMessage()
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
                        'status' => 0,
                        'message' => 'Something went wrong!!'
                    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
            }
        } catch (\Exception $ex) {
            return $res
                    ->withStatus(500)
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode([
                        'status' => 0,
                        'message' => $ex->getMessage()
                    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
        }
    }
}

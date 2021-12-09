<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;
use App\Models\Person;

class PersonController extends Controller
{
    public function getAll($request, $response, $args)
    {
        $page       = (int)$request->getQueryParam('page');
        $fname      = $request->getQueryParam('fname');
        $faction    = $request->getQueryParam('faction');
        $depart     = $request->getQueryParam('depart');
        $division   = $request->getQueryParam('division');

        $model = Person::whereNotIn('person_state', [6,7,8,9,99])
                    ->join('level', 'personal.person_id', '=', 'level.person_id')
                    ->where('level.faction_id', '5')
                    ->when($depart != '', function($q) use ($depart) {
                        $q->where('level.depart_id', $depart);
                    })
                    ->when($division != '', function($q) use ($division) {
                        $q->where('level.ward_id', $division);
                    })
                    ->when($fname != '', function($q) use ($fname) {
                        $q->where('person_firstname', 'like', '%'.$fname.'%');
                    })
                    ->with('prefix','position','academic')
                    ->with('memberOf','memberOf.depart');

        $reg = paginate($model, 10, $page, $request);
        
        $data = json_encode($reg, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE);

        return $response
                ->withStatus(200)
                ->withHeader("Content-Type", "application/json")
                ->write($data);
    }

    public function getById($request, $response, $args)
    {
        $person = Person::where('person_id', $args['id'])
                    ->with('prefix','position')
                    ->first();
        
        return $response
                ->withStatus(200)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($person, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
    }

    public function getHeadOfFaction($request, $response, $args)
    {
        $person = Person::join('level', 'personal.person_id', '=', 'level.person_id')
                    ->where('level.faction_id', $args['faction'])
                    ->where('level.duty_id', '1')
                    ->with('prefix','position')
                    ->first();
        
        return $response
                ->withStatus(200)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($person, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
    }

    // public function store($request, $response, $args)
    // {
    //     try {
    //         $post = (array)$request->getParsedBody();

    //         $reg = new Registration;
    //         $reg->an = $post['an'];
    //         $reg->hn = $post['hn'];
    //         $reg->reg_date = $post['reg_date'];            
    //         $reg->reg_time = $post['reg_time'];
    //         $reg->ward = $post['ward'];
    //         $reg->bed = $post['bed'];
    //         $reg->code = $post['code'];
    //         $reg->lab_date = $post['lab_date'];
    //         $reg->lab_result = $post['lab_result'];
    //         $reg->dx = $post['dx'];
    //         $reg->symptom = $post['symptom'];
    //         $reg->reg_from = $post['reg_from'];
    //         $reg->reg_state = $post['reg_state'];
    //         $reg->remark = $post['remark'];

    //         if($reg->save()) {
    //             return $response
    //                     ->withStatus(200)
    //                     ->withHeader("Content-Type", "application/json")
    //                     ->write(json_encode([
    //                         'status' => 1,
    //                         'message' => 'Inserting successfully',
    //                         'reg' => $reg
    //                     ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
    //         } else {
    //             return $response
    //                 ->withStatus(500)
    //                 ->withHeader("Content-Type", "application/json")
    //                 ->write(json_encode([
    //                     'status' => 0,
    //                     'message' => 'Something went wrong!!'
    //                 ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
    //         }
    //     } catch (\Exception $ex) {
    //         return $response
    //                 ->withStatus(500)
    //                 ->withHeader("Content-Type", "application/json")
    //                 ->write(json_encode([
    //                     'status' => 0,
    //                     'message' => $ex->getMessage()
    //                 ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
    //     }
    // }

    // public function update($request, $response, $args)
    // {
    //     try {
    //         $post = (array)$request->getParsedBody();

    //         $reg = Registration::find($args['id']);
    //         /** get old bed for updating */
    //         $oldBed = $reg->bed;

    //         $reg->ward = $post['ward'];
    //         $reg->bed = $post['bed'];
    //         $reg->code = $post['code'];
    //         $reg->lab_date = $post['lab_date'];
    //         $reg->lab_result = $post['lab_result'];
    //         $reg->dx = $post['dx'];
    //         $reg->symptom = $post['symptom'];
    //         $reg->reg_from = $post['reg_from'];
    //         $reg->reg_state = $post['reg_state'];
    //         $reg->remark = $post['remark'];

    //         if($reg->save()) {
    //             /** if change bed do this */
    //             if($oldBed !== (int)$post['bed']) {
    //                 /** Update old bed */
    //                 Bed::where('bed_id', $oldBed)->update(['bed_status' => 0]);
    //                 /** Update new bed */
    //                 Bed::where('bed_id', $post['bed'])->update(['bed_status' => 1]);
    //             }

    //             return $response
    //                     ->withStatus(200)
    //                     ->withHeader("Content-Type", "application/json")
    //                     ->write(json_encode([
    //                         'status' => 1,
    //                         'message' => 'Updating successfully',
    //                         'reg' => $reg
    //                     ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
    //         } else {
    //             return $response
    //                 ->withStatus(500)
    //                 ->withHeader("Content-Type", "application/json")
    //                 ->write(json_encode([
    //                     'status' => 0,
    //                     'message' => 'Something went wrong!!'
    //                 ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
    //         }
    //     } catch (\Exception $ex) {
    //         return $response
    //                 ->withStatus(500)
    //                 ->withHeader("Content-Type", "application/json")
    //                 ->write(json_encode([
    //                     'status' => 0,
    //                     'message' => $ex->getMessage()
    //                 ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
    //     }
    // }

    // public function delete($request, $response, $args)
    // {
    //     try {
    //         if(Registration::where('book_id', $args['id'])->delete()) {
    //             return $response
    //                     ->withStatus(200)
    //                     ->withHeader("Content-Type", "application/json")
    //                     ->write(json_encode([
    //                         'status' => 1,
    //                         'message' => 'Deleting successfully',
    //                         'booking' => $booking
    //                     ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
    //         } else {
    //             return $response
    //                 ->withStatus(500)
    //                 ->withHeader("Content-Type", "application/json")
    //                 ->write(json_encode([
    //                     'status' => 0,
    //                     'message' => 'Something went wrong!!'
    //                 ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
    //         }
    //     } catch (\Exception $ex) {
    //         return $response
    //                 ->withStatus(500)
    //                 ->withHeader("Content-Type", "application/json")
    //                 ->write(json_encode([
    //                     'status' => 0,
    //                     'message' => $ex->getMessage()
    //                 ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE));
    //     }
    // }
}

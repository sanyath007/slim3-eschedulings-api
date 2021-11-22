<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;

class DashboardController extends Controller
{
    public function overallPatientStats($req, $res, $args)
    {
        $sdate = $args['month']. '-01';
        $edate = $args['month']. '-31';

        $sql="SELECT
                COUNT(case when (reg_date=DATE(NOW())) then an end) as new_case,
                COUNT(case when (dch_date is null) then an end) as stil_case,
                COUNT(an) as col_case,
                COUNT(case when (dch_date is not null) then an end) as dch_case,
                COUNT(case when (dch_date is not null and dch_type='4') then an end) as dead_case
                FROM registrations ";

        return $res->withJson(collect(DB::select($sql))->first());
    }
    
    public function overallBedStats($req, $res, $args)
    {
        $sdate = $args['month']. '-01';
        $edate = $args['month']. '-31';

        $sql="SELECT
                COUNT(bed_id) as all_beds,
                COUNT(case when (bed_status='1') then bed_id end) as used_beds,
                COUNT(case when (bed_status='0') then bed_id end) as empty_beds
                FROM beds ";

        return $res->withJson(collect(DB::select($sql, [$sdate, $edate]))->first());
    }
    
    public function admitDayStats($req, $res, $args)
    {
        $sdate = $args['month']. '-01';
        $edate = $args['month']. '-31';

        $sql="SELECT CAST(DAY(reg_date) AS SIGNED) AS d,
                COUNT(DISTINCT an) as num_pt
                FROM registrations
                WHERE (reg_date BETWEEN ? AND ?)
                GROUP BY CAST(DAY(reg_date) AS SIGNED) 
                ORDER BY CAST(DAY(reg_date) AS SIGNED) ";

        return $res->withJson(DB::select($sql, [$sdate, $edate]));
    }
    
    public function collectDayStats($req, $res, $args)
    {
        $sdate = $args['month']. '-01';
        $edate = $args['month']. '-31';

        $sql="SELECT CAST(DAY(reg_date) AS SIGNED) AS d,
                COUNT(DISTINCT an) as num_pt
                FROM registrations
                WHERE (reg_date BETWEEN ? AND ?)
                GROUP BY CAST(DAY(reg_date) AS SIGNED) 
                ORDER BY CAST(DAY(reg_date) AS SIGNED) ";
        
        $admitDay = DB::select($sql, [$sdate, $edate]);

        $data  = [];
        $collect = 0;
        foreach($admitDay as $ad) {
            $collect += (int)$ad->num_pt;

            array_push($data, [
                'd' => $ad->d,
                'collect' => $collect
            ]);
        }

        return $res->withJson($data);
    }
}

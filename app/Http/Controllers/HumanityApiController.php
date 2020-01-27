<?php

namespace App\Http\Controllers;

use App\Services\HumanityAPI\HumanityAPICore;
use App\Services\HumanityAPI\EmployeeShiftAndTimeClock;

/**
 * Class HumanityApiController
 * @package App\Http\Controllers
 */
class HumanityApiController extends Controller
{
    public function index(HumanityAPICore $humanityAPICore)
    {
        $results = [];
        $error = '';

        $allShifts = $humanityAPICore->getAll('shifts');

        if(isset($allShifts['errorMessage']) && !empty($allShifts['errorMessage'])) {
            $error = $allShifts['errorMessage'];
        } elseif (!is_null($allShifts) && !isset($allShifts['errorMessage'])) {
            $allTimeClocks = $humanityAPICore->getAll('timeclocks');
            $shifts = collect($allShifts)
                ->filter(function ($shift) {
                    $currentDate = getdate();
                    $currentDayStartTimestamp = strtotime($currentDate['mday'] . '-' . $currentDate['mon'] . '-' . $currentDate['year']);
                    $currentDayEndTimestamp = $currentDayStartTimestamp + 86400;

                    if ($currentDayStartTimestamp <= $shift->start_date->timestamp && $currentDayEndTimestamp > $shift->end_date->timestamp) {
                        return $shift;
                    }
                });

            foreach ($shifts as $shift) {
                $employeeShiftAndTimeClock = new EmployeeShiftAndTimeClock();
                $employeeShiftAndTimeClock->setEmployee($shift->employees[0]->name);
                $employeeShiftAndTimeClock->setScheduleName($shift->schedule_name);
                $employeeShiftAndTimeClock->setFullShift(
                    date("H:i", strtotime($shift->start_timestamp)) .
                    ' - ' .
                    date("H:i", strtotime($shift->end_timestamp))
                );
                foreach ($allTimeClocks as $timeClock) {
                    if ($shift->id === $timeClock->shift) {
                        $employeeShiftAndTimeClock->setFullTimeClock(
                            date("H:i", strtotime($timeClock->in_time->time)) .
                            ' - ' .
                            date("H:i", strtotime($timeClock->out_time->time))
                        );
                    }
                }
                $results[] = $employeeShiftAndTimeClock->toArray();
            }
        }

        $currentDay = date('d-m-Y', time());

        return view('humanity', [
            'results'       => $results,
            'currentDay'    => $currentDay,
            'error'         => $error
        ]);
    }
}

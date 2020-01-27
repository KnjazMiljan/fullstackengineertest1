<?php

namespace App\Services\HumanityAPI;

/**
 * Class EmployeeShiftAndTimeClock
 * @package App\Services\HumanityAPI
 */
class EmployeeShiftAndTimeClock
{
    /** @var string $employee */
    private $employee;

    /** @var string $scheduleName */
    private $scheduleName;

    /** @var string $fullShift */
    private $fullShift;

    /** @var string $fullTimeClock */
    private $fullTimeClock;

    /**
     * @param string $employee
     */
    public function setEmployee(string $employee) {
        $this->employee = $employee;
    }

    /**
     * @param string $scheduleName
     */
    public function setScheduleName(string $scheduleName) {
        $this->scheduleName = $scheduleName;
    }

    /**
     * @param string $fullShift
     */
    public function setFullShift(string $fullShift) {
        $this->fullShift = $fullShift;
    }

    /**
     * @param string $fullTimeClock
     */
    public function setFullTimeClock(string $fullTimeClock) {
        $this->fullTimeClock = $fullTimeClock;
    }

    /**
     * @return array
     */
    public function toArray() {
        return [
            'employee'      => $this->employee,
            'scheduleName'  => $this->scheduleName,
            'shift'         => $this->fullShift,
            'timeClock'     => $this->fullTimeClock
        ];
    }
}

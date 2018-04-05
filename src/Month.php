<?php

namespace Dzava\CalendarMonth;

use Carbon\Carbon;

class Month
{
    protected $month;

    protected $weekStart = 0;
    protected $formatter;

    public function __construct($month = null, $year = null)
    {
        $this->month = Carbon::create($year, $month);
    }

    /**
     * Get the first day of the month
     *
     * @return Carbon
     */
    public function firstDay()
    {
        return $this->month->copy()->startOfMonth();
    }

    /**
     * Get the last day of the month
     *
     * @return Carbon
     */
    public function lastDay()
    {
        return $this->firstDay()->addDay($this->month->daysInMonth - 1);
    }

    /**
     * Get the days of the month
     *
     * @return array
     */
    public function days()
    {
        return $this->createDatesBetween($this->firstDay(), $this->lastDay()->addDay());
    }

    /**
     * Get a list of days per week
     *
     * @return array
     */
    public function weeks()
    {
        $days = array_merge($this->previousFillerDays(), $this->days(), $this->nextFillerDays());

        return array_chunk($days, 7);
    }

    /**
     * Check if the given date belongs to the month
     *
     * @param $day
     * @return bool
     */
    public function contains($day)
    {
        if (!$day instanceof Carbon) {
            $day = Carbon::parse($day);
        }

        return $this->month->isSameMonth($day, true);
    }

    /**
     * Get the filler days for the previous month
     *
     * @return array
     */
    public function previousFillerDays()
    {
        $fillerDays = $this->firstDay()->dayOfWeek - $this->weekStart;

        if ($fillerDays < 0) {
            $fillerDays += 7;
        }

        return $this->createDatesBetween($this->firstDay()->subDays($fillerDays), $this->firstDay());
    }

    /**
     * Get the filler days for the next month
     *
     * @return array
     */
    public function nextFillerDays()
    {
        $fillerDays = 7 - $this->lastDay()->dayOfWeek + $this->weekStart;

        if ($fillerDays > 7) {
            $fillerDays = $fillerDays % 7;
        }

        return $this->createDatesBetween($this->lastDay()->addDay(), $this->lastDay()->addDays($fillerDays));
    }

    /**
     * Set the first day of the week
     *
     * @param $day
     * @return $this
     */
    public function weekStartsAt($day)
    {
        $this->weekStart = $day;

        return $this;
    }

    public function setFormatter($formatter)
    {
        $this->formatter = $formatter;

        return $this;
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     * @return array
     */
    protected function createDatesBetween($start, $end)
    {
        $dates = [];
        $count = $start->diffInDays($end);

        for ($dayIndex = 0; $dayIndex < $count; $dayIndex++) {
            $dates[] = $this->format($start->copy()->addDay($dayIndex));
        }

        return $dates;
    }

    /**
     * @param Carbon $day
     * @return mixed
     */
    protected function format($day)
    {
        if ($this->formatter === null) {
            return $day;
        }

        if (is_callable($this->formatter)) {
            return call_user_func($this->formatter, $day);
        }

        if (class_exists($this->formatter)) {
            return new $this->formatter($day);
        }

        return $day->format($this->formatter);
    }
}

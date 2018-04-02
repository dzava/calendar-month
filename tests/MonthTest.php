<?php

namespace Dzava\CalendarMonth\Test;

use Dzava\CalendarMonth\Month;
use PHPUnit\Framework\TestCase;

class MonthTest extends TestCase
{
    /** @test */
    public function it_returns_the_first_day()
    {
        $month = new Month(5, 2018);
        $this->assertEquals('2018-05-01 00:00:00', $month->firstDay());
    }

    /** @test */
    public function it_returns_the_last_day()
    {
        $month = new Month(5, 2018);

        $this->assertEquals('2018-05-31 00:00:00', $month->lastDay());
    }

    /** @test */
    public function it_returns_all_days_in_the_month()
    {
        $month = new Month(5, 2018);

        $days = $month->days();

        $this->assertCount(31, $days);
        $this->assertEquals('2018-05-01 00:00:00', $days[0]);
        $this->assertEquals('2018-05-31 00:00:00', $days[30]);
    }

    /**
     * @test
     * @dataProvider previousFillerDataProvider
     */
    public function it_returns_filler_days_for_the_previous_month($month, $count, $start, $end)
    {
        $month = new Month(...$month);

        $days = $month->previousFillerDays();

        $this->assertCount($count, $days);
        if ($count === 0) {
            return;
        }
        $this->assertEquals($start, $days[0]);
        $this->assertEquals($end, $days[$count - 1]);
    }

    /**
     * @test
     * @dataProvider nextFillerDataProvider
     */
    public function it_returns_filler_days_for_the_next_month($month, $count, $start, $end)
    {
        $month = new Month(...$month);

        $days = $month->nextFillerDays();

        $this->assertCount($count, $days);
        if ($count === 0) {
            return;
        }
        $this->assertEquals($start, $days[0]);
        $this->assertEquals($end, $days[$count - 1]);
    }

    /** @test */
    public function it_returns_the_weeks()
    {
        $month = new Month(5, 2018);

        $weeks = $month->weeks();

        $this->assertCount(5, $weeks);
        $this->assertEquals('2018-04-29 00:00:00', $weeks[0][0]);
        $this->assertEquals('2018-05-05 00:00:00', $weeks[0][6]);
        $this->assertEquals('2018-05-06 00:00:00', $weeks[1][0]);
        $this->assertEquals('2018-05-12 00:00:00', $weeks[1][6]);
        $this->assertEquals('2018-05-13 00:00:00', $weeks[2][0]);
        $this->assertEquals('2018-05-19 00:00:00', $weeks[2][6]);
        $this->assertEquals('2018-05-20 00:00:00', $weeks[3][0]);
        $this->assertEquals('2018-05-26 00:00:00', $weeks[3][6]);
        $this->assertEquals('2018-05-27 00:00:00', $weeks[4][0]);
        $this->assertEquals('2018-06-02 00:00:00', $weeks[4][6]);
    }

    /** @test */
    public function can_check_if_a_date_belongs_to_the_month()
    {
        $month = new Month(5, 2018);

        $this->assertFalse($month->contains('2018-04-30 23:59:59'));
        $this->assertTrue($month->contains('2018-05-01 00:00:00'));
        $this->assertTrue($month->contains('2018-05-31 23:59:59'));
        $this->assertFalse($month->contains('2018-06-01 00:00:00'));

        $this->assertFalse($month->contains('2017-05-01 00:00:00'));
        $this->assertFalse($month->contains('2017-05-05 00:00:00'));
        $this->assertFalse($month->contains('2017-05-31 23:59:59'));
    }

    /** @test */
    public function can_set_the_first_day_of_the_week()
    {
        $month = new Month(5, 2018);

        $month->weekStartsAt(0);
        $this->assertCount(2, $month->previousFillerDays());
        $this->assertCount(2, $month->nextFillerDays());
        $this->assertArrayStartsAndEnds($month->previousFillerDays(), '2018-04-29', '2018-04-30');
        $this->assertArrayStartsAndEnds($month->nextFillerDays(), '2018-06-01', '2018-06-02');

        $month->weekStartsAt(1);
        $this->assertCount(1, $month->previousFillerDays());
        $this->assertCount(3, $month->nextFillerDays());
        $this->assertArrayStartsAndEnds($month->previousFillerDays(), '2018-04-30', '2018-04-30');
        $this->assertArrayStartsAndEnds($month->nextFillerDays(), '2018-06-01', '2018-06-03');

        $month->weekStartsAt(2);
        $this->assertCount(0, $month->previousFillerDays());
        $this->assertCount(4, $month->nextFillerDays());
        $this->assertArrayStartsAndEnds($month->nextFillerDays(), '2018-06-01', '2018-06-04');

        $month->weekStartsAt(3);
        $this->assertCount(6, $month->previousFillerDays());
        $this->assertCount(5, $month->nextFillerDays());
        $this->assertArrayStartsAndEnds($month->previousFillerDays(), '2018-04-25', '2018-04-30');
        $this->assertArrayStartsAndEnds($month->nextFillerDays(), '2018-06-01', '2018-06-05');

        $month->weekStartsAt(4);
        $this->assertCount(5, $month->previousFillerDays());
        $this->assertCount(6, $month->nextFillerDays());
        $this->assertArrayStartsAndEnds($month->previousFillerDays(), '2018-04-26', '2018-04-30');
        $this->assertArrayStartsAndEnds($month->nextFillerDays(), '2018-06-01', '2018-06-06');

        $month->weekStartsAt(5);
        $this->assertCount(4, $month->previousFillerDays());
        $this->assertCount(0, $month->nextFillerDays());
        $this->assertArrayStartsAndEnds($month->previousFillerDays(), '2018-04-27', '2018-04-30');

        $month->weekStartsAt(6);
        $this->assertCount(3, $month->previousFillerDays());
        $this->assertCount(1, $month->nextFillerDays());
        $this->assertArrayStartsAndEnds($month->previousFillerDays(), '2018-04-28', '2018-04-30');
        $this->assertArrayStartsAndEnds($month->nextFillerDays(), '2018-06-01', '2018-06-01');
    }

    public function previousFillerDataProvider()
    {
        return [
            [[1, 2018], 1, '2017-12-31 00:00:00', '2017-12-31 00:00:00'],
            [[2, 2018], 4, '2018-01-28 00:00:00', '2018-01-31 00:00:00'],
            [[3, 2018], 4, '2018-02-25 00:00:00', '2018-02-28 00:00:00'],
            [[4, 2018], 0, null, null],
            [[12, 2018], 6, '2018-11-25 00:00:00', '2018-11-30 00:00:00'],
        ];
    }

    public function nextFillerDataProvider()
    {
        return [
            [[1, 2018], 3, '2018-02-01 00:00:00', '2018-02-03 00:00:00'],
            [[2, 2018], 3, '2018-03-01 00:00:00', '2018-03-03 00:00:00'],
            [[3, 2018], 0, null, null],
            [[12, 2018], 5, '2019-01-01 00:00:00', '2019-01-05 00:00:00'],
        ];
    }

    protected function assertArrayStartsAndEnds($array, $start, $end)
    {
        $this->assertEquals($array[0], $start . ' 00:00:00');
        $this->assertEquals($array[count($array) - 1], $end . ' 00:00:00');
    }

    private function debug(Month $month)
    {
        echo PHP_EOL;
        foreach ($month->weeks()[0] as $day) {
            echo $day->format('D ');
        }
        echo PHP_EOL;
        foreach ($month->weeks() as $week) {
            foreach ($week as $day) {
                echo $day->format('d') . '  ';
            }
            echo PHP_EOL;
        }
    }
}

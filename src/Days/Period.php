<?php

declare(strict_types=1);

namespace TimeCollision\Days;

use DateTime;
use Exception;
use TimeCollision\Exceptions\InvalidPeriodException;
use TimeCollision\Exceptions\InvalidTimeException;

class Period
{
    private DateTime $start;

    private DateTime $end;

    public function __construct(string $start, string $end)
    {
        $onlyToValidateTime = date('Y-m-d');

        try {
            $start = new DateTime("{$onlyToValidateTime} {$start}");
            $end   = new DateTime("{$onlyToValidateTime} {$end}");
        } catch (Exception $e) {
            throw new InvalidTimeException($e->getMessage());
        }

        if ($start > $end) {
            throw new InvalidPeriodException('The end time must be greater than the start time of the period');
        }

        $this->start = $start;
        $this->end = $end;
    }

    /** @return \TimeCollision\Days\Time */
    public function getStart(): Time
    {
        return new Time($this->start);
    }

    /** @return \TimeCollision\Days\Time */
    public function getEnd(): Time
    {
        return new Time($this->end);
    }
}

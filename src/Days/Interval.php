<?php

declare(strict_types=1);

namespace TimeCollision\Days;

use DateTime;
use Exception;
use TimeCollision\Exceptions\InvalidDateTimeException;
use TimeCollision\Exceptions\InvalidPeriodException;
use TimeCollision\Exceptions\InvalidTimeException;

class Interval
{
    private DateTime $start;

    private DateTime $end;

    public function __construct(string $start, string $end)
    {
        try {
            $start = new DateTime($start);
            $end   = new DateTime($end);
        } catch (Exception $e) {
            throw new InvalidDateTimeException($e->getMessage());
        }

        if ($start > $end) {
            throw new InvalidDateTimeException(
                'The end datetime must be greater than the start datetime of the interval'
            );
        }

        $this->start = $start;
        $this->end = $end;
    }

    /** @return \DateTime */
    public function getStart(): DateTime
    {
        return $this->start;
    }

    /** @return \DateTime */
    public function getEnd(): DateTime
    {
        return $this->end;
    }
}

<?php

declare(strict_types=1);

namespace TimeCollision;

use DateTime;
use Exception;
use TimeCollision\Days\Week;
use TimeCollision\Days\Year;
use TimeCollision\Exceptions\InvalidDateTimeException;
use TimeCollision\Ranges\Chunks;
use TimeCollision\Ranges\Fillings;
use TimeCollision\Ranges\Minutes;
use TimeCollision\Ranges\RangeMaker;

class Collision
{
    private DateTime $rangeStart;

    private DateTime $rangeEnd;

    private Week $week;

    private Year $year;

    private Fillings $fillings;

    private Defaults $defaults;

    protected ?Minutes $minutesObject = null;

    public function __construct(string $start, ?string $end = null)
    {
        try {
            $customStart = new DateTime($start);

            $customEnd = clone $customStart;
            $customEnd->setTime(23, 59);

            if ($end !== null) {
                $customEnd = new DateTime($end);
            }
        } catch (Exception $e) {
            throw new InvalidDateTimeException($e->getMessage());
        }

        if ($customStart > $customEnd) {
            throw new InvalidDateTimeException('The end date must be greater than the start date of the period');
        }

        if ($customEnd->format('H:i') === '00:00') {
            $customEnd->modify('+ 24 hours');
        }

        if ($customEnd->format('H:i') === '23:59') {
            $customEnd->modify('+ 1 minute');
        }

        $this->rangeStart = $customStart;
        $this->rangeEnd   = $customEnd;

        $this->week     = new Week($this);
        $this->year     = new Year($this);
        $this->fillings = new Fillings($this);
        $this->defaults = new Defaults($this);
    }

    public function getStartOfRange(): DateTime
    {
        return $this->rangeStart;
    }

    public function getEndOfRange(): DateTime
    {
        return $this->rangeEnd;
    }

    public function fromWeek(): Week
    {
        return $this->week;
    }

    public function fromYear(): Year
    {
        return $this->year;
    }

    public function fromFillings(): Fillings
    {
        return $this->fillings;
    }

    public function fromDefaults(): Defaults
    {
        return $this->defaults;
    }

    public function fromMinutes(): Minutes
    {
        if ($this->minutesObject === null) {
            $calculation = new RangeMaker($this);
            $this->minutesObject = $calculation->makeMinutesRange();
        }

        return $this->minutesObject;
    }

    public function fromChunks(): Chunks
    {
        return $this->fromMinutes()->getChunks();
    }

    /**
     * Reinicia o objeto que calcula os minutos.
     */
    public function forceMinutesRecalculation(): void
    {
        $this->minutesObject = null;
    }
}

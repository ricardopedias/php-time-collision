<?php

declare(strict_types=1);

namespace TimeCollision\Ranges;

use DateTime;
use Exception;
use TimeCollision\Collision;
use TimeCollision\Exceptions\InvalidDateTimeException;

class Fillings
{
    protected Collision $collision;

    /** @var array<int, array> */
    protected array $fills = [];

    /** @var array<int, array> */
    protected array $cumulativeFills = [];

    public function __construct(Collision $collision)
    {
        $this->collision = $collision;
    }

    /**
     * Utiliza o período especificado.
     * @param string $start
     * @param string $end
     */
    public function fill(string $start, string $end): void
    {
        $this->collision->forceMinutesRecalculation();

        try {
            $start = new DateTime($start);
            $end   = new DateTime($end);
        } catch (Exception $e) {
            throw new InvalidDateTimeException($e->getMessage());
        }

        if ($end < $start) {
            throw new InvalidDateTimeException(
                'The end datetime must be greater than the start datetime of the period'
            );
        }

        $this->fills[] = [$start, $end];
    }

    /**
     * Utiliza o período especificado.
     * Os minutos são distribuídos para as lacunas seguintes até acabarem.
     * @param string $start
     * @param string $end
     */
    public function fillCumulative(string $start, string $end): void
    {
        $this->collision->forceMinutesRecalculation();

        try {
            $start = new DateTime($start);
            $end   = new DateTime($end);
        } catch (Exception $e) {
            throw new InvalidDateTimeException($e->getMessage());
        }

        if ($end < $start) {
            throw new InvalidDateTimeException(
                'The end datetime must be greater than the start datetime of the period'
            );
        }

        $this->cumulativeFills[] = [$start, $end];
    }

    /**
     * Obtém as lacunas onde os minutos especificados se encaixam
     * @return array<int, \TimeCollision\Ranges\Interval>
     */
    public function getFittingsFor(int $amountMinutes): array
    {
        return $this->collision->fromMinutes()->getChunks()->getFittings($amountMinutes);
    }

    /**
     * Obtém as lacunas disponíveis entre a data inicial e a final
     * @return array<int, \TimeCollision\Ranges\Interval>
     */
    public function getFittingsBetween(string $start, string $end): array
    {
        return $this->collision->fromMinutes()->getChunks()->getFillables($start, $end);
    }


    /** @return array<int, array> */
    public function getAll(): array
    {
        return $this->fills;
    }

    /** @return array<int, array> */
    public function getAllCumulatives(): array
    {
        return $this->cumulativeFills;
    }
}

<?php

declare(strict_types=1);

namespace TimeCollision\Ranges;

use DateTime;
use Exception;
use SplFixedArray;
use TimeCollision\Ranges\Interval;
use TimeCollision\Exceptions\InvalidDateTimeException;

/**
 * Esta classe é responsável pela extração de lacunas dentro
 * de um range de minutos.
*/
class Chunks
{
    /** @var SplFixedArray<int> */
    private SplFixedArray $range;

    private DateTime $start;

    private DateTime $end;

    private Minutes $minutes;

    public function __construct(Minutes $minutes)
    {
        $this->minutes = $minutes;
        $this->start   = $minutes->getStartOfRange();
        $this->end     = $minutes->getEndOfRange();
        $this->range   = $minutes->getRangeVector();
    }

    /**
     * Obtém os breakpoints para o inicio de novos dias dentro do range.
     * @return array<int, DateTime>
     * @see TimeCollision\RangeMaker::allowDayOfWeeks()
     */
    public function getDays(): array
    {
        $walk = clone $this->start;

        $days = [];
        $days[1] = clone $walk;

        $iterations = $this->range->getSize() / 60 / 24; /// dias disponiveis no range

        while ($iterations > 1) {
            $walk->modify('next day');
            $walk->setTime(0, 0);
            if ($walk > $this->end) {
                break;
            }
            $minute = $this->minutes->getMinutesBetween($days[1], $walk);
            $days[$minute] = clone $walk;
        }

        return $days;
    }

    /**
     * Obtém as lacunas disponiveis para a quantidade de minutos especificada.
     * @return array<int, \TimeCollision\Ranges\Interval>
     */
    public function getFittings(int $minutes): array
    {
        $chunks = $this->extractFillablesChunks($this->start, $this->end);

        $chunksDateTime = [];
        foreach ($chunks as $minuteIndex => $minutesAmount) {
            if ($minutesAmount >= $minutes) {
                $chunksDateTime[] = $this->makeIntervalChunks($minuteIndex, $minutesAmount);
            }
        }

        return $chunksDateTime;
    }

    /**
     * Obtém os períodos disponiveis entre a data inicial e a final
     * @return array<int, \TimeCollision\Ranges\Interval>
     */
    public function getFillables(string $start, string $end): array
    {
        $start  = $this->createDateTimeParam($start);
        $end    = $this->createDateTimeParam($end);
        $chunks = $this->extractFillablesChunks($start, $end);

        $chunksDateTime = [];
        foreach ($chunks as $minuteIndex => $minutesAmount) {
            $chunksDateTime[] = $this->makeIntervalChunks($minuteIndex, $minutesAmount);
        }

        return $chunksDateTime;
    }

    /**
     * @return \TimeCollision\Ranges\Interval
     */
    private function makeIntervalChunks(int $minuteIndex, int $minutesAmount): Interval
    {
        $startMinute = $minuteIndex === 0 ? 0 : $minuteIndex + 1;
        $endMinute   = $minuteIndex + $minutesAmount;

        return new Interval(
            $this->minutes->getDateTimeFromMinute($startMinute)->format('Y-m-d H:i:s'),
            $this->minutes->getDateTimeFromMinute($endMinute)->format('Y-m-d H:i:s')
        );
    }

    private function isFillable(int $minuteIndex, int $minuteType, DateTime $start, DateTime $end): bool
    {
        $currentDate = $this->minutes->getDateTimeFromMinute($minuteIndex + 1);
        if ($currentDate < $start || $currentDate > $end) {
            return false;
        }

        if ($minuteType !== Minutes::ALLOWED) {
            return false;
        }

        return true;
    }

    /** @return array<int> */
    private function grabOnlyFillables(DateTime $start, DateTime $end): array
    {
        $fillables = [];
        foreach ($this->range as $minuteIndex => $minuteType) {
            if ($this->isFillable($minuteIndex, $minuteType, $start, $end) === true) {
                $fillables[] = $minuteIndex;
                continue;
            }
        }

        return $fillables;
    }

    /**
     * @param array<int> $minutesList
     * @return array<int>
     */
    private function grabCheckpointsForChunks(array $minutesList): array
    {
        $last = -2;
        // -2 foi usado para a soma com +1 não ser zero
        // Porque zero pode coincidir com o indice do primeiro minuto
        // e a verificação de sequência não funcionaria

        $checkpoints = [];
        foreach ($minutesList as $minuteIndex) {
            // o último minuto e o atual são uma sequência?
            if ($minuteIndex !== $last + 1) {
                $checkpoints[] = $minuteIndex;
            }
            $last = $minuteIndex;
        }

        return $checkpoints;
    }

    /**
     * @param array<int> $minutesList
     * @param array<int> $checkpoints
     * @return array<int>
     */
    private function computeMinutesFromCheckpoints(array $minutesList, array $checkpoints): array
    {
        $chunks = [];

        foreach ($minutesList as $minuteIndex) {
            if (in_array($minuteIndex, $checkpoints) === true) {
                $chunks[$minuteIndex] = 0;
            }

            $minuteCheckpoint = array_key_last($chunks);
            $chunks[$minuteCheckpoint]++;
        }

        return $chunks;
    }

    /**
     * Obtém os períodos disponiveis entre a data inicial e a final
     * @return array<int>
     */
    private function extractFillablesChunks(DateTime $start, DateTime $end): array
    {
        $minutesList = $this->grabOnlyFillables($start, $end);
        $checkpoints = $this->grabCheckpointsForChunks($minutesList);
        $withMinutes = $this->computeMinutesFromCheckpoints($minutesList, $checkpoints);

        return $withMinutes;
    }

    private function createDateTimeParam(string $date): DateTime
    {
        try {
            $date = new DateTime($date);
        } catch (Exception $e) {
            throw new InvalidDateTimeException($e->getMessage());
        }

        if ($date < $this->start || $date > $this->end) {
            throw new InvalidDateTimeException('The specified date and time is out of range');
        }

        return $date;
    }
}

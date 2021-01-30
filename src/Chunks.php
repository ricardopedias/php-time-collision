<?php

declare(strict_types=1);

namespace Time;

use DateTime;
use Exception;
use SplFixedArray;
use Time\Exceptions\InvalidDateTimeException;

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
        $this->start   = $minutes->startRange();
        $this->end     = $minutes->endRange();
        $this->range   = $minutes->range();
    }

    /**
     * Obtém os breakpoints para o inicio de novos dias dentro do range.
     * @return array<int, DateTime>
     * @see Time\Calculation::allowDayOfWeeks()
     */
    public function days(): array
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
            $minute = $this->minutes->beetwen($days[1], $walk);
            $days[$minute] = clone $walk;
        }

        return $days;
    }

    /**
     * Obtém as lacunas disponiveis para a quantidade de minutos especificada.
     * @return array<int, array<\DateTime>>
     */
    public function fittings(int $minutes): array
    {
        $chunks = $this->extractFillablesChunks($this->start, $this->end);

        $chunksDateTime = [];
        foreach ($chunks as $minuteIndex => $minutesAmount) {
            if ($minutesAmount >= $minutes) {
                $chunksDateTime[] = $this->makeDateTimeChunks($minuteIndex, $minutesAmount);
            }
        }

        return $chunksDateTime;
    }

    /**
     * Obtém os períodos disponiveis entre a data inicial e a final
     * @return array<int, array<\DateTime>>
     */
    public function fillables(string $start, string $end): array
    {
        $start  = $this->createDateTimeParam($start);
        $end    = $this->createDateTimeParam($end);
        $chunks = $this->extractFillablesChunks($start, $end);

        $chunksDateTime = [];
        foreach ($chunks as $minuteIndex => $minutesAmount) {
            $chunksDateTime[] = $this->makeDateTimeChunks($minuteIndex, $minutesAmount);
        }

        return $chunksDateTime;
    }

    /** 
     * @return array<\DateTime> 
     */
    private function makeDateTimeChunks(int $minuteIndex, int $minutesAmount): array
    {
        $startMinute = $minuteIndex === 0 ? 0 : $minuteIndex + 1;
        $endMinute   = $minuteIndex + $minutesAmount;

        return [
            $this->getDateTimeFromMinute($startMinute),
            $this->getDateTimeFromMinute($endMinute)
        ];
    }

    private function isFillable(int $minuteIndex, int $minuteType, DateTime $start, DateTime $end): bool
    {
        $currentDate = $this->getDateTimeFromMinute($minuteIndex + 1);
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

    /**
     * Obtém os períodos preenchidos entre a data inicial e a final
     * @todo Ainda não está implementado
     * @return array<int, array>
     */
    public function filleds(string $start, string $end): array
    {
        $start = $this->createDateTimeParam($start);
        $end = $this->createDateTimeParam($end);

        return [];
        //return $this->chunksByType($this->range, Minutes::ALLOWED, $start, $end);
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

    /**
     * Devolve os minutos bloqueados para uso.
     * @return SplFixedArray<\DateTime>
     */
    public function unused(): SplFixedArray
    {
        $list = $this->minutes->range(Minutes::UNUSED);
        $list = array_map(fn($minute) => $this->getDateTimeFromMinute($minute), $list->toArray());
        return SplFixedArray::fromArray($list);
    }

    /**
     * Devolve os minutos que podem ser usados.
     * @return SplFixedArray<\DateTime>
     */
    public function allowed(): SplFixedArray
    {
        $list = $this->minutes->range(Minutes::ALLOWED);
        $list = array_map(fn($minute) => $this->getDateTimeFromMinute($minute), $list->toArray());
        return SplFixedArray::fromArray($list);
    }

    /**
     * Devolve os minutos usados dentro do horário comercial.
     * @return SplFixedArray<\DateTime>
     */
    public function filled(): SplFixedArray
    {
        $list = $this->minutes->range(Minutes::FILLED);
        $list = array_map(fn($minute) => $this->getDateTimeFromMinute($minute), $list->toArray());
        return SplFixedArray::fromArray($list);
    }

    private function getDateTimeFromMinute(int $minute): DateTime
    {
        $moment = clone $this->start;
        $moment->modify("+ {$minute} minutes");
        return $moment;
    }
}

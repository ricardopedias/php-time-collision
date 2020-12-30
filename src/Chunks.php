<?php

declare(strict_types=1);

namespace Time;

use DateTime;
use Exception;
use SplFixedArray;
use Time\Exceptions\InvalidDateException;
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
     * @return array<int, array>
     */
    public function fittings(int $minutes): array
    {
        $result = [];

        $chunksList = $this->chunksByType($this->range, Minutes::ALLOWED, $this->start, $this->end);
        foreach ($chunksList as $minuteIndex => $chunk) {
            $startDate = clone $chunk[0];
            $endDate = clone $chunk[1];

            // Verifica se a lacuna cabe os minutos necessários
            if ($startDate->modify("+ {$minutes} minutes") <= $endDate) {
                $result[$minuteIndex] = [
                    $chunk[0],
                    $chunk[1]
                ];
            }
        }

        return $result;
    }

    /**
     * Obtém os períodos preenchidos entre a data inicial e a final
     * @return array<int, array>
     */
    public function fillables(string $start, string $end): array
    {
        $start = $this->createDateTimeParam($start);
        $end = $this->createDateTimeParam($end);

        return $this->chunksByType($this->range, Minutes::ALLOWED, $start, $end);
    }

    /**
     * Obtém os períodos preenchidos entre a data inicial e a final
     * @return array<int, array>
     */
    public function filleds(string $start, string $end): array
    {
        $start = $this->createDateTimeParam($start);
        $end = $this->createDateTimeParam($end);

        return $this->chunksByType($this->range, Minutes::ALLOWED, $start, $end);
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
     * Método recursivo. Cada iteração extrai um pedaço do período
     * contendo uma sequência de minutos disponíveis
     * @param SplFixedArray<int> $range
     * @param int $type
     * @param DateTime $start
     * @param DateTime $end
     * @param int $startIndex
     * @param array<int, array<int, DateTime>> $result
     * @return array<int, array<int, DateTime>>
     */
    private function chunksByType(
        SplFixedArray $range,
        int $type,
        DateTime &$start,
        DateTime &$end,
        int &$startIndex = 0,
        array &$result = []
    ): array {
        $chunk  = [];
        $forwardIndex = $startIndex;
        $hasAllowed = false;

        for ($index = $startIndex; $index < $range->getSize(); $index++) {
            $minute = $index;
            $bit    = $range[$index];

            $isSecondChunk = $chunk !== [] // existe um ou mais minutos armazenados
                && isset($chunk[$minute - 1]) === false; // o minuto atual não é uma sequência do anterior
            if ($isSecondChunk === true) {
                // Apenas um pedaço deve ser devolvido por vez
                $forwardIndex = $index;
                break;
            }

            $isChunk = $bit === $type // é do tipo especificado
                && ($minute === 0 || isset($range[$minute]) === true); // é uma sequência;

            if ($isChunk === true) {
                $chunk[$minute] = $bit;
                $hasAllowed = true;
            }
        }

        if ($hasAllowed === false) {
            return $result;
        }

        $startMinute = (int)key($chunk);
        $endMinute   = (int)array_key_last($chunk);
        $result[$startMinute] = [
            // +1 porque os indices dos minutos são a partir de zero
            $this->getDateTimeFromMinute($startMinute + 1),
            $this->getDateTimeFromMinute($endMinute + 1)
        ];

        // Busca por outros pedaços
        $this->chunksByType($range, $type, $start, $end, $forwardIndex, $result);

        return $result;
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

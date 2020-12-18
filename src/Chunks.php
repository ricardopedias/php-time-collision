<?php

declare(strict_types=1);

namespace Time;

use DateTime;
use SplFixedArray;

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
        return $this->chunkFittings($this->range, $minutes);
    }

    /**
     * Método recursivo. Cada iteração extrai um pedaço do período
     * contendo uma sequência de minutos disponíveis
     * @param SplFixedArray<int, int|null> $range
     * @param int $minutes
     * @param int $index
     * @param array<int, array<int>> $result
     * @return array<int, array<int>>
     */
    private function chunkFittings(SplFixedArray $range, int $minutes, int &$startIndex = 0, array &$result = []): array
    {
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

            $isChunk = $bit === Minutes::ALLOWED // é um horário vago
                && ($minute === 0 || isset($range[$minute]) === true); // é uma sequência;

            if ($isChunk === true) {
                $chunk[$minute] = $bit;
                $hasAllowed = true;
            }
        }

        if ($hasAllowed === false) {
            return $result;
        }

        // Verifica se a lacuna cabe os minutos necessários
        $startMinute = (int)key($chunk);
        $endMinute   = (int)array_key_last($chunk);
        if ($minutes <= ($endMinute - $startMinute)) {
            $result[$startMinute] = [
                // +1 porque os indices dos minutos são a partir de zero
                $this->getDateTime($startMinute + 1), 
                $this->getDateTime($endMinute + 1)
            ];
        }

        // Busca por outros pedaços
        $this->chunkFittings($range, $minutes, $forwardIndex, $result);
        
        return $result;
    }

    /**
     * Devolve os minutos bloqueados para uso.
     * @return SplFixedArray<int>
     */
    public function unused(): SplFixedArray
    {
        $list = $this->minutes->range(Minutes::UNUSED);
        $list = array_map(fn($minute) => $this->getDateTime($minute), $list->toArray());
        return SplFixedArray::fromArray($list);
    }

    /**
     * Devolve os minutos que podem ser usados.
     * @return SplFixedArray<int>
     */
    public function allowed(): SplFixedArray
    {
        $list = $this->minutes->range(Minutes::ALLOWED);
        $list = array_map(fn($minute) => $this->getDateTime($minute), $list->toArray());
        return SplFixedArray::fromArray($list);
    }

    /**
     * Devolve os minutos usados dentro do horário comercial.
     * @return SplFixedArray<int>
     */
    public function filled(): SplFixedArray
    {
        $list = $this->minutes->range(Minutes::FILLED);
        $list = array_map(fn($minute) => $this->getDateTime($minute), $list->toArray());
        return SplFixedArray::fromArray($list);
    }

    private function getDateTime(int $minute): DateTime
    {
        $moment = clone $this->start;
        $moment->modify("+ {$minute} minutes");
        return $moment;
    }
}

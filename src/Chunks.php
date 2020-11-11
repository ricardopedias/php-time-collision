<?php

declare(strict_types=1);

namespace Time;

use DateTime;

/**
 * Esta classe é responsável pela extração de lacunas dentro
 * de um range de minutos.
*/
class Chunks
{
    /** @var array<int> */
    private array $range = [];

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
     * @return array<int, array<DateTime>>
     */
    public function days(): array
    {
        $walk = clone $this->start;

        $days = [];
        $days[1] = clone $walk;

        $iterations = count($this->range)/60/24; /// dias disponiveis no range

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
     * @return array<int, array<DateTime>>
     */
    public function fittings(int $minutes): array
    {
        return $this->chunkFittings($this->range, $minutes);
    }

    /**
     * Método recursivo. Cada iteração extrai um pedaço do período
     * contendo uma sequência de minutos disponíveis
     * @param array<int, int> $range
     * @param int $minutes
     * @param array<int, array<int>> $result
     * @return array<int, array<int>>
     */
    private function chunkFittings(array $range, int $minutes, array &$result = []): array
    {
        $chunk  = [];
        $hasAllowed = false;
        
        foreach ($range as $minute => $bit) {
            $isSecondChunk = $chunk !== [] // existe um ou mais minutos armazenados
                && isset($chunk[$minute - 1]) === false; // o minuto atual não é uma sequência do anterior
            if ($isSecondChunk === true) {
                // Apenas um pedaço deve ser devolvido por vez
                break;
            }

            $isChunck = $bit === Minutes::ALLOWED // é um horário vago
                && ($minute === 1 || isset($range[$minute - 1]) === true); // é uma sequência;

            if ($isChunck === true) {
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
                $this->getDateTime($startMinute),
                $this->getDateTime($endMinute)
            ];
        }

        // Extrai os itens já analisados
        $startRange = key($range);
        for ($minute = $startRange; $minute <= $endMinute; $minute++) {
            unset($range[$minute]);
        }

        // Busca por outros pedaços
        $this->chunkFittings($range, $minutes, $result);
        
        return $result;
    }

    private function getDateTime(int $minute): DateTime
    {
        $moment = clone $this->start;
        $moment->modify("+ {$minute} minutes");
        return $moment;
    }
}

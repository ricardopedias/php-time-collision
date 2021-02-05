<?php

declare(strict_types=1);

namespace Tests;

use TimeCollision\Collision;
use DateTime;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;
use SplFixedArray;
use TimeCollision\Minutes;

class TestCase extends PhpUnitTestCase
{
    protected float $start = 0;

    protected ?Datetime $dateStart = null;

    protected ?DateTime $dateEnd = null;

    protected function setUp(): void
    {
        $this->startBenchmark();

        // Determina a análise de 1 hora
        $this->dateStart = new DateTime('2020-11-01 12:00:00');
        $this->dateEnd   = new DateTime('2020-11-01 13:00:00');
    }

    protected function convert(int $size)
    {
        $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }

    protected function startBenchmark()
    {
        $this->start = microtime(true);
    }

    protected function benchmark(Collision $object)
    {
        echo "-----------------------------------------------------\n";
        echo "BENCHMARK:\n";
        echo "-----------------------------------------------------\n";
        $totalSeconds = round(microtime(true) - $this->start);
        echo "Test: " . get_called_class() . "::" .  debug_backtrace()[1]['function'] . "()\n";
        echo "Execution: {$totalSeconds} seconds\n";
        echo "Memory: " . $this->convert(memory_get_peak_usage(false)) . "\n";
        echo "\n";
        $this->timeline($object);
        die;
    }

    protected function period(string $notation, int $value)
    {
        if ($notation === '0..0') {
            return [];
        }

        $index = explode("..", $notation);
        $start = (int)$index[0];
        $end   = (int)$index[1];

        $result = [];
        for ($x = $start; $x<= $end; $x++) {
            $result[$x] = $value;
        }
        return $result;
    }

    protected function makeRange(string ...$periods): SplFixedArray
    {
        $list = [];
        foreach($periods as $range) {
            $list += $this->period($range, 9);
        }
        
        return SplFixedArray::fromArray(array_keys($list));
    }

    protected function rangeToDatetime(string $start, SplFixedArray $range)
    {
        $list = [];

        foreach($range as $index => $minute) {
            $moment = new Datetime($start);
            $moment->modify("+ {$minute} minutes");
            $list[$index] = $moment;
        }

        return SplFixedArray::fromArray($list);
    }

    protected function printCli($variable)
    {
        fwrite(STDERR, print_r($variable, TRUE));
    }

    public function timeline(Collision $object): void
    {
        $filledTime   = [];
        $minutes      = [];

        // ░
        // ▒
        // ▓

        $unused   = "\e[39m■\e[0m";
        // $business = "□";
        $allowed  = "\e[32m_\e[0m";
        $filled   = "\e[32m●\e[0m";
        $lost      = "○";
        $lost      = "\e[93m□\e[0m";

        // Marca o horario comercial
        foreach($object->minutes()->range() as $index => $bit) {

            $minute = $index+1;
            
            switch($bit){
                case Minutes::UNUSED: $signal = $unused; break;
                case Minutes::ALLOWED: $signal = $allowed; break;
                case Minutes::FILLED: $signal = $filled; break;
            }
            $minutes[$minute] = str_pad("$minute", 2, "0", STR_PAD_LEFT);
            $filledTime[$minute] = $signal . " ";
        }

        echo implode(" ", $filledTime);
        echo "\n";
        echo implode(" ", $minutes);
        echo "\n\n";
        echo "{$unused} = Fora do horário comercial\n";
        echo "{$lost} = Horário perdido\n";
        echo "{$filled} = Horário preenchido\n";
        echo "{$allowed} = Horário disponível\n";
    }

    public function minutesBeetwen(DateTime $start, Datetime $end): int
    {
        // Voltar para o passado é impossível
        if ($end < $start) {
            return 0;
        }

        $amount = $start->diff($end);

        $minutes  = $amount->days * 24 * 60;
        $minutes += $amount->h * 60;
        $minutes += $amount->i;

        return $minutes;
    }

    protected function dd(...$mixed): void
    {
        var_dump($mixed);
        die();
    }
}
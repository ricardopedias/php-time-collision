# 7. Direto ao ponto - API

## 7.1. Criação do intervalo

Um intervalo de tempo pode conter várias horas, dias, meses ou até anos, dependendo do problema a ser resolvido.

```php
// 24 horas no dia 10/01/2020
$object = new Collision('2020-01-10');
```

```php
// 24 horas no dia 10/01/2020
$object = new Collision('2020-01-10', '2020-01-10');
```

```php
// 12h e 25 minutos no dia 10/01/2020 das 11:35 às 24:00
$object = new Collision('2020-01-10 11:35');
```

```php
// 25 minutos no dia 10/01/2020 das 11:35 às 12:00
$object = new Collision('2020-01-10 11:35', '2020-01-10 12:00');
```

Para obter as informações setadas no construtor:

```php
// Devolve um objeto DateTime contendo o inicio do intervalo
$object->getStartOfRange();
```

```php
// Devolve um objeto DateTime contendo o final do intervalo
$object->getEndOfRange();
```

## 7.2. Informações padrões

As informações padrões são aplicadas para períodos de forma global.
Em outras palavras, ao setar períodos padrões, eles serão usados para todos os dias
das semana.

```php
// Libera dois períodos para todos os dias da semana
$object->fromDefaults()->enablePeriod('08:00', '12:00');
$object->fromDefaults()->enablePeriod('13:00', '18:00');
```

```php
// Libera dois períodos para todos os dias da semana
$object->fromDefaults()->getPeriods();
```

O resultado será um array contendo todos os períodos padrões setados anteriormente:

```php
[
    0 => Period('08:00', '12:00'),
    1 => Period('13:00', '18:00')
]
```

## 7.3. Disponibilizando dias da semana

Por padrão, todos os dias da semana são definidos como "utilizáveis", mas isso
pode ser mudado da seguinte forma:

```php
// Desativa os finais de semana
$object->fromWeek()->disableDay(WeekDay::SATURDAY);
$object->fromWeek()->disableDay(WeekDay::SUNDAY);
```

```php
// Desativa todos os dias da semana
$object->fromWeek()->disableAllDays();
```

```php
// Libera o Sábado para uso
$object->fromWeek()->enableDay(WeekDay::SATURDAY);
```

```php
// Libera a semana toda, ou seja, reativa o Sábado e o Domingo
$object->fromWeek()->enableAllDays();
```

```php
// Obtém os dias de semana liberados
$object->fromWeek()->getAllDays();
```

```php
// Obtém os dias de semana desativados
$object->fromWeek()->getAllDisabledDays();
```

O resultado de getAllDays() e getAllDisabledDays() terão o seguinte formato:

```php
[
    0 => new WeekDay(0), // Domingo no PHP
    6 => new WeekDay(6) // Sábado no PHP
]
```

Onde os índices são os [dias da semana no PHP](https://www.php.net/manual/pt_BR/function.date.php) e os valores, objetos WeekDay.

## 7.4. Disponibilizando dias específicos

Além dos dias da semana, em muitos casos, será preciso definir dias especiais, que serão ou não liberados.

```php
// Libera o dia 11, Sábado
$object->fromYear()->enableDay('2020-07-11');
```

```php
// Bloqueia o dia 09, Quinta-feira
$object->fromYear()->disableDay('2020-07-09');
```

```php
// Obtém os dias de semana liberados
$object->fromYear()->getAllDays();
```

```php
// Obtém os dias de semana desativados
$object->fromYear()->getAllDisabledDays();
```

O resultado de getAllDays() e getAllDisabledDays() terão o seguinte formato:

```php
[
    '2020-10-05' => new YearDay('2020-10-05'),
    '2020-10-09' => new YearDay('2020-10-09')
]
```

## 7.5. Disponibilizando horários para os dias desejados

Existem casos onde é necessário definir um período de trabalho específico para aquele dia desejado:

```php
// Na Quarta-feira, libera apenas meio período
$object->fromWeek()->enableDay(WeekDay::WEDNESDAY)
    ->withPeriod('08:00', '12:00');
```

```php
// Libera um período diferente para o Sábado
$object->fromWeek()->enableDay(WeekDay::SATURDAY)
    ->withPeriod('08:00', '11:00')
    ->withPeriod('12:00', '15:00');
```

```php
// Na Quarta-feira, dia 08/07/2020, libera apenas meio período
$object->fromYear()->enableDay('2020-07-08')
    ->withPeriod('08:00', '12:00');
```

## 7.6. Encontrando períodos por minutos

Para encontrar um horário vago de 30 minutos dentro do intervalo:

```php
// Obtém os períodos onde 01h30m (90 minutos) pode se encaixar
$fittings = $object->fromFillings()->getFittingsFor(90);
```

O resultado será um array contendo todos os intervalos que cabem 30 minutos:

```php
[
    0 => Interval('2020-01-10 15:00:00', '2020-01-10 18:00:00')
]
```

## 7.7. Encontrando períodos por extenção

Também é possível buscar os periodos disponíveis em uma extenção específica de tempo dentro do intervalo:

```php
// Obtém os períodos não preenchidos entre a data inicial e a data final
$fittings = $object->fromFillings()->getFittingsBetween('2020-10-01 12:00', '2020-10-01 16:00');
```

O resultado será um array contendo todos os intervalos disponíveis:

```php
[
    0 => Interval("2020-01-10 12:00:00","2020-01-10 12:40:00"),
    1 => Interval("2020-01-10 15:30:00","2020-01-10 16:00:00")
]
```

## 7.8. Preenchendo horários explicitamente

Pode-se preencher os horários da seguinte forma:

```php
// Preenche os períodos com base nos dados de $object->fittingsFor()
$fittings = $object->fromFillings()->fill('2020-01-10 15:00:00', '2020-01-10 18:00:00');
```

## 7.9. Preenchendo horários acumulativos

Outra forma de preencher as lacunas disponíveis é usando acumulação de tempo. Nesta modalidade, os minutos que colidirem com espaços indisponíveis **não serão ignorados**, mas usados para preencher as próximas lacunas até que todos os minutos acabem.

```php
// Tenta preencher das 13h às 16h do dia 10/01/2020
$fittings = $object->fromFillings()->fillCumulative('2020-01-10 13:00', '2020-01-10 16:00');
```

## 7.10. Obtendo informações de minutos

```php
// Devolve um array contendo todos os minutos em valores numéricos
$object->fromMinutes()->getRange(Minutes::ALL);
```

```php
// Devolve um array contendo todos os minutos em valores de Data e Hora
$object->fromMinutes()->getRangeInDateTime(Minutes::ALL);
```

As seguintes constantes podem ser usadas para devolver tipos específicos de minutos:

```php
// Devolve os minutos bloqueados para uso
Minutes::UNUSED;
```

```php
// Devolve os minutos que podem ser usados
Minutes::ALLOWED;
```

```php
// Devolve os minutos já usados
Minutes::FILLED;
```

```php
// Devolve o range total de minutos, começando com zero
Minutes::ALL;
```

## Sumário

1. [Criando intervalos para manipulação](ranges.md)
2. [Disponibilizando dias e horários utilizáveis](allowance.md)
3. [Encontrando horários disponíveis](search.md)
4. [Usando horários disponíveis](fitting.md)
5. [Arquitetura da biblioteca](architecture.md)
6. [Algoritmo de colisão](minutes.md)
7. [Direto ao ponto - API](api.md)

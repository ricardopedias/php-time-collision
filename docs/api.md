# API

## 1. Criação do range

Um range de tempo pode conter várias horas, dias, meses ou até anos, dependendo 
do problema a ser resolvido.

Existem várias maneiras de criar ranges de tempo para manipulação. A seguir as possibilidades:

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

## 2. Disponibilizando horários

Somente criar um range não é suficiente. Para manipular o tempo é preciso disponibilizar horários, marcando-os como "liberados" para uso.

Por exemplo, para determinar uma agenda semanal de uma empresa é preciso especificar o horário comercial da seguinte forma:

```php
// Cria o range de uma semana completa, 
// contemplando das 0h às 23h em todos os dias
$object = new Collision('2020-07-05', '2020-07-11');

// Libera dois períodos para todos os dias
$object->allowPeriod('08:00', '12:00');
$object->allowPeriod('13:00', '18:00');
```

A implementação acima especifica que, dentro do range, todos os dias terão
dois períodos disponíveis para manipulação: das 8h às 12h e das 13h às 18h.
Tudo o que não estiver liverado será entendido como "horário não utilizável".

## 3. Disponibilizando dias da semana

Além de manipular períodos de tempo, muitas vezes é necessário restringir a manipulação para dias específicos, tornando alguns dias disponíveis e outros indisponíveis para uso.

Por padrão, todos os dias da semana são definidos como "utilizáveis", mas isso
pode ser mudado da seguinte forma:

```php
// Cria o range de uma semana completa, 
// contemplando das 0h às 23h em todos os dias
$object = new Collision('2020-07-05', '2020-07-11');

// Libera dois períodos para os dias disponíveis
$object->allowPeriod('08:00', '12:00');
$object->allowPeriod('13:00', '18:00');

// Libera 5 dias da semana
$object->allowDay(WeekDay::MONDAY);
$object->allowDay(WeekDay::TUESDAY);
$object->allowDay(WeekDay::WEDNESDAY);
$object->allowDay(WeekDay::THURSDAY);
$object->allowDay(WeekDay::FRIDAY);
```

Na implementação acima, os dois períodos estarão disponíveis apenas para os dias úteis, determinando o Sábado e o Domingo como "não utilizáveis".

> TODO: falar de allowDate()

## 4. Disponibilizando horários para dias específicos

Existem casos onde é necessário definir um período de trabalho diferente
para um dia específico. Seja por ser um Sábado ou um feriado facultativo 
como Quarta-feira de cinzas que algumas empresas costumam liberar meio 
período de folga.

Isso pode ser feito da seguinte forma:

```php
// Cria o range de uma semana completa, 
// contemplando das 0h às 23h em todos os dias
$object = new Collision('2020-07-05', '2020-07-11');

// Libera dois períodos para os dias disponíveis
$object->allowPeriod('08:00', '12:00');
$object->allowPeriod('13:00', '18:00');

// Libera 5 dias da semana
$object->allowDay(WeekDay::MONDAY);
$object->allowDay(WeekDay::TUESDAY);
$object->allowDay(WeekDay::WEDNESDAY);
$object->allowDay(WeekDay::THURSDAY);
$object->allowDay(WeekDay::FRIDAY);

// Libera apenas meio período na Quarta-feira
$object->allowDay(WeekDay::WEDNESDAY)
    ->withPeriod('08:00', '12:00');

// Libera um período diferente para o Sábado
$object->allowDay(WeekDay::STURDAY)
    ->withPeriod('08:00', '11:00')
    ->withPeriod('12:00', '15:00');
```

## 5. Identificando horários disponíveis

Depois de determinar os horários "utilizáveis", pode-se manipulá-los,
obtendo informações úteis dentro dos períodos.

```php
// Cria um range de 24 horas no dia 10/01/2020
$object = new Collision('2020-01-10');

// Libera dois períodos dentro do range
$object->allowPeriod('10:00', '12:00');
$object->allowPeriod('13:00', '14:00');
$object->allowPeriod('15:00', '18:00');

// Obtém os períodos onde 01h30m pode se encaixar
$fittings = $object->fittingsFor(90);
```

O resultado será um array contendo todos os períodos disponíveis com
minutos suficientes para alocar 1h30m.

No exemplo acima, a variável *"$fittings"* terá o seguinte conteúdo:

```
[
    600 => [
        0 => DateTime("2020-01-10 10:00:00"),
        1 => DateTime("2020-01-10 12:00:00"),
    ],
    900 => [
        0 => DateTime("2020-01-10 15:00:00"),
        1 => DateTime("2020-01-10 18:00:00")
    ]
]
```

Dos três períodos "utilizáveis", apenas dois comportam 90 minutos. Estes,
justamente, são os retornados.

No resuldo:

1. O índice (**600** ou **900**) é o número de minutos desde o início do range até atingir o inicio do período;
2. Os dois valores de cada registro correspondem ao início e o fim dos períodos, ou seja, das 10:00 às 12:00 e das 15:00 às 18:00.

## 6. Marcando horários como "utilizados"

Depois de saber quais os horários disponíveis para o tempo especificado, 
é preciso marcá-los no range como "utilizados", preenchendo os minutos e 
impedindo que outros possam usá-los também.

Isso é feito da seguinte forma:

```php
// Cria um range de 24 horas no dia 10/01/2020
$object = new Collision('2020-01-10');

// Libera dois períodos dentro do range
$object->allowPeriod('10:00', '12:00');
$object->allowPeriod('13:00', '14:00');

// Obtém os períodos onde 01h30m pode se encaixar
$object->fill('10:00', '11:30');
```

> TODO: falar de fill() acumulativo

Dessa maneira, o horário das 10h00m às 11h30m não serão usados 
por outros pedidos.

## 7. Obtendo informações sobre os horários

Informação é a característica de maior valor em qualquer sistema. 
Por esse motivo, a biblioteca foi desenvolvida para fornecer 
o maior número de informações sobre o uso do tempo.

Pode-se obter as informações necessários da seguinte maneira:

```php
// O range completo de minutos
$object->minutes()->range();
```

O valor devolvido será um array composto de minutos, onde
sus valores representam as seguintes constantes:

```php
// Minutos "não utilizáveis"
Minutes::UNUSED = -1;
// Minutos "utilizáveis"
Minutes::ALLOWED = 0;
// Minutos "utilizados"
Minutes::FILLED  = 1;
```

```php
// Devolve o range de minutos "não utilizáveis"
$object->minutes()->unused();
```

```php
// Devolve o range de minutos "utilizáveis"
$object->minutes()->allowed();
```

```php
// Devolve o range de minutos "utilizados"
$object->minutes()->filled();
```

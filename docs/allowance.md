# 2. Disponibilizando dias e horários utilizáveis

Após [criar um intervalo](ranges.md), é preciso dizer para a biblioteca quais os horários estarão disponíveis para utilização dentro dele.

## 2.1. Liberando horários

Somente criar um intervalo não é suficiente. Para manipular o tempo é preciso disponibilizar horários, marcando-os como "liberados" para uso.

Embora  esta funcionalidade possa ser usada para diversos fins, um bom exemplo de utilização da liberação de horários pode ser o horário comercial de uma empresa, onde são definidos os períodos onde os clientes podem ser atendidos.

Por exemplo, para determinar uma agenda semanal de uma empresa é preciso especificar o horário comercial da seguinte forma:

```php
// Cria o range de uma semana completa, 
// contemplando das 0h às 23h em todos os dias
$object = new Collision('2020-07-05', '2020-07-11');

// Libera dois períodos para todos os dias da semana
$object->allowDefaultPeriod('08:00', '12:00');
$object->allowDefaultPeriod('13:00', '18:00');
```

O exemplo especifica que, dentro do intervalo, todos os dias terão dois períodos disponíveis para uso: das 8h às 12h e das 13h às 18h.
Tudo o que não estiver dentro desses períodos será considerado como "horário não utilizável".

## 2.2. Definindo os dias da semana

Além de especificar períodos de tempo, muitas vezes é necessário definir os dias onde estes períodos estarão liberados, tornando alguns dias disponíveis e outros não.

Por padrão todos os dias da semana são liberados como "utilizáveis". Mas existem casos, na vida real, onde não será interessante usar os finais de semana. 

Isso pode ser feito desativando apenas os dias indesejados da seguinte forma:

```php
// Restringe os períodos apenas para os dias úteis
$object->disableDayOfWeek(WeekDay::SATURDAY);
$object->disableDayOfWeek(WeekDay::SUNDAY);
```

No exemplo anterior, os dois períodos estarão disponíveis apenas para os dias úteis, determinando o Sábado e o Domingo como "não utilizáveis".

Durante a execução de uma determinada regra, pode ser necessário reativar um dia desativado em tempo de execução.
Isso pode ser feito da seguinte forma:

```php
...

// Libera o Sábado para uso
$object->allowDayOfWeek(WeekDay::SATURDAY);

// ou 

// Libera a semana toda, ou seja, reativa o Sábado e o Domingo
$object->allowAllWeekDays();
```

Se por algum motivo for necessário, é possível também desativar todos os dias da semana de uma vez:

```php
...

// Desativa a semana inteira
$object->disableAllWeekDays();
```

## 2.3. Definindo dias explicitamente

Além dos dias da semana, em muitos casos, será preciso definir dias especiais, que serão ou não liberados.

Na vida real isso pode ser aplicado em duas situações:

1.  Os dias úteis foram definidos de Segunda a Sexta-feira, mas um determinado Sábado deverá ser liberado para um expediente excepcional;
2.  Um feriado caiu num dia útil, que deverá ser desabilitado, pois não haverá expediente neste dia.

No primeiro caso (expediente excepcional), é preciso liberar um dia específico:

```php
// Libera o dia 11, Sábado
$object->allowDate('2020-07-11');
```

No segundo caso (feriado), é preciso bloquear um dia específico:

```php
// Bloqueia o dia 09, Quinta-feira
$object->disableDate('2020-07-09');
```

## 2.3. Disponibilizando horários para os dias desejados

Existem casos onde é necessário definir um período de trabalho diferente para um dia específico. Seja por ser um Sábado ou um feriado facultativo como Quarta-feira de cinzas que algumas empresas costumam liberar meio 
período de folga.

Isso pode ser feito na invocação de allowDayOfWeek() ou allowDate():

```php
// Libera apenas meio período na Quarta-feira
$object->allowDayOfWeek(WeekDay::WEDNESDAY)
    ->withPeriod('08:00', '12:00');

// Libera um período diferente para o Sábado
$object->allowDayOfWeek(WeekDay::SATURDAY)
    ->withPeriod('08:00', '11:00')
    ->withPeriod('12:00', '15:00');
```

Para definir os horários em um dia específico:

```php

// Libera apenas meio período na Quarta-feira
$object->allowDate('2020-07-08')
    ->withPeriod('08:00', '12:00');
```

## Sumário

1.   [Criando intervalos para manipulação](ranges.md)
2.   [Disponibilizando dias e horários utilizáveis](allowance.md)
3.   [Encontrando horários disponíveis](search.md)
4.   [Usando horários disponíveis](fitting.md)
5.   [Arquitetura da biblioteca](architecture.md)
6.   [Algoritmo de colisão](minutes.md)
7.   [Direto ao ponto - API](api.md)

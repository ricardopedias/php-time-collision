# 2. Disponibilizando dias e horários utilizáveis

Após [criar um range](ranges.md), é preciso dizer para a biblioteca quais os horários (e em quais dias) estarão disponíveis para utilização dentro do range.

## 2.1. Liberando horários

Somente criar um range não é suficiente. Para manipular o tempo é preciso disponibilizar horários, marcando-os como "liberados" para uso.

Embora possa ser usado para diversos fins, a liberação de horários segue o mesmo princípio dos horários comerciais de empresas, que são definidos para que os clientes saibam os períodos onde podem ser atendidos.

Por exemplo, para determinar uma agenda semanal de uma empresa é preciso especificar o horário comercial da seguinte forma:

```php
// Cria o range de uma semana completa, 
// contemplando das 0h às 23h em todos os dias
$object = new Collision('2020-07-05', '2020-07-11');

// Libera dois períodos para todos os dias da semana
$object->allowPeriod('08:00', '12:00');
$object->allowPeriod('13:00', '18:00');
```

O exemplo anterior especifica que, dentro do range, todos os dias terão dois períodos disponíveis para uso: das 8h às 12h e das 13h às 18h.
Tudo o que não estiver dentro desses períodos será considerado como "horário não utilizável".

## 2.2. Definindo os dias da semana

Além de especificar períodos de tempo, muitas vezes é necessário definir os dias onde eles estarão liberados, tornando alguns dias disponíveis e outros não.

Por padrão, como mostrado no exemplo anterior, todos os dias da semana são entendidos como "utilizáveis". Mas existem casos, na vida real, onde não será interessante usar os finais de semana. 

Isso pode ser feito ativando apenas os dias desejados da seguinte forma:

```php
// Cria o range de uma semana completa, 
// contemplando das 0h às 23h em todos os dias
$object = new Collision('2020-07-05', '2020-07-11');

// Libera dois períodos para todos os dias da semana
$object->allowPeriod('08:00', '12:00');
$object->allowPeriod('13:00', '18:00');

// Restringe os períodos apenas para os dias úteis
$object->allowDay(WeekDay::MONDAY);
$object->allowDay(WeekDay::TUESDAY);
$object->allowDay(WeekDay::WEDNESDAY);
$object->allowDay(WeekDay::THURSDAY);
$object->allowDay(WeekDay::FRIDAY);
```

A mesma implementação anterior pode ser feita desativando os dias indesejados:

```php
...

// Restringe os períodos apenas para os dias úteis
$object->disableDay(WeekDay::SATURDAY);
$object->disableDay(WeekDay::SUNDAY);
```

Nos exemplos anteriores, os dois períodos estarão disponíveis apenas para os dias úteis, determinando o Sábado e o Domingo como "não utilizáveis".

## 2.3. Definindo dias explicitamente

Além dos dias da semana, em muitos casos, será preciso definir dias especiais, que serão ou não liberados.

Na vida real isso pode ser aplicado em duas situações:

1.  Os dias úteis foram definidos de Segunda a Sexta-feira, mas um determinado Sábado deverá ser liberado para um expediente excepcional;
2.  Um feriado caiu num dia útil, que deverá ser desabilitado, pois não haverá expediente neste dia.

No primeiro caso, é preciso liberar um dia específico:

```php
// Cria o range contemplando das 0h às 23h em todos os dias
$object = new Collision('2020-07-05', '2020-07-12');

// Libera dois períodos para todos os dias
$object->allowPeriod('08:00', '12:00');
$object->allowPeriod('13:00', '18:00');

// Restringe os períodos apenas para os dias úteis
$object->disableDay(WeekDay::SATURDAY);
$object->disableDay(WeekDay::SUNDAY);

// Libera o dia 11, Sábado
$object->allowDate('2020-07-11');
```

No segundo caso, é preciso bloquear um dia específico:

```php
...

// Restringe os períodos apenas para os dias úteis
$object->disableDay(WeekDay::SATURDAY);
$object->disableDay(WeekDay::SUNDAY);

// Bloqueia o dia 09, Quinta-feira
$object->disableDate('2020-07-09');
```

## 2.3. Disponibilizando horários para dias específicos

Existem casos onde é necessário definir um período de trabalho diferente para um dia específico. Seja por ser um Sábado ou um feriado facultativo como Quarta-feira de cinzas que algumas empresas costumam liberar meio 
período de folga.

Isso pode ser feito na invocação de allowDay() ou allowDate():

```php
// Cria o range de uma semana completa, 
// contemplando das 0h às 23h em todos os dias
$object = new Collision('2020-07-05', '2020-07-11');

// Libera dois períodos para os dias disponíveis
$object->allowPeriod('08:00', '12:00');
$object->allowPeriod('13:00', '18:00');

// Libera 5 dias da semana
$object->disableDay(WeekDay::SATURDAY);
$object->disableDay(WeekDay::SUNDAY);

// Libera apenas meio período na Quarta-feira
$object->allowDay(WeekDay::WEDNESDAY)
    ->withPeriod('08:00', '12:00');

// Libera um período diferente para o Sábado
$object->allowDay(WeekDay::SATURDAY)
    ->withPeriod('08:00', '11:00')
    ->withPeriod('12:00', '15:00');
```

Para definir os horários em um dia específico:

```php
// Cria o range de uma semana completa, 
// contemplando das 0h às 23h em todos os dias
$object = new Collision('2020-07-05', '2020-07-11');

// Libera dois períodos para os dias disponíveis
$object->allowPeriod('08:00', '12:00');
$object->allowPeriod('13:00', '18:00');

// Libera 5 dias da semana
$object->disableDay(WeekDay::SATURDAY);
$object->disableDay(WeekDay::SUNDAY);

// Libera apenas meio período na Quarta-feira
$object->allowDate('2020-07-08')
    ->withPeriod('08:00', '12:00');
```

## Sumário

1.   [Criando ranges para manipulação](ranges.md)
2.   [Disponibilizando dias e horários utilizáveis](allowance.md)
3.   [Usando horários disponíveis](fitting.md)
4.   [Obtendo informações sobre os horários](informations.md)
5.   [Arquitetura da biblioteca](architecture.md)

# 1. Criando intervalos para manipulação

## 1.1. O que é um intervalo

A primeira coisa a fazer é definir um **intervalo**, ou seja, o início e o fim do período onde serão manipuladas as colisões de tempo. Estabelecer um intervalo é necessário para que o algoritmo defina as margens necessárias para os cálculos de tempo.

Suponha que seja preciso manipular os horários de uma agenda que contemple 7 dias. Neste caso, deverá ser especificado um intervalo que possa abranger uma semana inteira.

Por exemplo, de 22/11/2020 a 29/11/2020.

## 1.2. Criação do intervalo

Um intervalo é determinado no construtor da classe *Collision*, passando os valores no formato de string.

> A máscara de tempo usada em todos os lugares da biblioteca é baseada no formato DATETIME do SQL, de forma que um exemplo de valor completo seria 2001-03-10 17:16:18.

Existem várias maneiras de criar intervalos de tempo para manipulação. A seguir alguns casos possíveis.

Passando apenas a notação para o dia inicial e o dia final, a biblioteca irá definir o horário 00:00 no primeiro dia e 23:59 para o segundo.

```php
// 10/01/2020 das 00h00m até 23h59m
$object = new Collision('2020-01-10', '2020-01-10');
```

Omitindo o segundo parâmetro do exemplo anterior, a biblioteca utilizará o último minuto do mesmo dia para compor o intervalo. No exemplo abaixo, o mesmo resultado será obtido:

```php
// 10/01/2020 das 00h00m até 23h59m
$object = new Collision('2020-01-10');
```

Em alguns casos específicos, será necessário especificar as horas de início ou fim de em intervalo:

```php
// 10/01/2020 das 11h35m até 23h59m
$object = new Collision('2020-01-10 11:35');
```

```php
// 10/01/2020 das 11h35m até 12h00m
$object = new Collision('2020-01-10 11:35', '2020-01-10 12:00');
```

Lembrando que, como dito anterioremente, um intervalo não precisa ser definido em um único dia. Isso significa que pode-se criar intervalos abrangendo vários dias, meses ou até anos.

```php
// de 10/01/2020 às 08h00m até 15/01/2020 às 18h00m
$object = new Collision('2020-01-10 08:00', '2020-01-15 18:00');
```

## Sumário

1. [Criando intervalos para manipulação](ranges.md)
2. [Disponibilizando dias e horários utilizáveis](allowance.md)
3. [Encontrando horários disponíveis](search.md)
4. [Usando horários disponíveis](fitting.md)
5. [Arquitetura da biblioteca](architecture.md)
6. [Algoritmo de colisão](minutes.md)
7. [Direto ao ponto - API](api.md)

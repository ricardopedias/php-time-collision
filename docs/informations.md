# 5. Obtendo informações sobre os horários

Esta parte ainda está sendo desenvolvida.

```php
// Obtém os períodos onde 90 minutos podem se encaixar
$fittings = $object->fittingsFor(90);
```

```php
// Obtém os períodos preenchidos entre a data inicial e a data final
$fittings = $object->filledsBetween('2020-10-01 12:00', '2020-10-01 16:00');
```

```php
// Obtém os períodos não preenchidos entre a data inicial e a data final
$fittings = $object->fillablesBetween('2020-10-01 12:00', '2020-10-01 16:00');
```




## Sumário

1.   [Criando ranges para manipulação](ranges.md)
2.   [Disponibilizando dias e horários utilizáveis](allowance.md)
3.   [Encontrando horários disponíveis](search.md)
4.   [Usando horários disponíveis](fitting.md)
5.   [Obtendo informações sobre os horários](informations.md)
6.   [Arquitetura da biblioteca](architecture.md)

# 5. Obtendo informações sobre os horários

Para diversos fins, pode ser necessário obter uma lista contento os períodos disponíveis e manipulá-los de alguma forma.

Isso é possível de duas maneiras:

```php
// Obtém os períodos onde 90 minutos podem se encaixar
$fittings = $object->fittingsFor(90);
```

```php
// Obtém os períodos não preenchidos entre a data inicial e a data final
$fittings = $object->fittingsBetween('2020-10-01 12:00', '2020-10-01 16:00');
```

Ambos métodos devolverão uma lista contendo as lacunas possíveis de preenchimento:

```
[
    0 => [
        0 => DateTime("2020-01-10 12:00:00"),
        1 => DateTime("2020-01-10 12:40:00")
    ],
    1 => [
        0 => DateTime("2020-01-10 15:30:00"),
        1 => DateTime("2020-01-10 16:00:00")
    ]
]
```


## Sumário

1.   [Criando ranges para manipulação](ranges.md)
2.   [Disponibilizando dias e horários utilizáveis](allowance.md)
3.   [Encontrando horários disponíveis](search.md)
4.   [Usando horários disponíveis](fitting.md)
5.   [Obtendo informações sobre os horários](informations.md)
6.   [Arquitetura da biblioteca](architecture.md)

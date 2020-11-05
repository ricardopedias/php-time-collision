# PHP Business Hours

![PHP Version](https://img.shields.io/badge/php-%5E7.4.0-blue)
![License](https://img.shields.io/badge/license-MIT-blue)
![TDD](https://img.shields.io/badge/tdd-Tested%20100%25-blue)
[![Build Status](https://travis-ci.org/ricardopedias/php-reliability.svg?branch=master)](https://travis-ci.org/ricardopedias/php-reliability)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/5d9844c598e9425a98059e3d08c78f92)](https://www.codacy.com/manual/ricardopedias/php-reliability?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=ricardopedias/php-reliability&amp;utm_campaign=Badge_Grade)
[![Follow](https://img.shields.io/github/followers/ricardopedias?label=Siga%20no%20GitHUB&style=social)](https://github.com/ricardopedias)
[![Twitter](https://img.shields.io/twitter/follow/ricardopedias?label=Siga%20no%20Twitter)](https://twitter.com/ricardopedias)

PHP Reliability é uma simples biblioteca com implementações de funcções críticas do PHP, 
para atender às exigências mínimas de segurança.

Sinta-se à vontade para conferir o [changelog](https://github.com/ricardopedias/php-reliability/blob/master/changelog.md), os [releases](https://github.com/ricardopedias/php-reliability/releases) e a [licença](https://github.com/ricardopedias/php-reliability/blob/master/license.md).

## Características

-   Feito para o PHP 7 ou superior;
-   Codificado com boas práticas e máxima qualidade;
-   Bem documentado e amigável para IDEs;
-   Feito para TDD (Test Driven Development);
-   Feito com :heart: & :coffee:.

## Como usar

Com orientação a objetos:

```php
$reliability = new Reliability\Reliability();
$reliability->dirname('/meu/diretório/legal');
// meu/diretório
```

Através do helper:

```php
reliability()->dirname('/meu/diretório/legal');
// meu/diretório
```

## Lista de funções implementadas

Abaixo, a lista de funções implementadas pela biblioteca.

| Método                                 | Descrição                                                                                      |
| :------------------------------------: | ---------------------------------------------------------------------------------------------- |
| basename                               | Obtém o nome + extensão de um arquivo especificado.                                            | 
| filename                               | Obtém o nome de um arquivo especificado.                                                       |
| dirname                                | Obtém o nome de um diretório com base no caminho especificado.                                 |
| removeInvalidWhiteSpaces               | Remove caracteres não imprimíveis e caracteres unicode inválidos.                              |
| isDirectory                            | Verifica se o caminho especificado existe e é um diretório.                                    |
| isDirectoryOrException                 |                                                                                                |
| mountDirectory                         | Devolve uma instância do League\Flysystem\Filesystem apontando para o diretório especificado.  |
| removeDirectory                        |                                                                                                |
| copyDirectory                          |                                                                                                |
| moveDirectory                          |                                                                                                |
| isFile                                 | Verifica se o caminho especificado existe e é um arquivo.                                      |
| removeFile                             |                                                                                                |
| copyFile                               |                                                                                                |
| moveFile                               |                                                                                                |
| readFileWithoutCommentsAndWhiteSpaces  | Remove comentários e espaços desnecessários em um script PHP.                                  |
| readFileLines                          | Devolve todas as linhas de um arquivo em forma de array.                                       |
| pathInfo                               |                                                                                                |
| pathExists                             |  Verifica se o caminho especificado existe. Pode ser um diretório ou um arquivo.               |
| absolutePath                           |  Obtém o caminho absoluto do caminho relativo informado.                                       |

## Créditos 

[Ricardo Pereira Dias](http://www.ricardopedias.com.br)

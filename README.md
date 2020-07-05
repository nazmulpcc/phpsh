# PhpSh
[![Latest Version on Packagist](https://img.shields.io/packagist/v/nazmulpcc/phpsh.svg?style=flat-square)](https://packagist.org/packages/nazmulpcc/phpsh)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/nazmulpcc/phpsh/run-tests?label=tests)](https://github.com/nazmulpcc/phpsh/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/nazmulpcc/phpsh.svg?style=flat-square)](https://packagist.org/packages/nazmulpcc/phpsh)
 
## Installation

You can install the package via composer:

```bash
composer require nazmulpcc/phpsh
```

## Why?

If writing shell scripts make you *uncomfortable* because it feels like an ecrypted alient language, PhpSh is for you.

## Examples

```php
use PhpSh\Condition;
use PhpSh\Script;

$condition = Condition::create('$i')->lessThan(10);

(new Script())
    ->set('i', 0)
    ->while($condition, function (Script $script){
        $script->echo('$i');
        $script->increment('i');
    })
    ->generate();
```
The above example code will generate shell executable script like this:
``` sh
i=0
while [ $i -lt 10 ]; do
    echo -n $i
    let i+=1
done
```
Don't know how to do "if a file exists and is writable then do something"? PhpSh got your back!
```php
$condition = Condition::create()
    ->fileExists('/path/to/file')
    ->and()
    ->writable('/path/to/file');

(new Script())
    ->if($condition, function (Script $script){
        $script->printf("File found\n");
    })->else(function (Script $script){
        $script->printf("Oops!\n");
    })
    ->generate();
```  
## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email nazmulpcc [at] gmail.com instead of using the issue tracker.

## Credits

- [Nazmul Alam](https://github.com/nazmulpcc)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

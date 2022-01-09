# PHP-Unit Fixture - In a nutshell
PHP-Unit Fixture is a Composer package that enables the programmer to easily load and process any kind of file. It can load single files or even a list of files from you local disc. Data is then available as array or as JSON string and it is ready to be used in your PHP-Unit test.

You have to do two things to be able to load a fixture:

1. Create a configuration
2. Call the ```fixture``` method from the ```Fixture``` class.

## Create a configuration object
PHP-Unit Fixture is configured via a PHP array. The following snippet shows a complete configuration object.
```php
    $this->configuration = [
        'loaders' => [
            'Local' => '\\CrasyHorse\\Testing\\Loader\\LocalLoader'
        ],
        'encoders' => [
            'base64' => '\\CrasyHorse\\Testing\\Encoder\\Base64'
        ],
        'readers' => [
            'application/json' => '\\CrasyHorse\\Testing\\Reader\\JsonReader',
            '*/*' => '\\CrasyHorse\\Testing\\Reader\\BinaryReader'
        ],
        'sources' => [
            'default' => [
                'driver' => 'Local',
                'root_path' => implode(DIRECTORY_SEPARATOR, [__DIR__, 'data', 'default']),
                'default_file_extension' => 'json',
            ],
            'alternative' => [
                'driver' => 'Local',
                'root_path' => implode(DIRECTORY_SEPARATOR, [__DIR__, 'data', 'alternative']),
                'encode' => [
                    [
                        'mime-type' => '*/*',
                        'encoder' => 'base64'
                    ]
                ]
            ],
        ],
    ];
```
## Start writing a test
You may create such an array in your PHP-Unit test or in the ```setUp``` method to make available for all you specs. To use it just instantiate the [Fixture](https://github.com/crasyhorse/PHPUnitFixture/blob/master/DOCUMENTATION.md#the-fixture-class) class and here you go.
```php
public function test_how_to_use_fixture(): void
{
    $fixture = new Fixture($this->configuration);
    $expected = $fixture->fixture('persons.json')->toArray();
    
    $acutal = Person::all()->toArray();

    $this->assertEquals($expected, $actual);
}
```
this imaginary spec loads the file "persons.json" from disk and compares it to a list of persons from your applications database.

## The full monty
```php
<?php

namespace Tests;

use CrasyHorse\Testing\Fixture;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    private $configuration;

    public function setUp(): void
    {
        parent::setUp();

        $this->configuration = [
            'loaders' => [
                'Local' => '\\CrasyHorse\\Testing\\Loader\\LocalLoader'
            ],
            'encoders' => [
                'base64' => '\\CrasyHorse\\Testing\\Encoder\\Base64'
                'ubjson' => '\\CrasyHorse\\Testing\\Encoder\\UbJson'
            ],
            'readers' => [
                'application/json' => '\\CrasyHorse\\Testing\\Reader\\JsonReader',
                '*/*' => '\\CrasyHorse\\Testing\\Reader\BinaryReader'
            ],
            'sources' => [
                'default' => [
                    'driver' => 'Local',
                    'root_path' => implode(DIRECTORY_SEPARATOR, [__DIR__, 'data', 'default']),
                    'default_file_extension' => 'json',
                ],
                'alternative' => [
                    'driver' => 'Local',
                    'root_path' => implode(DIRECTORY_SEPARATOR, [__DIR__, 'data', 'alternative']),
                    'default_file_extension' => 'json',
                    'encode' => [
                        [
                            'mime-type' => '*/*',
                            'encoder' => 'base64'
                        ]
                    ]
                ],
            ],
        ];
    }

    public function test_how_to_use_fixture(): void
    {
        $fixture = new Fixture($this->configuration);
        $expected = $fixture->fixture('persons.json')->toArray();
        
        $acutal = Person::all()->toArray();

        $this->assertEquals($expected, $actual);
    }
}
```

```bash
phpunit --filter test_how_to_use_fixture
PHPUnit 9.5.6 by Sebastian Bergmann and contributors.

 ◓ running tests
Example (Tests\Unit\Example)
 ✔ How to use fixture
 
Time: 00:01.279, Memory: 32.00 MB

OK (1 tests, 1 assertions)

```
# Configuration in depth
## Sources

Mandatory property: **Yes**

Sources are exactly what their name says: they are named sources to fixtures. The listing below shows a source named "default" with the minimum set of properties.
```php
"sources" => [
    "default" => [
        "driver" => "Local",
        "root_path" => "/projects/persons/tests/data/"
    ]
]
```
### Mandatory properties
#### root_path
The ```root_path``` property defines the location where PHP-Unit Fixture should look for the files to load. Values valid for this property depend on the type of loader class that is registered with your source object.
#### driver
The ```driver``` property specifies how a fixture should be loaded. The value "Local" means that a Loader class named [LocalLoader](https://github.com/crasyhorse/PHPUnitFixture/blob/master/DOCUMENTATION.md#LocalLoader) should be responsible for loading a file from the local filesystem. It is a reference to the "[loaders](https://github.com/crasyhorse/PHPUnitFixture/blob/master/DOCUMENTATION.md#Loaders)" object in the configuration object.

> **Note**
>
> At the moment PHP-Unit Fixture can load files from local filesystem only. Future release should have more abilities like loading files via SSH/SFTP connection or loading files from a TAR archive.

If neccessary, you may configure more than one source like this:
```php
"sources" => [
    "default" => [
        "driver" => "Local",
        "root_path" => "/projects/persons/tests/data/"
    ],
    "invalid" => [
        "driver" => "Local",
        "root_path" => "/projects/persons/tests/data/invalid"
    ]
]
```
>**Note**
>
> Every configuration object **must** have at least a source named "default". Without a "default" source the Fixture class throws a ```InvalidConfigurationException```.
### Optional properties
#### default_file_extension
This property is very useful if most of your files have the same suffix. Let's say you want to load JSON files. Two ways exist to do this:

The first one is:
```php
"sources" => [
    "default" => [
        "driver" => "Local",
        "root_path" => "/projects/persons/tests/data/"
    ]
]
```

```php
...
$fixture = new Fixture($configuration);
$fixture->fixture('person.json');
...
```
Another option would be to add the ```default_file_extension``` property to your configuration.
```php
"sources" => [
    "default" => [
        "driver" => "Local",
        "root_path" => "/projects/persons/tests/data/",
        "default_file_extension" => "json",

    ]
]
```
Now you may omit the file extension when loading the fixture.
```php
...
$fixture = new Fixture($configuration);
$fixture->fixture('person');
...
```
This is even more useful if you want to load not only one fixuture but a list of many fixtures. 
```php
...
$fixture = new Fixture($configuration);
$fixture->fixture(['person-001', 'person-002', 'person-003']);
...
```
#### encode
In some cases it may happen, that you need the fixture data to be converted to another format. For example let's say you have a JSON file called "person.json" and you need the contents of this file encodes as Universal Binary JSON (UbJSON). This is where encoders come to play.

> **Note**
> 
> An encoder is a class that takes data in specfic format and coverts it to another one.

To tell PHP-Unit Fixture that your data has to be converted you have to configure the ```encode``` property of your source object.
```php
"sources" => [
    "default" => [
        "driver" => "Local",
        "root_path" => "/projects/persons/tests/data/",
        "default_file_extension" => "json",
        "encode" => [
            [
                "mime-type": "application/json",
                "encoder": "ubjson"
            ]
        ]

    ]
]
```
The configuration from above means: Encode all files with the mime-type "application/json" into UbJSON. The ```encoder``` property refers to an encoder class listed in the "[encoders](https://github.com/crasyhorse/PHPUnitFixture/blob/master/DOCUMENTATION.md#Encoders)" property.
## Loaders

Mandatory property: **Yes**

A loader class is responsible for reading a fixture from a resource. A resource may be something like:

* a local filesystem
* a remote filesystem (SSH, SFTP, ...)
* a database
* an archive (TAR, ...)

> **Note**
>
> At the moment PHP-Unit Fixture can load files from local filesystem only. Future release should have more abilities like loading files via SSH/SFTP connection or loading files from a TAR archive.
### Available loaders
#### LocalLoader
This is actually the only loader class available with PHP-Unit Fixture. It loads files from the local filesystem. To make this loader work properly the [root_path](https://github.com/crasyhorse/PHPUnitFixture/blob/master/DOCUMENTATION.md#root_path) property of your source object must be a valid Unix/Linux or Windows path. Both relative and absolute paths are permitted.
### Registering loaders
For every kind of resource a specialized loader class must be registered within the configuration object. The following snippet shows the loaders property with the [LocalLoader](https://github.com/crasyhorse/PHPUnitFixture/blob/master/DOCUMENTATION.md#LocalLoader) registered.
```php
$this->configuration = [
    'loaders' => [
        'Local' => '\\CrasyHorse\\Testing\\Loader\\LocalLoader'
    ],
    'sources' => [
        'default' => [
            'driver' => 'Local',
            'root_path' => implode(DIRECTORY_SEPARATOR, [__DIR__, 'data', 'default']),
            'default_file_extension' => 'json',
        ],
    ],
];
...
```
"Local" is the name of the Loader. By convention the name "Local" is used for this Loader because the class's name is "[LocalLoader](https://github.com/crasyhorse/PHPUnitFixture/blob/master/DOCUMENTATION.md#LocalLoader)".

"\\\\CrasyHorse\\\\Testing\\\\Loader\\\\LocalLoader" is a [PSR-4](https://www.php-fig.org/psr/psr-4/) compatible fully qualified class name.
> **Note**
>
> Be aware that every backslash has to be escaped with another backslash.

To enable a source to use a loader class for fixture loading the value of the [driver](https://github.com/crasyhorse/PHPUnitFixture/blob/master/DOCUMENTATION.md#driver) property of the source and the name of the loader class must be identical.

You can register as many loader class as you need for your tests.
## Readers

Mandatory property: **Yes**

The task of a reader class is to read the contents of a fixture that has been loaded into the system by a loader class. The reader then interprets the contents and stores them as an array in the system. For example the [JsonReader](https://github.com/crasyhorse/PHPUnitFixture/blob/master/DOCUMENTATION.md#JsonReader) class interprets the loaded content as JSON string and transforms it into a PHP array.

### Available readers
By default there are two readers:

* ```\\CrasyHorse\\Testing\\Reader\\JsonReader```
* ```\\CrasyHorse\\Testing\\Reader\\BinaryReader```

#### JsonReader
Reads the contents of JSON-fixture and decodes them into an array. To check if a fixture's contents are of type "application/json" a rather complex regular expression is used.
#### BinaryReader
Its primary task is to read the contents of binary files. However, it is able to read any kind of file. BinaryReader does not interpret what it reads. It just stores the read contents in the system.

>**Note**
>
> It is recommended to use the [Base64](https://github.com/crasyhorse/PHPUnitFixture/blob/master/DOCUMENTATION.md#Base64) encoder class in combination with BinaryReader especially if you are planing to read a binary file.
### Registering readers
Readers are registered in the same manner as loader classes are. For every reader class you have to supply a name and the [PSR-4](https://www.php-fig.org/psr/psr-4/) compatible fully qualified class name.
```php
$this->configuration = [
    'loaders' => [
        'Local' => '\\CrasyHorse\\Testing\\Loader\\LocalLoader'
    ],
    'readers' => [
        'application/json' => '\\CrasyHorse\\Testing\\Reader\\JsonReader',
        '*/*' => '\\CrasyHorse\\Testing\\Reader\\BinaryReader'
    ],
    'sources' => [
        'default' => [
            'driver' => 'Local',
            'root_path' => implode(DIRECTORY_SEPARATOR, [__DIR__, 'data', 'default']),
            'default_file_extension' => 'json',
        ],
    ],
];
...
```
The name of a reader class is by convention the mime-type interpreted by this reader. But this is only a rule of thumb. You may use any kind of name for a reader. The mime-type "*/*" used as name for the [BinaryReader](https://github.com/crasyhorse/PHPUnitFixture/blob/master/DOCUMENTATION.md#BinaryReader) symbolizes that this reader is able to read any type of fixture.

>**Note**
>
> If there is no proper reader available in your configuration object for a specific kind of file a ```ReaderNotFoundException``` exception is thrown. 
## Encoders

Mandatory property: **No**

Encoder classes have the task of putting the content of a fixture into a specific form. The already mentioned [Base64](https://github.com/crasyhorse/PHPUnitFixture/blob/master/DOCUMENTATION.md#Base64) encoders converts a string into a Base64-string.
### Available encoders
#### Base64
Converts a (binary) string into a Base64-string.
### Registering encoders
Registering encoders involves two steps.

1. Register the encoder class in the [encoders]() list.
```php
$this->configuration = [
    'loaders' => [
        'Local' => '\\CrasyHorse\\Testing\\Loader\\LocalLoader'
    ],
    'encoders' => [
        'base64' => '\\CrasyHorse\\Testing\\Encoder\\Base64'
    ],
    'readers' => [
        'application/json' => '\\CrasyHorse\\Testing\\Reader\\JsonReader',
        '*/*' => '\\CrasyHorse\\Testing\\Reader\\BinaryReader'
    ],
    'sources' => [
        'default' => [
            'driver' => 'Local',
            'root_path' => implode(DIRECTORY_SEPARATOR, [__DIR__, 'data', 'default']),
            'default_file_extension' => 'json',
        ],
    ],
];
...
```
2. Register an encoding for the source object to be used.
```php
$this->configuration = [
    'loaders' => [
        'Local' => '\\CrasyHorse\\Testing\\Loader\\LocalLoader'
    ],
    'encoders' => [
        'base64' => '\\CrasyHorse\\Testing\\Encoder\\Base64'
    ],
    'readers' => [
        'application/json' => '\\CrasyHorse\\Testing\\Reader\\JsonReader',
        '*/*' => '\\CrasyHorse\\Testing\\Reader\\BinaryReader'
    ],
    'sources' => [
        'default' => [
            'driver' => 'Local',
            'root_path' => implode(DIRECTORY_SEPARATOR, [__DIR__, 'data', 'default']),
            'default_file_extension' => 'json',
            'encode' => [
                [
                    'mime-type' => '*/*',
                    'encoder' => 'base64'
                ]
            ]
        ],
    ],
];
...
```
To connect an encoder with a source object the name of the encoder must be identical with the [encoder](https://github.com/crasyhorse/PHPUnitFixture/blob/master/DOCUMENTATION.md#Encode) properties value in this case both must be "base64".

The information ```'mime-type' => '*/*'``` means that only contents read by [BinaryReader](https://github.com/crasyhorse/PHPUnitFixture/blob/master/DOCUMENTATION.md#BinaryReader) should be encoded with [Base64](https://github.com/crasyhorse/PHPUnitFixture/blob/master/DOCUMENTATION.md#Base64). For contents read by another reader class no encoding takes place.

# The Fixture class
The ```CrasyHorse\\Testing\Fixture``` class is the main class of PHP-Unit Fixture. Everything is done with the help of this class.
## Loading fixtures
Let's say you have the following configuration object:
```php
    $this->configuration = [
        'loaders' => [
            'Local' => '\\CrasyHorse\\Testing\\Loader\\LocalLoader'
        ],
        'encoders' => [
            'base64' => '\\CrasyHorse\\Testing\\Encoder\\Base64'
        ],
        'readers' => [
            'application/json' => '\\CrasyHorse\\Testing\\Reader\\JsonReader',
            '*/*' => '\\CrasyHorse\\Testing\\Reader\\BinaryReader'
        ],
        'sources' => [
            'default' => [
                'driver' => 'Local',
                'root_path' => implode(DIRECTORY_SEPARATOR, [__DIR__, 'data', 'default']),
                'default_file_extension' => 'json',
            ],
            'alternative' => [
                'driver' => 'Local',
                'root_path' => implode(DIRECTORY_SEPARATOR, [__DIR__, 'data', 'alternative']),
                'encode' => [
                    [
                        'mime-type' => '*/*',
                        'encoder' => 'base64'
                    ]
                ]
            ],
        ],
    ];
```
You plan to load the file ```./data/default/alice.json```.
```json
{
    "data": {
        "persons": {
            "alice": {
                "firstname": "Alice",
                "lastname": "Smith",
                "age": 20,
                "gender": "female"
            }
        }
    }
}
```
All you have to do is to initialize a Fixture and to call the ```fixture``` method.
```php
$fixture = new Fixture($configuration);
$content = $fixture->fixture('alice');
```
What happens under the hood? 

* The Fixture class by default selects the "default" source in its constructor.
* The [default_file_extension](https://github.com/crasyhorse/PHPUnitFixture/blob/master/DOCUMENTATION.md#default_file_extension) property completes the filename with the ".json" suffix.

What you get in return from the ```fixture``` method is an instance of the ```CrasyHorse\\Testing\\Content``` class. 

### toArray()
To return the contents of the fixture as array you may call the ```toArray``` method of the ```Content``` class.
```php
print_r($content->toArray());

Array
(
  [data] => Array
    (
      [persons] => Array
        (
          [alice] => Array
            (
              [firstname] => Alice
              [lastname] => Smith
              [age] => 20
              [gender] => female
            )

        )
    )
)
```
### toJson()
You may also get the contents as JSON string.
```php
var_dump($content->toJson());

98) "{"data":{"persons":{"alice":{"firstname":"Alice","lastname":"Smith","age":20,"gender":"female"}}}}"
}
```
### get()
What is if you need a single attribute of alice's data only. You could do it like this:
```php
$age = $content['data']['persons']['alice']['age'];
var_dump($age);

int(20)
```
But this can be even more easier and that's the time where the ```get``` method comes into play.
```php
$age = $content->get('data.persons.alice.age');
var_dump($age);

int(20)
```
```get``` enables you to access the values of a fixture by using the Array-Dot-Notation. Even with deeply nested arrays it is very easy now to access your data.
>**Note**
>
> Executing ```get``` without any arguments returns the whole content.
>
> Executing ```get``` with a non-existing Array-Dot-String like ```data.persons.eddy``` returns ```null```.
## Setting a source
After you got Alice's data you need to load Bob's data. There is a JSON file ```bob.json``` in the ```./data/alternative``` directory. To be able to load Bob's data you have to set the source object to "alternative".
```php
$fixture = new Fixture($configuration);
$content = $fixture
    ->source('alternative')
    ->fixture('alice');

print_r($content->toArray());

Array
(
  [data] => Array
    (
      [persons] => Array
        (
          [bob] => Array
            (
              [firstname] => Bob
              [lastname] => Smith
              [age] => 23
              [gender] => male
            )
        )
    )
)
```
## Loading multiple fixtures at once
PHP-Unit Fixture makes it possible to load multiple fixtures at once. The result is a merged array containing the data of all loaded fixtures. For example you want to load the data for Bob, Chloe and Dave. All three files reside in the alternative source.
```php
$fixture = new Fixture($configuration);
$content = $fixture
    ->source('alternative')
    ->fixture(['bob','chloe','dave'])
    ->get();

print_r($content);

Array
(
  [data] => Array
    (
      [persons] => Array
        (
          [bob] => Array
            (
              [firstname] => Bob
              [lastname] => Smith
              [age] => 23
              [gender] => male
            )

          [chloe] => Array
            (
              [firstname] => Chloe
              [lastname] => Smith
              [age] => 25
              [gender] => female
            )

          [dave] => Array
            (
              [firstname] => Dave
              [lastname] => Smith
              [age] => 19
              [gender] => male
            )
        )
    )
)

$chloe = $content->get('data.persons.chloe');

print_r ($chloe);
Array
(
  [firstname] => Chloe
  [lastname] => Smith
  [age] => 25
  [gender] => female
)

$bobsAge = $content->get('data.persons.bob.age');

var_dump($bobsAge);

int(23)

$persons = $content->get('data.persons');

print_r($persons);

Array
(
  [bob] => Array
    (
      [firstname] => Bob
      [lastname] => Smith
      [age] => 23
      [gender] => male
    )

  [chloe] => Array
    (
      [firstname] => Chloe
      [lastname] => Smith
      [age] => 25
      [gender] => female
    )

  [dave] => Array
    (
      [firstname] => Dave
      [lastname] => Smith
      [age] => 19
      [gender] => male
    )
)
```
PHP-Unit Fixture uses PHP's [array_merge_recursive](https://www.php.net/manual/de/function.array-merge-recursive.php) function to join the fixtures data together.

>**Note**
>
> At the time writing this it is not possible to load multiple fixtures from different sources. This is planed for future releases.

## Unwrapping data
In the example above the data of alice, bob, chloe and dave is enclosed in the ```data.persons``` objects. Everytime you want to access one these persons you have to prefix your request with ```data.persons```.
```php
$fixture = new Fixture($configuration);
$content = $fixture
    ->source('alternative')
    ->fixture(['bob','chloe','dave'])
    ->get('data.persons.bob');
```
This can be anoying. To make life a little bit easery PHP-Unit Fixture comes with an ```unwrap``` method.
```php
$fixture = new Fixture($configuration);
$content = $fixture
    ->source('alternative')
    ->fixture(['bob','chloe','dave'])
    ->unwrap('data.persons');
    ->get();

print_r($content);

Array
(
  [bob] => Array
    (
      [firstname] => Bob
      [lastname] => Smith
      [age] => 23
      [gender] => male
    )

  [chloe] => Array
    (
      [firstname] => Chloe
      [lastname] => Smith
      [age] => 25
      [gender] => female
    )

  [dave] => Array
    (
      [firstname] => Dave
      [lastname] => Smith
      [age] => 19
      [gender] => male
    )
)

$bob = $content->get('bob');

print_r($bob);

Array
(
  [firstname] => Bob
  [lastname] => Smith
  [age] => 23
  [gender] => male
)
```
>**Note**
>
> If you execute ```unwrap``` without any arguments, it looks for a "data" object and unwraps the data!
```php
$fixture = new Fixture($configuration);
$content = $fixture
    ->source('alternative')
    ->fixture(['bob','chloe','dave'])
    ->unwrap();
    ->get();

print_r($content);

Array
(
  [persons] => Array
    (
      [bob] => Array
        (
          [firstname] => Bob
          [lastname] => Smith
          [age] => 23
          [gender] => male
        )

      [chloe] => Array
        (
          [firstname] => Chloe
          [lastname] => Smith
          [age] => 25
          [gender] => female
        )

      [dave] => Array
        (
          [firstname] => Dave
          [lastname] => Smith
          [age] => 19
          [gender] => male
        )
    )
)
```
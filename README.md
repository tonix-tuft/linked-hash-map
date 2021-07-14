# linked-hash-map

How I would implement a linked hash map in PHP if PHP wouldn't have associative arrays.

## Installation

Using [Composer](https://getcomposer.org/):

```
composer require tonix-tuft/linked-hash-map
```

## Usage

This map implements the [ArrayAccess](https://www.php.net/manual/en/class.arrayaccess.php) interface as well as the [Iterator](https://www.php.net/manual/en/class.iterator.php) and [Countable](https://www.php.net/manual/en/class.countable.php) interfaces and therefore can be used as a built-in PHP array:

```php
<?php

use LinkedHashMap\LinkedHashMap;

$map = new LinkedHashMap();
$map['abc'] = 'string (abc)';
$map['abcdef'] = 'string (abcdef)';
$map[123] = 'int (123)';

var_dump(count($map)); // 3

foreach ($map as $key => $value) {
  var_dump($key, $value);
}
```

### Using any PHP data type for the key

This map allows using any PHP type for the key (i.e. even an array or an object can be used for the key):

```php
<?php

use LinkedHashMap\LinkedHashMap;

$map = new LinkedHashMap();
$map[true] = 'bool (true)';
$map[false] = 'bool (false)';
$map[32441] = 'int (32441)';
$map[-32441] = 'int (-32441)';
$map[2147483647] = 'int (2147483647)';
$map[-2147483648] = 'int (-2147483648)';
$map[PHP_INT_MAX - 100] = 'int (PHP_INT_MAX - 100)';
$map[PHP_INT_MIN] = 'int (PHP_INT_MIN)';
$map[0.5] = 'float/double (0.5)';
$map[-0.5] = 'float/double (-0.5)';
$map[123891.73] = 'float/double (123891.73)';
$map[-123891.73] = 'float/double (-123891.73)';
$map[PHP_INT_MAX + 10] = 'float/double (PHP_INT_MAX + 10)';
$map[PHP_INT_MIN - 10] = 'float/double (PHP_INT_MIN - 10)';
$map['abc'] = 'string (abc)';
$map["abcdef"] = "string (abcdef)";
$map['hfudsh873hu2ifl'] = "string (hfudsh873hu2ifl)";
$map["The quick brown fox jumps over the lazy dog"] =
  'string (The quick brown fox jumps over the lazy dog)';
$map[[1, 2, 3]] = 'array ([1, 2, 3])';
$map[['a', 'b', 'c']] = "array (['a', 'b', 'c'])";
$map[[1, 'a', false, 5, true, [1, 2, 3, ['f', 5, []]]]] =
  "array ([1, 'a', false, 5, true, [1, 2, 3, ['f', 5, []]]])";

$arrayKey = [
  1,
  'a',
  false,
  5,
  true,
  [1, 2, 3, ['f', 5, [new stdClass(), new stdClass()]]],
  new ArrayIterator(),
];
$map[$arrayKey] =
  "array ([1, 'a', false, 5, true, [1, 2, 3, ['f', 5, [new stdClass(), new stdClass()]]], new ArrayIterator()])";

$stdClassObj = new stdClass();
$map[$stdClassObj] = "object (new stdClass())";

$arrayIterator = new ArrayIterator();
$map[$arrayIterator] = "object (new ArrayIterator())";

class A {
}
$objA = new A();
$map[$objA] = "object (new A())";

$fp = fopen(__DIR__ . '/private_local_file', 'w');
$map[$fp] = "resource (fopen())";

$ch = curl_init();
$map[$ch] = "resource (curl_init())";

// All the values can be retrieved later using the corresponding key, e.g.:
var_dump($map[[1, 2, 3]]); // "array ([1, 2, 3])"
var_dump($map[$objA]); // "object (new A())"
var_dump($map[$ch]); // "resource (curl_init())"
```

### Differences and similarities between this map and built-in PHP arrays

The differences between this map and built-in PHP arrays as well as any similarities are the following:

1. Any PHP data type can be used for the key (`bool`, `int`, `float/double`, `string`, `array`, `object`, `callable`, `iterable`, `resource`) when using this map.
   This also means that for example a float/double `1.5` will be used for the key as-is, whereas in built-in PHP arrays `1.5` is [type-juggled](https://www.php.net/manual/en/language.types.type-juggling.php) to `1`:

```php
<?php

use LinkedHashMap\LinkedHashMap;

$map = new LinkedHashMap();
$map[1.5] = 'A value for key 1.5';
var_dump($map[1.5]); // "A value for key 1.5"
var_dump($map[1]); // NULL

$arr = [];
$arr[1.5] = 'A value'; // [1 => "A value"];
var_dump($arr[1.5]); // "A value"
var_dump($arr[1]); // "A value"
```

2. This map allows prepending instead of appending when setting the `LinkedHashMap::INSERT_MODE_PREPEND` flag (using the `setInsertMode` method):

```php
<?php

use LinkedHashMap\LinkedHashMap;

$map = new LinkedHashMap();
$map->setInsertMode(LinkedHashMap::INSERT_MODE_PREPEND); // Defaults to `LinkedHashMap::INSERT_MODE_APPEND`
$map['a'] = 1;
$map['b'] = 2;

foreach ($map as $key => $value) {
  var_dump($key, $value);
}
// 'b', 2
// 'a', 1
```

3. This map also allows setting the loop order (iteration) order (using the `setLoopOrder` method), whether normal (`LinkedHashMap::LOOP_ORDER_NORMAL`, the default) or reversed (`LinkedHashMap::LOOP_ORDER_REVERSE`):

```php
<?php

use LinkedHashMap\LinkedHashMap;

// Example 1:
$map = new LinkedHashMap();
$map->setLoopOrder(LinkedHashMap::LOOP_ORDER_REVERSE); // Defaults to `LinkedHashMap::LOOP_ORDER_NORMAL`
$map['a'] = 1;
$map['b'] = 2;

foreach ($map as $key => $value) {
  var_dump($key, $value);
}
// 'b', 2
// 'a', 1

// Example 2:
$map = new LinkedHashMap();
$map->setInsertMode(LinkedHashMap::INSERT_MODE_PREPEND); // Defaults to `LinkedHashMap::INSERT_MODE_APPEND`
$map->setLoopOrder(LinkedHashMap::LOOP_ORDER_REVERSE); // Defaults to `LinkedHashMap::LOOP_ORDER_NORMAL`
$map['a'] = 1;
$map['b'] = 2;

foreach ($map as $key => $value) {
  var_dump($key, $value);
}
// 'a', 1
// 'b', 2
```

4. Appending/prepending to the map works in the same way as with built-in PHP arrays (a positional index (an int >= 0) is created or the highest positional index used so far is incremented internally).
   Accessing an unknown index does not trigger/emit a notice (just returns `NULL`):

```php
<?php

use LinkedHashMap\LinkedHashMap;

$map = new LinkedHashMap();
$map[] = 'Value for index 0';
$map[] = 'Value for index 1';
$map[1234] = 'Value for index 1234';
$map[] = 'Value for index 1235';
var_dump($map[0]); // "Value for index 0"
var_dump($map[1]); // "Value for index 1"
var_dump($map[2]); // NULL (no E_NOTICE/E_USER_NOTICE)
var_dump($map[1234]); // "Value for index 1234"
var_dump($map[1235]); // "Value for index 1235"

$arr = [];
$arr[] = 'Value for index 0';
$arr[] = 'Value for index 1';
$arr[1234] = 'Value for index 1234';
$arr[] = 'Value for index 1235';
var_dump($arr[0]); // "Value for index 0"
var_dump($arr[1]); // "Value for index 1"
var_dump($arr[2]); // NULL (emits E_NOTICE)
var_dump($arr[1234]); // "Value for index 1234"
var_dump($arr[1235]); // "Value for index 1235"
```

3. Because [ArrayAccess::offsetSet](https://www.php.net/manual/en/arrayaccess.offsetset.php) doesn't allow to differentiate between `NULL` and an append/prepend operation (`$map[] = 'A value'`), `NULL` cannot be used for the key. Using `NULL` for the key will be considered as an append/prepend operation. As built-in PHP arrays map `NULL` to an empty string `''`, this shouldn't be an issue:

```php
<?php

use LinkedHashMap\LinkedHashMap;

$map = new LinkedHashMap();
$map[null] = 'Value for index 0';
$map[null] = 'Value for index 1';
$map[1234] = 'Value for index 1234';
$map[null] = 'Value for index 1235';
var_dump($map[0]); // "Value for index 0"
var_dump($map[1]); // "Value for index 1"
var_dump($map[1234]); // "Value for index 1234"
var_dump($map[1235]); // "Value for index 1235"
var_dump($map[null]); // NULL
var_dump($map['']); // NULL

$arr = [];
$arr[null] = 'Value for index 0';
$arr[null] = 'Value for index 1';
$arr[1234] = 'Value for index 1234';
$arr[null] = 'Value for index 1235';
var_dump($arr[0]); // NULL (emits E_NOTICE)
var_dump($arr[1]); // NULL (emits E_NOTICE)
var_dump($arr[1234]); // "Value for index 1234"
var_dump($arr[1235]); // NULL (emits E_NOTICE)
var_dump($arr[null]); // "Value for index 1235"
var_dump($arr['']); // "Value for index 1235"
```

### Using a custom hash code

Internally, the map computes the hash for the given key in order to retrieve the corresponding value using the package [int-hash](https://packagist.org/packages/tonix-tuft/int-hash).
If the key is an instance of a class implementing the `LinkedHashMap\HashCodeInterface` interface, its `hashCode` method will be called and the returned hash code (an integer) will be used instead:

```php
<?php

use LinkedHashMap\LinkedHashMap;
use LinkedHashMap\HashCodeInterface;

class ClassWithCustomHashCode implements HashCodeInterface {
  /**
   * @var int
   */
  protected $propertyA;

  /**
   * @var int
   */
  protected $propertyB;

  public function __construct() {
    $this->propertyA = rand(0, 100000);
    $this->propertyB = rand(0, 100000);
  }

  // ...

  /**
   * {@inheritdoc}
   */
  public function hashCode() {
    // Compute the hash code somehow...
    $prime = 31;
    $hash = 1;
    $hash = $prime * $hash + $this->propertyA;
    $hash = $prime * $hash + $this->propertyB;
    return $hash;
  }
}

$map = new LinkedHashMap();

$obj1 = new ClassWithCustomHashCode();
$obj2 = new ClassWithCustomHashCode();

$map[$obj1] = "A value";
$map[$obj2] = "Another value";

var_dump($map[$obj1]); // "A value"
var_dump($map[$obj2]); // "Another value"
```

## License

MIT © [Anton Bagdatyev (Tonix)](https://github.com/tonix-tuft)

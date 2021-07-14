<?php

require_once __DIR__ . '/../vendor/autoload.php';

use LinkedHashMap\HashCodeInterface;
use LinkedHashMap\LinkedHashMap;

$map = new LinkedHashMap();

// $map->setInsertMode(LinkedHashMap::INSERT_MODE_APPEND); // Default
// $map->setInsertMode(LinkedHashMap::INSERT_MODE_PREPEND);

// $map->setLoopOrder(LinkedHashMap::LOOP_ORDER_NORMAL); // Default
// $map->setLoopOrder(LinkedHashMap::LOOP_ORDER_REVERSE);

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

$map[] = 'append 0';
$map[] = 'append 1';
$map[] = 'append 2';
$map[] = 'append 3';
$map[null] = 'append 4';

$a = 1;
$b = 2;
$c = 3;
$fn = function ($d, $e) use ($a, &$b, $c) {
  var_dump($a, $b, $c, $d, $e);
};
$map[$fn] = 'function ($d, $e) use ($a, &$b, $c) {...}';

$map[PHP_INT_MAX] = 'int (PHP_INT_MAX)';

echo PHP_EOL;
echo json_encode(['count($map)' => count($map)], JSON_PRETTY_PRINT);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  ['get $map[true]' => $map[true], 'isset($map[true])' => isset($map[true])],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[false]' => $map[false],
    'isset($map[false])' => isset($map[false]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[32441]' => $map[32441],
    'isset($map[32441])' => isset($map[32441]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[-32441]' => $map[-32441],
    'isset($map[-32441])' => isset($map[-32441]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[2147483647]' => $map[2147483647],
    'isset($map[2147483647])' => isset($map[2147483647]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[-2147483648]' => $map[-2147483648],
    'isset($map[-2147483648])' => isset($map[-2147483648]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[PHP_INT_MAX - 100]' => $map[PHP_INT_MAX - 100],
    'isset($map[PHP_INT_MAX - 100])' => isset($map[PHP_INT_MAX - 100]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[PHP_INT_MIN]' => $map[PHP_INT_MIN],
    'isset($map[PHP_INT_MIN])' => isset($map[PHP_INT_MIN]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  ['get $map[0.5]' => $map[0.5], 'isset($map[0.5])' => isset($map[0.5])],
  JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  ['get $map[-0.5]' => $map[-0.5], 'isset($map[-0.5])' => isset($map[-0.5])],
  JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[123891.73]' => $map[123891.73],
    'isset($map[123891.73])' => isset($map[123891.73]),
  ],
  JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[-123891.73]' => $map[-123891.73],
    'isset($map[-123891.73])' => isset($map[-123891.73]),
  ],
  JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[PHP_INT_MAX + 10]' => $map[PHP_INT_MAX + 10],
    'isset($map[PHP_INT_MAX + 10])' => isset($map[PHP_INT_MAX + 10]),
  ],
  JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[PHP_INT_MIN - 10]' => $map[PHP_INT_MIN - 10],
    'isset($map[PHP_INT_MIN - 10])' => isset($map[PHP_INT_MIN - 10]),
  ],
  JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  ['get $map[abc]' => $map['abc'], 'isset($map[abc])' => isset($map['abc'])],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[abcdef]' => $map["abcdef"],
    'isset($map[abcdef])' => isset($map["abcdef"]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[hfudsh873hu2ifl]' => $map["hfudsh873hu2ifl"],
    'isset($map[hfudsh873hu2ifl])' => isset($map['hfudsh873hu2ifl']),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[The quick brown fox jumps over the lazy dog]' =>
      $map["The quick brown fox jumps over the lazy dog"],
    'isset($map[The quick brown fox jumps over the lazy dog])' => isset(
      $map["The quick brown fox jumps over the lazy dog"]
    ),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[[1, 2, 3]]' => $map[[1, 2, 3]],
    'isset($map[[1, 2, 3]])' => isset($map[[1, 2, 3]]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    "get \$map[['a', 'b', 'c']]" => $map[['a', 'b', 'c']],
    "isset(\$map[['a', 'b', 'c']])" => isset($map[['a', 'b', 'c']]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    "get \$map[[1, 'a', false, 5, true, [1, 2, 3, ['f', 5, []]]]]" =>
      $map[[1, 'a', false, 5, true, [1, 2, 3, ['f', 5, []]]]],
    "isset(\$map[[1, 'a', false, 5, true, [1, 2, 3, ['f', 5, []]]]])" => isset(
      $map[[1, 'a', false, 5, true, [1, 2, 3, ['f', 5, []]]]]
    ),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[$arrayKey]' => $map[$arrayKey],
    'isset($map[$arrayKey])' => isset($map[$arrayKey]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[$stdClassObj]' => $map[$stdClassObj],
    'isset($map[$stdClassObj])' => isset($map[$stdClassObj]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[$arrayIterator]' => $map[$arrayIterator],
    'isset($map[$arrayIterator])' => isset($map[$arrayIterator]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[$objA]' => $map[$objA],
    'isset($map[$objA])' => isset($map[$objA]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[$fp]' => $map[$fp],
    'isset($map[$fp])' => isset($map[$fp]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[$ch]' => $map[$ch],
    'isset($map[$ch])' => isset($map[$ch]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[PHP_INT_MAX - 99]' => $map[PHP_INT_MAX - 99],
    'isset($map[PHP_INT_MAX - 99])' => isset($map[PHP_INT_MAX - 99]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[PHP_INT_MAX - 98]' => $map[PHP_INT_MAX - 98],
    'isset($map[PHP_INT_MAX - 98])' => isset($map[PHP_INT_MAX - 98]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[PHP_INT_MAX - 97]' => $map[PHP_INT_MAX - 97],
    'isset($map[PHP_INT_MAX - 97])' => isset($map[PHP_INT_MAX - 97]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[PHP_INT_MAX - 96]' => $map[PHP_INT_MAX - 96],
    'isset($map[PHP_INT_MAX - 96])' => isset($map[PHP_INT_MAX - 96]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[PHP_INT_MAX - 95]' => $map[PHP_INT_MAX - 95],
    'isset($map[PHP_INT_MAX - 95])' => isset($map[PHP_INT_MAX - 95]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[$fn]' => $map[$fn],
    'isset($map[$fn])' => isset($map[$fn]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'get $map[PHP_INT_MAX]' => $map[PHP_INT_MAX],
    'isset($map[PHP_INT_MAX])' => isset($map[PHP_INT_MAX]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(
  [
    'count($map)' => count($map),
    'isset($map[PHP_INT_MAX])' => isset($map[PHP_INT_MAX]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

$map[] = 'append 5';

unset($map[PHP_INT_MAX]);
//unset($map[9223372036854775710]);

/*
echo PHP_EOL;
echo "foreach >";
echo PHP_EOL;
echo "------------------------------------------------------------------------------------------------------------------------------------------";
$i = 1;
$count = count($map);
foreach ($map as $key => $value) {
  echo PHP_EOL;
  $label = "Iteration $i";
  echo $label;
  echo PHP_EOL;
  echo str_pad('', strlen($label), '=');
  echo PHP_EOL;
  echo PHP_EOL;
  $varExport = var_export($key, true);
  echo "key:" .
    PHP_EOL .
    ($varExport === "NULL" && $key !== null
      ? '(' . gettype($key) . ')'
      : $varExport . ' (' . gettype($key) . ')');
  echo PHP_EOL;
  echo PHP_EOL;
  echo "value:" . PHP_EOL . $value;
  echo PHP_EOL;
  echo PHP_EOL;
  if ($count !== $i) {
    echo "------------------------------------------------------------------------------------------------------------------------------------------";
  }
  $i++;
}
echo "------------------------------------------------------------------------------------------------------------------------------------------";
echo PHP_EOL;
echo "< foreach";
echo PHP_EOL;
//*/

echo PHP_EOL;
echo json_encode(
  [
    'count($map)' => count($map),
    'isset($map[PHP_INT_MAX])' => isset($map[PHP_INT_MAX]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

$map[] = 'append 5';

echo PHP_EOL;
echo json_encode(
  [
    'get $map[PHP_INT_MAX]' => $map[PHP_INT_MAX],
    'isset($map[PHP_INT_MAX])' => isset($map[PHP_INT_MAX]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(['count($map)' => count($map)], JSON_PRETTY_PRINT);
echo PHP_EOL;

echo PHP_EOL;
echo "foreach >";
echo PHP_EOL;
echo "------------------------------------------------------------------------------------------------------------------------------------------";
$i = 1;
$count = count($map);
foreach ($map as $key => $value) {
  echo PHP_EOL;
  $label = "Iteration $i";
  echo $label;
  echo PHP_EOL;
  echo str_pad('', strlen($label), '=');
  echo PHP_EOL;
  echo PHP_EOL;
  $varExport = var_export($key, true);
  echo "key:" .
    PHP_EOL .
    ($varExport === "NULL" && $key !== null
      ? '(' . gettype($key) . ')'
      : $varExport . ' (' . gettype($key) . ')');
  echo PHP_EOL;
  echo PHP_EOL;
  echo "value:" . PHP_EOL . $value;
  echo PHP_EOL;
  echo PHP_EOL;
  if ($count !== $i) {
    echo "------------------------------------------------------------------------------------------------------------------------------------------";
  }
  $i++;
}
echo "------------------------------------------------------------------------------------------------------------------------------------------";
echo PHP_EOL;
echo "< foreach";
echo PHP_EOL;

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

$obj1 = new ClassWithCustomHashCode();
$obj2 = new ClassWithCustomHashCode();

$map[$obj1] = "A value";
$map[$obj2] = "Another value";

echo PHP_EOL;
echo json_encode(
  [
    'get $map[$obj1]' => $map[$obj1],
    'isset($map[$obj1])' => isset($map[$obj1]),
    'get $map[$obj2]' => $map[$obj2],
    'isset($map[$obj2])' => isset($map[$obj2]),
  ],
  JSON_PRETTY_PRINT
);
echo PHP_EOL;

echo PHP_EOL;
echo json_encode(['count($map)' => count($map)], JSON_PRETTY_PRINT);
echo PHP_EOL;

echo PHP_EOL;

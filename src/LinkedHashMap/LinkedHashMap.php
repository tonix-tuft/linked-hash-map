<?php

/*
 * Copyright (c) 2021 Anton Bagdatyev (Tonix)
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 */

namespace LinkedHashMap;

use DeclarativeFactory\DeclarativeFactory;
use Fun\Fun;
use IntHash\Hasher;
use LinkedHashMap\Behaviours\InsertMode\AppendInsertModeBehaviour;
use LinkedHashMap\Behaviours\InsertMode\PrependInsertModeBehaviour;
use LinkedHashMap\Behaviours\LoopOrder\NormalLoopOrderBehaviour;
use LinkedHashMap\Behaviours\LoopOrder\ReverseLoopOrderBehaviour;
use LinkedHashMap\LinkedHashMapNode;
use Tonix\PHPUtils\ArrayUtils;
use Tonix\PHPUtils\IntUtils;

/**
 * A linked hash map data structure.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class LinkedHashMap implements \Countable, \Iterator, \ArrayAccess {
  /**
   * Insert mode (append). This is the default insert mode.
   */
  const INSERT_MODE_APPEND = 1;

  /**
   * Insert mode (prepend).
   */
  const INSERT_MODE_PREPEND = 2;

  /**
   * Normal loop order (from first to last element). This is the default loop order.
   */
  const LOOP_ORDER_NORMAL = 1;

  /**
   * Reverse loop order (from last to first element).
   */
  const LOOP_ORDER_REVERSE = 2;

  /**
   * Primes used to define the internal multi-dimensional array of buckets.
   */
  const DIMENSION_PRIMES = [
    1147488061, // 0
    1147485919, // 1
    1147483837, // 2
    860617801, // 3
    573745439, // 4
    430311703, // 5
    215158439, // 6
    143438059, // 7
    71721511, // 8
    39447563, // 9
    19725653, // 10
    9865993, // 11
    4933301, // 12
    2468657, // 13
    1470373, // 14
    769429, // 15
    371311, // 16
    169199, // 17
    88721, // 18
    49741, // 19
    25457, // 20
    11261, // 21
    5657, // 22
    2593, // 23
    1777, // 24
    1291, // 25
    997, // 26
    863, // 27
    431, // 28
    233, // 29
    109, // 30
    83, // 31
    41, // 32
    23, // 33
    11, // 34
    5, // 35
    2, // 36
  ];

  /**
   * @var array
   */
  protected $bucketsArray = [];

  /**
   * @var int
   */
  protected $count = 0;

  /**
   * @var int
   */
  protected $nextPositionalOffset = 0;

  /**
   * @var int
   */
  protected $longestBucketLinkedListSize = 0;

  /**
   * @var LinkedHashMapNode|null
   */
  protected $headNode = null;

  /**
   * @var LinkedHashMapNode|null
   */
  protected $tailNode = null;

  /**
   * @var int Either INSERT_MODE_APPEND|INSERT_MODE_PREPEND
   */
  protected $insertMode = self::INSERT_MODE_APPEND;

  /**
   * @var int Either LOOP_ORDER_NORMAL|LOOP_ORDER_REVERSE
   */
  protected $loopOrder = self::LOOP_ORDER_NORMAL;

  /**
   * @var NormalLoopOrderBehaviour|ReverseLoopOrderBehaviour
   */
  protected $loopOrderBehaviour;

  /**
   * @var AppendInsertModeBehaviour|PrependInsertModeBehaviour
   */
  protected $insertModeBehaviour;

  /**
   * @var LinkedHashMapNode|null
   */
  protected $currentIteratedNode = null;

  /**
   * Constructs a new linked hash map.
   *
   * @param array[] An array of initial key/value tuples to store in the map,
   *                i.e. each element being an array with the key at index 0 and the value at index 1, such that:
   *
   *                    - The key at index 0 can be any PHP data type (mixed).
   *                      If the key is an object implementing the {@link \LinkedHashMap\HashCodeInterface},
   *                      then its {@link \LinkedHashMap\HashCodeInterface::hashCode()} method will be used to compute
   *                      the hash used for the storing the key and its value at the corresponding indices
   *                      in the multidimensional array of buckets;
   *
   *                    - The value at index 1 can also be any PHP data type (mixed).
   *
   * @param int $insertMode The insert mode (either {@link \LinkedHashMap\LinkedHashMap::INSERT_MODE_APPEND}
   *                        or {@link \LinkedHashMap\LinkedHashMap::INSERT_MODE_PREPEND}).
   *
   *                        The default mode is {@link \LinkedHashMap\LinkedHashMap::INSERT_MODE_APPEND}.
   *
   * @param int $loopOrder The loop order (either {@link \LinkedHashMap\LinkedHashMap::LOOP_ORDER_NORMAL}
   *                       or {@link \LinkedHashMap\LinkedHashMap::LOOP_ORDER_REVERSE}).
   *
   *                       The default mode is {@link \LinkedHashMap\LinkedHashMap::LOOP_ORDER_NORMAL}.
   *
   */
  public function __construct(
    $keyValueTuples = [],
    $insertMode = self::INSERT_MODE_APPEND,
    $loopOrder = self::LOOP_ORDER_NORMAL
  ) {
    $this->insertMode = $insertMode;
    $this->loopOrder = $loopOrder;
    $this->initBehaviours();
    foreach ($keyValueTuples as $keyValueTuple) {
      [$key, $value] = $keyValueTuple;
      $this->offsetSet($key, $value);
    }
  }

  /**
   * Initializes the internal behaviours of the linked hash map.
   *
   * @return void
   */
  protected function initBehaviours() {
    $this->initInsertModeBehaviour();
    $this->initLoopOrderBehaviour();
  }

  /**
   * Initializes the insert mode behaviour of the linked hash map.
   *
   * @return void
   */
  protected function initInsertModeBehaviour() {
    $insertModeBehaviour = DeclarativeFactory::factory([
      [
        $this->insertMode === self::INSERT_MODE_PREPEND,
        Fun::fnReturnNew(PrependInsertModeBehaviour::class),
      ],
      Fun::fnReturnNew(AppendInsertModeBehaviour::class),
    ]);
    $this->insertModeBehaviour = $insertModeBehaviour;
  }

  /**
   * Initializes the loop order behaviour of the linked hash map.
   *
   * @return void
   */
  protected function initLoopOrderBehaviour() {
    $loopOrderBehaviour = DeclarativeFactory::factory([
      [
        $this->loopOrder === self::LOOP_ORDER_REVERSE,
        Fun::fnReturnNew(ReverseLoopOrderBehaviour::class),
      ],
      Fun::fnReturnNew(NormalLoopOrderBehaviour::class),
    ]);
    $this->loopOrderBehaviour = $loopOrderBehaviour;
  }

  /**
   * Sets the insert mode (either append or prepend insert mode).
   *
   * @param int $insertMode The insert mode (either {@link \LinkedHashMap\LinkedHashMap::INSERT_MODE_APPEND}
   *                        or {@link \LinkedHashMap\LinkedHashMap::INSERT_MODE_PREPEND}).
   *
   *                        The default mode is {@link \LinkedHashMap\LinkedHashMap::INSERT_MODE_APPEND}.
   * @return void
   */
  public function setInsertMode($insertMode) {
    $this->insertMode = $insertMode;
    $this->initInsertModeBehaviour();
  }

  /**
   * Sets the loop order (either from the first element to the last or in reverse order,
   * i.e. from the last to the first element).
   *
   * @param int $loopOrder The loop order (either {@link \LinkedHashMap\LinkedHashMap::LOOP_ORDER_NORMAL}
   *                       or {@link \LinkedHashMap\LinkedHashMap::LOOP_ORDER_REVERSE}).
   *
   *                       The default mode is {@link \LinkedHashMap\LinkedHashMap::LOOP_ORDER_NORMAL}.
   * @return void
   */
  public function setLoopOrder($loopOrder) {
    $this->loopOrder = $loopOrder;
    $this->initLoopOrderBehaviour();
  }

  /**
   * Returns the hash for the given key.
   *
   * @param mixed $key The key.
   * @return int The hash.
   */
  protected function getHashForKey($key) {
    $hasher = DeclarativeFactory::factory([
      [
        $key instanceof HashCodeInterface,
        function () {
          return function (HashCodeInterface $key) {
            $hash = $key->hashCode();
            return $hash;
          };
        },
      ],
      function () {
        return function ($key) {
          $hasher = new Hasher();
          $hash = $hasher->hash($key);
          return $hash;
        };
      },
    ]);
    $hash = $hasher($key);
    return $hash;
  }

  /**
   * Retrieves a node.
   *
   * @param mixed $key The key.
   * @param int $hash The hash of the key.
   * @param array $bucketsArrayIndicesPath The indices path for the given hash in the multidimensional array of buckets.
   * @param callable|null $onMissingNode An optional callable returning a node ({@link \LinkedHashMap\LinkedHashMapNode}) to return to the caller
   *                                     if a node for the given key and hash is missing.
   * @return LinkedHashMapNode|null The retrieved node or the node returned by the `$onMissingNode` callback
   *                                if the node for the given key and hash is missing and the `$onMissingNode` callback is passed.
   *                                If the node is missing and the `$onMissingNode` callback is not passed, NULL is returned.
   */
  protected function retrieveNode(
    $key,
    $hash,
    $bucketsArrayIndicesPath,
    $onMissingNode = null
  ) {
    $node = ArrayUtils::nestedArrayValue(
      $this->bucketsArray,
      $bucketsArrayIndicesPath
    );

    $prevBucketNode = null;
    $bucketLinkedListSize = 0;
    if ($node instanceof LinkedHashMapNode) {
      do {
        $bucketLinkedListSize++;

        // prettier-ignore
        if (
          $node->hash === $hash &&
          (
            $node->key === $key
            ||
            // Integer string/int type juggling:
            (
              IntUtils::isIntOrIntString($node->key)
              &&
              IntUtils::isIntOrIntString($key)
              &&
              $node->key == $key
            )
          )
        ) {
          // Node found.
          break;
        }

        // After this iteration, the current node becomes the previous one of the bucket.
        $prevBucketNode = $node;
      } while (($node = $node->nextBucketNode) instanceof LinkedHashMapNode);
      if ($this->longestBucketLinkedListSize < $bucketLinkedListSize) {
        $this->longestBucketLinkedListSize = $bucketLinkedListSize;
      }
    }
    if ($node === null && is_callable($onMissingNode)) {
      $node = $onMissingNode(
        $key,
        $hash,
        $bucketsArrayIndicesPath,
        $bucketLinkedListSize,
        $prevBucketNode
      );
    }
    return $node;
  }

  /**
   * Returns the indices path for the given hash in the multidimensional array of buckets.
   *
   * @param int $hash The hash of the key.
   * @return array The indices path for the given hash in the multidimensional array of buckets.
   */
  protected function getBucketsArrayIndicesPathForHash($hash) {
    $nodePath = [];
    foreach (self::DIMENSION_PRIMES as $dimensionPrime) {
      $index = $hash % $dimensionPrime;
      if ($index < 0) {
        $index = $dimensionPrime + $index;
      }
      $nodePath[] = $index;
    }
    return $nodePath;
  }

  /**
   * Creates a new node.
   *
   * @param mixed $key The key.
   * @param int $hash The hash of the key.
   * @param array $bucketsArrayIndicesPath The indices path for the given hash in the multidimensional array of buckets.
   * @param LinkedHashMapNode|null $prevBucketNode The previous node in the bucket, or NULL if there isn't any.
   * @return LinkedHashMapNode The new node.
   */
  protected function createNewNode(
    $key,
    $hash,
    $bucketsArrayIndicesPath,
    $prevBucketNode = null
  ) {
    $node = new LinkedHashMapNode($key, $hash);
    if (
      ArrayUtils::nestedArrayValue(
        $this->bucketsArray,
        $bucketsArrayIndicesPath
      ) === null
    ) {
      $nodePath = array_merge($bucketsArrayIndicesPath, [$node]);
      ArrayUtils::setNestedArrayValue($this->bucketsArray, ...$nodePath);
    }
    if ($prevBucketNode instanceof LinkedHashMapNode) {
      $prevBucketNode->nextBucketNode = $node;
      $node->prevBucketNode = $prevBucketNode;
    }
    $this->headNode = $this->insertModeBehaviour->linkHeadNode(
      $this->headNode,
      $node
    );
    $this->tailNode = $this->insertModeBehaviour->linkTailNode(
      $this->tailNode,
      $node
    );
    return $node;
  }

  /**
   * {@inheritdoc}
   */
  public function count() {
    return $this->count;
  }

  /**
   * {@inheritdoc}
   */
  public function rewind() {
    $this->currentIteratedNode = $this->loopOrderBehaviour->rewindIteratedNode(
      $this->headNode,
      $this->tailNode
    );
  }

  /**
   * {@inheritdoc}
   */
  public function current() {
    $value = null;
    if ($this->currentIteratedNode instanceof LinkedHashMapNode) {
      $value = $this->currentIteratedNode->value;
    }
    return $value;
  }

  /**
   * {@inheritdoc}
   */
  public function key() {
    $key = null;
    if ($this->currentIteratedNode instanceof LinkedHashMapNode) {
      $key = $this->currentIteratedNode->key;
    }
    return $key;
  }

  /**
   * {@inheritdoc}
   */
  public function next() {
    $this->currentIteratedNode = $this->loopOrderBehaviour->nextIteratedNode(
      $this->currentIteratedNode
    );
  }

  /**
   * {@inheritdoc}
   */
  public function valid() {
    $valid = $this->currentIteratedNode instanceof LinkedHashMapNode;
    return $valid;
  }

  /**
   * {@inheritdoc}
   */
  public function offsetExists($offset) {
    $hash = $this->getHashForKey($offset);
    $bucketsArrayIndicesPath = $this->getBucketsArrayIndicesPathForHash($hash);

    /**
     * @var LinkedHashMapNode $node
     */
    $node = $this->retrieveNode($offset, $hash, $bucketsArrayIndicesPath);

    $exists = $node instanceof LinkedHashMapNode;
    return $exists;
  }

  /**
   * {@inheritdoc}
   */
  public function offsetGet($offset) {
    $hash = $this->getHashForKey($offset);
    $bucketsArrayIndicesPath = $this->getBucketsArrayIndicesPathForHash($hash);

    /**
     * @var LinkedHashMapNode $node
     */
    $node = $this->retrieveNode($offset, $hash, $bucketsArrayIndicesPath);

    $value = null;
    if ($node instanceof LinkedHashMapNode) {
      $value = $node->value;
    }
    return $value;
  }

  /**
   * {@inheritdoc}
   */
  public function offsetSet($offset, $value) {
    $shouldIncrementNextPositionalOffset = false;
    $shouldOverrideNextPositionalOffset = false;
    if (is_null($offset)) {
      // Positional append/prepend.
      $offset = $this->nextPositionalOffset;
      $shouldIncrementNextPositionalOffset = true;

      /**
       * If `$this->nextPositionalOffset > PHP_INT_MAX`, then it means that `$this->nextPositionalOffset` became a double
       * (i.e. `$this->nextPositionalOffset` is not an int anymore).
       *
       * With built-in PHP arrays, appending to an array which has reached its PHP_INT_MAX offset
       * will not append the element and will emit the following warning:
       *
       *     `Warning: Cannot add element to the array as the next element is already occupied`.
       *
       */
      if (!is_int($this->nextPositionalOffset)) {
        trigger_error(
          'Warning: Cannot add element to the linked hash map as the next element is already occupied',
          E_USER_WARNING
        );
        return;
      }
    } elseif (
      IntUtils::isIntOrIntString($offset) &&
      is_int($this->nextPositionalOffset) &&
      $offset >= $this->nextPositionalOffset
    ) {
      // Positive integer greater than or equal to the current next positional offset.
      $shouldIncrementNextPositionalOffset = true;
      $shouldOverrideNextPositionalOffset = true;
    }

    $hash = $this->getHashForKey($offset);
    $bucketsArrayIndicesPath = $this->getBucketsArrayIndicesPathForHash($hash);

    $onMissingNode = function (
      $key,
      $hash,
      $bucketsArrayIndicesPath,
      $bucketLinkedListSize,
      $prevBucketNode = null
    ) {
      /**
       * @var LinkedHashMapNode $node
       */
      $node = $this->createNewNode(
        $key,
        $hash,
        $bucketsArrayIndicesPath,
        $prevBucketNode
      );
      $this->count++;
      if ($this->longestBucketLinkedListSize <= 0) {
        $this->longestBucketLinkedListSize = 1;
      }
      if ($bucketLinkedListSize + 1 > $this->longestBucketLinkedListSize) {
        $this->longestBucketLinkedListSize = $bucketLinkedListSize + 1;
      }
      return $node;
    };

    /**
     * @var LinkedHashMapNode $node
     */
    $node = $this->retrieveNode(
      $offset,
      $hash,
      $bucketsArrayIndicesPath,
      $onMissingNode
    );
    $node->value = $value;

    if ($shouldOverrideNextPositionalOffset) {
      $this->nextPositionalOffset = $offset;
    }
    if ($shouldIncrementNextPositionalOffset) {
      $this->nextPositionalOffset++;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function offsetUnset($offset) {
    $hash = $this->getHashForKey($offset);
    $bucketsArrayIndicesPath = $this->getBucketsArrayIndicesPathForHash($hash);

    /**
     * @var LinkedHashMapNode $node
     */
    $node = $this->retrieveNode($offset, $hash, $bucketsArrayIndicesPath);

    if ($node instanceof LinkedHashMapNode) {
      $prevNode = $node->prevNode;
      $nextNode = $node->nextNode;
      $prevBucketNode = $node->prevBucketNode;
      $nextBucketNode = $node->nextBucketNode;

      if ($prevNode instanceof LinkedHashMapNode) {
        // There is a previous node.
        $prevNode->nextNode = $nextNode;
      }
      if ($nextNode instanceof LinkedHashMapNode) {
        // There is a next node.
        $nextNode->prevNode = $prevNode;
      }

      if ($prevBucketNode instanceof LinkedHashMapNode) {
        $prevBucketNode->nextBucketNode = $nextBucketNode;
      }
      if ($nextBucketNode instanceof LinkedHashMapNode) {
        $nextBucketNode->prevBucketNode = $prevBucketNode;
      }

      if ($this->headNode === $node) {
        $this->headNode = $nextNode;
      }
      if ($this->tailNode === $node) {
        $this->tailNode = $prevNode;
      }

      if (!($prevBucketNode instanceof LinkedHashMapNode)) {
        $nodePath = array_merge($bucketsArrayIndicesPath, [$nextBucketNode]);
        ArrayUtils::setNestedArrayValue($this->bucketsArray, ...$nodePath);
      }
      $this->count--;

      if ($offset === PHP_INT_MAX) {
        $this->nextPositionalOffset = PHP_INT_MAX;
      }
    }
  }
}

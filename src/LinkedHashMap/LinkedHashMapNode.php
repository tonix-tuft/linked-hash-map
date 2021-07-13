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

/**
 * The node of a linked hash map.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class LinkedHashMapNode {
  /**
   * @var mixed
   */
  public $key;

  /**
   * @var int
   */
  public $hash;

  /**
   * @var mixed
   */
  public $value;

  /**
   * @var LinkedHashMapNode|null
   */
  public $nextNode = null;

  /**
   * @var LinkedHashMapNode|null
   */
  public $prevNode = null;

  /**
   * @var LinkedHashMapNode|null
   */
  public $nextBucketNode = null;

  /**
   * @var LinkedHashMapNode|null
   */
  public $prevBucketNode = null;

  /**
   * Constructs a new node.
   *
   * @param mixed $key The key associated with the value.
   * @param int $hash The hash of the key.
   * @param mixed $value The value associated with the key.
   * @param LinkedHashMapNode|null $nextNode The next node of the corresponding linked hash map.
   * @param LinkedHashMapNode|null $prevNode The previous node of the corresponding linked hash map.
   * @param LinkedHashMapNode|null $nextBucketNode The next node of the multidimensional array of buckets of the corresponding linked hash map.
   * @param LinkedHashMapNode|null $prevBucketNode The previous node of the multidimensional array of buckets of the corresponding linked hash map.
   */
  public function __construct(
    $key,
    $hash,
    $value = null,
    $nextNode = null,
    $prevNode = null,
    $nextBucketNode = null,
    $prevBucketNode = null
  ) {
    $this->key = $key;
    $this->hash = $hash;
    $this->value = $value;
    $this->nextNode = $nextNode;
    $this->prevNode = $prevNode;
    $this->nextBucketNode = $nextBucketNode;
    $this->prevBucketNode = $prevBucketNode;
  }
}

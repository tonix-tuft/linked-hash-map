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

namespace LinkedHashMap\Behaviours\LoopOrder;

use LinkedHashMap\LinkedHashMapNode;

/**
 * Normal loop order behaviour.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class NormalLoopOrderBehaviour {
  /**
   * Returns the current iterated node when the linked hash map's iterator is rewound.
   *
   * @param LinkedHashMapNode|null $headNode The current head node, or NULL if there isn't any.
   * @param LinkedHashMapNode|null $tailNode The current tail node, or NULL if there isn't any.
   * @return LinkedHashMapNode The current iterated node.
   */
  public function rewindIteratedNode($headNode, $tailNode) {
    return $headNode;
  }

  /**
   * Returns the next iterated node when the linked hash map's iterator moves on.
   *
   * @param LinkedHashMapNode|null $headNode The current iterated node, or NULL if there isn't any.
   * @return LinkedHashMapNode|null The next iterated node, or NULL if there isn't any.
   */
  public function nextIteratedNode($node) {
    $nextIteratedNode = null;
    if ($node instanceof LinkedHashMapNode) {
      $nextIteratedNode = $node->nextNode;
    }
    return $nextIteratedNode;
  }
}

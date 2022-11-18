<?php

/*
 * This file is part of the API Platform project.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Soyuka\Dto;

use Symfony\Component\Marshaller\Attribute\Name;

final class Collection implements \IteratorAggregate
{
    #[Name('hydra:member')]
    public array $collection;

    #[Name('@type')]
    public string $type = 'hydra:Collection';

    #[Name('hydra:totalItems')]
    public int $totalItems = 0;

    public function __construct(...$collection)
    {
        $this->collection = $collection;
        $this->totalItems = \count($collection);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->collection);
    }
}


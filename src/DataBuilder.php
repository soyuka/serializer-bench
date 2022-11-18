<?php

namespace Soyuka;

use Soyuka\Dto\Collection;
use Soyuka\Dto\Element;
use Soyuka\Dto\Relation;

class DataBuilder
{
    static Collection $data;

    static public function build(): void {
        $relations = [];
        for ($i = 0; $i < 1000; $i++) {
            $relations[] = new Relation(id: $i, value: bin2hex(random_bytes(10)), createdAt: new \DateTimeImmutable());
        }

        $collection = [];
        for ($i = 0; $i < 10000; $i++) {
            $collection[] = new Element(id: $i, price: (float) sprintf('%s.%s', random_int(1, 100), random_int(1, 10)), relation: $relations[array_rand($relations)]);
        }

        static::$data = new Collection($collection);
    }
}

<?php

namespace Soyuka;

use Soyuka\Dto\Collection;
use Soyuka\Dto\Element;
use Soyuka\Dto\Relation;

class DataBuilder
{
    static Collection $data;
    static int $num = 10000;

    static public function build(): void {
        $relations = [];
        for ($i = 0; $i < self::$num / 10; $i++) {
            $relations[] = new Relation(id: $i, value: bin2hex(random_bytes(10)), createdAt: new \DateTimeImmutable());
        }

        $collection = [];
        for ($i = 0; $i < self::$num; $i++) {
            $collection[] = new Element(id: $i, price: (float) sprintf('%s.%s', random_int(1, 100), random_int(1, 10)), relation: $relations[array_rand($relations)]);
        }

        static::$data = new Collection(...$collection);
    }
}

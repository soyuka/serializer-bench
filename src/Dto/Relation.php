<?php

namespace Soyuka\Dto;

class Relation
{
    public function __construct(
        public int $id,
        public \DateTimeImmutable $createdAt,
        public string $value
    ) {}
}

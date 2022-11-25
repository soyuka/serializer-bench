<?php

namespace Soyuka\Dto;

use Symfony\Component\Marshaller\Attribute\Formatter;

class Relation
{
    public function __construct(
        public int $id,
        #[Formatter([self::class, 'formatDate'])]
        public \DateTimeImmutable $createdAt,
        public string $value
    ) {}

    public static function formatDate(\DateTimeInterface $value, array $context): string {
        return $value->format('dmY');
    }
}

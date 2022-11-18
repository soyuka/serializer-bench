<?php

namespace Soyuka\Dto;

class Element
{
    public function __construct(public int $id, public float $price, public Relation $relation) {}
}

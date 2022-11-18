<?php
namespace Soyuka;

class TimeConsumer
{
    public function __construct() {
        DataBuilder::build();
    }

    public function consume()
    {
        usleep(100);
    }
}

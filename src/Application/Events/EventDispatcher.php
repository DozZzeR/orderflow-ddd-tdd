<?php

namespace OrderFlow\Application\Events;

interface EventDispatcher
{
    public function dispatch(object $event): void;
}
<?php

namespace Spatie\Browsershot\Enums;

enum Polling: string
{
    case RequestAnimationFrame = 'raf';
    case Mutation = 'mutation';
}

<?php

namespace App\Interfaces;

interface IDangerStrategy
{
    public function check(array $data): ?string;
}

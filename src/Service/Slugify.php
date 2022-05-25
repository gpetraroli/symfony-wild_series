<?php

namespace App\Service;

class Slugify
{
    public function generate(string $input): string
    {
        return str_replace(' ', '-', $input);
    }

}

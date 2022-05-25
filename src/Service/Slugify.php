<?php

namespace App\Service;

class Slugify
{
    public function generate(string $input): string
    {
        $separator = '-';
        // replace non letter or digits by divider
        $slug = preg_replace('~[^\pL\d]+~u', $separator, $input);

        // remove unwanted characters
        $slug = preg_replace('~[^-\w]+~', '', $slug);

        // trim
        $slug = trim($slug);

        // remove duplicate divider
        $slug = preg_replace('~-+~', $separator, $slug);

        // lowercase
        $slug = strtolower($slug);

        if (empty($slug)) {
            return 'n-a';
        }

        return $slug;
    }

}

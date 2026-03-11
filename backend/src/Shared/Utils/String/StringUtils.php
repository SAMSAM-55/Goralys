<?php

namespace Goralys\Shared\Utils\String;

use Goralys\Shared\Utils\String\Data\StringCase;

class StringUtils
{
    public function sanitize(string $s, StringCase $c = StringCase::NONE): string
    {
        $temp =  trim(str_replace(
            ['à','â','ä','á','ã','å','À','Â','Ä','Á','Ã','Å',
                'è','ê','ë','é','È','Ê','Ë','É',
                'ì','î','ï','í','Ì','Î','Ï','Í',
                'ò','ô','ö','ó','õ','ø','Ò','Ô','Ö','Ó','Õ','Ø',
                'ù','û','ü','ú','Ù','Û','Ü','Ú',
                'ý','ÿ','Ý',
                'ñ','Ñ',
                'ç','Ç',
                'æ','Æ','œ','Œ'],
            ['a','a','a','a','a','a','A','A','A','A','A','A',
                'e','e','e','e','E','E','E','E',
                'i','i','i','i','I','I','I','I',
                'o','o','o','o','o','o','O','O','O','O','O','O',
                'u','u','u','u','U','U','U','U',
                'y','y','Y',
                'n','N',
                'c','C',
                'ae','AE','oe','OE'],
            $s
        ));
        return match ($c) {
            StringCase::NONE => $temp,
            StringCase::LOWER => strtolower($temp),
            StringCase::UPPER => strtoupper($temp)
        };
    }
}

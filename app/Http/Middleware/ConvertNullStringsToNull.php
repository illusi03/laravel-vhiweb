<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;

class ConvertNullStringsToNull extends TransformsRequest
{
    /**
     * Transform the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function transform($key, $value)
    {
        if (is_string($value)) {
            $isNullStr = strtolower($value) === 'null';
            $isUndefinedStr = strtolower($value) === 'undefined';
            if ($isUndefinedStr || $isNullStr) {
                return null;
            } else {
                return $value;
            }
        }
        return $value;
    }
}

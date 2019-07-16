<?php

namespace W7\Http\Message\Helper;

use W7\Contract\Arrayable;

/**
 * Class JsonHelper
 * @package W7\Helper
 */
class JsonHelper
{

    /**
     * Wrapper for json_decode that throws when an error occurs.
     *
     * @param string $json    JSON data to parse
     * @param bool   $assoc   When true, returned objects will be converted
     *                        into associative arrays.
     * @param int    $depth   User specified recursion depth.
     * @param int    $options Bitmask of JSON decode options.
     * @return mixed
     * @throws \InvalidArgumentException if the JSON cannot be decoded.
     * @link http://www.php.net/manual/en/function.json-decode.php
     */
    public static function decode($json, $assoc = false, $depth = 512, $options = 0)
    {
        $data = \json_decode($json, $assoc, $depth, $options);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException('json_decode error: ' . json_last_error_msg());
        }

        return $data;
    }

    /**
     * Wrapper for JSON encoding that throws when an error occurs.
     *
     * @param mixed $value   The value being encoded
     * @param int   $options JSON encode option bitmask
     * @param int   $depth   Set the maximum depth. Must be greater than zero.
     * @return string
     * @throws \InvalidArgumentException if the JSON cannot be encoded.
     * @link http://www.php.net/manual/en/function.json-encode.php
     */
    public static function encode($value, $options = 0, $depth = 512): string
    {
        if ($value instanceof Arrayable) {
            $value = $value->toArray();
        }
        $json = \json_encode($value, $options, $depth);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException('json_encode error: ' . json_last_error_msg());
        }

        return $json;
    }
}

<?php

if (!function_exists('formatResponseArray')) {

    /**
     * Format response array
     *
     * @param array $data    Data format
     * @param bool  $isTrans Translate check
     *
     * @return array
     */
    function formatResponseArray($data, $isTrans = false)
    {
        array_walk($data, function (&$item, $key) use ($isTrans) {
            $item = [
                'id' => $key,
                'name' => $isTrans ? trans($item) : $item,
            ];
        });
        return array_values($data);
    }
}

if (!function_exists('transArr')) {

    /**
     * Translate with array
     *
     * @param array $data   Data format
     * @param array $locale Locale
     * @param bool  $isFlip Check use flip array
     *
     * @return array
     */
    function transArr(array $data, $locale = null, bool $isFlip = false)
    {
        $locales = explode(",", $locale);
        $arr = [];

        foreach ($data as $key => $value) {
            foreach ($locales as $locale) {
                if ($isFlip) {
                    $arr[trans($value, [], 'messages', $locale)] = $key;
                } else {
                    $arr[$key] = trans($value, [], 'messages', $locale);
                }
            }
        }

        return $arr;
    }
}

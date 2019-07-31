<?php

use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;

if (!function_exists('transArr')) {

    /**
     * Translate with array
     *
     * @param array  $data   Data format
     * @param string $locale Locale
     * @param bool   $isFlip Check use flip array
     *
     * @return array
     */
    function transArr(array $data, string $locale = null, bool $isFlip = false)
    {
        $locales = explode(",", $locale);

        $arr = [];

        foreach ($data as $key => $value) {
            foreach ($locales as $locale) {
                if ($isFlip) {
                    $arr[trans($value, [], $locale)] = $key;
                } else {
                    $arr[$key] = trans($value, [], $locale);
                }
            }
        }

        return $arr;
    }
}

if (!function_exists('toPgArray')) {

    /**
     * Convert to array postgres
     *
     * @param array $data Data
     *
     * @return bool
     */
    function toPgArray(array $data)
    {
        $result = [];
        foreach ($data as $value) {
            if (is_array($value)) {
                $result[] = toPgArray($value);
            } else {
                $value = str_replace('"', '\\"', $value);
                if (!is_numeric($value)) {
                    $value = '"' . $value . '"';
                }
                $result[] = $value;
            }
        }

        return '{' . implode(",", $result) . '}';
    }
}

if (!function_exists('arrayEncoding')) {

    /**
     * Convert encoding from array
     *
     * @param array  $data Data
     * @param string $to   To encoding
     * @param string $from From encoding
     *
     * @return array
     */
    function arrayEncoding(array $data, string $to = 'UTF-8', string $from = 'UTF-8')
    {
        array_walk_recursive($data, function (&$value, $key) use ($from, $to) {
            if (is_string($value)) {
                $value = mb_convert_encoding($value, $to, $from);
            }
        });

        return $data;
    }
}

if (!function_exists('getSql')) {

    /**
     * Convert encoding from array
     *
     * @param Builder $builder Query builder
     *
     * @return array
     */
    function getSql($builder)
    {
        $sql = $builder->toSql();
        foreach ($builder->getBindings() as $binding) {
            $value = is_numeric($binding) ? $binding : "'" . $binding . "'";
            $sql = preg_replace('/\?/', $value, $sql, 1);
        }

        return $sql;
    }
}

if (!function_exists('getCurrentUserLogin')) {

    /**
     * Get Current User Login
     *
     * @param array $fields Fields
     *
     * @return User
     */
    function getCurrentUserLogin($fields = ['*'])
    {
        $user = Auth::user();

        if ($user) {
            return $user->setVisible($fields);
        }

        return null;
    }
}

if (!function_exists('getSortConditions')) {

    /**
     * Get sort condition
     *
     * @param array  $data              Data
     * @param array  $sortColumns       Sort Columns
     * @param string $sortColumnDefault Sort column Default
     *
     * @return array
     */
    function getSortConditions(array $data = [], array $sortColumns = [], string $sortColumnDefault = 'id')
    {
        $sortDirections = ['desc', 'asc'];
        if (!isset($data['sort_column']) || !in_array($data['sort_column'], $sortColumns)) {
            $data['sort_column'] = isset($sortColumns[$data['sort_column']]) ? $sortColumns[$data['sort_column']]
                : $sortColumnDefault;
        }

        if (!in_array($data['sort_direction'], $sortDirections)) {
            $data['sort_direction'] = 'desc';
        }

        return $data;
    }
}

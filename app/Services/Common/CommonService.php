<?php

namespace App\Services\Common;

class CommonService
{

    /**
     * Get sort data
     *
     * @param array  $data          Data
     * @param array  $sortColumns   Sort columns
     * @param string $defaultColumn Default Column
     *
     * @return array
     */
    public function getSortData(
        array $data = [],
        array $sortColumns = [],
        string $defaultColumn = 'id'
    ) {
        if (empty($data) || empty($sortColumns)) {
            return [
                'sort_column' => $defaultColumn,
                'sort_direction' => 'desc',
            ];
        }

        $column = $data['sort_column'] ?? $defaultColumn;

        if (!in_array($column, $sortColumns)) {
            $column = $defaultColumn;
        }

        $directions = ['desc', 'asc'];

        $direction = $data['sort_direction'] ?? 'desc';

        if (!in_array($direction, $directions)) {
            $direction = 'desc';
        }

        return [
            'sort_column' => $column,
            'sort_direction' => $direction,
        ];
    }
}

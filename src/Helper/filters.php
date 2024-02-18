<?php

use Contatoseguro\TesteBackend\Enum\FilterTypes;

function get_filters_query(array $queryParams, $allowedFilters = [])
{
    $filters = isset($queryParams['filter']) ? $queryParams['filter'] : [];
    $filtersQuery = '';

    foreach ($filters as $key => $value) {
        $filter = $allowedFilters[$key];

        switch ($filter->type) {
            case FilterTypes::Date:
                $filtersQuery .= "AND STRFTIME('%Y-%m-%d', $filter->columnName) = '$value'";
                break;
            
            default:
                $filtersQuery .= "AND $filter->columnName = $value ";
                break;
        }

    }

    return $filtersQuery;
}

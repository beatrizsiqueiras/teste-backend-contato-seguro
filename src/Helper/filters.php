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
                $date = DateTime::createFromFormat('d/m/Y', $value)->format('Y-m-d');
                $filtersQuery .= "AND STRFTIME('%Y-%m-%d', $filter->columnName) = '$date' ";
                break;

            case FilterTypes::CompareString:
                $filtersQuery .= "AND $filter->columnName LIKE '%$value%' ";
                break;

            case FilterTypes::String:
                $filtersQuery .= "AND $filter->columnName = '$value' ";
                break;

            default:
                $filtersQuery .= "AND $filter->columnName = $value ";
                break;
        }
    }
    return $filtersQuery;
}

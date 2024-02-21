<?php

use ContatoSeguro\TesteBackend\Enum\FilterTypes;
use ContatoSeguro\TesteBackend\Enum\OrderByDirection;

function get_filters_query(array $queryParams, array $allowedFilters = []): string
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
            case FilterTypes::Number:
                $filtersQuery .= "AND $filter->columnName = $value ";
                break;
            default:
                $filtersQuery .= "";
                break;
        }
    }
    return $filtersQuery;
}

function get_sorting_query(array $queryParams): string
{
    $sortingQuery = '';
    $orderByColumn = isset($queryParams['orderBy']) ? $queryParams['orderBy'] : null;
    $orderByDirection = isset($queryParams['orderByDirection']) ? OrderByDirection::from($queryParams['orderByDirection'])->value : OrderByDirection::ASC->value;

    if ($orderByColumn) {
        $sortingQuery = " ORDER BY $orderByColumn $orderByDirection";
    }

    return $sortingQuery;
}

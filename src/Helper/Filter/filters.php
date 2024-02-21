<?php

use ContatoSeguro\TesteBackend\Enum\FilterTypes;
use ContatoSeguro\TesteBackend\Enum\SortDirection;

function get_filters_query(array $queryParams, array $allowedFilters = []): string
{
    $filters = isset($queryParams['filter']) ? $queryParams['filter'] : [];
    $filtersQuery = '';

    if (count($filters)) {
        foreach ($filters as $key => $value) {
            $filter = isset($allowedFilters[$key]) ? $allowedFilters[$key] : false;
    
            if (!$filter) {
                break;
            }

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
    }
    
    return $filtersQuery;
}

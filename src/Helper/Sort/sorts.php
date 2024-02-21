<?php

use ContatoSeguro\TesteBackend\Enum\SortDirection;
use ContatoSeguro\TesteBackend\Helper\Sort\AllowedSort;

function get_sorting_query(array $queryParams, array $allowedSorts): string
{
    $sorts = isset($queryParams['sort']) ? explode(',', $queryParams['sort']) : null;
    $sortingQuery = '';

    if ($sorts) {
        $sortDirection = SortDirection::ASC->value;
        $sortsAddedToQuery = 0;

        foreach ($sorts as $key => $sortName) {
            if (str_starts_with($sortName, '-')) {
                $sortName = substr($sortName, 1);
                $sortDirection = SortDirection::DESC->value;
            }
    
            $allowedSort = isset($allowedSorts[$sortName]) ? $allowedSorts[$sortName] : false;
    
            if (!$allowedSort) {
                continue;
            }
    
            if ($sortsAddedToQuery === 0) {
                $sortingQuery .= " ORDER BY";
            } else {
                $sortingQuery .= ", ";
            }
            
            $sortingQuery .= " $allowedSort->columnName $sortDirection";
            $sortsAddedToQuery++;
        }
    }

    return $sortingQuery;
}
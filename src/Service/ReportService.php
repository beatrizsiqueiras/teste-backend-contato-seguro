<?php

namespace ContatoSeguro\TesteBackend\Service;

use ContatoSeguro\TesteBackend\Config\DB;

class ReportService
{
    private ProductService $productService;
    private ProductLogService $productLogService;
    private CompanyService $companyService;

    public function __construct()
    {
        $this->productService = new ProductService();
        $this->productLogService = new ProductLogService();
        $this->companyService = new CompanyService();
    }

    public function generateReport($adminUserId): string
    {

        $data = [];
        $data[] = [
            'Id do produto',
            'Nome da Empresa',
            'Nome do Produto',
            'Valor do Produto',
            'Categorias do Produto',
            'Data de Criação',
            'Logs de Alterações'
        ];

        $stm = $this->productService->getAll($adminUserId);
        $products = $stm;

        foreach ($products as $i => $product) {
            $product = (object) $product;

            $companyName = $this->companyService->getNameById($product->company_id);

            $productLogs = $this->productLogService->generateProductLogsString($product->id);

            $data[$i + 1][] = $product->id;
            $data[$i + 1][] = $companyName;
            $data[$i + 1][] = $product->title;
            $data[$i + 1][] = $product->price;
            $data[$i + 1][] = $product->category;
            $data[$i + 1][] = $product->created_at;
            $data[$i + 1][] = $productLogs;
        }

        $report = "<table style='font-size: 10px;'>";

        foreach ($data as $row) {
            $report .= "<tr>";

            foreach ($row as $column) {
                $report .= "<td>{$column}</td>";
            }

            $report .= "</tr>";
        }

        $report .= "</table>";

        return $report;
    }
}

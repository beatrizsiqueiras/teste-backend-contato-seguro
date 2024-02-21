<?php

namespace ContatoSeguro\TesteBackend\Service;

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

    public function generateReport(int $adminUserId): string
    {
        $reportData = [];
        $reportData[] = [
            'Id do produto',
            'Nome da Empresa',
            'Nome do Produto',
            'Valor do Produto',
            'Categorias do Produto',
            'Data de Criação',
            'Logs de Alterações'
        ];

        $products = $this->productService->getAll(intval($adminUserId));

        foreach ($products as $product) {
            $product = (object) $product;

            $companyName = $this->companyService->getNameById($product->company_id);
            $productLogs = $this->productLogService->generateProductLogsString($product->id);

            $reportData[] = [
                $product->id,
                $companyName,
                $product->title,
                $product->price,
                $product->category,
                $product->created_at,
                $productLogs,
            ];
        }
        return $this->generateHtmlTable($reportData);
    }

    private function generateHtmlTable(array $data): string
    {
        $table = "<table style='font-size: 10px;'>";

        foreach ($data as $row) {
            $table .= "<tr>";
            foreach ($row as $column) {
                $table .= "<td>{$column}</td>";
            }
            $table .= "</tr>";
        }

        $table .= "</table>";

        return $table;
    }
}

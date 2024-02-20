<?php

namespace ContatoSeguro\TesteBackend\Controller;

use ContatoSeguro\TesteBackend\Service\CompanyService;
use ContatoSeguro\TesteBackend\Service\ProductService;
use ContatoSeguro\TesteBackend\Service\ReportService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ReportController
{
    private ReportService $reportService;
    private CompanyService $companyService;

    public function __construct()
    {
        $this->reportService = new ReportService();
    }

    public function generate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $adminUserId = $request->getHeader('admin_user_id')[0]; 
        $report = $this->reportService->generateReport($adminUserId);

        $response->getBody()->write($report); 
        return $response->withStatus(200)->withHeader('Content-Type', 'text/html'); 
    }
}

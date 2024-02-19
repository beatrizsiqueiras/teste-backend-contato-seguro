<?php

namespace Contatoseguro\TesteBackend\Controller;

use Contatoseguro\TesteBackend\Service\AdminUserService;
use Contatoseguro\TesteBackend\Service\ProductLogService;
use DateTime;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProductLogController
{
    private ProductLogService $service;
    private AdminUserService $adminUserService;

    public function __construct()
    {
        $this->service = new ProductLogService();
        $this->adminUserService = new AdminUserService();
    }

    public function getAll(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $stm = $this->service->getAll();

        $response->getBody()->write(json_encode($stm->fetchAll()));
        return $response->withStatus(200);
    }

    public function getLogsByProductId(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $stm = $this->service->getLogsByProductId($args['id']);

        $response->getBody()->write(json_encode($stm->fetchAll()));
        return $response->withStatus(200);
    }

    public function generateProductLogsString($productId)
    {
        $productLogString = '';
        $productLogs = $this->service->getLogsByProductId($productId)->fetchAll();

        if (empty($productLogs)) {
            return 'Logs não encontrados';
        }

        foreach ($productLogs as $log) {
            $logUser = $this->adminUserService->getOne($log->admin_user_id)->fetch();

            $logUserName = !empty($logUser) ? ucfirst($logUser->name) : "Usuário não encontrado (Id: $log->admin_user_id)";

            $logAction = get_translated_log_action($log->action);
            $logDate = DateTime::createFromFormat('Y-m-d H:i:s', $log->timestamp)->format('d/m/Y H:i:m');

            $productLogString .= "($logUserName, $logAction, $logDate),";
        }

        return $productLogString;
    }
}

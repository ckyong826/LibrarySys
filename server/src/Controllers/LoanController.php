<?php

namespace App\Controllers;

use App\Models\Loan;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LoanController
{
    private $loanModel;

    public function __construct(Loan $loanModel)
    {
        $this->loanModel = $loanModel;
    }

    public function index(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $loans = $this->loanModel->getAll($params);
        $response->getBody()->write(json_encode($loans));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $loan = $this->loanModel->getById($args['id']);
        if (!$loan) {
            $response->getBody()->write(json_encode(['error' => 'Loan not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode($loan));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $loanId = $this->loanModel->create($data);
        if ($loanId) {
            $this->loanModel->updateBookAvailability($data['book_id'], -1);
            $newLoan = $this->loanModel->getById($loanId);
            $response->getBody()->write(json_encode($newLoan));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode(['error' => 'Failed to create loan']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

    public function returnLoan(Request $request, Response $response, array $args): Response
    {
        $loanId = $args['id'];
        $loan = $this->loanModel->getById($loanId);

        if (!$loan) {
            $response->getBody()->write(json_encode(['error' => 'Loan not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $returned = $this->loanModel->updateStatus($loanId, 'returned');
        if ($returned) {
            $this->loanModel->updateBookAvailability($loan['book_id'], 1);
            $response->getBody()->write(json_encode(['message' => 'Book returned successfully']));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['error' => 'Failed to return book']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $loan = $this->loanModel->getById($args['id']);
        if ($loan) {
            $this->loanModel->delete($args['id']);
            if ($loan['status'] === 'borrowed') {
                $this->loanModel->updateBookAvailability($loan['book_id'], 1);
            }
        }
        return $response->withStatus(204);
    }
    
    public function getStats(Request $request, Response $response): Response
    {
        $stats = $this->loanModel->getDashboardStats();
        $response->getBody()->write(json_encode($stats));
        return $response->withHeader('Content-Type', 'application/json');
    }
} 
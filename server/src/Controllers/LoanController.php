<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Loan;

class LoanController {
    private $loanModel;

    public function __construct($db) {
        $this->loanModel = new Loan($db);
    }

    public function index(Request $request, Response $response) {
        $loans = $this->loanModel->getAll();
        $response->getBody()->write(json_encode($loans));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, array $args) {
        $id = $args['id'];
        $loan = $this->loanModel->getById($id);
        if ($loan) {
            $response->getBody()->write(json_encode($loan));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            $response->getBody()->write(json_encode(['error' => 'Loan not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
    }

    public function create(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if (!$this->validateLoanData($data)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid loan data. Required fields: user_id, book_id, loan_date, due_date, status']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        $loanId = $this->loanModel->create($data);
        if ($loanId) {
            $newLoan = $this->loanModel->getById($loanId);
            $response->getBody()->write(json_encode(['message' => 'Loan created successfully', 'loan' => $newLoan]));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode(['error' => 'Failed to create loan']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, array $args) {
        $id = $args['id'];
        $data = $request->getParsedBody();
        if (!$this->validateLoanData($data)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid loan data. Required fields: user_id, book_id, loan_date, due_date, status']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        $success = $this->loanModel->update($id, $data);
        if ($success) {
            $updatedLoan = $this->loanModel->getById($id);
            $response->getBody()->write(json_encode(['message' => 'Loan updated successfully', 'loan' => $updatedLoan]));
            return $response->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode(['error' => 'Failed to update loan']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, array $args) {
        $id = $args['id'];
        $success = $this->loanModel->delete($id);
        if ($success) {
            $response->getBody()->write(json_encode(['message' => 'Loan deleted successfully']));
            return $response->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode(['error' => 'Failed to delete loan']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

    private function validateLoanData($data) {
        $required = ['user_id', 'book_id', 'loan_date', 'due_date', 'status'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                return false;
            }
        }
        return true;
    }
} 
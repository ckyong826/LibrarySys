<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Book;

class BookController {
    private $bookModel;

    public function __construct($db) {
        $this->bookModel = new Book($db);
    }

    public function index(Request $request, Response $response) {
        $books = $this->bookModel->getAll();
        $response->getBody()->write(json_encode($books));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, array $args) {
        $id = $args['id'];
        $book = $this->bookModel->getById($id);
        if ($book) {
            $response->getBody()->write(json_encode($book));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            $response->getBody()->write(json_encode(['error' => 'Book not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
    }

    public function create(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if (!$this->validateBookData($data)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid book data. Required fields: title, author_id, available_copies, total_copies']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        $bookId = $this->bookModel->create($data);
        if ($bookId) {
            $newBook = $this->bookModel->getById($bookId);
            $response->getBody()->write(json_encode(['message' => 'Book created successfully', 'book' => $newBook]));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode(['error' => 'Failed to create book']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, array $args) {
        $id = $args['id'];
        $data = $request->getParsedBody();
        if (!$this->validateBookData($data)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid book data. Required fields: title, author_id, available_copies, total_copies']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        $success = $this->bookModel->update($id, $data);
        if ($success) {
            $updatedBook = $this->bookModel->getById($id);
            $response->getBody()->write(json_encode(['message' => 'Book updated successfully', 'book' => $updatedBook]));
            return $response->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode(['error' => 'Failed to update book']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, array $args) {
        $id = $args['id'];
        $success = $this->bookModel->delete($id);
        if ($success) {
            $response->getBody()->write(json_encode(['message' => 'Book deleted successfully']));
            return $response->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode(['error' => 'Failed to delete book']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

    private function validateBookData($data) {
        $required = ['title', 'author_id', 'available_copies', 'total_copies'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                return false;
            }
        }
        return true;
    }
} 
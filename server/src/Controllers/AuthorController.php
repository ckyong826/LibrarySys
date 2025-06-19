<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Author;

class AuthorController {
    private $authorModel;

    public function __construct($db) {
        $this->authorModel = new Author($db);
    }

    public function index(Request $request, Response $response) {
        $authors = $this->authorModel->getAll();
        $response->getBody()->write(json_encode($authors));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, array $args) {
        $id = $args['id'];
        $author = $this->authorModel->getById($id);
        if ($author) {
            $response->getBody()->write(json_encode($author));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            $response->getBody()->write(json_encode(['error' => 'Author not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
    }

    public function create(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if (!$this->validateAuthorData($data)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid author data. Required field: name']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        $authorId = $this->authorModel->create($data);
        if ($authorId) {
            $newAuthor = $this->authorModel->getById($authorId);
            $response->getBody()->write(json_encode(['message' => 'Author created successfully', 'author' => $newAuthor]));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode(['error' => 'Failed to create author']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, array $args) {
        $id = $args['id'];
        $data = $request->getParsedBody();
        if (!$this->validateAuthorData($data)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid author data. Required field: name']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        $success = $this->authorModel->update($id, $data);
        if ($success) {
            $updatedAuthor = $this->authorModel->getById($id);
            $response->getBody()->write(json_encode(['message' => 'Author updated successfully', 'author' => $updatedAuthor]));
            return $response->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode(['error' => 'Failed to update author']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, array $args) {
        $id = $args['id'];
        $success = $this->authorModel->delete($id);
        if ($success) {
            $response->getBody()->write(json_encode(['message' => 'Author deleted successfully']));
            return $response->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode(['error' => 'Failed to delete author']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

    private function validateAuthorData($data) {
        return isset($data['name']) && $data['name'] !== '';
    }
} 
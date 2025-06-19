<?php

namespace App\Models;

class Book {
    private $db;
    private $table = 'books';

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $query = "SELECT books.*, authors.name AS author_name FROM {$this->table} JOIN authors ON books.author_id = authors.id ORDER BY books.title ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $query = "SELECT books.*, authors.name AS author_name FROM {$this->table} JOIN authors ON books.author_id = authors.id WHERE books.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($data) {
        $query = "INSERT INTO {$this->table} (title, author_id, isbn, published_year, available_copies, total_copies) VALUES (:title, :author_id, :isbn, :published_year, :available_copies, :total_copies)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':author_id', $data['author_id']);
        $stmt->bindParam(':isbn', $data['isbn']);
        $stmt->bindParam(':published_year', $data['published_year']);
        $stmt->bindParam(':available_copies', $data['available_copies']);
        $stmt->bindParam(':total_copies', $data['total_copies']);
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function update($id, $data) {
        $query = "UPDATE {$this->table} SET title = :title, author_id = :author_id, isbn = :isbn, published_year = :published_year, available_copies = :available_copies, total_copies = :total_copies WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':author_id', $data['author_id']);
        $stmt->bindParam(':isbn', $data['isbn']);
        $stmt->bindParam(':published_year', $data['published_year']);
        $stmt->bindParam(':available_copies', $data['available_copies']);
        $stmt->bindParam(':total_copies', $data['total_copies']);
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
} 
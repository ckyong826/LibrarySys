<?php

namespace App\Models;

class Loan {
    private $db;
    private $table = 'loans';

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $query = "SELECT loans.*, users.name AS user_name, books.title AS book_title FROM {$this->table} JOIN users ON loans.user_id = users.id JOIN books ON loans.book_id = books.id ORDER BY loan_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $query = "SELECT loans.*, users.name AS user_name, books.title AS book_title FROM {$this->table} JOIN users ON loans.user_id = users.id JOIN books ON loans.book_id = books.id WHERE loans.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($data) {
        $query = "INSERT INTO {$this->table} (user_id, book_id, loan_date, due_date, return_date, status) VALUES (:user_id, :book_id, :loan_date, :due_date, :return_date, :status)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':book_id', $data['book_id']);
        $stmt->bindParam(':loan_date', $data['loan_date']);
        $stmt->bindParam(':due_date', $data['due_date']);
        $stmt->bindParam(':return_date', $data['return_date']);
        $stmt->bindParam(':status', $data['status']);
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function update($id, $data) {
        $query = "UPDATE {$this->table} SET user_id = :user_id, book_id = :book_id, loan_date = :loan_date, due_date = :due_date, return_date = :return_date, status = :status WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':book_id', $data['book_id']);
        $stmt->bindParam(':loan_date', $data['loan_date']);
        $stmt->bindParam(':due_date', $data['due_date']);
        $stmt->bindParam(':return_date', $data['return_date']);
        $stmt->bindParam(':status', $data['status']);
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
} 
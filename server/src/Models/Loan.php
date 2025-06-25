<?php

namespace App\Models;

class Loan
{
    private $db;
    private $table = 'loans';

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function getAll(array $params = [])
    {
        $query = "SELECT l.*, u.name AS user_name, b.title AS book_title 
                  FROM {$this->table} l 
                  JOIN users u ON l.user_id = u.id 
                  JOIN books b ON l.book_id = b.id";

        if (isset($params['_sort']) && isset($params['_order'])) {
            $sort = $params['_sort'];
            $order = $params['_order'];
            if (in_array($sort, ['loan_date', 'due_date', 'status']) && in_array(strtoupper($order), ['ASC', 'DESC'])) {
                $query .= " ORDER BY $sort $order";
            }
        } else {
            $query .= " ORDER BY loan_date DESC";
        }

        if (isset($params['_limit'])) {
            $limit = (int) $params['_limit'];
            $query .= " LIMIT $limit";
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $query = "SELECT l.*, u.name AS user_name, b.title AS book_title 
                  FROM {$this->table} l
                  JOIN users u ON l.user_id = u.id 
                  JOIN books b ON l.book_id = b.id 
                  WHERE l.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($data)
    {
        $query = "INSERT INTO {$this->table} (user_id, book_id, loan_date, due_date, status) 
                  VALUES (:user_id, :book_id, :loan_date, :due_date, 'borrowed')";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':book_id', $data['book_id']);
        $stmt->bindParam(':loan_date', $data['loan_date']);
        $stmt->bindParam(':due_date', $data['due_date']);
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function updateStatus($id, $status)
    {
        $returnDate = ($status === 'returned') ? date('Y-m-d H:i:s') : null;
        $query = "UPDATE {$this->table} SET status = :status, return_date = :return_date WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':return_date', $returnDate);
        return $stmt->execute();
    }

    public function updateBookAvailability($bookId, $change)
    {
        $query = "UPDATE books SET available_copies = available_copies + :change WHERE id = :book_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':book_id', $bookId);
        $stmt->bindParam(':change', $change, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getDashboardStats()
    {
        $stats = [];
        $stats['books'] = $this->db->query("SELECT COUNT(*) FROM books")->fetchColumn();
        $stats['authors'] = $this->db->query("SELECT COUNT(*) FROM authors")->fetchColumn();
        $stats['users'] = $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $stats['activeLoans'] = $this->db->query("SELECT COUNT(*) FROM loans WHERE status = 'borrowed'")->fetchColumn();
        return $stats;
    }
}
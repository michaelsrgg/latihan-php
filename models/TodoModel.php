<?php
// models/TodoModel.php
require_once __DIR__ . '/../config.php';

class TodoModel
{
    private $db;

    public function __construct()
    {
        $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";";
        try {
            $this->db = new PDO($dsn, DB_USER, DB_PASSWORD, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            // tampil pesan yang ramah saat development
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    public function getTodos($filter = 'all', $search = '')
    {
        $sql = "SELECT * FROM todo WHERE 1=1";
        $params = [];

        if ($filter === 'completed') {
            $sql .= " AND is_finished = true";
        } elseif ($filter === 'active') {
            $sql .= " AND is_finished = false";
        }

        if (!empty($search)) {
            $sql .= " AND (title ILIKE :search OR COALESCE(description, '') ILIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY sort_order ASC, id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function createTodo($title, $description = '')
    {
        // check unique
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM todo WHERE title = :title");
        $stmt->execute([':title' => $title]);
        if ($stmt->fetchColumn() > 0) {
            return ['success' => false, 'error' => 'Todo dengan judul tersebut sudah ada'];
        }

        // get max sort_order
        $stmt = $this->db->query("SELECT COALESCE(MAX(sort_order), 0) + 1 FROM todo");
        $sortOrder = (int)$stmt->fetchColumn();

        $stmt = $this->db->prepare("INSERT INTO todo (title, description, is_finished, sort_order) VALUES (:title, :description, false, :sort_order)");
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':sort_order' => $sortOrder
        ]);
        return ['success' => true];
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM todo WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function updateTodo($id, $title, $description, $is_finished)
    {
        // check unique title except this id
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM todo WHERE title = :title AND id != :id");
        $stmt->execute([':title' => $title, ':id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            return ['success' => false, 'error' => 'Todo dengan judul tersebut sudah ada'];
        }

        $stmt = $this->db->prepare("UPDATE todo SET title = :title, description = :description, is_finished = :is_finished WHERE id = :id");
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':is_finished' => $is_finished ? true : false,
            ':id' => $id
        ]);
        return ['success' => true];
    }

    public function deleteTodo($id)
    {
        $stmt = $this->db->prepare("DELETE FROM todo WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function updateSortOrder(array $positions)
    {
        if (!is_array($positions)) return false;
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("UPDATE todo SET sort_order = :sort_order WHERE id = :id");
            foreach ($positions as $p) {
                $stmt->execute([
                    ':sort_order' => (int)$p['order'],
                    ':id' => (int)$p['id']
                ]);
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function toggleStatus($id, $is_finished)
    {
        $stmt = $this->db->prepare("UPDATE todo SET is_finished = :is_finished WHERE id = :id");
        return $stmt->execute([':is_finished' => $is_finished ? true : false, ':id' => $id]);
    }
}

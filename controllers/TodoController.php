<?php
// controllers/TodoController.php
require_once __DIR__ . '/../models/TodoModel.php';

class TodoController {
    private $model;

    public function __construct() {
        $this->model = new TodoModel();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // LIST
    public function index() {
        $filter = $_GET['filter'] ?? 'all';
        $search = $_GET['search'] ?? '';
        $todos = $this->model->getTodos($filter, $search);
        // variables expected by view: $todos, $filter, $search
        require_once __DIR__ . '/../views/TodoView.php';
    }

    // CREATE (POST)
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($title === '') {
            $_SESSION['error'] = 'Title is required';
            header('Location: index.php');
            exit;
        }

        $res = $this->model->createTodo($title, $description);
        if (!$res['success']) {
            $_SESSION['error'] = $res['error'];
        }

        header('Location: index.php');
        exit;
    }

    // UPDATE (GET -> show edit form, POST -> process)
    public function update() {
        // show edit form
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!isset($_GET['id'])) {
                header('Location: index.php');
                exit;
            }
            $id = (int)$_GET['id'];
            $todo = $this->model->getById($id);
            if (!$todo) {
                $_SESSION['error'] = 'Todo not found';
                header('Location: index.php');
                exit;
            }
            require_once __DIR__ . '/../views/todo_edit.php';
            return;
        }

        // process POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $isFinished = isset($_POST['is_finished']) ? true : false;

            if ($title === '') {
                $_SESSION['error'] = 'Title is required';
                header("Location: index.php?page=update&id=$id");
                exit;
            }

            $res = $this->model->updateTodo($id, $title, $description, $isFinished);
            if (!$res['success']) {
                $_SESSION['error'] = $res['error'];
                header("Location: index.php?page=update&id=$id");
                exit;
            }

            header('Location: index.php');
            exit;
        }
    }

    // DELETE
    public function delete() {
        if (!isset($_GET['id'])) {
            header('Location: index.php');
            exit;
        }
        $id = (int)$_GET['id'];
        $this->model->deleteTodo($id);
        header('Location: index.php');
        exit;
    }

    // DETAIL
    public function detail() {
        if (!isset($_GET['id'])) {
            header('Location: index.php');
            exit;
        }
        $id = (int)$_GET['id'];
        $todo = $this->model->getById($id);
        if (!$todo) {
            $_SESSION['error'] = 'Todo not found';
            header('Location: index.php');
            exit;
        }
        require_once __DIR__ . '/../views/todo_detail.php';
    }

    // Update Sort (AJAX POST)
    public function updateSort() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }
        $positions = json_decode($_POST['positions'] ?? '[]', true);
        $ok = $this->model->updateSortOrder($positions);
        header('Content-Type: application/json');
        echo json_encode(['success' => (bool)$ok]);
        exit;
    }

    // Toggle status (AJAX POST)
    public function toggleStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }
        $id = (int)($_POST['id'] ?? 0);
        $is_finished = (isset($_POST['is_finished']) && $_POST['is_finished'] == 1) ? 1 : 0;
        $ok = $this->model->toggleStatus($id, $is_finished);
        header('Content-Type: application/json');
        echo json_encode(['success' => (bool)$ok]);
        exit;
    }
}

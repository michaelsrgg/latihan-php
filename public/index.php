<?php
// public/index.php
session_start();
require_once __DIR__ . '/../controllers/TodoController.php';

$controller = new TodoController();

$page = $_GET['page'] ?? 'index';

// Accept POST for create/update/updateSort/toggleStatus
switch ($page) {
    case 'create':
        $controller->create();
        break;
    case 'update': // handles both GET (show form) and POST (process)
        $controller->update();
        break;
    case 'delete':
        $controller->delete();
        break;
    case 'detail':
        $controller->detail();
        break;
    case 'updateSort':
        $controller->updateSort();
        break;
    case 'toggleStatus':
        $controller->toggleStatus();
        break;
    default:
        $controller->index();
        break;
}

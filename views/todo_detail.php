<?php
// views/todo_detail.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Todo Detail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <a href="index.php" class="btn btn-secondary mb-3">Back to list</a>

    <div class="card">
        <div class="card-header">
            <h4><?php echo htmlspecialchars($todo['title']); ?></h4>
        </div>
        <div class="card-body">
            <p><strong>Description:</strong></p>
            <p><?php echo nl2br(htmlspecialchars($todo['description'])); ?></p>

            <p><strong>Status:</strong> <?php echo $todo['is_finished'] ? 'Completed' : 'Active'; ?></p>

            <p class="text-muted">
                Created: <?php echo (new DateTime($todo['created_at']))->format('Y-m-d H:i'); ?> |
                Updated: <?php echo (new DateTime($todo['updated_at']))->format('Y-m-d H:i'); ?>
            </p>

            <a href="index.php?page=update&id=<?php echo (int)$todo['id']; ?>" class="btn btn-warning">Edit</a>
            <a href="index.php?page=delete&id=<?php echo (int)$todo['id']; ?>" class="btn btn-danger" onclick="return confirm('Yakin hapus?')">Delete</a>
        </div>
    </div>
</div>
</body>
</html>

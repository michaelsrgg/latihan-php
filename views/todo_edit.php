<?php
// views/todo_edit.php
if (session_status() === PHP_SESSION_NONE) session_start();
// $todo variable must exist (controller provides)
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit Todo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <a href="index.php" class="btn btn-secondary mb-3">Back to list</a>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h4>Edit Todo</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?page=update">
                <input type="hidden" name="id" value="<?php echo (int)$todo['id']; ?>">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($todo['title']); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($todo['description']); ?></textarea>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" name="is_finished" id="is_finished" class="form-check-input" <?php echo $todo['is_finished'] ? 'checked' : ''; ?>>
                    <label for="is_finished" class="form-check-label">Finished</label>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>

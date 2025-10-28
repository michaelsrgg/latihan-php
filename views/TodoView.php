<?php
// views/TodoView.php atau views/todo/index.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Todo List</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet">
    <style>
        .sortable-placeholder { border: 2px dashed #ccc; margin-bottom: 1rem; height: 70px; background: rgba(0,0,0,0.02); }
        .card.dragging { opacity: 0.6; }
        .todo-meta { font-size: 0.85rem; color: #666; }
        .todo-actions > a { margin-left: 0.3rem; }
        .todo-title { margin-bottom: 0; }
    </style>
</head>
<body>
    <div class="container mt-5">

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex align-items-center mb-4">
            <h1 class="me-3">Todo List</h1>
            <span class="badge bg-secondary"><?php echo count($todos); ?> items</span>
        </div>

        <!-- Search + Add -->
        <div class="row mb-3">
            <div class="col-md-8">
                <form method="GET" action="index.php" class="d-flex">
                    <input type="hidden" name="page" value="home">
                    <select name="filter" class="form-select me-2">
                        <option value="all" <?php echo ($filter === 'all') ? 'selected' : ''; ?>>All</option>
                        <option value="active" <?php echo ($filter === 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="completed" <?php echo ($filter === 'completed') ? 'selected' : ''; ?>>Completed</option>
                    </select>
                    <input type="text" name="search" class="form-control me-2"
                        placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary me-2">Search</button>
                    <a href="index.php" class="btn btn-outline-secondary">Reset</a>
                </form>
            </div>

            <div class="col-md-4">
                <form method="POST" action="index.php?page=create" class="d-flex">
                    <input type="text" name="title" class="form-control me-2"
                        placeholder="New todo title" required>
                    <button type="submit" class="btn btn-success">Add</button>
                </form>
                <small class="text-muted">Description bisa ditambah ketika Edit.</small>
            </div>
        </div>

        <!-- Todo List -->
        <div id="todoList" class="mb-5">
            <?php if (empty($todos)): ?>
                <div class="alert alert-info">No todos found.</div>
            <?php endif; ?>

            <?php foreach ($todos as $todo): ?>
                <div class="card mb-2" data-id="<?php echo (int)$todo['id']; ?>">
                    <div class="card-body d-flex justify-content-between align-items-center">

                        <div class="d-flex align-items-start">
                            <input type="checkbox" class="form-check-input me-3 toggle-finish"
                                   data-id="<?php echo (int)$todo['id']; ?>"
                                   <?php echo ($todo['is_finished']) ? 'checked' : ''; ?>>

                            <div>
                                <h5 class="todo-title <?php echo ($todo['is_finished']) ? 'text-decoration-line-through text-muted' : ''; ?>">
                                    <?php echo htmlspecialchars($todo['title']); ?>
                                </h5>

                                <?php if (!empty($todo['description'])): ?>
                                    <p class="mb-1"><?php echo nl2br(htmlspecialchars($todo['description'])); ?></p>
                                <?php endif; ?>

                                <div class="todo-meta">
                                    <small>
                                        Created: <?php echo (new DateTime($todo['created_at']))->format('Y-m-d H:i'); ?>
                                        • Updated: <?php echo (new DateTime($todo['updated_at']))->format('Y-m-d H:i'); ?>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="todo-actions">
                            <a href="index.php?page=detail&id=<?php echo $todo['id']; ?>" class="btn btn-info btn-sm">Detail</a>
                            <a href="index.php?page=update&id=<?php echo $todo['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="index.php?page=delete&id=<?php echo $todo['id']; ?>"
                               onclick="return confirm('Yakin hapus?')"
                               class="btn btn-danger btn-sm">Delete</a>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-muted">Drag and Drop untuk menyusun urutan. Tersimpan otomatis.</div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $(function() {

            // Reorder
            $("#todoList").sortable({
                placeholder: "sortable-placeholder",
                update: function() {
                    const positions = [];
                    $("#todoList .card").each(function(i) {
                        positions.push({
                            id: $(this).data('id'),
                            order: i + 1
                        });
                    });

                    $.post("index.php?page=updateSort", {
                        positions: JSON.stringify(positions)
                    });
                }
            });

            // Toggle finish
            $(".toggle-finish").change(function() {
                const id = $(this).data('id');
                const is_finished = $(this).is(":checked") ? 1 : 0;

                $.post("index.php?page=toggleStatus", {
                    id: id,
                    is_finished: is_finished
                });
            });

        });
    </script>
</body>
</html>

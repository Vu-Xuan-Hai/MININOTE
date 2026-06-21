<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$note_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = '';
$success = '';

// 1. Lấy dữ liệu cũ của Note ra đổ vào form
try {
    $sql = "SELECT * FROM notes WHERE id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$note_id, $user_id]);
    $note = $stmt->fetch();

    if (!$note) {
        die("<div class='container mt-5'><div class='alert alert-danger'>Ghi chú không tồn tại!</div></div>");
    }
} catch (\PDOException $e) {
    die("Lỗi: " . $e->getMessage());
}

// 2. Xử lý khi user bấm nút Cập nhật (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (empty($title)) {
        $error = 'Tiêu đề không được để trống!';
    } else {
        try {
            $sql = "UPDATE notes SET title = ?, content = ? WHERE id = ? AND user_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $content, $note_id, $user_id]);

            $success = 'Cập nhật ghi chú thành công! Đang quay lại...';
            header('Refresh: 1; url=detail.php?id=' . $note_id);
        } catch (\PDOException $e) {
            $error = 'Lỗi cập nhật: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Ghi Chú</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="../index.php"><i class="bi bi-journal-text"></i> Mini Note</a>
        <span class="navbar-text text-white">Sửa ghi chú</span>
    </div>
</nav>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0"><i class="bi bi-pencil-square"></i> Chỉnh Sửa Ghi Chú</h5>
                </div>
                <div class="card-body">
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= $error; ?></div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?= $success; ?></div>
                    <?php endif; ?>

                    <form action="edit.php?id=<?= $note_id; ?>" method="POST">
                        <div class="mb-3">
                            <label for="title" class="form-label"><strong>Tiêu đề <span class="text-danger">*</span></strong></label>
                            <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($note['title']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label"><strong>Nội dung ghi chú</strong></label>
                            <textarea name="content" id="content" rows="6" class="form-control"><?= htmlspecialchars($note['content'] ?? ''); ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="detail.php?id=<?= $note_id; ?>" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Hủy bỏ</a>
                            <button type="submit" class="btn btn-warning">Cập nhật</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
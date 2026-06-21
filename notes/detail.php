<?php
session_start();
require_once '../config/database.php';

// Bảo mật: Nếu chưa đăng nhập, đẩy về login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
// Lấy ID ghi chú từ URL
$note_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    // BẢO MẬT: Chỉ lấy note nếu nó thuộc về đúng user_id đang đăng nhập
    $sql = "SELECT * FROM notes WHERE id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$note_id, $user_id]);
    $note = $stmt->fetch();

    // Nếu không tìm thấy ghi chú hoặc ghi chú thuộc về người khác
    if (!$note) {
        die("<div class='container mt-5'><div class='alert alert-danger'>Ghi chú không tồn tại hoặc bạn không có quyền xem!</div><a href='../index.php' class='btn btn-primary'>Quay lại trang chủ</a></div>");
    }
} catch (\PDOException $e) {
    die("Lỗi hệ thống: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($note['title']); ?> - Chi tiết Note</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="../index.php"><i class="bi bi-journal-text"></i> Mini Note</a>
        <span class="navbar-text text-white">Chi tiết ghi chú</span>
    </div>
</nav>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title mb-3 text-primary"><?= htmlspecialchars($note['title']); ?></h2>
                    
                    <div class="text-muted small mb-4">
                        <i class="bi bi-calendar3"></i> Ngày tạo: <?= date('d/m/Y H:i', strtotime($note['created_at'])); ?> 
                        <?php if ($note['created_at'] !== $note['updated_at']): ?>
                            | <i class="bi bi-pencil-square"></i> Cập nhật: <?= date('d/m/Y H:i', strtotime($note['updated_at'])); ?>
                        <?php endif; ?>
                    </div>
                    
                    <hr>
                    
                    <div class="note-content my-4" style="white-space: pre-wrap; font-size: 1.1rem; line-height: 1.6;">
                        <?= nl2br(htmlspecialchars($note['content'] ?? 'Không có nội dung...')); ?>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="../index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Trang chủ</a>
                        <div>
                            <a href="edit.php?id=<?= $note['id']; ?>" class="btn btn-warning"><i class="bi bi-pencil"></i> Sửa</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
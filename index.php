<?php
session_start();

require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try{
    if (!empty($search)) {
        $sql = "SELECT * FROM notes WHERE user_id = ? AND (title LIKE ? OR content LIKE ?) ORDER BY created_at DESC";
        $stmt = $pdo->prepare($sql);
        $like_search = "%$search%";
        $stmt->execute([$user_id, $like_search, $like_search]);
    } else {
        $sql = "SELECT * FROM notes WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
    }
    $notes = $stmt->fetchAll();
} catch (\PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang chủ - Mini Note</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel = "stylesheet" href = "https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="bi bi-journal-text"></i> Mini Note</a>
        <div class="d-flex align-items-center">
            <span class="navbar-text text-white me-3">Xin chào, <strong><?= htmlspecialchars($username); ?></strong>!</span>
            <a href="auth/logout.php" class="btn btn-outline-danger btn-sm">Đăng xuất</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <form action="index.php" method="GET" class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="Tìm kiếm theo tiêu đề hoặc nội dung..." value="<?= htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary">Tìm</button>
                <?php if (!empty($search)): ?>
                    <a href="index.php" class="btn btn-secondary ms-2">Xóa</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="col-md-4 text-end mt-2 mt-md-0">
            <a href="notes/create.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Thêm Note Mới</a>
        </div>
    </div>

    <h4 class="mb-3">Ghi chú của tôi</h4>
    
    <?php if (empty($notes)): ?>
        <div class="alert alert-warning text-center mt-4">
            <?= !empty($search) ? "Không tìm thấy ghi chú nào khớp với từ khóa!" : "Bạn chưa có ghi chú nào. Hãy tạo ghi chú đầu tiên!"; ?>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($notes as $note): ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-truncate"><?= htmlspecialchars($note['title']); ?></h5>
                            <p class="card-text text-muted text-truncate flex-grow-1">
                                <?= htmlspecialchars($note['content'] ?? 'Không có nội dung...'); ?>
                            </p>
                            <small class="text-muted d-block mb-3">
                                <i class="bi bi-clock"></i> Cập nhật: <?= date('d/m/Y H:i', strtotime($note['updated_at'])); ?>
                            </small>
                            <div class="mt-auto pt-2 border-top d-flex justify-content-between">
                                <a href="notes/detail.php?id=<?= $note['id']; ?>" class="btn btn-sm btn-outline-primary">Xem</a>
                                <div>
                                    <a href="notes/edit.php?id=<?= $note['id']; ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i> Sửa</a>
                                    <a href="notes/delete.php?id=<?= $note['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa note này?')"><i class="bi bi-trash"></i> Xóa</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
<?php
// 1. Khởi động session để kiểm tra quyền sở hữu
session_start();

// 2. Nhúng file kết nối DB (Vì file này nằm trong thư mục con 'notes', cần dùng '../' để nhảy ra ngoài)
require_once '../config/database.php';

// 3. BẢO MẬT: Nếu chưa đăng nhập, đá về trang login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$error = '';
$success = '';

// 4. XỬ LÝ KHI USER BẤM NÚT LƯU GHI CHÚ (SUBMIT FORM)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form và loại bỏ khoảng trắng thừa
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $user_id = $_SESSION['user_id']; // Lấy ID của người dùng đang đăng nhập

    // Kiểm tra tính hợp lệ (Validation)
    if (empty($title)) {
        $error = 'Vui lòng nhập tiêu đề cho ghi chú!';
    } else {
        try {
            // Câu lệnh SQL thêm ghi chú mới vào DB
            $sql = "INSERT INTO notes (user_id, title, content) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user_id, $title, $content]);

            // Thêm thành công thì tự động chuyển hướng về trang chủ sau 1 giây
            $success = 'Thêm ghi chú thành công! Đang quay lại trang chủ...';
            header('Refresh: 1; url=../index.php');
        } catch (\PDOException $e) {
            $error = 'Lỗi hệ thống: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Ghi Chú Mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="../index.php"><i class="bi bi-journal-text"></i> Mini Note</a>
        <span class="navbar-text text-white">Thêm Ghi Chú</span>
    </div>
</nav>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0"><i class="bi bi-plus-circle"></i> Tạo Ghi Chú Mới</h5>
                </div>
                <div class="card-body">
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= $error; ?></div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?= $success; ?></div>
                    <?php endif; ?>

                    <form action="create.php" method="POST">
                        <div class="mb-3">
                            <label for="title" class="form-label"><strong>Tiêu đề <span class="text-danger">*</span></strong></label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Nhập tiêu đề ghi chú..." value="<?= isset($title) ? htmlspecialchars($title) : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label"><strong>Nội dung ghi chú</strong></label>
                            <textarea name="content" id="content" rows="6" class="form-control" placeholder="Viết nội dung vào đây..."><?= isset($content) ? htmlspecialchars($content) : ''; ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="../index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
                            <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Lưu Ghi Chú</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
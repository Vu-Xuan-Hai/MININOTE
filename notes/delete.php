<?php
// 1. Khởi động session để kiểm tra quyền sở hữu
session_start();

// 2. Nhúng file kết nối cơ sở dữ liệu
require_once '../config/database.php';

// 3. BẢO MẬT: Nếu chưa đăng nhập, đá về trang login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
// Lấy ID của ghi chú cần xóa từ URL (ví dụ: delete.php?id=5)
$note_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($note_id > 0) {
    try {
        // BẢO MẬT TUYỆT ĐỐI: Chỉ xóa khi id của note trùng khớp với user_id đang đăng nhập
        $sql = "DELETE FROM notes WHERE id = ? AND user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$note_id, $user_id]);
        
        // Xóa xong chuyển hướng ngay về trang chủ index.php
        header('Location: ../index.php');
        exit;
    } catch (\PDOException $e) {
        die("Lỗi hệ thống khi xóa dữ liệu: " . $e->getMessage());
    }
} else {
    // Nếu ID không hợp lệ, trả về trang chủ
    header('Location: ../index.php');
    exit;
}
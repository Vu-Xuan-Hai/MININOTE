<?php
require_once '../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Vui lòng điền đầy đủ tên đăng nhập và mật khẩu!";
    } elseif (strlen($password) < 6) {
        $error = "Mật khẩu phải có ít nhất 6 ký tự!";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            
            if ($stmt->rowCount() > 0) {
                $error = "Tên đăng nhập này đã tồn tại, vui lòng chọn tên khác!";
            } else {
    
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // chèn
                $insert_stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                
                if ($insert_stmt->execute([$username, $hashed_password])) {
                    $success = "Đăng ký tài khoản thành công! <a href='login.php'>Đăng nhập ngay</a>";
                } else {
                    $error = "Có lỗi xảy ra trong quá trình đăng ký.";
                }
            }
        } catch (\PDOException $e) {
            $error = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký - Mini Note</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="card-title text-center mb-4">Đăng Ký Tài Khoản</h3>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= $error; ?></div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?= $success; ?></div>
                    <?php endif; ?>

                    <form action="register.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" name="username" id="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Đăng Ký</button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
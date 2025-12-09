<?php
require_once 'config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('dashboard_admin.php');
    } else {
        redirect('dashboard.php');
    }
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = escape($conn, $_POST['name'] ?? '');
    $email = escape($conn, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Semua field harus diisi!';
    } elseif ($password !== $confirm_password) {
        $error = 'Konfirmasi password tidak cocok!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } else {
        // Check if email already exists
        $check_query = "SELECT id FROM user WHERE email = '$email'";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $error = 'Email sudah terdaftar!';
        } else {
            // Insert user with plain password
            $query = "INSERT INTO user (name, email, password, role) VALUES ('$name', '$email', '$password', 'user')";

            if (mysqli_query($conn, $query)) {
                $success = 'Registrasi berhasil! Silakan login.';
            } else {
                $error = 'Gagal mendaftar. Silakan coba lagi.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Perpustakaan Digital</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="auth-page">
        <div class="auth-card fade-in-up">
            <div class="auth-header">
                <h2><i class="fas fa-book-open me-2"></i>Perpustakaan</h2>
                <p>Buat akun baru</p>
            </div>
            <div class="auth-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-custom mb-4">
                        <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-custom mb-4">
                        <i class="fas fa-check-circle me-2"></i><?= $success ?>
                        <a href="login.php" class="alert-link ms-2">Login sekarang</a>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-user text-muted"></i>
                            </span>
                            <input type="text" name="name" class="form-control border-start-0 ps-0"
                                placeholder="Masukkan nama lengkap" required value="<?= $_POST['name'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-envelope text-muted"></i>
                            </span>
                            <input type="email" name="email" class="form-control border-start-0 ps-0"
                                placeholder="nama@email.com" required value="<?= $_POST['email'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-lock text-muted"></i>
                            </span>
                            <input type="password" name="password" class="form-control border-start-0 ps-0"
                                placeholder="Minimal 6 karakter" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Konfirmasi Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-lock text-muted"></i>
                            </span>
                            <input type="password" name="confirm_password" class="form-control border-start-0 ps-0"
                                placeholder="Ulangi password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-gradient w-100 py-3 mb-4">
                        <i class="fas fa-user-plus me-2"></i>Daftar
                    </button>

                    <p class="text-center text-muted mb-0">
                        Sudah punya akun?
                        <a href="login.php" class="text-decoration-none fw-semibold"
                            style="color: var(--primary-color);">
                            Masuk di sini
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body></html>
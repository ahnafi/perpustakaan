<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$is_admin = isAdmin();

$error = '';
$success = '';

// Process password change using GET method (INSECURE - for learning purposes)
if (isset($_GET['change']) && $_GET['change'] === 'true') {
    $new_password = $_GET['new_password'] ?? '';
    $confirm_password = $_GET['confirm_password'] ?? '';

    if (empty($new_password)) {
        $error = 'Password baru harus diisi!';
    } elseif (strlen($new_password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak cocok!';
    } else {
        // Update password (plain text - ALSO INSECURE for learning purposes)
        $update_query = "UPDATE user SET password = '$new_password' WHERE id = $user_id";

        if (mysqli_query($conn, $update_query)) {
            $success = 'Password berhasil diubah! Silakan login kembali.';
            // Log out user after password change
            session_destroy();
            header('refresh:3;url=login.php');
        } else {
            $error = 'Gagal mengubah password!';
        }
    }
}

// Get flash message
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Password - Perpustakaan Digital</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-book-open"></i>
                Perpustakaan
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $is_admin ? 'dashboard_admin.php' : 'dashboard.php' ?>">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i><?= $user_name ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="change_password.php">
                                    <i class="fas fa-key me-2"></i>Ubah Password
                                </a></li>
                            <li><a class="dropdown-item" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5" style="margin-top: 70px;">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="page-header">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="<?= $is_admin ? 'dashboard_admin.php' : 'dashboard.php' ?>">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Ubah Password</li>
                        </ol>
                    </nav>
                    <h1 class="page-title">Ubah Password</h1>
                    <p class="page-subtitle">Ubah password akun Anda</p>
                </div>

                <!-- Security Warning Alert
                <div class="alert alert-warning alert-custom mb-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>⚠️ PERINGATAN KEAMANAN:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Halaman ini menggunakan <strong>METHOD GET</strong> yang TIDAK AMAN</li>
                        <li>Password akan terlihat di <strong>URL dan Browser History</strong></li>
                        <li>Password disimpan dalam <strong>Plain Text</strong> tanpa enkripsi</li>
                        <li>Ini hanya untuk <strong>PEMBELAJARAN KEAMANAN INFORMASI</strong></li>
                        <li><strong>JANGAN</strong> gunakan di aplikasi production!</li>
                    </ul>
                </div> -->

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-custom mb-4">
                        <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-custom mb-4">
                        <i class="fas fa-check-circle me-2"></i><?= $success ?>
                        <br><small>Anda akan diarahkan ke halaman login...</small>
                    </div>
                <?php endif; ?>

                <?php if ($flash): ?>
                    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-custom mb-4">
                        <i
                            class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
                        <?= $flash['message'] ?>
                    </div>
                <?php endif; ?>

                <div class="card-custom">
                    <div class="card-body p-4">
                        <!-- Using GET method - INSECURE! -->
                        <form method="GET" action="change_password.php" id="passwordForm">
                            <!-- Hidden field to trigger password change -->
                            <input type="hidden" name="change" value="true">

                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Password Baru <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" name="new_password" id="new_password"
                                        class="form-control form-control-lg"
                                        placeholder="Masukkan password baru (min. 6 karakter)" required minlength="6">
                                    <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Password akan terlihat di URL (tidak aman!)
                                </small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Konfirmasi Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" name="confirm_password" id="confirm_password"
                                        class="form-control form-control-lg" placeholder="Masukkan ulang password baru"
                                        required minlength="6">
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- <div class="alert alert-info mb-4">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Contoh URL yang akan terbentuk:</strong>
                                <br>
                                <code id="urlPreview" class="text-break">
                                    change_password.php?change=true&new_password=yourpass&confirm_password=yourpass
                                </code>
                            </div> -->

                            <div class="d-flex gap-3">
                                <a href="<?= $is_admin ? 'dashboard_admin.php' : 'dashboard.php' ?>"
                                    class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-gradient btn-lg">
                                    <i class="fas fa-key me-2"></i>Ubah Password
                                </button>
                            </div>
                        </form>

                        <!-- Vulnerability Explanation -->
                        <!-- <div class="mt-5 pt-4 border-top">
                            <h5 class="fw-bold mb-3">
                                <i class="fas fa-shield-alt me-2 text-danger"></i>
                                Kerentanan Keamanan (Vulnerabilities):
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="p-3 border rounded">
                                        <h6 class="fw-bold text-danger">
                                            <i class="fas fa-times-circle me-1"></i> GET Method
                                        </h6>
                                        <ul class="small mb-0">
                                            <li>Password terlihat di URL</li>
                                            <li>Tersimpan di browser history</li>
                                            <li>Terekam di server logs</li>
                                            <li>Bisa di-share/bookmark</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded">
                                        <h6 class="fw-bold text-danger">
                                            <i class="fas fa-times-circle me-1"></i> Plain Text Storage
                                        </h6>
                                        <ul class="small mb-0">
                                            <li>Password tidak di-hash</li>
                                            <li>Admin bisa lihat password</li>
                                            <li>Rentan jika database bocor</li>
                                            <li>Tidak ada enkripsi</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-3 bg-success bg-opacity-10 border border-success rounded">
                                        <h6 class="fw-bold text-success">
                                            <i class="fas fa-check-circle me-1"></i> Solusi yang Benar:
                                        </h6>
                                        <ul class="small mb-0">
                                            <li>Gunakan <strong>POST method</strong> untuk mengirim password</li>
                                            <li>Gunakan <strong>password_hash()</strong> untuk enkripsi</li>
                                            <li>Gunakan <strong>HTTPS</strong> untuk koneksi aman</li>
                                            <li>Implementasi <strong>CSRF token</strong> untuk keamanan</li>
                                            <li>Validasi password lama sebelum mengubah</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-auto">
        <div class="container text-center">
            <p class="mb-0">&copy; 2024 Perpustakaan Digital. All rights reserved.</p>
        </div>
    </footer>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('toggleNewPassword').addEventListener('click', function () {
            const input = document.getElementById('new_password');
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
            const input = document.getElementById('confirm_password');
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Update URL preview as user types
        const form = document.getElementById('passwordForm');
        const urlPreview = document.getElementById('urlPreview');
        const newPasswordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('confirm_password');

        function updateUrlPreview() {
            const newPass = newPasswordInput.value || 'yourpass';
            const confirmPass = confirmPasswordInput.value || 'yourpass';
            urlPreview.textContent = `change_password.php?change=true&new_password=${newPass}&confirm_password=${confirmPass}`;
        }

        newPasswordInput.addEventListener('input', updateUrlPreview);
        confirmPasswordInput.addEventListener('input', updateUrlPreview);

        // Warning before submit
        form.addEventListener('submit', function (e) {
            if (!confirm('Are you sure you want to change your password?')) {
                e.preventDefault();
            }
        });
    </script>
</body>

</html>
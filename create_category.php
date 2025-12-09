<?php
require_once 'config.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = escape($conn, $_POST['name'] ?? '');

    if (empty($name)) {
        $error = 'Nama kategori harus diisi!';
    } else {
        // Check if category already exists
        $check_query = "SELECT id FROM category WHERE name = '$name'";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $error = 'Kategori sudah ada!';
        } else {
            $query = "INSERT INTO category (name) VALUES ('$name')";

            if (mysqli_query($conn, $query)) {
                setFlash('success', 'Kategori berhasil ditambahkan!');
                redirect('list_category.php');
            } else {
                $error = 'Gagal menambahkan kategori!';
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
    <title>Tambah Kategori - Perpustakaan Digital</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <i class="fas fa-book-open"></i>
                Perpustakaan
            </div>
            <ul class="sidebar-nav">
                <li>
                    <a href="dashboard_admin.php">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="list_book.php">
                        <i class="fas fa-book"></i>
                        Kelola Buku
                    </a>
                </li>
                <li>
                    <a href="list_category.php" class="active">
                        <i class="fas fa-tags"></i>
                        Kelola Kategori
                    </a>
                </li>
                <li class="mt-4">
                    <a href="index.php">
                        <i class="fas fa-home"></i>
                        Ke Beranda
                    </a>
                </li>
                <li>
                    <a href="change_password.php">
                        <i class="fas fa-key"></i>
                        Ubah Password
                    </a>
                </li>
                <li>
                    <a href="logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="list_category.php">Kelola Kategori</a></li>
                        <li class="breadcrumb-item active">Tambah Kategori</li>
                    </ol>
                </nav>
                <h1 class="page-title">Tambah Kategori Baru</h1>
                <p class="page-subtitle">Isi form berikut untuk menambahkan kategori baru</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-custom mb-4">
                    <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card-custom">
                        <div class="card-body p-4">
                            <form method="POST" action="">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-tag me-2 text-primary"></i>Nama Kategori
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" class="form-control form-control-lg"
                                        placeholder="Masukkan nama kategori" required
                                        value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" autofocus>
                                    <div class="form-text">Contoh: Fiksi, Non-Fiksi, Teknologi, dll.</div>
                                </div>

                                <hr class="my-4">

                                <div class="d-flex gap-3">
                                    <button type="submit" class="btn btn-gradient btn-lg">
                                        <i class="fas fa-save me-2"></i>Simpan Kategori
                                    </button>
                                    <a href="list_category.php" class="btn btn-outline-secondary btn-lg">
                                        <i class="fas fa-times me-2"></i>Batal
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card-custom p-4" style="background: var(--light-bg);">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-info-circle text-primary me-2"></i>Tips
                        </h5>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Gunakan nama yang jelas dan mudah dipahami
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Hindari nama kategori yang terlalu panjang
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Pastikan nama kategori belum ada sebelumnya
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>
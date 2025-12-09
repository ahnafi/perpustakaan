<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Get book ID
$id = (int) ($_GET['id'] ?? 0);
if ($id == 0) {
    redirect(isAdmin() ? 'list_book.php' : 'dashboard.php');
}

// Get book data with category
$query = "SELECT b.*, c.name as category_name FROM book b LEFT JOIN category c ON b.category_id = c.id WHERE b.id = $id";
$result = mysqli_query($conn, $query);
$book = mysqli_fetch_assoc($result);

if (!$book) {
    setFlash('error', 'Buku tidak ditemukan!');
    redirect(isAdmin() ? 'list_book.php' : 'dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $book['title'] ?> - Perpustakaan Digital</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php if (isAdmin()): ?>
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
                        <a href="list_book.php" class="active">
                            <i class="fas fa-book"></i>
                            Kelola Buku
                        </a>
                    </li>
                    <li>
                        <a href="list_category.php">
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
                        <a href="logout.php">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </a>
                    </li>
                </ul>
            </aside>

            <!-- Main Content -->
            <main class="main-content">
            <?php else: ?>
                <!-- Navbar for non-admin -->
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
                                    <a class="nav-link" href="dashboard.php">
                                        <i class="fas fa-home me-1"></i>Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="my_borrowings.php">
                                        <i class="fas fa-book-reader me-1"></i>Peminjaman Saya
                                    </a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                        <i
                                            class="fas fa-user-circle me-1"></i><?= ($_SESSION['user_name']) ?>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
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
                <?php endif; ?>

                <div class="page-header">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="<?= isAdmin() ? 'list_book.php' : 'dashboard.php' ?>">
                                    <?= isAdmin() ? 'Kelola Buku' : 'Dashboard' ?>
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Detail Buku</li>
                        </ol>
                    </nav>
                </div>

                <div class="card-custom">
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <div class="col-md-4">
                                <div class="p-4 h-100 d-flex align-items-center justify-content-center"
                                    style="background: linear-gradient(135deg, #e0e5ec 0%, #f8f9fc 100%);">
                                    <?php if ($book['cover']): ?>
                                        <img src="uploads/<?= ($book['cover']) ?>"
                                            alt="<?= ($book['title']) ?>" class="img-fluid rounded shadow"
                                            style="max-height: 400px;">
                                    <?php else: ?>
                                        <div class="text-center py-5">
                                            <i class="fas fa-book fa-6x text-muted mb-3"></i>
                                            <p class="text-muted">Tidak ada cover</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="p-5">
                                    <span class="book-category mb-3 d-inline-block">
                                        <?= ($book['category_name'] ?? 'Umum') ?>
                                    </span>

                                    <h1 class="fw-bold mb-3" style="color: var(--dark-color);">
                                        <?= ($book['title']) ?>
                                    </h1>

                                    <div class="mb-4">
                                        <span class="badge bg-<?= $book['stock'] > 0 ? 'success' : 'danger' ?> fs-6">
                                            <i class="fas fa-layer-group me-1"></i>
                                            Stok: <?= $book['stock'] ?> <?= $book['stock'] > 0 ? 'Tersedia' : 'Habis' ?>
                                        </span>
                                    </div>

                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="ps-0" style="width: 150px;">
                                                <i class="fas fa-user-edit me-2 text-primary"></i>Penulis
                                            </td>
                                            <td class="fw-semibold"><?= ($book['author']) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0">
                                                <i class="fas fa-building me-2 text-primary"></i>Penerbit
                                            </td>
                                            <td class="fw-semibold"><?= ($book['publisher']) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0">
                                                <i class="fas fa-calendar me-2 text-primary"></i>Tahun Terbit
                                            </td>
                                            <td class="fw-semibold"><?= $book['year'] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0">
                                                <i class="fas fa-tags me-2 text-primary"></i>Kategori
                                            </td>
                                            <td class="fw-semibold">
                                                <?= ($book['category_name'] ?? 'Umum') ?></td>
                                        </tr>
                                    </table>

                                    <hr class="my-4">

                                    <div class="d-flex gap-3 flex-wrap">
                                        <a href="<?= isAdmin() ? 'list_book.php' : 'dashboard.php' ?>"
                                            class="btn btn-outline-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>Kembali
                                        </a>
                                        <?php if (isAdmin()): ?>
                                            <a href="update_book.php?id=<?= $book['id'] ?>" class="btn btn-gradient">
                                                <i class="fas fa-edit me-2"></i>Edit Buku
                                            </a>
                                            <a href="list_book.php?delete=<?= $book['id'] ?>"
                                                class="btn btn-gradient-danger"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">
                                                <i class="fas fa-trash me-2"></i>Hapus
                                            </a>
                                        <?php else: ?>
                                            <?php if ($book['stock'] > 0): ?>
                                                <a href="borrow_book.php?id=<?= $book['id'] ?>" class="btn btn-gradient">
                                                    <i class="fas fa-hand-holding me-2"></i>Pinjam Buku
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-secondary" disabled>
                                                    <i class="fas fa-times-circle me-2"></i>Stok Habis
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (isAdmin()): ?>
            </main>
        </div>
    <?php else: ?>
        </div>
    <?php endif; ?>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>
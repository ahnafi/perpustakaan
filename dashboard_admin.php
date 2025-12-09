<?php
require_once 'config.php';

// Check if user is logged in and is admin
if (!isLoggedIn()) {
    redirect('login.php');
}

if (!isAdmin()) {
    redirect('dashboard.php');
}

// Get statistics
$books_query = "SELECT COUNT(*) as total FROM book";
$books_result = mysqli_query($conn, $books_query);
$total_books = mysqli_fetch_assoc($books_result)['total'];

$categories_query = "SELECT COUNT(*) as total FROM category";
$categories_result = mysqli_query($conn, $categories_query);
$total_categories = mysqli_fetch_assoc($categories_result)['total'];

$users_query = "SELECT COUNT(*) as total FROM user WHERE role = 'user'";
$users_result = mysqli_query($conn, $users_query);
$total_users = mysqli_fetch_assoc($users_result)['total'];

$borrowings_query = "SELECT COUNT(*) as total FROM borrowing WHERE status = 'borrowed'";
$borrowings_result = mysqli_query($conn, $borrowings_query);
$total_borrowings = mysqli_fetch_assoc($borrowings_result)['total'];

// Get recent books
$recent_books_query = "SELECT b.*, c.name as category_name FROM book b LEFT JOIN category c ON b.category_id = c.id ORDER BY b.created_at DESC LIMIT 5";
$recent_books_result = mysqli_query($conn, $recent_books_query);

// Get flash message
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Perpustakaan Digital</title>
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
                    <a href="dashboard_admin.php" class="active">
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
                    <a href="list_category.php">
                        <i class="fas fa-tags"></i>
                        Kelola Kategori
                    </a>
                </li>
                <li>
                    <a href="list_borrowing.php">
                        <i class="fas fa-hand-holding"></i>
                        Kelola Peminjaman
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
            <div class="page-header">
                <h1 class="page-title">Dashboard Admin</h1>
                <p class="page-subtitle">Selamat datang, <?= $_SESSION['user_name'] ?>!</p>
            </div>

            <?php if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-custom alert-dismissible fade show"
                    role="alert">
                    <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
                    <?= $flash['message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon books">
                            <i class="fas fa-book"></i>
                        </div>
                        <div>
                            <div class="stat-number"><?= $total_books ?></div>
                            <div class="stat-label">Total Buku</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon categories">
                            <i class="fas fa-tags"></i>
                        </div>
                        <div>
                            <div class="stat-number"><?= $total_categories ?></div>
                            <div class="stat-label">Kategori</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon users">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <div class="stat-number"><?= $total_users ?></div>
                            <div class="stat-label">Pengguna</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                            <i class="fas fa-hand-holding"></i>
                        </div>
                        <div>
                            <div class="stat-number"><?= $total_borrowings ?></div>
                            <div class="stat-label">Peminjaman Aktif</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="card-custom p-4">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-plus-circle me-2" style="color: var(--primary-color);"></i>
                            Aksi Cepat
                        </h5>
                        <div class="d-flex gap-3 flex-wrap">
                            <a href="create_book.php" class="btn btn-gradient">
                                <i class="fas fa-book-medical me-2"></i>Tambah Buku
                            </a>
                            <a href="create_category.php" class="btn btn-outline-primary">
                                <i class="fas fa-tag me-2"></i>Tambah Kategori
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-custom p-4">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-chart-pie me-2" style="color: var(--accent-color);"></i>
                            Ringkasan
                        </h5>
                        <p class="text-muted mb-0">
                            Total <?= $total_books ?> buku dalam <?= $total_categories ?> kategori,
                            dengan <?= $total_users ?> pengguna terdaftar.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Recent Books -->
            <div class="card-custom">
                <div class="p-4 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-clock me-2" style="color: var(--primary-color);"></i>
                            Buku Terbaru
                        </h5>
                        <a href="list_book.php" class="btn btn-sm btn-gradient">
                            Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background: var(--light-bg);">
                            <tr>
                                <th class="border-0 ps-4">Judul</th>
                                <th class="border-0">Penulis</th>
                                <th class="border-0">Kategori</th>
                                <th class="border-0">Tahun</th>
                                <th class="border-0">Stok</th>
                                <th class="border-0 pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($recent_books_result) > 0): ?>
                                <?php while ($book = mysqli_fetch_assoc($recent_books_result)): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <?php if ($book['cover']): ?>
                                                    <img src="uploads/<?= $book['cover'] ?>" alt="Cover" class="rounded me-3"
                                                        style="width: 40px; height: 50px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="rounded me-3 d-flex align-items-center justify-content-center bg-light"
                                                        style="width: 40px; height: 50px;">
                                                        <i class="fas fa-book text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <span class="fw-semibold"><?= $book['title'] ?></span>
                                            </div>
                                        </td>
                                        <td><?= $book['author'] ?></td>
                                        <td>
                                            <span class="badge" style="background: var(--primary-gradient);">
                                                <?= $book['category_name'] ?? 'Umum' ?>
                                            </span>
                                        </td>
                                        <td><?= $book['year'] ?></td>
                                        <td>
                                            <span class="badge bg-<?= $book['stock'] > 0 ? 'success' : 'danger' ?>">
                                                <?= $book['stock'] ?>
                                            </span>
                                        </td>
                                        <td class="pe-4">
                                            <a href="update_book.php?id=<?= $book['id'] ?>"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        Belum ada buku
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>
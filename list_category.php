<?php
require_once 'config.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    // Check if category has books
    $check_query = "SELECT COUNT(*) as count FROM book WHERE category_id = $id";
    $check_result = mysqli_query($conn, $check_query);
    $count = mysqli_fetch_assoc($check_result)['count'];

    if ($count > 0) {
        setFlash('error', 'Tidak dapat menghapus kategori yang masih memiliki buku!');
    } else {
        $delete_query = "DELETE FROM category WHERE id = $id";
        if (mysqli_query($conn, $delete_query)) {
            setFlash('success', 'Kategori berhasil dihapus!');
        } else {
            setFlash('error', 'Gagal menghapus kategori!');
        }
    }
    redirect('list_category.php');
}

// Get flash message
$flash = getFlash();

// Get all categories with book count
$query = "SELECT c.*, COUNT(b.id) as book_count FROM category c LEFT JOIN book b ON c.id = b.category_id GROUP BY c.id ORDER BY c.name ASC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori - Perpustakaan Digital</title>
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
            <div class="page-header d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">Kelola Kategori</h1>
                    <p class="page-subtitle">Kelola kategori buku perpustakaan</p>
                </div>
                <a href="create_category.php" class="btn btn-gradient">
                    <i class="fas fa-plus me-2"></i>Tambah Kategori
                </a>
            </div>

            <?php if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-custom alert-dismissible fade show"
                    role="alert">
                    <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
                    <?= htmlspecialchars($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Categories Table -->
            <div class="table-custom">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>Nama Kategori</th>
                            <th style="width: 150px;">Jumlah Buku</th>
                            <th style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php $no = 1;
                            while ($category = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td>
                                        <span class="badge rounded-pill" style="background: var(--primary-gradient);">
                                            <?= $no++ ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                                style="width: 40px; height: 40px; background: var(--primary-gradient);">
                                                <i class="fas fa-tag text-white"></i>
                                            </div>
                                            <span class="fw-semibold"><?= htmlspecialchars($category['name']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $category['book_count'] > 0 ? 'success' : 'secondary' ?>">
                                            <?= $category['book_count'] ?> buku
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="update_category.php?id=<?= $category['id'] ?>"
                                                class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="list_category.php?delete=<?= $category['id'] ?>"
                                                class="btn btn-sm btn-outline-danger" title="Hapus"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <i class="fas fa-tags"></i>
                                        </div>
                                        <h4 class="empty-title">Belum Ada Kategori</h4>
                                        <p class="empty-desc">Mulai dengan menambahkan kategori pertama</p>
                                        <a href="create_category.php" class="btn btn-gradient">
                                            <i class="fas fa-plus me-2"></i>Tambah Kategori
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
require_once 'config.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    // Get book cover to delete
    $get_query = "SELECT cover FROM book WHERE id = $id";
    $get_result = mysqli_query($conn, $get_query);
    if ($book = mysqli_fetch_assoc($get_result)) {
        if ($book['cover'] && file_exists('uploads/' . $book['cover'])) {
            unlink('uploads/' . $book['cover']);
        }
    }

    $delete_query = "DELETE FROM book WHERE id = $id";
    if (mysqli_query($conn, $delete_query)) {
        setFlash('success', 'Buku berhasil dihapus!');
    } else {
        setFlash('error', 'Gagal menghapus buku!');
    }
    redirect('list_book.php');
}

// Get flash message
$flash = getFlash();

// Get all books with categories
$query = "SELECT b.*, c.name as category_name FROM book b LEFT JOIN category c ON b.category_id = c.id ORDER BY b.created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Buku - Perpustakaan Digital</title>
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
            <div class="page-header d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">Kelola Buku</h1>
                    <p class="page-subtitle">Kelola semua koleksi buku perpustakaan</p>
                </div>
                <a href="create_book.php" class="btn btn-gradient">
                    <i class="fas fa-plus me-2"></i>Tambah Buku
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

            <!-- Books Table -->
            <div class="table-custom">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Cover</th>
                            <th>Judul</th>
                            <th>Penulis</th>
                            <th>Kategori</th>
                            <th>Penerbit</th>
                            <th>Tahun</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($book = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td>
                                        <?php if ($book['cover']): ?>
                                            <img src="uploads/<?= htmlspecialchars($book['cover']) ?>" alt="Cover" class="rounded"
                                                style="width: 50px; height: 65px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded d-flex align-items-center justify-content-center bg-light"
                                                style="width: 50px; height: 65px;">
                                                <i class="fas fa-book text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="fw-semibold"><?= $book['title'] ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($book['author']) ?></td>
                                    <td>
                                        <span class="badge" style="background: var(--primary-gradient);">
                                            <?= htmlspecialchars($book['category_name'] ?? 'Umum') ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($book['publisher']) ?></td>
                                    <td><?= $book['year'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= $book['stock'] > 0 ? 'success' : 'danger' ?>">
                                            <?= $book['stock'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="detail_book.php?id=<?= $book['id'] ?>" class="btn btn-sm btn-outline-info"
                                                title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="update_book.php?id=<?= $book['id'] ?>"
                                                class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="list_book.php?delete=<?= $book['id'] ?>"
                                                class="btn btn-sm btn-outline-danger" title="Hapus"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <i class="fas fa-book-open"></i>
                                        </div>
                                        <h4 class="empty-title">Belum Ada Buku</h4>
                                        <p class="empty-desc">Mulai dengan menambahkan buku pertama</p>
                                        <a href="create_book.php" class="btn btn-gradient">
                                            <i class="fas fa-plus me-2"></i>Tambah Buku
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
<?php
require_once 'config.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

// Get all borrowings
$query = "SELECT br.*, b.title, b.author, u.name as user_name, u.email as user_email 
          FROM borrowing br 
          JOIN book b ON br.book_id = b.id 
          JOIN user u ON br.user_id = u.id 
          ORDER BY br.created_at DESC";
$result = mysqli_query($conn, $query);

// Count stats
$total_borrowed = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM borrowing WHERE status = 'borrowed'"))['total'];
$total_returned = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM borrowing WHERE status = 'returned'"))['total'];
$total_overdue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM borrowing WHERE status = 'overdue'"))['total'];

// Get flash message
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Peminjaman - Perpustakaan Digital</title>
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
                    <a href="list_category.php">
                        <i class="fas fa-tags"></i>
                        Kelola Kategori
                    </a>
                </li>
                <li>
                    <a href="list_borrowing.php" class="active">
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
            <div class="page-header d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">Kelola Peminjaman</h1>
                    <p class="page-subtitle">Daftar semua peminjaman buku</p>
                </div>
                <a href="return_book.php" class="btn btn-gradient">
                    <i class="fas fa-undo me-2"></i>Form Pengembalian
                </a>
            </div>

            <?php if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-custom mb-4">
                    <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
                    <?= $flash['message'] ?>
                </div>
            <?php endif; ?>

            <!-- Stats -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon books">
                            <i class="fas fa-book-reader"></i>
                        </div>
                        <div>
                            <div class="stat-number"><?= $total_borrowed ?></div>
                            <div class="stat-label">Sedang Dipinjam</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon categories">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <div class="stat-number"><?= $total_returned ?></div>
                            <div class="stat-label">Dikembalikan</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div>
                            <div class="stat-number"><?= $total_overdue ?></div>
                            <div class="stat-label">Terlambat</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-custom">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Peminjam</th>
                                <th>Buku</th>
                                <th>No HP</th>
                                <th>Alamat</th>
                                <th>Tanggal Pinjam</th>
                                <th>Jatuh Tempo</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td>
                                            <strong><?= $row['user_name'] ?></strong>
                                            <br><small class="text-muted"><?= $row['user_email'] ?></small>
                                        </td>
                                        <td>
                                            <strong><?= $row['title'] ?></strong>
                                            <br><small class="text-muted"><?= $row['author'] ?></small>
                                        </td>
                                        <td><?= $row['phone'] ?? '-' ?></td>
                                        <td><?= $row['address'] ?? '-' ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['borrow_date'])) ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['due_date'])) ?></td>
                                        <td>
                                            <?php
                                            $status_class = 'warning';
                                            $status_text = 'Dipinjam';
                                            if ($row['status'] === 'returned') {
                                                $status_class = 'success';
                                                $status_text = 'Dikembalikan';
                                            } elseif ($row['status'] === 'overdue') {
                                                $status_class = 'danger';
                                                $status_text = 'Terlambat';
                                            }
                                            ?>
                                            <span class="badge bg-<?= $status_class ?>"><?= $status_text ?></span>
                                        </td>
                                        <td>
                                            <?php if ($row['status'] !== 'returned'): ?>
                                                <a href="return_book.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-gradient"
                                                    onclick="return confirm('Konfirmasi pengembalian buku ini?')">
                                                    <i class="fas fa-undo me-1"></i>Kembalikan
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">
                                                    <i class="fas fa-check me-1"></i>Selesai
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="empty-state">
                                            <div class="empty-icon">
                                                <i class="fas fa-hand-holding"></i>
                                            </div>
                                            <h4 class="empty-title">Belum Ada Peminjaman</h4>
                                            <p class="empty-desc">Belum ada peminjaman buku</p>
                                        </div>
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
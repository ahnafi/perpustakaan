<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Only for non-admin users
if (isAdmin()) {
    redirect('dashboard_admin.php');
}

$user_id = $_SESSION['user_id'];

// Get user's borrowings
$query = "SELECT br.*, b.title, b.author, b.cover FROM borrowing br 
          JOIN book b ON br.book_id = b.id 
          WHERE br.user_id = $user_id 
          ORDER BY br.created_at DESC";
$result = mysqli_query($conn, $query);

// Get flash message
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Saya - Perpustakaan Digital</title>
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
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="my_borrowings.php">
                            <i class="fas fa-book-reader me-1"></i>Peminjaman Saya
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i><?= $_SESSION['user_name'] ?>
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
        <div class="page-header">
            <h1 class="page-title">Peminjaman Saya</h1>
            <p class="page-subtitle">Daftar buku yang Anda pinjam</p>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-custom mb-4">
                <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
                <?= $flash['message'] ?>
            </div>
        <?php endif; ?>

        <div class="card-custom">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Buku</th>
                            <th>Tanggal Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Tanggal Kembali</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if ($row['cover']): ?>
                                                <img src="uploads/<?= $row['cover'] ?>" alt="<?= $row['title'] ?>"
                                                    class="rounded me-3" style="width: 50px; height: 70px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="rounded me-3 d-flex align-items-center justify-content-center bg-light"
                                                    style="width: 50px; height: 70px;">
                                                    <i class="fas fa-book text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <strong><?= $row['title'] ?></strong>
                                                <br><small class="text-muted"><?= $row['author'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($row['borrow_date'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['due_date'])) ?></td>
                                    <td><?= $row['return_date'] ? date('d/m/Y', strtotime($row['return_date'])) : '-' ?></td>
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
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <i class="fas fa-book-reader"></i>
                                        </div>
                                        <h4 class="empty-title">Belum Ada Peminjaman</h4>
                                        <p class="empty-desc">Anda belum meminjam buku apapun</p>
                                        <a href="dashboard.php" class="btn btn-gradient">
                                            <i class="fas fa-search me-2"></i>Cari Buku
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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
</body>

</html>
<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Redirect admin to admin dashboard
if (isAdmin()) {
    redirect('dashboard_admin.php');
}

// Get statistics
$books_query = "SELECT COUNT(*) as total FROM book";
$books_result = mysqli_query($conn, $books_query);
$total_books = mysqli_fetch_assoc($books_result)['total'];

$categories_query = "SELECT COUNT(*) as total FROM category";
$categories_result = mysqli_query($conn, $categories_query);
$total_categories = mysqli_fetch_assoc($categories_result)['total'];

// Search and filter
// $search = escape($conn, $_GET['search'] ?? '');
$search = $_GET['search'] ?? '';
$category_filter = (int) ($_GET['category'] ?? 0);

// Build books query with search
$books_list_query = "SELECT b.*, c.name as category_name FROM book b LEFT JOIN category c ON b.category_id = c.id WHERE 1=1";

if (!empty($search)) {
    $books_list_query .= " AND (b.title LIKE '%$search%' OR b.author LIKE '%$search%' OR b.publisher LIKE '%$search%')";
}

if ($category_filter > 0) {
    $books_list_query .= " AND b.category_id = $category_filter";
}

$books_list_query .= " ORDER BY b.created_at DESC";
$books_list_result = mysqli_query($conn, $books_list_query);

// Get categories for filter
$categories_list_query = "SELECT * FROM category ORDER BY name ASC";
$categories_list_result = mysqli_query($conn, $categories_list_query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Perpustakaan Digital</title>
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
                        <a class="nav-link active" href="dashboard.php">
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
        <!-- Welcome Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card-custom p-4" style="background: var(--primary-gradient);">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h2 class="text-white fw-bold mb-2">
                                Selamat Datang, <?= $_SESSION['user_name'] ?>! 👋
                            </h2>
                            <p class="text-white-50 mb-0">
                                Jelajahi koleksi buku kami dan temukan bacaan favoritmu
                            </p>
                        </div>
                        <div class="col-lg-4 text-end d-none d-lg-block">
                            <i class="fas fa-books fa-4x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-6">
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
            <div class="col-md-6">
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
        </div>

        <!-- Search Section -->
        <div class="card-custom p-4 mb-4">
            <form method="GET" action="" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Cari Buku</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control"
                            placeholder="Judul, penulis, atau penerbit..." value="<?= $search ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kategori</label>
                    <select name="category" class="form-select">
                        <option value="0">Semua Kategori</option>
                        <?php while ($cat = mysqli_fetch_assoc($categories_list_result)): ?>
                            <option value="<?= $cat['id'] ?>" <?= $category_filter == $cat['id'] ? 'selected' : '' ?>>
                                <?= $cat['name'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-gradient flex-grow-1">
                            <i class="fas fa-search me-1"></i>Cari
                        </button>
                        <?php if (!empty($search) || $category_filter > 0): ?>
                            <a href="dashboard.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <!-- Books Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0" style="color: var(--dark-color);">
                <i class="fas fa-book-open me-2"></i>Koleksi Buku
                <?php if (!empty($search) || $category_filter > 0): ?>
                    <small class="text-muted fs-6">(<?= mysqli_num_rows($books_list_result) ?> hasil)</small>
                <?php endif; ?>
            </h4>
        </div>

        <div class="row g-4">
            <?php if (mysqli_num_rows($books_list_result) > 0): ?>
                <?php while ($book = mysqli_fetch_assoc($books_list_result)): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="book-card">
                            <?php if ($book['cover']): ?>
                                <img src="uploads/<?= $book['cover'] ?>" alt="<?= $book['title'] ?>" class="book-cover">
                            <?php else: ?>
                                <div class="book-cover d-flex align-items-center justify-content-center">
                                    <i class="fas fa-book fa-4x text-muted"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <span class="book-category"><?= $book['category_name'] ?? 'Umum' ?></span>
                                <h5 class="book-title mt-2"><?= $book['title'] ?></h5>
                                <p class="book-author">
                                    <i class="fas fa-user-edit me-1"></i><?= $book['author'] ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-building me-1"></i><?= $book['publisher'] ?>
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i><?= $book['year'] ?>
                                    </small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-<?= $book['stock'] > 0 ? 'success' : 'danger' ?>">
                                        Stok: <?= $book['stock'] ?>
                                    </span>
                                    <a href="detail_book.php?id=<?= $book['id'] ?>" class="btn btn-sm btn-gradient">
                                        <i class="fas fa-eye me-1"></i>Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="card-custom">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <h4 class="empty-title">Belum Ada Buku</h4>
                            <p class="empty-desc">Koleksi buku akan segera tersedia</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
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
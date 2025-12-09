<?php
require_once 'config.php';

// Get some books to display
$books_query = "SELECT b.*, c.name as category_name FROM book b LEFT JOIN category c ON b.category_id = c.id ORDER BY b.created_at DESC LIMIT 6";
$books_result = mysqli_query($conn, $books_query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Digital - Temukan Buku Favoritmu</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
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
                        <a class="nav-link" href="#features">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#books">Koleksi</a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= isAdmin() ? 'dashboard_admin.php' : 'dashboard.php' ?>">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt me-1"></i>Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <h1 class="hero-title fade-in-up">
                        Jelajahi Dunia Melalui Buku
                    </h1>
                    <p class="hero-subtitle fade-in-up delay-1">
                        Temukan ribuan koleksi buku dari berbagai genre. Perpustakaan digital yang modern,
                        mudah diakses, dan selalu ada untuk menemani petualangan literasimu.
                    </p>
                    <div class="d-flex gap-3 fade-in-up delay-2">
                        <?php if (isLoggedIn()): ?>
                            <a href="<?= isAdmin() ? 'dashboard_admin.php' : 'dashboard.php' ?>"
                                class="btn btn-custom-primary">
                                <i class="fas fa-arrow-right me-2"></i>Ke Dashboard
                            </a>
                        <?php else: ?>
                            <a href="register.php" class="btn btn-custom-primary">
                                <i class="fas fa-rocket me-2"></i>Mulai Sekarang
                            </a>
                            <a href="login.php" class="btn btn-custom-secondary">
                                <i class="fas fa-sign-in-alt me-2"></i>Masuk
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block text-center fade-in-up delay-3">
                    <img src="https://illustrations.popsy.co/violet/student-reading-a-book.svg"
                        alt="Reading illustration" style="max-width: 100%; height: auto;">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5" style="background: var(--light-bg);">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3" style="color: var(--dark-color);">Mengapa Memilih Kami?</h2>
                <p class="text-muted">Nikmati pengalaman membaca yang lebih baik dengan fitur-fitur unggulan</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card fade-in-up">
                        <div class="feature-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <h4 class="feature-title">Koleksi Lengkap</h4>
                        <p class="feature-desc">
                            Ribuan judul buku dari berbagai kategori tersedia untuk dieksplorasi
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card fade-in-up delay-1">
                        <div class="feature-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h4 class="feature-title">Pencarian Mudah</h4>
                        <p class="feature-desc">
                            Temukan buku yang kamu cari dengan cepat melalui fitur pencarian canggih
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card fade-in-up delay-2">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h4 class="feature-title">Akses 24/7</h4>
                        <p class="feature-desc">
                            Akses perpustakaan kapan saja dan di mana saja melalui perangkat apapun
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Books Section -->
    <section id="books" class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3" style="color: var(--dark-color);">Koleksi Terbaru</h2>
                <p class="text-muted">Jelajahi buku-buku terbaru yang baru ditambahkan</p>
            </div>
            <div class="row g-4">
                <?php if (mysqli_num_rows($books_result) > 0): ?>
                    <?php while ($book = mysqli_fetch_assoc($books_result)): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="book-card fade-in-up">
                                <?php if ($book['cover']): ?>
                                    <img src="uploads/<?= htmlspecialchars($book['cover']) ?>"
                                        alt="<?= htmlspecialchars($book['title']) ?>" class="book-cover">
                                <?php else: ?>
                                    <div class="book-cover d-flex align-items-center justify-content-center">
                                        <i class="fas fa-book fa-4x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body">
                                    <span class="book-category"><?= htmlspecialchars($book['category_name'] ?? 'Umum') ?></span>
                                    <h5 class="book-title mt-2"><?= htmlspecialchars($book['title']) ?></h5>
                                    <p class="book-author">
                                        <i class="fas fa-user-edit me-1"></i><?= htmlspecialchars($book['author']) ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i><?= $book['year'] ?>
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-layer-group me-1"></i>Stok: <?= $book['stock'] ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-books"></i>
                            </div>
                            <h4 class="empty-title">Belum Ada Buku</h4>
                            <p class="empty-desc">Koleksi buku akan segera tersedia</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (mysqli_num_rows($books_result) > 0): ?>
                <div class="text-center mt-5">
                    <?php if (isLoggedIn()): ?>
                        <a href="<?= isAdmin() ? 'list_book.php' : 'dashboard.php' ?>" class="btn btn-gradient btn-lg">
                            <i class="fas fa-book-open me-2"></i>Lihat Semua Buku
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-gradient btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Masuk untuk Melihat Semua
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h4 class="footer-brand">
                        <i class="fas fa-book-open me-2"></i>Perpustakaan Digital
                    </h4>
                    <p class="footer-text">
                        Platform perpustakaan modern yang menyediakan akses mudah ke berbagai koleksi buku
                        untuk mendukung literasi dan pendidikan.
                    </p>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <h5 class="text-white mb-3">Menu</h5>
                    <ul class="list-unstyled footer-text">
                        <li class="mb-2"><a href="#features" class="text-decoration-none footer-text">Fitur</a></li>
                        <li class="mb-2"><a href="#books" class="text-decoration-none footer-text">Koleksi</a></li>
                        <li class="mb-2"><a href="login.php" class="text-decoration-none footer-text">Login</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white mb-3">Kontak</h5>
                    <ul class="list-unstyled footer-text">
                        <li class="mb-2"><i class="fas fa-envelope me-2"></i>info@perpustakaan.com</li>
                        <li class="mb-2"><i class="fas fa-phone me-2"></i>+62 123 456 789</li>
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>Jakarta, Indonesia</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">
            <div class="text-center footer-text">
                <p class="mb-0">&copy; 2024 Perpustakaan Digital. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>
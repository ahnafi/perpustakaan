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

// Get book ID from URL
$book_id = (int) ($_GET['id'] ?? 0);
$selected_book = null;

if ($book_id > 0) {
    $query = "SELECT * FROM book WHERE id = $book_id AND stock > 0";
    $result = mysqli_query($conn, $query);
    $selected_book = mysqli_fetch_assoc($result);
}

// Get available books for dropdown
$books_query = "SELECT * FROM book WHERE stock > 0 ORDER BY title ASC";
$books_result = mysqli_query($conn, $books_query);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = (int) ($_POST['book_id'] ?? 0);
    $borrow_date = $_POST['borrow_date'] ?? date('Y-m-d');
    $duration = (int) ($_POST['duration'] ?? 7);
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';

    if ($book_id == 0) {
        $error = 'Pilih buku yang akan dipinjam!';
    } elseif (empty($phone)) {
        $error = 'Nomor HP harus diisi!';
    } elseif (empty($address)) {
        $error = 'Alamat harus diisi!';
    } else {
        // Check book exists and has stock
        $book_query = "SELECT * FROM book WHERE id = $book_id";
        $book_result = mysqli_query($conn, $book_query);
        $book = mysqli_fetch_assoc($book_result);

        if (!$book) {
            $error = 'Buku tidak ditemukan!';
        } elseif ($book['stock'] <= 0) {
            $error = 'Stok buku habis!';
        } else {
            // Check if already borrowed
            $check_query = "SELECT * FROM borrowing WHERE user_id = $user_id AND book_id = $book_id AND status = 'borrowed'";
            $check_result = mysqli_query($conn, $check_query);

            if (mysqli_num_rows($check_result) > 0) {
                $error = 'Anda sudah meminjam buku ini!';
            } else {
                $due_date = date('Y-m-d', strtotime($borrow_date . " + $duration days"));

                mysqli_begin_transaction($conn);
                try {
                    $insert_query = "INSERT INTO borrowing (user_id, book_id, borrow_date, due_date, phone, address, status) VALUES ($user_id, $book_id, '$borrow_date', '$due_date', '$phone', '$address', 'borrowed')";
                    mysqli_query($conn, $insert_query);

                    $update_query = "UPDATE book SET stock = stock - 1 WHERE id = $book_id";
                    mysqli_query($conn, $update_query);

                    mysqli_commit($conn);
                    setFlash('success', 'Buku berhasil dipinjam! Jatuh tempo: ' . date('d/m/Y', strtotime($due_date)));
                    redirect('my_borrowings.php');
                } catch (Exception $e) {
                    mysqli_rollback($conn);
                    $error = 'Gagal meminjam buku!';
                }
            }
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
    <title>Pinjam Buku - Perpustakaan Digital</title>
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
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="page-header">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Pinjam Buku</li>
                        </ol>
                    </nav>
                    <h1 class="page-title">Form Peminjaman Buku</h1>
                    <p class="page-subtitle">Isi form berikut untuk meminjam buku</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-custom mb-4">
                        <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
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
                        <form method="POST" action="">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Pilih Buku <span
                                        class="text-danger">*</span></label>
                                <select name="book_id" class="form-select form-select-lg" required>
                                    <option value="">-- Pilih Buku --</option>
                                    <?php while ($book = mysqli_fetch_assoc($books_result)): ?>
                                        <option value="<?= $book['id'] ?>" <?= ($selected_book && $selected_book['id'] == $book['id']) ? 'selected' : '' ?>>
                                            <?= $book['title'] ?> - <?= $book['author'] ?> (Stok: <?= $book['stock'] ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">Nomor HP <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control form-control-lg"
                                        placeholder="Contoh: 08123456789" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">Alamat <span
                                            class="text-danger">*</span></label>
                                    <textarea name="address" class="form-control form-control-lg" rows="1"
                                        placeholder="Masukkan alamat lengkap" required></textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">Tanggal Pinjam <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="borrow_date" class="form-control form-control-lg"
                                        value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">Durasi Pinjam <span
                                            class="text-danger">*</span></label>
                                    <select name="duration" class="form-select form-select-lg" required>
                                        <option value="7">7 Hari</option>
                                        <option value="14">14 Hari</option>
                                        <option value="21">21 Hari</option>
                                        <option value="30">30 Hari</option>
                                    </select>
                                </div>
                            </div>

                            <div class="alert alert-info mb-4">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Ketentuan Peminjaman:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Maksimal durasi peminjaman 30 hari</li>
                                    <li>Buku harus dikembalikan sebelum atau pada tanggal jatuh tempo</li>
                                    <li>Keterlambatan pengembalian akan dikenakan denda</li>
                                </ul>
                            </div>

                            <div class="d-flex gap-3">
                                <a href="dashboard.php" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-gradient btn-lg">
                                    <i class="fas fa-hand-holding me-2"></i>Pinjam Buku
                                </button>
                            </div>
                        </form>
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
</body>

</html>
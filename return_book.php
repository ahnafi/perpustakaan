<?php
require_once 'config.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

// Get borrowing ID from URL
$borrowing_id = (int) ($_GET['id'] ?? 0);
$selected_borrowing = null;

if ($borrowing_id > 0) {
    $query = "SELECT br.*, b.title, b.author, u.name as user_name 
              FROM borrowing br 
              JOIN book b ON br.book_id = b.id 
              JOIN user u ON br.user_id = u.id 
              WHERE br.id = $borrowing_id AND br.status = 'borrowed'";
    $result = mysqli_query($conn, $query);
    $selected_borrowing = mysqli_fetch_assoc($result);
}

// Get active borrowings for dropdown
$borrowings_query = "SELECT br.*, b.title, b.author, u.name as user_name 
                     FROM borrowing br 
                     JOIN book b ON br.book_id = b.id 
                     JOIN user u ON br.user_id = u.id 
                     WHERE br.status = 'borrowed' 
                     ORDER BY br.due_date ASC";
$borrowings_result = mysqli_query($conn, $borrowings_query);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $borrowing_id = (int) ($_POST['borrowing_id'] ?? 0);
    $return_date = $_POST['return_date'] ?? date('Y-m-d');
    $notes = escape($conn, $_POST['notes'] ?? '');

    if ($borrowing_id == 0) {
        $error = 'Pilih peminjaman yang akan dikembalikan!';
    } else {
        $borrow_query = "SELECT * FROM borrowing WHERE id = $borrowing_id AND status = 'borrowed'";
        $borrow_result = mysqli_query($conn, $borrow_query);
        $borrowing = mysqli_fetch_assoc($borrow_result);

        if (!$borrowing) {
            $error = 'Data peminjaman tidak ditemukan atau sudah dikembalikan!';
        } else {
            $book_id = $borrowing['book_id'];
            $status = 'returned';
            
            // Check if overdue
            if (strtotime($return_date) > strtotime($borrowing['due_date'])) {
                $status = 'returned'; // Still returned but was overdue
            }

            mysqli_begin_transaction($conn);
            try {
                $update_borrowing = "UPDATE borrowing SET status = '$status', return_date = '$return_date' WHERE id = $borrowing_id";
                mysqli_query($conn, $update_borrowing);

                $update_book = "UPDATE book SET stock = stock + 1 WHERE id = $book_id";
                mysqli_query($conn, $update_book);

                mysqli_commit($conn);
                setFlash('success', 'Buku berhasil dikembalikan!');
                redirect('list_borrowing.php');
            } catch (Exception $e) {
                mysqli_rollback($conn);
                $error = 'Gagal memproses pengembalian!';
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
    <title>Form Pengembalian - Perpustakaan Digital</title>
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
            <div class="page-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="list_borrowing.php">Kelola Peminjaman</a></li>
                        <li class="breadcrumb-item active">Form Pengembalian</li>
                    </ol>
                </nav>
                <h1 class="page-title">Form Pengembalian Buku</h1>
                <p class="page-subtitle">Proses pengembalian buku yang dipinjam</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-custom mb-4">
                    <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                </div>
            <?php endif; ?>

            <?php if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-custom mb-4">
                    <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
                    <?= $flash['message'] ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card-custom">
                        <div class="card-body p-4">
                            <form method="POST" action="">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Pilih Peminjaman <span class="text-danger">*</span></label>
                                    <select name="borrowing_id" class="form-select form-select-lg" required id="borrowingSelect">
                                        <option value="">-- Pilih Peminjaman --</option>
                                        <?php while ($row = mysqli_fetch_assoc($borrowings_result)): ?>
                                            <?php 
                                            $is_overdue = strtotime($row['due_date']) < strtotime(date('Y-m-d'));
                                            ?>
                                            <option value="<?= $row['id'] ?>" 
                                                <?= ($selected_borrowing && $selected_borrowing['id'] == $row['id']) ? 'selected' : '' ?>
                                                data-user="<?= $row['user_name'] ?>"
                                                data-book="<?= $row['title'] ?>"
                                                data-borrow="<?= date('d/m/Y', strtotime($row['borrow_date'])) ?>"
                                                data-due="<?= date('d/m/Y', strtotime($row['due_date'])) ?>"
                                                data-overdue="<?= $is_overdue ? '1' : '0' ?>">
                                                <?= $row['user_name'] ?> - <?= $row['title'] ?> 
                                                (Jatuh tempo: <?= date('d/m/Y', strtotime($row['due_date'])) ?>)
                                                <?= $is_overdue ? ' [TERLAMBAT]' : '' ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div id="borrowingDetails" class="mb-4" style="display: none;">
                                    <div class="alert alert-secondary">
                                        <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i>Detail Peminjaman</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Peminjam:</strong> <span id="detailUser"></span></p>
                                                <p class="mb-0"><strong>Buku:</strong> <span id="detailBook"></span></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Tanggal Pinjam:</strong> <span id="detailBorrow"></span></p>
                                                <p class="mb-0"><strong>Jatuh Tempo:</strong> <span id="detailDue"></span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="overdueAlert" class="alert alert-danger mb-4" style="display: none;">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Perhatian!</strong> Peminjaman ini sudah melewati tanggal jatuh tempo.
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Tanggal Pengembalian <span class="text-danger">*</span></label>
                                    <input type="date" name="return_date" class="form-control form-control-lg"
                                        value="<?= date('Y-m-d') ?>" required>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Catatan (Opsional)</label>
                                    <textarea name="notes" class="form-control" rows="3" placeholder="Catatan tambahan..."></textarea>
                                </div>

                                <div class="d-flex gap-3">
                                    <a href="list_borrowing.php" class="btn btn-outline-secondary btn-lg">
                                        <i class="fas fa-arrow-left me-2"></i>Kembali
                                    </a>
                                    <button type="submit" class="btn btn-gradient btn-lg">
                                        <i class="fas fa-undo me-2"></i>Proses Pengembalian
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('borrowingSelect').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const detailsDiv = document.getElementById('borrowingDetails');
            const overdueAlert = document.getElementById('overdueAlert');
            
            if (this.value) {
                document.getElementById('detailUser').textContent = selected.dataset.user;
                document.getElementById('detailBook').textContent = selected.dataset.book;
                document.getElementById('detailBorrow').textContent = selected.dataset.borrow;
                document.getElementById('detailDue').textContent = selected.dataset.due;
                detailsDiv.style.display = 'block';
                
                if (selected.dataset.overdue === '1') {
                    overdueAlert.style.display = 'block';
                } else {
                    overdueAlert.style.display = 'none';
                }
            } else {
                detailsDiv.style.display = 'none';
                overdueAlert.style.display = 'none';
            }
        });

        // Trigger on page load if pre-selected
        if (document.getElementById('borrowingSelect').value) {
            document.getElementById('borrowingSelect').dispatchEvent(new Event('change'));
        }
    </script>
</body>

</html>
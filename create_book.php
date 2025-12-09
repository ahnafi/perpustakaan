<?php
require_once 'config.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$error = '';
$success = '';

// Get categories for dropdown
$categories_query = "SELECT * FROM category ORDER BY name ASC";
$categories_result = mysqli_query($conn, $categories_query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = escape($conn, $_POST['title'] ?? '');
    $author = escape($conn, $_POST['author'] ?? '');
    $publisher = escape($conn, $_POST['publisher'] ?? '');
    $year = (int)($_POST['year'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    
    if (empty($title) || empty($author) || empty($publisher) || $year == 0 || $category_id == 0) {
        $error = 'Semua field harus diisi!';
    } else {
        $cover = '';
        
        // Handle file upload
        if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['cover']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $cover = uniqid() . '_' . time() . '.' . $ext;
                $upload_path = 'uploads/' . $cover;
                
                if (!move_uploaded_file($_FILES['cover']['tmp_name'], $upload_path)) {
                    $error = 'Gagal mengupload cover!';
                }
            } else {
                $error = 'Format file tidak valid! (jpg, jpeg, png, gif, webp)';
            }
        }
        
        if (empty($error)) {
            $query = "INSERT INTO book (title, author, cover, publisher, year, stock, category_id) 
                      VALUES ('$title', '$author', '$cover', '$publisher', $year, $stock, $category_id)";
            
            if (mysqli_query($conn, $query)) {
                setFlash('success', 'Buku berhasil ditambahkan!');
                redirect('list_book.php');
            } else {
                $error = 'Gagal menambahkan buku!';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku - Perpustakaan Digital</title>
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
            <div class="page-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="list_book.php">Kelola Buku</a></li>
                        <li class="breadcrumb-item active">Tambah Buku</li>
                    </ol>
                </nav>
                <h1 class="page-title">Tambah Buku Baru</h1>
                <p class="page-subtitle">Isi form berikut untuk menambahkan buku baru</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-custom mb-4">
                    <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                </div>
            <?php endif; ?>

            <div class="card-custom">
                <div class="card-body p-4">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Judul Buku <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control form-control-lg" 
                                           placeholder="Masukkan judul buku" required
                                           value="<?= $_POST['title'] ?? '' ?>">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-semibold">Penulis <span class="text-danger">*</span></label>
                                        <input type="text" name="author" class="form-control" 
                                               placeholder="Nama penulis" required
                                               value="<?= $_POST['author'] ?? '' ?>">
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-semibold">Penerbit <span class="text-danger">*</span></label>
                                        <input type="text" name="publisher" class="form-control" 
                                               placeholder="Nama penerbit" required
                                               value="<?= $_POST['publisher'] ?? '' ?>">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-4">
                                        <label class="form-label fw-semibold">Tahun <span class="text-danger">*</span></label>
                                        <input type="number" name="year" class="form-control" 
                                               placeholder="Tahun terbit" min="1900" max="<?= date('Y') ?>" required
                                               value="<?= $_POST['year'] ?? '' ?>">
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <label class="form-label fw-semibold">Stok <span class="text-danger">*</span></label>
                                        <input type="number" name="stock" class="form-control" 
                                               placeholder="Jumlah stok" min="0" required
                                               value="<?= $_POST['stock'] ?? '0' ?>">
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                                        <select name="category_id" class="form-select" required>
                                            <option value="">Pilih Kategori</option>
                                            <?php 
                                            mysqli_data_seek($categories_result, 0);
                                            while ($cat = mysqli_fetch_assoc($categories_result)): 
                                            ?>
                                                <option value="<?= $cat['id'] ?>" 
                                                    <?= (($_POST['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                                                    <?= $cat['name'] ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Cover Buku</label>
                                    <div class="border rounded p-4 text-center" style="min-height: 200px;">
                                        <div id="cover-preview" class="mb-3">
                                            <i class="fas fa-image fa-4x text-muted"></i>
                                        </div>
                                        <input type="file" name="cover" id="cover-input" class="form-control" 
                                               accept="image/*" onchange="previewCover(this)">
                                        <small class="text-muted mt-2 d-block">Format: JPG, PNG, GIF, WebP</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-gradient btn-lg">
                                <i class="fas fa-save me-2"></i>Simpan Buku
                            </button>
                            <a href="list_book.php" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        function previewCover(input) {
            const preview = document.getElementById('cover-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" class="img-fluid rounded" style="max-height: 180px;">';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>

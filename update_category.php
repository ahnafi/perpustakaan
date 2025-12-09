<?php
require_once 'config.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$error = '';

// Get category ID
$id = (int) ($_GET['id'] ?? 0);
if ($id == 0) {
    redirect('list_category.php');
}

// Get category data
$query = "SELECT * FROM category WHERE id = $id";
$result = mysqli_query($conn, $query);
$category = mysqli_fetch_assoc($result);

if (!$category) {
    setFlash('error', 'Kategori tidak ditemukan!');
    redirect('list_category.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = escape($conn, $_POST['name'] ?? '');

    if (empty($name)) {
        $error = 'Nama kategori harus diisi!';
    } else {
        // Check if category already exists (excluding current)
        $check_query = "SELECT id FROM category WHERE name = '$name' AND id != $id";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $error = 'Kategori dengan nama tersebut sudah ada!';
        } else {
            $query = "UPDATE category SET name = '$name' WHERE id = $id";

            if (mysqli_query($conn, $query)) {
                setFlash('success', 'Kategori berhasil diperbarui!');
                redirect('list_category.php');
            } else {
                $error = 'Gagal memperbarui kategori!';
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
    <title>Edit Kategori - Perpustakaan Digital</title>
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
                        <li class="breadcrumb-item"><a href="list_category.php">Kelola Kategori</a></li>
                        <li class="breadcrumb-item active">Edit Kategori</li>
                    </ol>
                </nav>
                <h1 class="page-title">Edit Kategori</h1>
                <p class="page-subtitle">Perbarui nama kategori</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-custom mb-4">
                    <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card-custom">
                        <div class="card-body p-4">
                            <form method="POST" action="">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-tag me-2 text-primary"></i>Nama Kategori
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" class="form-control form-control-lg"
                                        placeholder="Masukkan nama kategori" required
                                        value="<?= htmlspecialchars($category['name']) ?>" autofocus>
                                </div>

                                <hr class="my-4">

                                <div class="d-flex gap-3">
                                    <button type="submit" class="btn btn-gradient btn-lg">
                                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                                    </button>
                                    <a href="list_category.php" class="btn btn-outline-secondary btn-lg">
                                        <i class="fas fa-times me-2"></i>Batal
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card-custom p-4">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-info-circle text-primary me-2"></i>Informasi Kategori
                        </h5>
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="ps-0" style="width: 120px;">ID</td>
                                <td class="fw-semibold">#<?= $category['id'] ?></td>
                            </tr>
                            <tr>
                                <td class="ps-0">Nama Saat Ini</td>
                                <td class="fw-semibold"><?= htmlspecialchars($category['name']) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>
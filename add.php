<?php
include 'config/database.php';
include 'header.php';

$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $maSV = $_POST['MaSV'];
    $hoTen = $_POST['HoTen'];
    $gioiTinh = $_POST['GioiTinh'];
    $ngaySinh = $_POST['NgaySinh'];
    $hinh = $_POST['Hinh'];
    $maNganh = $_POST['MaNganh'];

    // Kiểm tra trùng mã sinh viên
    $check_sql = "SELECT * FROM SinhVien WHERE MaSV = :MaSV";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bindParam(':MaSV', $maSV);
    $check_stmt->execute();
    
    if ($check_stmt->rowCount() > 0) {
        $error_message = "Mã sinh viên đã tồn tại!";
    } else {
        $sql = "INSERT INTO SinhVien (MaSV, HoTen, GioiTinh, NgaySinh, Hinh, MaNganh) 
                VALUES (:MaSV, :HoTen, :GioiTinh, :NgaySinh, :Hinh, :MaNganh)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':MaSV', $maSV);
        $stmt->bindParam(':HoTen', $hoTen);
        $stmt->bindParam(':GioiTinh', $gioiTinh);
        $stmt->bindParam(':NgaySinh', $ngaySinh);
        $stmt->bindParam(':Hinh', $hinh);
        $stmt->bindParam(':MaNganh', $maNganh);

        if ($stmt->execute()) {
            $success_message = "Thêm sinh viên thành công!";
        } else {
            $error_message = "Lỗi khi thêm sinh viên!";
        }
    }
}

// Lấy danh sách ngành học để hiển thị trong form
$nganh_sql = "SELECT * FROM NganhHoc";
$nganh_stmt = $conn->prepare($nganh_sql);
$nganh_stmt->execute();
$nganh_list = $nganh_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sinh Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Thêm Sinh Viên</h2>

    <!-- Hiển thị thông báo -->
    <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Mã Sinh Viên:</label>
            <input type="text" name="MaSV" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Họ Tên:</label>
            <input type="text" name="HoTen" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Giới Tính:</label>
            <select name="GioiTinh" class="form-control" required>
                <option value="Nam">Nam</option>
                <option value="Nữ">Nữ</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày Sinh:</label>
            <input type="date" name="NgaySinh" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Hình Ảnh (URL):</label>
            <input type="text" name="Hinh" class="form-control" placeholder="Nhập URL ảnh sinh viên">
        </div>

        <div class="mb-3">
            <label class="form-label">Ngành Học:</label>
            <select name="MaNganh" class="form-control" required>
                <option value="">-- Chọn Ngành --</option>
                <?php foreach ($nganh_list as $nganh): ?>
                    <option value="<?php echo $nganh['MaNganh']; ?>">
                        <?php echo $nganh['TenNganh']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Thêm Sinh Viên</button>
        <a href="index.php" class="btn btn-secondary">Quay Lại</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
include 'config/database.php';
include 'header.php';

$success_message = '';
$error_message = '';

// Kiểm tra nếu có tham số `id`
if (isset($_GET['id'])) {
    $maSV = $_GET['id'];

    // Lấy thông tin sinh viên theo mã sinh viên
    $sql = "SELECT * FROM SinhVien WHERE MaSV = :MaSV";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':MaSV', $maSV);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        die("Sinh viên không tồn tại!");
    }
} else {
    die("Thiếu mã sinh viên!");
}

// Xử lý cập nhật thông tin sinh viên khi form được gửi đi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hoTen = $_POST['HoTen'];
    $gioiTinh = $_POST['GioiTinh'];
    $ngaySinh = $_POST['NgaySinh'];
    $maNganh = $_POST['MaNganh'];
    $hinh = $student['Hinh']; // Mặc định giữ hình cũ

    if (!empty($_FILES['Hinh']['name'])) {
        $target_dir = "uploads/"; // Thư mục lưu ảnh
        $file_name = basename($_FILES["Hinh"]["name"]);
        $target_file = $target_dir . time() . "_" . $file_name; // Đổi tên file để tránh trùng lặp
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Kiểm tra định dạng ảnh
        $allowed_types = ['jpg', 'jpeg', 'png'];
        if (!in_array($imageFileType, $allowed_types)) {
            $error_message = "Chỉ chấp nhận file JPG, JPEG, PNG.";
        } else {
            // Di chuyển file vào thư mục uploads
            if (move_uploaded_file($_FILES["Hinh"]["tmp_name"], $target_file)) {
                $hinh = $target_file; // Cập nhật đường dẫn mới của ảnh
            } else {
                $error_message = "Lỗi khi tải ảnh lên!";
            }
        }
    }

    $update_sql = "UPDATE SinhVien 
                   SET HoTen = :HoTen, GioiTinh = :GioiTinh, NgaySinh = :NgaySinh, Hinh = :Hinh, MaNganh = :MaNganh 
                   WHERE MaSV = :MaSV";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bindParam(':MaSV', $maSV);
    $update_stmt->bindParam(':HoTen', $hoTen);
    $update_stmt->bindParam(':GioiTinh', $gioiTinh);
    $update_stmt->bindParam(':NgaySinh', $ngaySinh);
    $update_stmt->bindParam(':Hinh', $hinh);
    $update_stmt->bindParam(':MaNganh', $maNganh);

    if ($update_stmt->execute()) {
        $success_message = "Cập nhật thông tin thành công!";
        $student['Hinh'] = $hinh; // Cập nhật lại dữ liệu hiển thị
    } else {
        $error_message = "Lỗi khi cập nhật!";
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
    <title>Sửa Sinh Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .student-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Sửa Thông Tin Sinh Viên</h2>

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
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['MaSV']); ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Họ Tên:</label>
            <input type="text" name="HoTen" class="form-control" value="<?php echo htmlspecialchars($student['HoTen']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Giới Tính:</label>
            <select name="GioiTinh" class="form-control">
                <option value="Nam" <?php echo ($student['GioiTinh'] == 'Nam') ? 'selected' : ''; ?>>Nam</option>
                <option value="Nữ" <?php echo ($student['GioiTinh'] == 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày Sinh:</label>
            <input type="date" name="NgaySinh" class="form-control" value="<?php echo htmlspecialchars($student['NgaySinh']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Hình Ảnh Hiện Tại:</label><br>
            <img src="<?php echo htmlspecialchars($student['Hinh']); ?>" class="student-img" alt="Student Image">
        </div>

        <div class="mb-3">
            <label class="form-label">Chọn Ảnh Mới:</label>
            <input type="file" name="Hinh" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Ngành Học:</label>
            <select name="MaNganh" class="form-control" required>
                <option value="">-- Chọn Ngành --</option>
                <?php foreach ($nganh_list as $nganh): ?>
                    <option value="<?php echo $nganh['MaNganh']; ?>" 
                        <?php echo ($student['MaNganh'] == $nganh['MaNganh']) ? 'selected' : ''; ?>>
                        <?php echo $nganh['TenNganh']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Cập Nhật</button>
        <a href="index.php" class="btn btn-secondary">Quay Lại</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

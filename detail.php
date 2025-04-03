<?php
include 'config/database.php';
include 'header.php';

$error_message = "";

// Kiểm tra nếu có ID sinh viên trong URL
if (isset($_GET['id'])) {
    $maSV = $_GET['id'];

    // Lấy thông tin sinh viên từ database
    $sql = "SELECT sv.MaSV, sv.HoTen, sv.GioiTinh, sv.NgaySinh, sv.Hinh, nh.TenNganh 
            FROM SinhVien sv 
            JOIN NganhHoc nh ON sv.MaNganh = nh.MaNganh 
            WHERE sv.MaSV = :MaSV";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':MaSV', $maSV);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    // Nếu không tìm thấy sinh viên
    if (!$student) {
        $error_message = "Không tìm thấy sinh viên!";
    }
} else {
    $error_message = "Thiếu mã sinh viên!";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Tin Sinh Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .student-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
    </style>
    
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Thông Tin Sinh Viên</h2>

    <?php if ($error_message): ?>
        <div class="alert alert-danger text-center"><?php echo $error_message; ?></div>
    <?php elseif ($student): ?>
        <div class="card shadow">
            <div class="card-header bg-primary text-white">Chi tiết sinh viên</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img src="<?php echo htmlspecialchars($student['Hinh']); ?>" class="student-img" alt="Hình Sinh Viên">
                    </div>
                    <div class="col-md-8">
                        <p><strong>Mã SV:</strong> <?php echo htmlspecialchars($student['MaSV']); ?></p>
                        <p><strong>Họ Tên:</strong> <?php echo htmlspecialchars($student['HoTen']); ?></p>
                        <p><strong>Giới Tính:</strong> <?php echo htmlspecialchars($student['GioiTinh']); ?></p>
                        <p><strong>Ngày Sinh:</strong> <?php echo htmlspecialchars($student['NgaySinh']); ?></p>
                        <p><strong>Ngành Học:</strong> <?php echo htmlspecialchars($student['TenNganh']); ?></p>
                    </div>
                </div>
                <a href="index.php" class="btn btn-secondary mt-3">Quay Lại</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

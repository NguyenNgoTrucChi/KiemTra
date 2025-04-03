<?php
session_start(); // Bắt đầu session để sử dụng giỏ hàng
include 'config/database.php';
include 'header.php';

// Kiểm tra nếu có yêu cầu thêm môn học vào giỏ hàng
if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];

    // Lấy thông tin môn học từ database
    $sql = "SELECT * FROM HocPhan WHERE MaHP = :course_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':course_id', $course_id);
    $stmt->execute();
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($course) {
        // Lưu vào giỏ hàng (Session)
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = []; // Nếu giỏ hàng chưa tồn tại, tạo mới
        }

        // Kiểm tra nếu môn học đã có trong giỏ hàng chưa
        if (!array_key_exists($course_id, $_SESSION['cart'])) {
            $_SESSION['cart'][$course_id] = $course;
        }
    }

    // Chuyển hướng lại trang danh sách học phần sau khi thêm vào giỏ hàng
    header("Location: course_list.php");
    exit();
}

// Lấy danh sách học phần
$sql = "SELECT * FROM HocPhan";
$stmt = $conn->prepare($sql);
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Học Phần</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Danh Sách Học Phần</h2>
    <table class="table table-bordered table-striped mt-3">
        <thead class="table-dark">
            <tr>
                <th>Mã Học Phần</th>
                <th>Tên Học Phần</th>
                <th>Số Tín Chỉ</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courses as $course): ?>
                <tr>
                    <td><?php echo htmlspecialchars($course['MaHP']); ?></td>
                    <td><?php echo htmlspecialchars($course['TenHP']); ?></td>
                    <td><?php echo htmlspecialchars($course['SoTinChi']); ?></td>
                    <td>
                        <a href="?course_id=<?php echo htmlspecialchars($course['MaHP']); ?>" class="btn btn-primary btn-sm">
                            Đăng Ký
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="index.php" class="btn btn-secondary">Quay Lại</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

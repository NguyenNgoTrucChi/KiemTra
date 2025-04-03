<?php
session_start(); // Bắt đầu session
include 'config/database.php';
include 'header.php';

// Xóa một học phần khỏi giỏ hàng nếu có yêu cầu
if (isset($_GET['remove_id'])) {
    $remove_id = $_GET['remove_id'];
    unset($_SESSION['cart'][$remove_id]); // Xóa học phần dựa trên mã ID
    header("Location: register_list.php"); // Tải lại trang sau khi xóa
    exit();
}

// Xóa toàn bộ học phần khi nhấn nút "Xóa đăng ký"
if (isset($_POST['delete_registration'])) {
    unset($_SESSION['cart']); // Xóa tất cả học phần trong session
    header("Location: register_list.php"); // Tải lại trang sau khi xóa
    exit();
}

$total_courses = 0;
$total_credits = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $course) {
        $total_courses++;
        $total_credits += $course['SoTinChi']; // Cộng số tín chỉ
    }
}

// Xử lý đăng ký chính thức
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $student_id = "0123456789"; // Giả định mã sinh viên, thay thế bằng session đăng nhập thực tế

    // Tạo đăng ký mới
    $sql = "INSERT INTO DangKy (MaSV, NgayDK) VALUES (:student_id, CURDATE())";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $register_id = $conn->lastInsertId();

    // Lưu chi tiết đăng ký
    foreach ($_SESSION['cart'] as $course) {
        $sql = "INSERT INTO ChiTietDangKy (MaDK, MaHP) VALUES (:register_id, :course_id)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':register_id', $register_id);
        $stmt->bindParam(':course_id', $course['MaHP']);
        $stmt->execute();
    }

    // Xóa giỏ hàng sau khi đăng ký thành công
    unset($_SESSION['cart']);

    echo "<script>alert('Đăng ký môn học thành công!'); window.location.href='register_list.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng - Đăng Ký Học Phần</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Giỏ Hàng - Môn Học Đã Chọn</h2>
    
    <?php if (!empty($_SESSION['cart'])): ?>
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
                <?php foreach ($_SESSION['cart'] as $course): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($course['MaHP']); ?></td>
                        <td><?php echo htmlspecialchars($course['TenHP']); ?></td>
                        <td><?php echo htmlspecialchars($course['SoTinChi']); ?></td>
                        <td>
                            <a href="?remove_id=<?php echo htmlspecialchars($course['MaHP']); ?>" class="btn btn-danger btn-sm">
                                Xóa
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="table-info">
                    <td colspan="2" class="text-head"><strong>Tổng số môn học:</strong></td>
                    <td colspan="2"><?php echo $total_courses; ?></td>
                </tr>
                <tr class="table-info">
                    <td colspan="2" class="text-head"><strong>Tổng số tín chỉ:</strong></td>
                    <td colspan="2"><?php echo $total_credits; ?></td>
                </tr>
            </tfoot>
        </table>

        <form method="POST">
            <button type="submit" name="delete_registration" class="btn btn-danger">Xóa đăng ký</button>
            <button type="submit" class="btn btn-success">Lưu Đăng Ký</button>
        </form>
    <?php else: ?>
        <p class="text-center">Chưa có môn học nào trong giỏ hàng.</p>
    <?php endif; ?>

    <a href="course_list.php" class="btn btn-secondary mt-3">Tiếp Tục Đăng Ký</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Danh Sách Sinh Viên</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .student-img {
            width: 150px; /* Increased width for larger images */
            height: 150px; /* Increased height for larger images */
            object-fit: cover; /* Ensures the image scales properly */
            border-radius: 5px; /* Optional: adds a slight border radius for better aesthetics */
        }
        /* Optional: Adjust table cell padding for better spacing with larger images */
        .table td, .table th {
            vertical-align: middle; /* Center-align content vertically */
            padding: 10px; /* Add more padding for better spacing */
        }
    </style>
</head>
<body>
    

    <!-- Main Content -->
    <div class="container mt-4">
        <h2 class="text-center mb-4">TRANG SINH VIÊN</h2>
        <a href="add.php" class="btn btn-primary mb-3">Add Student</a>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>MaSV</th>
                    <th>Họ Tên</th>
                    <th>Giới Tính</th>
                    <th>Ngày Sinh</th>
                    <th>Hình</th>
                    <th>Mã Ngành</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'config/database.php';

                // Pagination settings
                $students_per_page = 4; // Number of students per page
                $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Get current page from URL, default to 1
                $offset = ($current_page - 1) * $students_per_page; // Calculate the offset for the SQL query

                // Get total number of students
                $total_sql = "SELECT COUNT(*) FROM SinhVien";
                $total_stmt = $conn->prepare($total_sql);
                $total_stmt->execute();
                $total_students = $total_stmt->fetchColumn();
                $total_pages = ceil($total_students / $students_per_page); // Calculate total pages

                // Fetch students for the current page
                $sql = "SELECT sv.MaSV, sv.HoTen, sv.GioiTinh, sv.NgaySinh, sv.Hinh, nh.MaNganh 
                        FROM SinhVien sv 
                        JOIN NganhHoc nh ON sv.MaNganh = nh.MaNganh 
                        LIMIT :offset, :students_per_page";
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                $stmt->bindValue(':students_per_page', $students_per_page, PDO::PARAM_INT);
                $stmt->execute();
                $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($students as $student) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($student['MaSV']) . "</td>";
                    echo "<td>" . htmlspecialchars($student['HoTen']) . "</td>";
                    echo "<td>" . htmlspecialchars($student['GioiTinh']) . "</td>";
                    echo "<td>" . htmlspecialchars($student['NgaySinh']) . "</td>";
                    echo "<td><img src='" . htmlspecialchars($student['Hinh']) . "' class='student-img' alt='Student Image'></td>";
                    echo "<td>" . htmlspecialchars($student['MaNganh']) . "</td>";
                    echo "<td>";
                    echo "<a href='detail.php?id=" . htmlspecialchars($student['MaSV']) . "' class='btn btn-sm btn-info me-2'>Detail</a>";
                    echo "<a href='update.php?id=" . htmlspecialchars($student['MaSV']) . "' class='btn btn-sm btn-warning me-2'>Edit</a>";
                    echo "<a href='delete.php?id=" . htmlspecialchars($student['MaSV']) . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this student?\")'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Pagination Controls -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <!-- Previous Button -->
                <li class="page-item <?php if ($current_page <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $current_page - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo; Previous</span>
                    </a>
                </li>

                <!-- Page Numbers -->
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php if ($i == $current_page) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <!-- Next Button -->
                <li class="page-item <?php if ($current_page >= $total_pages) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $current_page + 1; ?>" aria-label="Next">
                        <span aria-hidden="true">Next &raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
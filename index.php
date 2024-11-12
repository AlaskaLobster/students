<?php
// Gọi file db.php để sử dụng kết nối cơ sở dữ liệu
include 'db.php';

// Khởi tạo các biến từ form tìm kiếm và phân trang
$name = isset($_GET['name']) ? $_GET['name'] : '';
$age = isset($_GET['age']) ? $_GET['age'] : '';
$class = isset($_GET['class']) ? $_GET['class'] : '';
$address = isset($_GET['address']) ? $_GET['address'] : '';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;  // Số lượng kết quả mỗi trang
$offset = ($page - 1) * $limit;

// Khởi tạo câu truy vấn SQL an toàn với Prepared Statements
$sql = "SELECT * FROM students WHERE 1=1";
$params = [];
$param_types = '';  // Chuỗi chứa kiểu của các tham số

if ($name) {
    $sql .= " AND name LIKE ?";
    $params[] = "%$name%";
    $param_types .= 's';
}
if ($age) {
    $sql .= " AND age = ?";
    $params[] = $age;
    $param_types .= 'i';
}
if ($class) {
    $sql .= " AND class LIKE ?";
    $params[] = "%$class%";
    $param_types .= 's';
}
if ($address) {
    $sql .= " AND address LIKE ?";
    $params[] = "%$address%";
    $param_types .= 's';
}

// Thêm điều kiện phân trang
$sql .= " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$param_types .= 'ii';  // 'i' cho số nguyên của LIMIT và OFFSET

// Thực hiện truy vấn với Prepared Statements
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    // Ràng buộc các tham số nếu có
    $stmt->bind_param($param_types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Lấy tổng số bản ghi để tính tổng số trang
$count_sql = "SELECT COUNT(*) FROM students WHERE 1=1";
$count_params = [];
$count_param_types = '';

// Thêm các điều kiện tìm kiếm vào câu truy vấn đếm
if ($name) {
    $count_sql .= " AND name LIKE ?";
    $count_params[] = "%$name%";
    $count_param_types .= 's';
}
if ($age) {
    $count_sql .= " AND age = ?";
    $count_params[] = $age;
    $count_param_types .= 'i';
}
if ($class) {
    $count_sql .= " AND class LIKE ?";
    $count_params[] = "%$class%";
    $count_param_types .= 's';
}
if ($address) {
    $count_sql .= " AND address LIKE ?";
    $count_params[] = "%$address%";
    $count_param_types .= 's';
}

$stmt_count = $conn->prepare($count_sql);
if (!empty($count_params)) {
    $stmt_count->bind_param($count_param_types, ...$count_params);
}
$stmt_count->execute();
$total_rows = $stmt_count->get_result()->fetch_row()[0];
$total_pages = ceil($total_rows / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Danh sách học sinh</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            color: #333;
            display: flex;
            justify-content: center;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            max-width: 1000px;
            margin-top: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #4CAF50;
            margin-bottom: 20px;
        }
        .search-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 20px;
        }
        .search-form input[type="text"],
        .search-form input[type="number"] {
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 150px;
        }
        .search-form button {
            padding: 10px 15px;
            font-size: 14px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-form button:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        table th {
            background-color: #4CAF50;
            color: #fff;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        .pagination a {
            padding: 8px 12px;
            margin: 0 4px;
            text-decoration: none;
            color: #4CAF50;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .pagination a.active {
            background-color: #4CAF50;
            color: #fff;
            border: none;
        }
        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }
        p {
            text-align: center;
            color: #666;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Danh sách học sinh</h1>
    <form class="search-form" method="GET" action="index.php">
        <input type="text" name="name" placeholder="Tên" value="<?php echo htmlspecialchars($name); ?>">
        <input type="number" name="age" placeholder="Tuổi" value="<?php echo htmlspecialchars($age); ?>">
        <input type="text" name="class" placeholder="Lớp" value="<?php echo htmlspecialchars($class); ?>">
        <input type="text" name="address" placeholder="Địa chỉ" value="<?php echo htmlspecialchars($address); ?>">
        <button type="submit">Tìm kiếm</button>
    </form>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Tên</th>
                <th>Tuổi</th>
                <th>Lớp</th>
                <th>Địa chỉ</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['age']); ?></td>
                    <td><?php echo htmlspecialchars($row['class']); ?></td>
                    <td><?php echo htmlspecialchars($row['address']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Không tìm thấy kết quả.</p>
    <?php endif; ?>

    <!-- Phân trang -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&name=<?php echo urlencode($name); ?>&age=<?php echo urlencode($age); ?>&class=<?php echo urlencode($class); ?>&address=<?php echo urlencode($address); ?>"
               <?php if ($i == $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
</div>
</body>
</html>


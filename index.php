<?php
// Gọi file db.php để sử dụng kết nối cơ sở dữ liệu
include 'db.php';

// Lấy các giá trị từ form tìm kiếm
$name = isset($_GET['name']) ? $_GET['name'] : '';
$age = isset($_GET['age']) ? $_GET['age'] : '';
$class = isset($_GET['class']) ? $_GET['class'] : '';
$address = isset($_GET['address']) ? $_GET['address'] : '';

// Khởi tạo câu truy vấn SQL
$sql = "SELECT * FROM students WHERE 1=1";

// Thêm các điều kiện tìm kiếm vào câu truy vấn
if ($name) {
    $sql .= " AND name LIKE '%$name%'";
}
if ($age) {
    $sql .= " AND age = $age";
}
if ($class) {
    $sql .= " AND class LIKE '%$class%'";
}
if ($address) {
    $sql .= " AND address LIKE '%$address%'";
}

// Thực hiện truy vấn
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Danh sách học sinh</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1 {
            color: #4CAF50;
        }
        form {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            padding: 20px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            width: 80%;
            max-width: 600px;
        }
        label, input, button {
            font-size: 14px;
        }
        label {
            flex-basis: 100%;
            margin-top: 10px;
            color: #555;
        }
        input[type="text"], input[type="number"] {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
        }
        button {
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background-color: #45a049;
        }
        table {
            width: 80%;
            max-width: 800px;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Danh sách học sinh</h1>

    <!-- Form tìm kiếm nâng cao -->
    <form method="GET" action="index.php">
        <label for="name">Tên:</label>
        <input type="text" id="name" name="name" placeholder="Tìm theo tên..." value="<?php echo $name; ?>">

        <label for="age">Tuổi:</label>
        <input type="number" id="age" name="age" placeholder="Tìm theo tuổi..." value="<?php echo $age; ?>">

        <label for="class">Lớp:</label>
        <input type="text" id="class" name="class" placeholder="Tìm theo lớp..." value="<?php echo $class; ?>">

        <label for="address">Địa chỉ:</label>
        <input type="text" id="address" name="address" placeholder="Tìm theo địa chỉ..." value="<?php echo $address; ?>">

        <button type="submit">Tìm kiếm</button>
    </form>

    <!-- Hiển thị danh sách học sinh -->
    <table>
        <tr>
            <th>ID</th>
            <th>Tên</th>
            <th>Tuổi</th>
            <th>Lớp</th>
            <th>Địa chỉ</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['age']}</td>
                        <td>{$row['class']}</td>
                        <td>{$row['address']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Không có học sinh nào được tìm thấy</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
// Đóng kết nối
$conn->close();
?>

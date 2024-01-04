<?php
header('Content-Type: application/json');

// 连接到 MySQL 数据库
$conn = new mysqli("localhost", "root", "", "social");

// 检查连接是否成功
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// 处理从前端传递过来的数据
$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'];
$password = $data['password'];

// 执行数据库操作，例如检查用户名和密码是否匹配
// 这里假设有一个名为 "users" 的表，包含 "id", "username", "password" 字段
$result = $conn->query("SELECT * FROM users WHERE username='$username' AND password='$password'");

if ($result->num_rows > 0) {
    // 用户名和密码匹配
    echo json_encode(['success' => true, 'message' => 'Login successful']);
} else {
    // 用户名和密码不匹配
    echo json_encode(['success' => false, 'message' => 'Incorrect username or password']);
}

// 关闭数据库连接
$conn->close();
?>

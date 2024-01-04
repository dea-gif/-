<?php
header('Content-Type: application/json');

// 接收前端发送的JSON数据
$data = json_decode(file_get_contents('php://input'), true);


// 连接到数据库（请替换为您的数据库信息）
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "social";

$conn = new mysqli($servername, $username, $password, $dbname);

// 检查数据库连接是否成功
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// 从数据中获取用户名、邮箱和密码
$username = $data['username'];
$email = $data['email'];
$password = $data['password'];

// 将用户数据插入数据库
$sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";

if ($conn->query($sql) === TRUE) {
    $response = array('success' => true);
} else {
    $response = array('success' => false);
}

$conn->close();

// 将响应以JSON格式返回给前端
echo json_encode($response);
?>

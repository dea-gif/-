<?php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 获取评论相关数据
    $postId = $_POST["postId"];
    $commentText = $_POST["commentText"];

    // 连接到数据库（请根据实际情况修改连接信息）
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "social";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // 检查连接是否成功
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }

    // 将评论插入数据库的适当表格（请根据实际情况修改表格名称）
    $sql = "INSERT INTO comments (postId, text) VALUES (?, ?)";
    
    // 使用预处理语句防止 SQL 注入
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $postId, $commentText);

    if ($stmt->execute()) {
        // 更新帖子的当前评论数量
        updateCommentCount($conn, $postId);

        // 返回成功的 JSON 响应
        echo json_encode(array("success" => true));
    } else {
        // 返回失败的 JSON 响应
        echo json_encode(array("success" => false, "error" => $stmt->error));
    }

    // 关闭数据库连接
    $stmt->close();
    $conn->close();
} else {
    // 如果不是 POST 请求，返回错误的 JSON 响应
    echo json_encode(array("success" => false, "error" => "Invalid request method"));
}

// 更新帖子的当前评论数量
function updateCommentCount($conn, $postId) {
    $sql = "UPDATE posts SET comments = comments + 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $stmt->close();
}
?>

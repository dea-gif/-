<?php
header('Content-Type: application/json');

// 检查是否收到 GET 请求并且带有 postId 参数
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["postId"])) {
    // 获取帖子 ID
    $postId = $_GET["postId"];

    // 连接到数据库，请根据你的实际情况修改连接信息
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "social";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }

    // 查询数据库获取帖子的喜欢数、不喜欢数和评论数
    $sql = "SELECT likes, dislikes, comments FROM posts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $stmt->bind_result($likes, $dislikes, $comments);

    // 检查是否成功获取数据
    if ($stmt->fetch()) {
        // 返回 JSON 格式的数据
        echo json_encode(array(
            "likes" => $likes,
            "dislikes" => $dislikes,
            "comments" => $comments
        ));
    } else {
        // 返回错误响应，指示未找到具有给定 ID 的帖子
        echo json_encode(array("error" => "Post not found"));
    }

    $stmt->close();
    $conn->close();
} else {
    // 返回错误响应，指示未提供所需的 "postId" 参数
    echo json_encode(array("error" => "postId not provided"));
}
?>

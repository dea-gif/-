<?php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 获取分享相关数据
    $postId = $_POST["postId"];

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

    // 查询帖子的当前分享数量
    $getSharesCountSql = "SELECT shares FROM posts WHERE id = ?";
    $getSharesCountStmt = $conn->prepare($getSharesCountSql);
    $getSharesCountStmt->bind_param("i", $postId);
    $getSharesCountStmt->execute();
    $getSharesCountStmt->bind_result($currentSharesCount);
    $getSharesCountStmt->fetch();
    $getSharesCountStmt->close();

    // 新分享数量加一
    $newSharesCount = $currentSharesCount + 1;

    // 更新帖子表中的分享数量
    $updateSharesCountSql = "UPDATE posts SET shares = ? WHERE id = ?";
    $updateSharesCountStmt = $conn->prepare($updateSharesCountSql);
    $updateSharesCountStmt->bind_param("ii", $newSharesCount, $postId);
    $updateSharesCountStmt->execute();
    $updateSharesCountStmt->close();

    // 返回成功的 JSON 响应
    echo json_encode(array("success" => true));
    
    // 关闭数据库连接
    $conn->close();
} else {
    // 如果不是 POST 请求，返回错误的 JSON 响应
    echo json_encode(array("success" => false, "error" => "Invalid request method"));
}
?>

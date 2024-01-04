<?php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $postId = $_POST["postId"];

    // 连接到数据库，请根据你的实际情况修改连接信息
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "social";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }

    // 更新数据库中的不喜欢数量
    $sql = "UPDATE posts SET dislikes = dislikes + 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $postId);

    if ($stmt->execute()) {
        // 获取更新后的不喜欢数量
        $updatedDislikes = getDislikes($conn, $postId);

        // 返回成功的 JSON 响应
        echo json_encode(array("success" => true, "dislikes" => $updatedDislikes));
    } else {
        // 返回失败的 JSON 响应
        echo json_encode(array("success" => false, "error" => $stmt->error));
    }

    $stmt->close();
    $conn->close();
}

function getDislikes($conn, $postId) {
    $sql = "SELECT dislikes FROM posts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $stmt->bind_result($dislikes);
    $stmt->fetch();
    $stmt->close();

    return $dislikes;
}
?>

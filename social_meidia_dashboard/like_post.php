<?php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 检查 "postId" 是否存在
    if (isset($_POST["postId"])) {
        // 获取 "postId"
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

        // 更新数据库中的点赞数量
        $sql = "UPDATE posts SET likes = likes + 1 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $postId);

        if ($stmt->execute()) {
            // 获取更新后的点赞数量
            $updatedLikes = getLikes($conn, $postId);

            // 返回成功的 JSON 响应
            echo json_encode(array("success" => true, "likes" => $updatedLikes));
        } else {
            // 返回失败的 JSON 响应
            echo json_encode(array("success" => false, "error" => "Failed to update likes"));
        }

        $stmt->close();
        $conn->close();
    } else {
        // 返回错误响应，指示未提供所需的 "postId" 键
        echo json_encode(array("success" => false, "error" => "postId not provided"));
    }
}

function getLikes($conn, $postId) {
    $sql = "SELECT likes FROM posts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $stmt->bind_result($likes);
    $stmt->fetch();
    $stmt->close();

    return $likes;
}
?>

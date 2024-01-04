<?php
header('Content-Type: application/json');

// 检查是否收到 POST 请求
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 获取表单数据
    $postTitle = $_POST["postTitle"];
    $postContent = $_POST["postContent"];

    // 连接到 MySQL 数据库（请根据实际情况修改连接信息）
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "social";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // 检查连接是否成功
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }

    // 将数据插入数据库的适当表格（请根据实际情况修改表格名称）
    $sql = "INSERT INTO posts (postTitle, postContent) VALUES (?, ?)";
    
    // 使用预处理语句防止 SQL 注入
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $postTitle, $postContent);

    if ($stmt->execute()) {
        // 获取刚插入数据的 ID
        $postId = $conn->insert_id;
    
        // 返回成功的 JSON 响应，包含新帖子的 ID
        echo json_encode(array("success" => true, "postId" => $postId));
    } else {
        // 返回失败的 JSON 响应
        echo json_encode(array("success" => false, "error" => $stmt->error));
    }
    
    // 关闭数据库连接
    $stmt->close();
    $conn->close();
} else {
    // 如果是 GET 请求，获取所有帖子和评论
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "social";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // 检查连接是否成功
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }

    // 获取所有帖子
    $sql = "SELECT * FROM posts";
    $result = $conn->query($sql);

    $posts = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // 获取每个帖子的评论
            $postId = $row["id"];
            $comments = array();

            $commentSql = "SELECT * FROM comments WHERE postId = $postId";
            $commentResult = $conn->query($commentSql);

            if ($commentResult->num_rows > 0) {
                while ($commentRow = $commentResult->fetch_assoc()) {
                    $comments[] = array(
                        "id" => $commentRow["id"],
                        "text" => $commentRow["text"]
                    );
                }
            }

            $posts[] = array(
                "id" => $row["id"],
                "postTitle" => $row["postTitle"],
                "postContent" => $row["postContent"],
                "likes" => $row["likes"],
                "dislikes" => $row["dislikes"],
                "comments" => $comments,
                "shares" => $row["shares"]
            );
        }
    }

    // 返回 JSON 响应，包含所有帖子和评论
    echo json_encode(array("posts" => $posts));

    // 关闭数据库连接
    $conn->close();
}
?>

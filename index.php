<?php
// 设置SQLite数据库文件路径
$databaseFile = 'data.db';

// 初始化数据库表
function initializeDatabase($databaseFile) {
    $db = new SQLite3($databaseFile);
    $db->exec('CREATE TABLE IF NOT EXISTS practices (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT,
        content TEXT,
        name TEXT,
        student_number TEXT,
        image_path TEXT,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
    )');
    $db->close();
}

// 检查并初始化数据库
if (!file_exists($databaseFile)) {
    initializeDatabase($databaseFile);
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 处理上传的图片
    $uploadedImages = [];
    $imageUploadPath = 'uploads/';

    if (!file_exists($imageUploadPath)) {
        mkdir($imageUploadPath, 0777, true);
    }

    for ($i = 1; $i <= 9; $i++) {
        $fieldName = 'image' . $i;

        if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === 0) {
            $uploadFileName = date('Y-m-d') . '-' . $i . '.jpg'; // 修改文件名为“年-月-日-序号”
            $destination = $imageUploadPath . $uploadFileName;

            // 检查文件格式为常见图片格式
            $allowedFormats = ['image/jpeg', 'image/png', 'image/gif'];

            if (in_array($_FILES[$fieldName]['type'], $allowedFormats)) {
                move_uploaded_file($_FILES[$fieldName]['tmp_name'], $destination);
                $uploadedImages[] = $destination;
            }
        }
    }

    // 存储表单数据到SQLite数据库
    $db = new SQLite3($databaseFile);
    $title = $_POST['title'];
    $content = $_POST['content'];
    $name = $_POST['name'];
    $studentNumber = $_POST['student_number'];

    $db->exec("INSERT INTO practices (title, content, name, student_number, image_path) VALUES ('$title', '$content', '$name', '$studentNumber', '" . implode(',', $uploadedImages) . "')");

    $db->close();
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>综合实践信息填写</title>
</head>
<body>
    <h2>填写综合实践信息</h2>
    <form method="post" enctype="multipart/form-data">
        <label for="title">标题：</label>
        <input type="text" name="title" required><br>

        <label for="content">内容：</label>
        <textarea name="content"></textarea><br>

        <label for="name">姓名：</label>
        <input type="text" name="name" required><br>

        <label for="student_number">学号：</label>
        <input type="text" name="student_number" required><br>

        <label for="image1">上传图片1：</label>
        <input type="file" name="image1" accept="image/jpeg, image/png, image/gif" required><br>

        <label for="image2">上传图片2：</label>
        <input type="file" name="image2" accept="image/jpeg, image/png, image/gif" required><br>

        <label for="image3">上传图片3：</label>
        <input type="file" name="image3" accept="image/jpeg, image/png, image/gif" required><br>

        <label for="image4">上传图片4：</label>
        <input type="file" name="image4" accept="image/jpeg, image/png, image/gif" required><br>

        <input type="submit" value="提交">
    </form>
</body>
</html>
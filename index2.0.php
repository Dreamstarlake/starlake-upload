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
    // 检查姓名字段是否填写
    if (empty($_POST['name'])) {
        echo '<script>alert("姓名字段未填写！");</script>';
    } else {
        // 存储表单数据到SQLite数据库
        $db = new SQLite3($databaseFile);
        $title = $_POST['title'];
        $content = $_POST['content'];
        $name = $_POST['name'];
        $studentNumber = $_POST['student_number'];
        
        // 处理上传的图片
        $uploadedImages = [];
        for ($i = 1; $i <= 4; $i++) {
            $imageName = "photo{$i}";
            if (isset($_FILES[$imageName]) && $_FILES[$imageName]['error'] === 0) {
                $uploadedImages[] = $_FILES[$imageName]['tmp_name'];
            }
        }

        // 确保至少上传了一张图片
        if (count($uploadedImages) < 1) {
            echo '<script>alert("至少需要上传一张图片！");</script>';
        } else {
            // 存储图片路径
            $imagePaths = [];
            foreach ($uploadedImages as $image) {
                $imageName = basename($image);
                $imagePath = "uploads/{$imageName}";
                move_uploaded_file($image, $imagePath);
                $imagePaths[] = $imagePath;
            }

            // 插入数据到数据库
            $stmt = $db->prepare('INSERT INTO practices (title, content, name, student_number, image_path) VALUES (?, ?, ?, ?, ?)');
            $stmt->bindValue(1, $title);
            $stmt->bindValue(2, $content);
            $stmt->bindValue(3, $name);
            $stmt->bindValue(4, $studentNumber);
            $stmt->bindValue(5, implode(',', $imagePaths));
            $stmt->execute();

            echo '<script>alert("提交成功！");</script>';
        }

        $db->close();
    }
}
?>

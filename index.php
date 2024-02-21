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
        $imageUploadPath = 'uploads/';

        if (!file_exists($imageUploadPath)) {
            mkdir($imageUploadPath, 0777, true);
        }

        for ($i = 1; $i <= 9; $i++) {
            $fieldName = 'image' . $i;

            if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === 0) {
                $uploadFileName = date('Y-m-d') . '-' . $i . '.jpg';
                $destination = $imageUploadPath . $_POST['name'] . '/' . $uploadFileName;

                $allowedFormats = ['image/jpeg', 'image/png', 'image/gif'];

                if (in_array($_FILES[$fieldName]['type'], $allowedFormats)) {
                    if (!file_exists($imageUploadPath . $_POST['name'])) {
                        mkdir($imageUploadPath . $_POST['name'], 0777, true);
                    }

                    move_uploaded_file($_FILES[$fieldName]['tmp_name'], $destination);
                    $uploadedImages[] = $destination;
                }
            }
        }

        $db->exec("INSERT INTO practices (title, content, name, student_number, image_path) VALUES ('$title', '$content', '$name', '$studentNumber', '" . implode(',', $uploadedImages) . "')");

        $db->close();

        // 提示提交成功
        echo '<script>alert("提交成功！");</script>';
    }

    // 阻止页面刷新
    die();
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>综合实践信息填写</title>
    <style>
        .image-upload-container {
            display: flex;
            flex-wrap: wrap;
        }

        .image-upload-container img {
            max-width: 100px;
            max-height: 100px;
            margin: 5px;
        }
    </style>
</head>
<body>
    <h2>填写综合实践信息</h2>
    <form method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
        <label for="title">标题：</label>
        <input type="text" name="title"><br>

        <label for="content">内容：</label>
        <textarea name="content"></textarea><br>

        <label for="name">姓名：</label>
        <input type="text" name="name" required><br>

        <label for="student_number">学号：</label>
        <input type="text" name="student_number"><br>

        <div class="image-upload-container">
            <?php
            for ($i = 1; $i <= 4; $i++) {
                echo '<label for="image' . $i . '">上传图片' . $i . '：</label>';
                echo '<input type="file" name="image' . $i . '" accept="image/jpeg, image/png, image/gif">';
            }
            ?>
        </div>

        <input type="hidden" name="current_uploads" value="4">

        <button type="button" onclick="addMoreImages()">添加更多图片...</button>

        <br>
        <input type="submit" value="提交">
    </form>

    <script>
        function addMoreImages() {
            var currentUploads = document.querySelector('[name="current_uploads"]');
            currentUploads.value = parseInt(currentUploads.value) + 1;

            var container = document.querySelector('.image-upload-container');
            var newLabel = document.createElement('label');
            newLabel.setAttribute('for', 'image' + currentUploads.value);
            newLabel.innerText = '上传图片' + currentUploads.value + '：';

            var newInput = document.createElement('input');
            newInput.setAttribute('type', 'file');
            newInput.setAttribute('name', 'image' + currentUploads.value);
            newInput.setAttribute('accept', 'image/jpeg, image/png, image/gif');

            container.appendChild(newLabel);
            container.appendChild(newInput);
        }

        function validateForm() {
            // 自定义验证逻辑
            var nameField = document.querySelector('[name="name"]');
            if (nameField.value.trim() === '') {
                alert('姓名字段未填写！');
                return false;
            }

            // 添加其他字段的验证逻辑...

            return true;  // 如果所有验证通过，返回true允许提交
        }
    </script>
</body>
</html>

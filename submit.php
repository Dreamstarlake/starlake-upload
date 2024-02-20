<?php
// submit.php
session_start();

// 检查是否已上传文件
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 获取表单数据
    $title = $_POST['title'];
    $content = $_POST['content'];
    $name = $_POST['name'];
    $student_id = $_POST['student_id'];
    
    // 获取上传的文件
    $files = $_FILES['file'];
    
    // 检查文件数量
    if (count($files['name']) < 4) {
        die('请上传至少4个图片文件。');
    }
    
    // 处理图片上传
    $upload_dir = 'uploads/';
    $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
    
    foreach ($files['name'] as $key => $file_name) {
        // 检查文件扩展名
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        if (!in_array($file_ext, $allowed_extensions)) {
            die('不支持的文件格式。');
        }
        
        // 生成新的文件名
        $new_file_name = uniqid() . '.' . $file_ext;
        
        // 移动文件到上传目录
        if (move_uploaded_file($files['tmp_name'][$key], $upload_dir . $new_file_name)) {
            // 文件上传成功
        } else {
            die('文件上传失败。');
        }
    }
    
    // 这里可以添加数据库操作，将数据保存到数据库
    // ...

    // 重定向到成功页面或显示成功消息
    header('Location: success.php');
    exit;
}
?>
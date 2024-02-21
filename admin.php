<?php
// 设置SQLite数据库文件路径和上传文件夹路径
$databaseFile = 'data.db';
$uploadsFolder = 'uploads/';

// 处理下载全部图片
if (isset($_POST['download_images'])) {
    createAndDownloadZip($uploadsFolder);
}

// 处理生成和下载CSV文件
if (isset($_POST['download_csv'])) {
    generateAndDownloadCSV($databaseFile);
}

// 处理在线预览data.db信息
if (isset($_POST['preview_db'])) {
    previewDatabase($databaseFile);
}

// 处理反查数据库信息
if (isset($_POST['search_db'])) {
    $searchField = $_POST['search_field'];
    $searchValue = $_POST['search_value'];
    searchDatabase($databaseFile, $searchField, $searchValue);
}

// 创建并下载压缩文件
// 使用ZipArchive扩展，确保已安装
function createAndDownloadZip($folderPath) {
    $zip = new ZipArchive();
    $zipFileName = 'images.zip';

    if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folderPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($folderPath) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();

        // 提示下载
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=' . $zipFileName);
        header('Content-Length: ' . filesize($zipFileName));
        readfile($zipFileName);

        // 删除临时文件
        unlink($zipFileName);
    }
}


// 生成并下载CSV文件
// 使用 fputcsv 函数
function generateAndDownloadCSV($databaseFile) {
    $db = new SQLite3($databaseFile);
    $result = $db->query('SELECT * FROM practices');

    $csvFileName = 'data.csv';
    $csvFile = fopen($csvFileName, 'w');

    // 添加CSV头
    $header = ['ID', '标题', '内容', '姓名', '学号', '图片路径', '时间戳'];
    fputcsv($csvFile, $header);

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        fputcsv($csvFile, $row);
    }

    fclose($csvFile);
    $db->close();

    // 提示下载
    header('Content-Type: text/csv');
    header('Content-disposition: attachment; filename=' . $csvFileName);
    header('Content-Length: ' . filesize($csvFileName));
    readfile($csvFileName);

    // 删除临时文件
    unlink($csvFileName);
}


// 在线预览data.db信息
function previewDatabase($databaseFile) {
    $db = new SQLite3($databaseFile);
    $result = $db->query('SELECT * FROM practices');

    echo '<table border="1"><tr>';
    for ($i = 0; $i < $result->numColumns(); $i++) {
        echo '<th>' . $result->columnName($i) . '</th>';
    }
    echo '</tr>';

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        echo '<tr>';
        foreach ($row as $value) {
            echo '<td>' . $value . '</td>';
        }
        echo '</tr>';
    }

    echo '</table>';

    $db->close();
}


// 反查数据库信息
function searchDatabase($databaseFile, $field, $value) {
    $db = new SQLite3($databaseFile);
    $query = "SELECT * FROM practices WHERE $field = :value";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':value', $value, SQLITE3_TEXT);
    $result = $stmt->execute();

    echo '<table border="1"><tr>';
    for ($i = 0; $i < $result->numColumns(); $i++) {
        echo '<th>' . $result->columnName($i) . '</th>';
    }
    echo '</tr>';

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        echo '<tr>';
        foreach ($row as $value) {
            echo '<td>' . $value . '</td>';
        }
        echo '</tr>';
    }

    echo '</table>';

    $db->close();
}

?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>管理员页面</title>
</head>
<body>
    <h2>管理员页面</h2>

    <form method="post" action="">
        <button type="submit" name="download_images">下载全部图片</button>
        <button type="submit" name="download_csv">下载CSV文件</button>
        <button type="submit" name="preview_db">在线预览data.db信息</button>

        <label for="search_field">反查数据库字段：</label>
        <select name="search_field">
            <option value="title">标题</option>
            <option value="content">内容</option>
            <option value="name">姓名</option>
            <option value="student_number">学号</option>
            <option value="image_path">图片路径</option>
        </select>
        <input type="text" name="search_value" placeholder="搜索值">
        <button type="submit" name="search_db">搜索</button>
    </form>
</body>
</html>

### 综合实践图片上传系统

###环境要求
1.对目录有写入权限
2.确保你的PHP环境已经安装了以下扩展：

	•	SQLite3：处理SQLite数据库
	•	ZipArchive：创建和处理Zip文件
	•	如果使用图形库预览图片，则可能需要GD或Imagick扩展

3.PHP 5+ 环境（可以使用宝塔面板，1panel面板或自行搭建Nginx，Apache或Openresty等环境）

###使用方法
直接访问站点即可，会随着第一次提交，自动初始化数据库和目录。

###关于后台
访问您的站点，地址如下
` https://example.com/admin.php `
可查看各类数据执行各类操作
也可以搭配sqlite-web等项目实现数据库管理（https://github.com/coleifer/sqlite-web）
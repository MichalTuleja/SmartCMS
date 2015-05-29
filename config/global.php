; <?php die() ?> this is a common INI configuration file
[database]
host = localhost
port = 3306
dbname = mycms
charset = utf8
user = root
pass = 
;dbfile = data.sqlite3

[tables]
db_news = test_news
db_articles = test_articles
db_pictures = test_pictures
db_albums = test_picture_categories
db_logs = test_logs
db_users = test_users
db_sessions = test_sessions

[general]
title = SmartCMS
debug = 0
filesystem_encoding = utf8

[directories]
temp_dir = ../tmp/cache
upload_dir = ../tmp/upload
img_dir = media

[time_handling]
date = d.m.Y
time = H:i

[pictures]
thumb_res = 200
medium_res = 720
high_res = 1200
jpeg_quality = 95

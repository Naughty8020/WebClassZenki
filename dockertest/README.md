# 画像投稿できる掲示板

##  使い方

### dockerの起動
```bash
docker compose up
```

### mysqlに入る
``` bash
docker compose exec docker compose exec mysql mysql YOUR_DB_NAME
```

### テーブルの作成
```sql
CREATE TABLE `bbs_entries` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `body` TEXT NOT NULL,
    `image_filename` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### URL
```bash
http://54.80.10.178/bbsimagetest
```



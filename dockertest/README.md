# 画像投稿できる掲示板

##  使い方

### Clone 
``` bash
git clone git@github.com:Naughty8020/WebClassZenki.git
```

### dockerのインストール
``` bash
sudo yum install -y docker
sudo systemctl start docker
sudo systemctl enable docker
```

### デフォルトのユーザー（ec2-user）でもsudoつけずにdockerコマンドを立たけるように、dockerグループに追加
``` bash
sudo usermod -a -G docker ec2-user
```

### Docker Compose インストール
``` bash
DOCKER_CONFIG=${DOCKER_CONFIG:-$HOME/.docker}
mkdir -p $DOCKER_CONFIG/cli-plugins
curl -SL https://github.com/docker/compose/releases/download/v5.1.2/docker-compose-linux-x86_64 -o $DOCKER_CONFIG/cli-plugins/docker-compose
chmod +x $DOCKER_CONFIG/cli-plugins/docker-compose
```

### インストールできたかどうかの確認
``` bash
docker compose version
```

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



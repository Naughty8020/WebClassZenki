<?php
$dbh = new PDO('mysql:host=mysql;dbname=example_db', 'root', '');

if (isset($_POST['body'])) {
  // POSTで送られてくるフォームパラメータ body がある場合

  $image_filename = null;
  if (isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {
    // アップロードされた画像がある場合
    if (preg_match('/^image\//', mime_content_type($_FILES['image']['tmp_name'])) !== 1) {
      header("HTTP/1.1 302 Found");
      // アップロードされたものが画像ではなかった場合処理を強制的に終了
      header("Location: ./bbsimagetest.php");
      return;
    }

    // 元のファイル名から拡張子を取得
    $pathinfo = pathinfo($_FILES['image']['name']);
    $extension = $pathinfo['extension'];
    // 新しいファイル名を決める。他の投稿の画像ファイルと重複しないように時間+乱数で決める。
    $image_filename = strval(time()) . bin2hex(random_bytes(25)) . '.' . $extension;
    $filepath =  '/var/www/upload/image/' . $image_filename;
    move_uploaded_file($_FILES['image']['tmp_name'], $filepath);
  }

  // insertする
  $insert_sth = $dbh->prepare("INSERT INTO bbs_entries (body, image_filename) VALUES (:body, :image_filename)");
  $insert_sth->execute([
    ':body' => $_POST['body'],
    ':image_filename' => $image_filename,
  ]);

  // 処理が終わったらリダイレクトする
  // リダイレクトしないと，リロード時にまた同じ内容でPOSTすることになる
  header("HTTP/1.1 302 Found");
  header("Location: ./bbsimagetest.php");
  return;
}

// いままで保存してきたものを取得
$select_sth = $dbh->prepare('SELECT * FROM bbs_entries ORDER BY created_at DESC');
$select_sth->execute();
?>

<head>
<meta name="viewport" content="width=device-width">
  <title>画像更新できる掲示板</title>
</head>
<script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.js"></script>
<div style="padding: 10px; max-width:1000px; margin: 0 auto;">

<form method="POST" action="./bbsimagetest.php" enctype="multipart/form-data" style="max-width: 600px; margin: 0 auto; padding: 10px;">
  <textarea name="body" required style="width: 100%; box-sizing: border-box; height: 100px;"> </textarea>
  <div style="margin: 1em 0;">
    <input type="file" accept="image/*" name="image" id="imageInput" style="max-width: 100%;">
  </div>
  <button type="submit" style="padding: 10px 20px; font-size:16px;">送信</button>
</form>

<hr>

<?php foreach($select_sth as $entry): ?>

<div style="display: flex; flex-wrap: wrap; gap: 15px; border-bottom: 1px solid ; align-items: center;">

<div style="flex: 1; min-width: 150px; ">
  <dl class="post-container" style="margin-bottom: 1em; padding-bottom: 1em; font-size: 16px;">
    <dt>ID</dt>
    <dd><?= $entry['id'] ?></dd>
    <dt>日時</dt>
    <dd><?= $entry['created_at'] ?></dd>
    <dt>内容</dt>
    <dd style="margin: 0;">
      <?= nl2br(htmlspecialchars($entry['body'])) // 必ず htmlspecialchars() すること ?>
    </div>
      <?php if(!empty($entry['image_filename'])): // 画像がある場合は img 要素を使って表示 ?>
      <div style="flex-shrink: 0;">
        <img class="post-image" src="/image/<?= $entry['image_filename'] ?>" style="height: auto;">
      </div>
      <?php endif; ?>
    </dd>
  </dl>
</div>
<?php endforeach ?>
</div>

<script>
const option = {
maxSizeMB:5
}

document.addEventListener("DOMContentLoaded", () => {
  const imageInput = document.getElementById("imageInput");
  imageInput.addEventListener("change",async () => {
    if (imageInput.files.length < 1) {
      return;
    }
    if (imageInput.files[0].size > 5 * 1024 * 1024) {
      console.log("aa")
      const file = imageInput.files[0]
      const compressedFile = await imageCompression(file,option)

      const dataTransfer = new DataTransfer();
      dataTransfer.items.add(new File([compressedFile],file.name,{type:compressedFile.type}))
      imageInput.files = dataTransfer.files;
      console.log(file.size,compressedFile.size)

    }
  });
});
</script>

<style>

.post-container{
font-size: 16px;
}
.post-image{
max-width: 250px;
}

@media(max-width:600px){
.post-container{
font-size:13px;
}
.post-image{
max-width:160px;
}
}
</style>

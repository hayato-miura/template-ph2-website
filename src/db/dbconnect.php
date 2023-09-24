<?php

/* ドライバ呼び出しを使用して MySQL データベースに接続する */
$dsn = 'mysql:host=db;dbname=posse';
$username = 'root';
$password = 'root';

try {
    $dbh = new PDO($dsn, $username, $password);
        // エラーモードの設定
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 以降はPDOを使ってデータベースに対してクエリや操作を行うことができる

    $questions = $dbh->query("SELECT * FROM questions")->fetchAll(PDO::FETCH_ASSOC);
    $choices = $dbh->query("SELECT * FROM choices")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($choices as $key => $choice) {
        $index = array_search($choice["question_id"], array_column($questions, 'id'));
        $questions[$index]["choices"][] = $choice;
// var_dump($questions);
};
} catch (PDOException $e) {
    // エラーハンドリング
}
?>
<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<pre>  
<?
// var_dump($questions);
?>
</pre>
</body>
</html> -->
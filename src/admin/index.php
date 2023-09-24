<!-- 疑問点
・sample-ph2-websiteのフォルダに作っていく感じでやっていけばいいのか？それともtemplate-ph2-websiteのほうに作ってやっていけばいいのか？ -->
<?php
session_start();


// ログインしていない場合ログイン画面に遷移させるようにした。
// if (!isset($_SESSION['id'])) {
//   header('Location: /admin/auth/signin.php');
// } else {
//   if (isset($_SESSION['message'])) {
//     $message = $_SESSION['message'];
//     unset($_SESSION['message']);
//   }

  $pdo = new PDO('mysql:host=db;dbname=posse', 'root', 'root');
  $questions = $pdo->query("SELECT * FROM questions")->fetchAll(PDO::FETCH_ASSOC);
  //   問題数を数えつつ初期値を0に設定している
  $is_empty = count($questions) === 0;
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // トランザクションの開始
    // トランザクション（Transaction）は、データベース操作のまとまりを指します。トランザクションは、一連のデータベースクエリや操作を1つのまとまった処理として扱うために使用されます。
    $pdo->beginTransaction();
    try {
        // 問題に関連する選択肢を削除する
      $sql = "DELETE FROM choices WHERE question_id = :question_id";
    //   prepareはqueryと似ているがprepareは変動値がある場合に使用されることが多く、この場合は$sql自体がテーブルの中身を削除するという変動を行うためprepare関数を用いている
      $stmt = $pdo->prepare($sql);
    //   第一引数で指定したsqlテーブルの:question_idプレースホルダに$_POST["id"]を代入しています。
      $stmt->bindValue(":question_id", $_POST["id"]);
    //   execute関数はPDOオブジェクトを作成してDBへの接続、プリペアドステートメントのSQLをprepare関数を使って設定しておきます。設定できた上でexecute関数を使うことでSQL文を実行する関数
      $stmt->execute();

      // 問題を削除する
      $sql = "DELETE FROM questions WHERE id = :id";
      $stmt = $pdo->prepare($sql);
      $stmt->bindValue(":id", $_POST["id"]);
      $stmt->execute();
      $pdo->commit();
       // トランザクションのコミット
      $message = "問題削除に成功しました";
    } catch(Error $e) {
      $pdo->rollBack();
      $message = "問題削除に失敗しました";
    }
  }

?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>POSSE 管理画面ダッシュボード</title>
  <!-- スタイルシート読み込み -->
  <link rel="stylesheet" href="./assets/styles/common.css">
  <link rel="stylesheet" href="./admin.css">
  <!-- Google Fonts読み込み -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&family=Plus+Jakarta+Sans:wght@400;700&display=swap"
    rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <script src="../assets/scripts/common.js" defer></script>
</head>

<body>
  <?php 
  include(dirname(__FILE__) . '/../components/admin/header.php'); 
  ?>
  <div class="wrapper">
    <?php 
    include(dirname(__FILE__) . '/../components/admin/sidebar.php');
     ?>
    <main>
      <div class="container">
        <h1 class="mb-4">問題一覧</h1>
        <?php if(isset($_SESSION['message'])) { ?>
          <p><?= $_SESSION['message'] ?></p>
        <?php } ?>
        <?php if(isset($message)) { ?>
          <p><?= $message ?></p>
        <?php } ?>
        <?php if(!$is_empty) { ?>
        <table class="table">
          <thead>
            <tr>
                <th>ID</th>
                <th>問題</th>
                <th></th>
            </tr>
         </thead>
         <tbody>
            <?php foreach($questions as $question) { ?>
            <tr id="question-<?= $question["id"] ?>">
                <td><?= $question["id"]; ?></td>
                <td>
                  <a href="./questions/edit.php?id=<?= $question["id"] ?>">
                    <?= $question["content"]; ?>
                  </a>
                </td>
                <td>
                  <form method="POST">
                    <input type="hidden" value="<?= $question["id"] ?>" name="id">
                    <input type="submit" value="削除" class="submit">
                  </form>
                </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
        <?php } else {?>
          問題がありません。
        <?php } ?>
      </div>
    </main>
  </div>
</body>
</html>
















<?php

//ここにデータベースへの接続、検索などの処理をここに記述
// include('./dbconnect.php');
?>





<!-- <!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POSSE 初めてのWeb制作</title>
    <link rel="stylesheet" href="../css/reset.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;700&display=swap" rel="stylesheet">
    <script src="../js/quiz2.js" defer></script>
</head>
<body>
    <header class="l-header p-header">
        <div class="p-header_logo">
            <img src="../assets/img/logo.svg" alt="POSSE">
        </div>
        <div class="openbtn quiz-openbtn" id="openbtn"><span></span><span></span></div>
                <nav id="g-nav">
            <div id="g-nav-list">
            <ul class=>
            <li><a href="./index.html">POSSEとは</a></li> 
            <li><a href="./quiz2.html">クイズ</a></li>  
            </ul>
            <div class="openbtn__footer">
                <div class="openbtn__line">
                    <img src="../assets/img/icon/icon-line.svg" alt="" class="fixed__line_icon">
                    <p>POSSE公式LINE追加</p>
                </div>
                <div>
                    <ul class="openbtn__footer_sns">
                    <li class="footer_sns_twitter openbtn__footer__icon">
                        <a href="http://twitter.com/posse_program"><img src="../assets/img/icon/icon-twitter.svg"  target="_blank" alt="twitter"></a>
                    </li>
                    <li class="footer_sns_instagram openbtn__footer__icon">
                        <a href="https://www.instagram.com/posse_programming/"><img src="../assets/img/icon/icon-instagram.svg" target="_blank" alt="instagram"></a>
                    </li>
                    </ul>
                </div>
            </div>
            </div>

        </nav>
        <div class="p-header_inner">
            <nav class="p-header_nav">
                <ul class="p-header_nav_list">
                <li class="p-header_nav_item">
                    <a href="./index.html">POSSEとは</a>
                </li>
                <li class="p-header_nav_item">
                    <a href="./quiz2.html">クイズ</a>
                </li>
                <li class="p-header_nav_item header_icon">
                    <a href="http://twitter.com/posse_program" >
                        <img src="../assets/img/icon/icon-twitter.svg" alt="ツイッターのアイコン">
                    </a>
                </li>
                <li class="p-header_nav_item header_icon">
                    <a href="https://www.instagram.com/posse_programming/">
                        <img src="../assets/img/icon/icon-instagram.svg" alt="インスタのアイコン">
                    </a>
                </li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="l-main quiz-l-main">
        <section class="p-hero p-quiz-hero">
            <div class="l-container">
                <h1 class="p-hero__title__label">POSSE</h1>
                <span class="p-hero__title__inline">ITクイズ</span>
            </div>
        </section>

        <div class="p-quiz-container l-container" id="js-quizContainer">
        <?php for ($i = 0; $i < count($questions); $i++) { ?>
    <h2 class="p-quiz-box__question__title">
    <span class="p-quiz-box__label">Q<?= $i + 1 ?></span>
    <span class="p-quiz-box__question__title__text"><?= $questions[$i]["content"]; ?></span>
    </h2>
<?php } ?>
        </div>
    <div class="p-line">
        <div class="l-container">
            <div class="p-line__body">
                <div class="p-line__body__inner">
                    <h2 class="p-heading -light p-line__title">
                        <img src="../assets/img/icon/used-line.png" alt="" class="p-line__title__img">
                    <i class="u-icon__line"></i>POSSE 公式LINE
                    </h2>
                <div class="p-line__content">
                <p>
                    公式LINEにてご質問を随時受け付けております。<br />詳細やPOSSE最新情報につきましては、公式LINEにてお知らせ致しますので<br />下記ボタンより友達追加をお願いします！
                </p>
                </div>
                    <div class="p-line__footer">
                        <div class="p-line__button__box">
                            <a  href="https://line.me/R/ti/p/@651htnqp?from=page" target="_blank" class="p-line__button">LINE追加</a>
                            <img src="../assets/img/icon/icon-link-dark.svg" alt="" class="arrow">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<footer class="footer quiz-footer">
    <div class="p-fixedLine">
        <a href="https://line.me/R/ti/p/@651htnqp?from=page" target="_blank" class="p-fixedLine__link">
            <i class="u-icon__line"></i>
                        <img src="../assets/img/icon/icon-line.svg" alt="" class="fixed__line_icon">
                        <p class="p-fixedLine__link__text quiz-p-fixedLine__link__text">
                        <span class="u-sp-hidden">POSSE</span>公式LINEで<br />最新情報をGET！
                        </p>
                        <img src="../assets/img/icon/icon-link-light.svg" alt="" class="p-fixedLine__arrow">
            <i class="u-icon__link"></i>
        </a>
    </div>
        
    <div class="footer_logo">
        <span>
            <img src="../assets/img/logo.svg" alt="POSSEのロゴ">
        </span>
    </div>
    <div class="p-footer__siteinfo__link">
        <a href="https://posse-ap.com/" >POSSE公式サイト</a>
    </div>
    <div>
        <ul class="footer_sns">
        <li class="footer_sns_twitter footer__icon">
            <a href="http://twitter.com/posse_program"><img src="../assets/img/icon/icon-twitter.svg"  target="_blank" alt="twitter"></a>
        </li>
        <li class="footer_sns_instagram footer__icon">
            <a href="https://www.instagram.com/posse_programming/"><img src="../assets/img/icon/icon-instagram.svg" target="_blank" alt="instagram"></a>
        </li>
        </ul>
    </div>
    <div class="p-footer__copyright">
        <small lang="en">©︎2022 POSSE</small>
    </div>
</footer>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="../js/openbtn.js"></script>
</body>
</html>  -->
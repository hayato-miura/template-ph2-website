<?php
// この行は、HTTPリクエストのメソッドがPOSTかどうかをチェックしています。POSTメソッドでリクエストが送信された場合に、以下のコードブロックが実行されます。
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 次の行では、一意のファイル名を生成しています。uniqid() 関数は一意の文字列を生成し、mt_rand() は乱数を生成します。これにより、重複のない一意のファイル名が作成されます。その後、$_FILES['image']['name'] から送信された画像ファイル名の拡張子を取得し、新しいファイル名を生成しています。substr関数はファイル名の切り取りを行っており、ファイル名の最後が「gif」「jpg」「png」もののみの受付するサイトの仕様にするために使われている。substr(ファイル名, 場所指定)という構造をとる。今回はstrrchr($_FILES['image']['name'], '.')の前から１文字目以降を切り取るという意味である。次にstrrchr()関数についてである。これは文字列内で最後に出現する文字列の位置を検索し末尾までの全ての文字を取得するスクリプトを書いていきます。
    $image_name = uniqid(mt_rand(), true) . '.' . substr(strrchr($_FILES['image']['name'], '.'), 1);
//   この行では、保存する画像ファイルのパスを生成しています。__FILE__ は現在のスクリプトのファイルパスを表し、dirname(__FILE__) でそのファイルのディレクトリパスを取得します。そして、/../../assets/img/quiz/ を結合して、画像ファイルの保存先のパスを構築しています。
    $image_path = dirname(__FILE__) . '/../../assets/img/quiz/' . $image_name;
//   最後の部分で、move_uploaded_file() 関数を使用してアップロードされた一時ファイルを目的の場所に移動させます。$_FILES['image']['tmp_name'] は一時的にアップロードされたファイルのパスを表しています。$image_path に指定されたファイルパスにファイルが移動されます。こうすることで、アップロードされたファイルがサーバー上で永続的に保存されることになります。
// move_uploaded_file(①仮保存されているファイル,②作成するファイル)という構造をとります
    move_uploaded_file(
    $_FILES['image']['tmp_name'],
    $image_path
    );
    
    $pdo = new PDO('mysql:host=db;dbname=posse', 'root', 'root');
    $stmt = $pdo->prepare("INSERT INTO questions(content, image, supplement) VALUES(:content, :image, :supplement)");
    $stmt->execute([
    "content" => $_POST["content"],
    "image" => $image_name,
    "supplement" => $_POST["supplement"]
    ]);
    // PDOには元々lastInsertIdメソッドが用意されており、データ登録後にこのメソッドを呼び出すことでIDを取得することができます。
    $lastInsertId = $pdo->lastInsertId();
    
    $stmt = $pdo->prepare("INSERT INTO choices(name, valid, question_id) VALUES(:name, :valid, :question_id)");
    
    // 問題に対する選択肢をデータベースに保存するためのループ
    for ($i = 0; $i < count($_POST["choices"]); $i++) {
      // 選択肢はフォームから送信されたデータ $_POST["choices"] に格納されており、正しい選択肢のインデックスは $_POST['correctChoice'] に格納されています。 execute() メソッドには、連想配列の形式でパラメータを渡すことができます。この場合、次の情報をパラメータとして渡しています。

// "name": 選択肢の内容。$_POST["choices"][$i] で選択肢の内容を取得しています。"valid": 選択肢が正解かどうか。$_POST['correctChoice'] に格納されている正解のインデックスと、現在のループのインデックス $i を比較して、正解ならば 1、そうでなければ 0 を設定します。"question_id": この選択肢が属する問題のID。すでにデータベースに挿入した問題のIDが $lastInsertId に格納されているため、これを使用します。
    $stmt->execute([
        "name" => $_POST["choices"][$i],
        "valid" => (int)$_POST['correctChoice'] === $i + 1 ? 1 : 0,
        "question_id" => $lastInsertId
    ]);
  }
  // 処理が完了した後、ユーザーを管理者用のページに自動的に転送するためのコード
  header("Location: ". "/admin/index.php");
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
  <link rel="stylesheet" href="../assets/styles/common.css">
  <link rel="stylesheet" href="../admin.css">
  <!-- Google Fonts読み込み -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&family=Plus+Jakarta+Sans:wght@400;700&display=swap"
    rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
  <?php 
//   include(dirname(__FILE__) . '/../../components/admin/header.php'); 
  ?>
  <div class="wrapper">
    <?php 
    // include(dirname(__FILE__) . '/../../components/admin/sidebar.php');
     ?>
    <main>
      <div class="container">
        <h1 class="mb-4">問題作成</h1>
        <form class="question-form" method="POST" enctype="multipart/form-data">
          <div class="mb-4">
            <label for="question" class="form-label">問題文:</label>
            <input type="text" name="content" id="question"
            class="form-control required"
            placeholder="問題文を入力してください" />
          </div>
          <div class="mb-4">
            <label class="form-label">選択肢:</label>
            <input type="text" name="choices[]" class="required form-control mb-2" placeholder="選択肢1を入力してください">
            <input type="text" name="choices[]" class="required form-control mb-2" placeholder="選択肢2を入力してください">
            <input type="text" name="choices[]" class="required form-control mb-2" placeholder="選択肢3を入力してください">
          </div>
          <div class="mb-4">
            <label class="form-label">正解の選択肢</label>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="correctChoice" id="correctChoice1" checked value="1">
              <label class="form-check-label" for="correctChoice1">
                選択肢1
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="correctChoice" id="correctChoice2" value="2">
              <label class="form-check-label" for="correctChoice2">
                選択肢2
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="correctChoice" id="correctChoice2" value="3">
              <label class="form-check-label" for="correctChoice2">
                選択肢3
              </label>
            </div>
          </div>
          <div class="mb-4">
            <label for="question" class="form-label">問題の画像</label>
            <input type="file" name="image" id="image"
            class="form-control required"
            placeholder="問題文を入力してください" />
          </div>
          <div class="mb-4">
            <label for="question" class="form-label">補足:</label>
            <input type="text" name="supplement" id="supplement"
            class="form-control"
            placeholder="補足を入力してください" />
          </div>
          <button type="submit" disabled class="btn submit">作成</button>
        </form>
      </div>
    </main>
  </div>
  <script>
    // このJavaScriptコードは、特定の条件に基づいてフォームの送信ボタンの有効/無効を切り替えるためのものです。主に、フォーム内の必須入力フィールドが全て入力されている場合に送信ボタンを有効にし、それ以外の場合は無効にするコード
    const submitButton = document.querySelector('.btn.submit')
    const inputDoms = Array.from(document.querySelectorAll('.required'))
    inputDoms.forEach(inputDom => {
      inputDom.addEventListener('input', event => {
        // ここでdはdatumの略でイベント変数eと同じ感じでデータの値をdとあらわす
        const isFilled = inputDoms.filter(d => d.value).length === inputDoms.length
        submitButton.disabled = !isFilled
      })
    })
  </script>
</body>

</html>
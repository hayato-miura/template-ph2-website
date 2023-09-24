<?php
// if (!isset($_SESSION['id'])) {
//     // ログインしていない場合ログインページに遷移させるようにした
//   header('Location: /admin/auth/signin.php');
// } else {
  $pdo = new PDO('mysql:host=db;dbname=posse', 'root', 'root');
  $sql = "SELECT * FROM questions WHERE id = :id";
  $stmt = $pdo->prepare($sql);
  // $stmt->bindValue(":id", $_REQUEST["id"]);: bindValue メソッドを使用して、プリペアドステートメント内の :id プレースホルダに具体的な値をバインドしています。この場合、$_REQUEST["id"] から取得したIDがバインド.$_REQUESTは$_GET $_POST $_COOKIE などの内容をまとめた連想配列
// ・$_REQUESTは連想配列
// ・$_REQUESTはリクエスト変数
// ・_GETの代わりに使うのはOK
// ・_POSTの代わりに使うのはNG
  $stmt->bindValue(":id", $_REQUEST["id"]);
  $stmt->execute();
  // $question = $stmt->fetch();: fetch メソッドを使用して、実行されたクエリの結果セットから1行分のデータを取得します。ここでは、特定のIDに対応する問題データを取得しています。$question 変数に問題のデータが格納されます。
  $question = $stmt->fetch();
  
  $sql = "SELECT * FROM choices WHERE question_id = :question_id";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(":question_id", $_REQUEST["id"]);
  $stmt->execute();
  $choices = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $params = [
      "content" => $_POST["content"],
      "supplement" => $_POST["supplement"],
      "id" => $_POST["question_id"],
    ];
    $set_query = "SET content = :content, supplement = :supplement";
    if ($_FILES["image"]["tmp_name"] !== "") {
      // , image = :imageをset_queryに代入している.= 演算子は、文字列の連結を行うためのものです。左辺の文字列に右辺の文字列を連結して、結果を左辺に代入します。上のSET文にimage = :imageが加えられる
      $set_query .= ", image = :image";
      // $params 配列内の "image" キーには空の文字列が設定されます。これにより、画像の更新がクエリ内で考慮され、バインド時に空の文字列が image カラムに代入されます。
      $params["image"] = "";
    }
    
    $sql = "UPDATE questions $set_query WHERE id = :id";
    // ここでは、beginTransaction() でトランザクションを開始し、成功したら commit() で確定させている。トランザクション処理を開始するためのメソッド呼び出しです。トランザクションは、データベースの一連の操作をまとめて、全てが成功するか、あるいは一部が失敗した場合には全てを取り消すことができる仕組みです。このメソッドを呼び出すことで、以降のデータベースクエリや操作は、トランザクション内で実行され、最終的なコミット（確定）またはロールバック（取り消し）のいずれかが行われるまで、データベースには反映されません。
    $pdo->beginTransaction();
    try { 
      if(isset($params["image"])) {
        $image_name = uniqid(mt_rand(), true) . '.' . substr(strrchr($_FILES['image']['name'], '.'), 1);
        $image_path = dirname(__FILE__) . '/../../assets/img/quiz/' . $image_name;
        move_uploaded_file(
          $_FILES['image']['tmp_name'], 
          $image_path
        );
        $params["image"] = $image_name;
      }
    
      $stmt = $pdo->prepare($sql);
      $result = $stmt->execute($params);
    
      $sql = "DELETE FROM choices WHERE question_id = :question_id ";
      $stmt = $pdo->prepare($sql);
      $stmt->bindValue(":question_id", $_POST["question_id"]);
      $stmt->execute();
    
      $stmt = $pdo->prepare("INSERT INTO choices(name, valid, question_id) VALUES(:name, :valid, :question_id)");
      for ($i = 0; $i < count($_POST["choices"]); $i++) {
        $stmt->execute([
          "name" => $_POST["choices"][$i],
          "valid" => (int)$_POST['correctChoice'] === $i + 1 ? 1 : 0,
          "question_id" => $_POST["question_id"]
        ]);
      }
      $pdo->commit();
    } catch(Error $e) {
      $pdo->rollBack();
    }
  }
  
// }
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
        <h1 class="mb-4">問題編集</h1>
        <form action="../../services/update_question.php" class="question-form" method="POST" enctype="multipart/form-data">
          <div class="mb-4">
            <label for="question" class="form-label">問題文:</label>
            <input type="text" name="content" id="question"
            class="form-control required"
            value="<?= $question["content"] ?>"
            placeholder="問題文を入力してください" />
          </div>
          <div class="mb-4">
            <label class="form-label">選択肢:</label>
            <!-- name="choices[]"：これは、HTMLフォーム内の要素の名前属性です。choices[] という名前を使うことで、同じ名前の複数の入力フィールドが配列としてグループ化されます。フォームが送信されると、各入力フィールドの値が配列としてサーバーに送信される仕組みです。class="required form-control mb-2"：ここでは、HTML要素に適用されるクラス属性を指定しています。required クラスは、ブラウザのバリデーション機能により、この入力フィールドが空でないことを要求します。form-control クラスは、Bootstrapなどのフレームワークで提供されるスタイルを適用するためのもので、mb-2 クラスは、下方向のマージンを追加します。placeholder="選択肢を入力してください"：これは、入力フィールド内に表示されるプレースホルダーテキストです。ユーザーに対して、どの種類の情報を入力するべきかを示す役割を果たします。value="... $choice["name"] "：ここでは、PHPコードを使って、各選択肢の名前を value 属性に設定しています。これにより、各入力フィールドが対応する選択肢の名前で事前に埋められた状態で表示されます。以上の要素が組み合わさって、選択肢の名前を入力するためのフォーム部品が生成されることになります。このフォームを使って複数の選択肢を一度に入力できるようになります。 -->
            <?php foreach($choices as $key => $choice) { ?>
              <input type="text" name="choices[]" class="required form-control mb-2" placeholder="選択肢を入力してください" value=<?= $choice["name"] ?>>
            <?php } ?>
          </div>
          <div class="mb-4">
            <label class="form-label">正解の選択肢</label>
            <?php foreach($choices as $key => $choice) { ?>
              <!-- Bootstrapなどのフレームワークで提供されるスタイルを適用するための <div> 要素です。ラジオボタンをグループ化して表示します。 -->
              <div class="form-check">
                <input 
                  class="form-check-input" 
                  type="radio" name="correctChoice" id="correctChoice<?= $key ?>" 
                  value="<?= $key + 1 ?>"
                  <?= $choice["valid"] === 1 ? 'checked' : '' ?>
                >
                <!-- ラジオボタンのIDと対応するラベルを関連付けます。 -->
                <label class="form-check-label" for="correctChoice<?= $key ?>">
                  選択肢<?= $key + 1 ?>
                </label>
              </div>
            <?php } ?>
          </div>
          <div class="mb-4">
            <label for="question" class="form-label">問題の画像</label>
            <input type="file" name="image" id="image"
              class="form-control"
              placeholder="問題文を入力してください"
            />
          </div>
          <div class="mb-4">
            <label for="question" class="form-label">補足:</label>
            <input type="text" name="supplement" id="supplement"
            class="form-control"
            placeholder="補足を入力してください"
            value="<?= $question["supplement"] ?>"
          />
          </div>
          <input type="hidden" name="question_id" value="<?= $question["id"] ?>">
          <button type="submit" class="btn submit">更新</button>
        </form>
      </div>
    </main>
  </div>
  <script>
    const submitButton = document.querySelector('.btn.submit')
    const inputDoms = Array.from(document.querySelectorAll('.required'))
    inputDoms.forEach(inputDom => {
      inputDom.addEventListener('input', event => {
        const isFilled = inputDoms.filter(d => d.value).length === inputDoms.length
        submitButton.disabled = !isFilled
      })
    })
  </script>
</body>

</html>
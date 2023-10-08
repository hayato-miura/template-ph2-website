<?php
    
    $study_array = array();

// データベースに接続
try {
    $pdo = new PDO('mysql:host=db;dbname=ph2-webapp', "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // エラーハンドリングを追加
} catch (PDOException $e) {
    echo $e->getMessage();
}

// フォームが送信された場合
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["submit"])) {
        $study_at = date("Y-m-d H:i:s");
        $stmt = $pdo->prepare("INSERT INTO `studies` (`study_at`, `study_hours`) VALUES (:study_at, :study_hours)");
        $stmt->bindParam(":study_at", $study_at);
        $stmt->bindParam(":study_hours", $_POST["studyHour"]); 
        $stmt->execute();


        $study_id = $pdo->lastInsertId();
        foreach ($_POST["content"] as $content) {
            $stmt = $pdo->prepare("INSERT INTO `study_contents` (`study_id`, `content_id`) VALUES (:study_id, :content_id)");
            $stmt->bindParam(":study_id", $study_id);
            $stmt->bindParam(":content_id", $content);
            $stmt->execute();
        }
        foreach ($_POST["study_lang"] as $lang) {
            $stmt = $pdo->prepare("INSERT INTO `study_languages` (`study_id`, `language_id`) VALUES (:study_id, :language_id)");
            $stmt->bindParam(":study_id", $study_id);
            $stmt->bindParam(":language_id", $lang);
            $stmt->execute();
        }
    }
}

// studiesテーブルから学習時間を取得するクエリ
$sqlToday = "SELECT SUM(study_hours) AS today FROM `studies` WHERE DATE(`study_at`) = CURDATE()";
$sqlMonth = "SELECT SUM(study_hours) AS month FROM `studies` WHERE MONTH(`study_at`) = MONTH(CURDATE())";
$sqlTotal = "SELECT SUM(study_hours) AS total FROM `studies`";

// 各学習時間を取得
$study_hour_today = $pdo->query($sqlToday)->fetch(PDO::FETCH_ASSOC);
$study_hour_month = $pdo->query($sqlMonth)->fetch(PDO::FETCH_ASSOC);
$study_hour_total = $pdo->query($sqlTotal)->fetch(PDO::FETCH_ASSOC);

// 学習言語データの取得
$sqlLanguageData = "SELECT `language_id`, SUM(`study_hours`) / COUNT(DISTINCT `study_id`) AS `average_hours` FROM `study_languages` GROUP BY `language_id`";
// $language_data = $pdo->query($sqlLanguageData)->fetchAll(PDO::FETCH_ASSOC);

// 学習コンテンツデータの取得
$sqlContentData = "SELECT `content_id`, SUM(`study_hours`) / COUNT(DISTINCT `study_id`) AS `average_hours` FROM `study_contents` GROUP BY `content_id`";
// $content_data = $pdo->query($sqlContentData)->fetchAll(PDO::FETCH_ASSOC);

// データベース接続を閉じる
$pdo = null;



?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./style/reset.css">
    <link rel="stylesheet" href="./style/style.css">
    <!-- google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=BIZ+UDPGothic&family=Noto+Sans+JP:wght@300;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" defer></script>
    <script src="./script/main.js" defer></script>
    <script src="./script/common.js" defer></script>
</head>
<!-- メモ
    ・まずは学習時間3項目の意図つの項目を完成させる。
    そのあとにほかの2つの項目に同様のCSSをきかせるあげればいい
    ・モーダルウィンドウのリストタグにdisplay flexをかけたいがかけられない
    .ドーナツのパーセント表示のやり方がわからない


-->
<body>
    <!-- ヘッダーここから---------------------------------------------------------------------------- -->
    <header class="site-header">
        <div class="wrapper site-header__wrapper">
                <!-- ヘッダのコンテンツ -->
                <div class="header__logo">
                    <img src="./assets/logo.svg" alt="posse">
                </div>
                <div class="header__week">4th week</div>
                <div class="header__btn modal-open">記録・投稿</div>
        </div>
    </header>
    <!-- ヘッダーここまで -->
    <!-- モーダルここから -->
    <form action="#" method="POST">
        <div class="modal-container">
            <div class="modal-body">
                <!-- 閉じるボタン -->
                <div class="modal-close">×</div>
                <!-- モーダル内のコンテンツ -->
                <div class="modal-content">
                    <div class="upside">
                        <div class="modal__left">
                            <div class="study__date">
                                <p class="modal__subheading">学習日</p>
                                <!-- 学習日カレンダー -->
        
                                    <div>
                                        <label class="date-edit"><input type="date" value="2019-08-22" name="postDate" /></label>
                                    </div>
        
                            </div>
                            <div class="study__contents">
                                <p class="modal__subheading">学習コンテンツ(複数選択可)</p>
        
                                    <div class="che">
                                        <div class="check__btn__list__box">
                                            <label class="ECM_CheckboxInput"><input class="ECM_CheckboxInput-Input" type="checkbox" name="content[]" value="dotInstall" ><span class="ECM_CheckboxInput-DummyInput"></span><span class="ECM_CheckboxInput-LabelText">ドットインストール</span></label>
                                        </div>
                                        <div class="check__btn__list__box">
                                            <label class="ECM_CheckboxInput"><input class="ECM_CheckboxInput-Input" type="checkbox" name="content[]" value="nStudy"><span class="ECM_CheckboxInput-DummyInput"></span><span class="ECM_CheckboxInput-LabelText">N予備校</span></label>
                                        </div>
                                        <div class="check__btn__list__box">
                                            <label class="ECM_CheckboxInput"><input class="ECM_CheckboxInput-Input" type="checkbox" name="content[]" value="posseHomework"><span class="ECM_CheckboxInput-DummyInput"></span><span class="ECM_CheckboxInput-LabelText">POSSE課題</span></label>
                                        </div>
                                                                </div>
        
                                    </div>
                            <div class="study__lang"><p class="modal__subheading">学習言語(複数選択可)</p></div>
        
                                <div class="check__btn__list__box">
                                    <label class="ECM_CheckboxInput"><input class="ECM_CheckboxInput-Input" type="checkbox" name="study_lang[]" value="html"><span class="ECM_CheckboxInput-DummyInput"></span><span class="ECM_CheckboxInput-LabelText">HTML</span></label>
                                </div>
                                <div class="check__btn__list__box">
                                    <label class="ECM_CheckboxInput"><input class="ECM_CheckboxInput-Input" type="checkbox" name="study_lang[]" value="css"><span class="ECM_CheckboxInput-DummyInput"></span><span class="ECM_CheckboxInput-LabelText">CSS</span></label>
                                </div>
                                <div class="check__btn__list__box">
                                    <label class="ECM_CheckboxInput"><input class="ECM_CheckboxInput-Input" type="checkbox" name="study_lang[]" value="js"><span class="ECM_CheckboxInput-DummyInput"></span><span class="ECM_CheckboxInput-LabelText">JavaScript</span></label>
                                </div>
        
                        </div>
                        <div class="modal__right">
                            <div class="study__time">
                                <p class="modal__subheading">学習時間</p>
        
                                    <div class="comment__form"><input type="number" placeholder="数字を入力" required name="studyHour"></div>
                            </div>
                            <div class="study__twitter__comment">
                                <p class="modal__subheading">Twitter用コメント</p>
                                <form action="#" method="post" class="comment__form" name="commentForm" method="POST">
        
                                    <textarea name="comment" placeholder="コメントを入力" rows="15" cols="35" id="content"></textarea>
                                </form>
                            </div>
                            <div class="twitter__share" id="twitter__share">
                                    <div class="check__btn__list__box">
                                        <label class="ECM_CheckboxInput"><input class="ECM_CheckboxInput-Input twitter-share-btn" type="checkbox" name="twitterShare"><span class="ECM_CheckboxInput-DummyInput"></span><span class="ECM_CheckboxInput-LabelText">Twitterにシェアする</span></label>
                                    </div>
                                <!--入力フォーム-->
                                <!-- <input type="text" id="content"> -->
        <!--ご自身のTwitterアカウントへ行きます-->
        
                            </div>
                        </div>
                    </div>
                        <div class="bottomside">
                            <div class="submit__button">
                                <button class="button btn" id="twitter" name="submit" type="submit" value="submit">
                                    記録・投稿</button>
                                <div class="load__complete__box">
                                    <div class="load__complete__img__box">
                                        <img src="./assets/IMG_4908.jpg" alt="" class="load__complete__img" >
                                    </div>
                                    <div class="load__complete__content">
                                            記録・投稿が完了しました
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </form>
    
    <!-- モーダルここまで -->
    <div class="wrapper">
        <aside></aside>
        
        <main>
            <div class="content-area">
                <!-- 学習時間３項目と学習時間のグラフのセクション ここから-->
                <div class="content">
                    <!-- 学習時間３項目 -->
                    <!-- 学習時間３項目 -->
<div class="study__hour">
    <div class=" study__hour__box study__hour__today">
        <div class="study__hour__container">
            <div class="study__hour__subheading">Today</div>
            <div class="study__hour__number"><?php echo isset($study_hour_today['today']) ? $study_hour_today['today'] : 0; ?></div>
            <div class="study__hour__unit">hour</div>
        </div>
    </div>
    <div class="study__hour__box study__hour__month">
        <div class="study__hour__container">
            <div class="study__hour__subheading">Month</div>
            <div class="study__hour__number"><?php echo isset($study_hour_month['month']) ? $study_hour_month['month'] : 0; ?></div>
            <div class="study__hour__unit">hour</div>
        </div>
    </div>
    <div class=" study__hour__box study__hour__total">
        <div class="study__hour__container">
            <div class="study__hour__subheading">Total</div>
            <div class="study__hour__number"><?php echo isset($study_hour_total['total']) ? $study_hour_total['total'] : 0; ?></div>
            <div class="study__hour__unit">hour</div>
        </div>
    </div>
</div>

                    <!-- 学習時間のグラフ -->
                    <div class="study__hour__graph">
                        <div class="container" align='center'>
                            <canvas id="canvas" class="bar"></canvas>
                        </div>
                    </div>
                </div>
                <!-- 学習時間３項目と学習時間のグラフのセクション ここまで-->
                <!-- 学習言語と学習コンテンツのセクション ここから-->
                <div class="content lang__and__contents__box">
                    <!-- 学習言語のセクション -->
                    <div class="contents__box lang ">
                        <div class="subheading">学習言語</div>
                        <div class="pie__chart">
                            <canvas id="myDoughnutChart1">
                            </canvas>
                        </div>
                        <div class="study__content__list">
                            <ul class="study__lang__list">
                                <li><span></span> JavaScript</li>
                                <li><span></span> CSS</li>
                                <li><span></span> PHP</li>
                                <li><span></span> HTML</li>
                            </ul>
                        </div>
                    </div>
                    <!-- 学習コンテンツのセクション -->
                    <div class=" contents__box study__contents">
                        <div class="subheading">学習コンテンツ</div>
                        <div class="pie__chart">
                            <!-- 2個目の円グラフはid名を変えてまた同様に作成すればいい -->
                            <canvas id="myDoughnutChart2">
                            </canvas>
                        </div>
                        <div class="study__content__list">
                            <ul class="study__content__list">
                                <li><span></span> ドットインストール</li>
                                <li><span></span> N予備校</li>
                                <li><span></span> POSSE課題</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- 学習言語と学習コンテンツのセクション ここまで-->
            </div>
        </main>
    </div>
<!-- フッターここから -->
    <footer class="footer">

        <div class="main__data"><button class="data__button">＜</button><p>2020年10月</p> <button class="data__button">＞</button></div>
    </footer>
    <!-- フッターここまで -->
</body>
</html>
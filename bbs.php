<?php
    //データベース接続
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING)); 
    
    //テーブル作成
    $sql = "CREATE TABLE IF NOT EXISTS bbs3"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
    . "comment TEXT,"
    . "password TEXT,"
    . "time TEXT"
	.");";
	$stmt = $pdo->query($sql);
?>


<?php
    //コメント送信ボタンが押されたとき
    if(isset($_POST["commentsubmit"])) {

        //フォームが入力されていないとき
        if(empty($_POST["submitname"]) || empty($_POST["submitcomment"]) || empty($_POST["submitpassword"])) {
            echo "名前、コメント、パスワードを入力してください" . "<hr><br>";
       
        //フォームが入力されているとき
        } else {

            //入力テンプレート
            $submittemplate = "名前:". ($_POST["submitname"]). "<br>". "コメント:". ($_POST["submitcomment"]);
            
            //mysqlテーブルにデータ入力
            $sql = $pdo -> prepare("INSERT INTO bbs3 (name, comment, password, time) VALUES (:name, :comment, :password,:time)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':password', $password, PDO::PARAM_STR);
            $sql -> bindParam(':time', $time, PDO::PARAM_STR);
            $name = ($_POST["submitname"]);
            $comment = ($_POST["submitcomment"]);
            $password = ($_POST["submitpassword"]);
            $time = date("Y/m/d H:i:s");
            $sql -> execute();

            //入力確認
            echo  "コメントありがとうございます!" . "<br>" . "下記の内容で受け付けました" . "<br><br>" .
            "名前:" . $_POST["submitname"] . "<br>" .
            "コメント内容:" . $_POST["submitcomment"] . "<br>" .
            "コメント時間:" . $time . "<hr><br>";

        }

    }

    //コメント編集ボタンが押されたとき
    if(isset($_POST["editsubmit"])) {

        //フォームが入力されていないとき
        if(empty($_POST["editname"]) || empty($_POST["editcomment"]) || empty($_POST['editpassword'])) {
            echo "名前、コメント、パスワードを入力してください" . "<hr><br>";
        
        //フォームが入力されているとき
        } else {

            //mysqlテーブルを変数に格納
            $sql = "SELECT * FROM bbs3";
            $stmt = $pdo->query($sql);
            foreach ($stmt as $row) {

                //フォームのコメント番号とテーブルのIDが一致したとき
                if($_POST["editid"] == $row['id']) {

                    //フォームのパスワードとテーブルのパスワードが一致したとき
                    if($_POST["editpassword"] == $row['password']) {
                        
                        //テーブルから編集
                        $editid = $_POST["editid"];
                        $editname = $_POST["editname"]; 
                        $editcomment = $_POST["editcomment"];
                        $edittime = date("Y/m/d H:i:s");

                        $sql = 'update bbs3 set name=:name,comment=:comment,time=:time where id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':name', $editname, PDO::PARAM_STR);
                        $stmt->bindParam(':comment', $editcomment, PDO::PARAM_STR);
                        $stmt->bindParam(':time', $edittime, PDO::PARAM_STR);
                        $stmt->bindParam(':id', $editid, PDO::PARAM_INT);
                        $stmt->execute();
                    
                        //編集したことを表示
                        echo "下記のように変更しました" .
                        
                        "<h3>変更前</h3>" .
                        "コメント番号: ". $row['id'] . "<br>" . 
                        "名前: " . $row['name'] . "<br>" .
                        "コメント内容: " . $row['comment'] . "<br>" .
                        "コメント時間: " . $row['time'] ."<br><br><h3>↓</h3><br><br>" .
                        
                        "<h3>変更後</h3>" .
                        "コメント番号: ". $row['id'] . "<br>" .
                        "名前: " . $_POST["editname"] . "<br>" .
                        "コメント内容: " . $_POST["editcomment"] . "<br>" .
                        "コメント時間: " . $edittime . "<hr><br>";
                    
                    //パスワードが一致しないとき
                    } else {
                        echo "パスワードが違います" . "<hr><br>";
                    }
                }
            }
        }
    }

    //削除ボタンが押されたとき
    if(isset($_POST["deletesubmit"])) {

        //フォームが入力されていないとき
        if(empty($_POST["deletenumber"]) || empty($_POST["deletepassword"])) {
            echo "コメント番号とパスワードを入力してください" . "<hr><br>";
        
        //フォームが入力されているとき
        } else {
            //mysqlテーブルを変数に格納
            $sql = "SELECT * FROM bbs3";
            $stmt = $pdo->query($sql);
            foreach ($stmt as $row) {
            
                //フォームのコメント番号とテーブルのIDが一致したとき
                if($_POST["deletenumber"] == $row['id']) {
                    
                    //フォームのパスワードとテーブルのパスワードが一致したとき
                    if($_POST["deletepassword"] == $row['password']) {
                        
                        //テーブルからデータを消去
                        $deleteid = ($_POST["deletenumber"]);
                        $sql = 'delete from bbs3 where id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $deleteid, PDO::PARAM_INT);
                        $stmt->execute();
                        
                        //削除したことを表示
                        echo "コメント番号: ". $row['id'] . "<br>" . 
                             "名前: " . $row['name'] . "<br>" .
                             "コメント内容: " . $row['comment'] . "<br>" .
                             "コメント時間: " . $row['time'] . " を削除しました" . "<hr><br>";
                    
                             //パスワードが一致しないとき
                    } else {
                        echo "パスワードが違います" . "<hr><br>";
                    }
                }
            }
        
        }
    }
?>


<?php
    echo "<h1>みうらの掲示板</h1><br>";

    //今までのコメント表示
    echo "今までのコメント↓" . "<br><br>";
    $sql = 'SELECT * FROM bbs3';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row) {
        echo $row['id'] . ". 名前 : " . "<strong>" . $row['name'] ."</strong>". ":" . $row['time'] . "<br>" . "　　" . $row['comment'] .  "<br><br>";
    }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
<form acrion="mission_4-1.php" method="post">
<h3>
    送信用フォーム
</h3>
<p>
    名前: <input type="text" name="submitname" id="submitname">
</p>
    コメント: <input type="text" name="submitcomment" id="submitcomment">
<p>
    パスワード: <input type="password" name="submitpassword" id="submitpassword">
</p>
<br>
    <input type="submit" value="送信" name="commentsubmit" id="commentsubmit">

<br>
<br>

<h3>
    編集用フォーム
</h3>
<p>
    コメント番号: <input type="text" name="editid" id="editid">
</p>
<p>
    名前: <input type="text" name="editname" id="editname">
</p>
<p>
    コメント: <input type="text" name="editcomment" id="editcomment">
</p>
<p>
    パスワード: <input type="password" name="editpassword" id="editpassword">
</p>
<br>
    <input type="submit" value="送信" name="editsubmit" id="editsubmit">

<br>
<br>

<h3>
    削除用フォーム
</h3>
<p>
    コメント番号: <input type="text" name="deletenumber" id="deletenumber">
</p>
<p>
    パスワード: <input type="password" name="deletepassword" id="deletepassword">
</p>
<br>
    <input type="submit" value="送信" name="deletesubmit" id="deletesubmit">
</body>
</html>
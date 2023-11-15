<?php
    session_start();
    //ログイン状態でない場合unloggedに飛ばす
    if (!isset($_SESSION["userId"])) {
        header("Location: https://tech-base.net/tb-250435/mission6/unlogged.php");
        exit();
    }
    
    $dsn = 'mysql:dbname=データベース名;host=localhost';//data source number
    $user = 'ユーザ名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));//php data object
    
    //投稿データの保存先
    $sql = "CREATE TABLE IF NOT EXISTS info"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name CHAR(32),"
    . "comment TEXT,"
    . "date DATETIME"
    .");";
    $stmt = $pdo->query($sql);
    
    //ユーザーデータの保存先
    $sql = "CREATE TABLE IF NOT EXISTS user_info"
    ." ("
    . "user CHAR(32),"
    . "userpass TEXT"
    .");";
    $stmt = $pdo->query($sql);
        
    //新規投稿
    if (!empty($_POST["comment"])){
        $name=$_SESSION["userId"];
        $comment=$_POST["comment"];
        $date=date("Y/m/d H:i:s");
        $sql = "INSERT INTO info (name, comment, date) VALUES (:name, :comment, :date)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->execute();
    }
    
    //削除機能
    if (!empty($_POST["deleteNum"])) {
        $deleteNum=$_POST["deleteNum"];
        
        //削除対象番号の行の情報取得
        $sql = 'SELECT * FROM info WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $deleteNum, PDO::PARAM_INT);
        $stmt->execute();
        $row=$stmt->fetch();
            
        //削除実行
        if($row) {
            $id = $deleteNum;
            $sql = 'delete from info where id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }
    }
        
?>
    
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>TECH-BASE生の日常</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="main">
        <h1>TECH-BASE生の日常</h1>
        <?php
        //書き出し
        $sql = 'SELECT * FROM info';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            echo $row['id'].' : ';
            echo '<div class= postName><strong>'.$row['name'].'</strong></div> : ';
            echo date("Y/m/d H:i:s", strtotime($row['date'])).'<br>';
            echo '<div class="comment">'.nl2br($row['comment']).'</div>';
            //削除ボタン
            if($row['name'] == $_SESSION['userId']){
                echo '<form action="" method="post">';
                echo '<input type="hidden" name="deleteNum" value="'.$row['id'].'">';
                echo '<div class=deleteButton><input type="submit" name="deleteButton" value="削除"></div>';
                echo '</form>';
                
            }
        echo '<hr>';
        }
        ?>
        
        <br>
        <form action="" method="post">
            <textarea id="comment" name="comment" placeholder="コメントを入力してください"></textarea><br>
            <input type="submit" name="submit" value="投稿する" class>
        </form>
    </div>
    <div class="right-section">
        <div class= "right-form">
            <p>こんにちは、</p>
            <strong><?php echo $_SESSION['userId']?>さん</strong>
            <p><a href='https://tech-base.net/tb-250435/mission6/logout.php'>ログアウト</a></p>
        </div>
    </div>
</body>
</html>
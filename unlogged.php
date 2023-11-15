<?php
    session_start();
    //ログイン状態であればログイン済みページへ
    if (isset($_SESSION["login"])) {
        session_regenerate_id(TRUE);
        header("Location: https://tech-base.net/tb-250435/mission6/logged.php");
        exit();
    }
    //データベース登録
    $dsn = 'mysql:dbname=データベース名;host=localhost';//data source number
    $user = 'ユーザ名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));//php data object
    $sql = "CREATE TABLE IF NOT EXISTS info"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name CHAR(32),"
    . "comment TEXT,"
    . "date DATETIME,"
    . "pass TEXT"
    .");";
    $stmt = $pdo->query($sql);
    $sql = "CREATE TABLE IF NOT EXISTS user_info"
    ." ("
    . "user CHAR(32),"
    . "userpass TEXT"
    .");";
    $stmt = $pdo->query($sql);
    
    //ユーザー登録機能
    if (!empty($_POST["user"]) && isset($_POST["userpass"])) {
        $user = $_POST["user"];
        $sql = 'SELECT COUNT(*) FROM user_info WHERE user=:user';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user', $user, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        // 重複がなければ登録
        if ($count == 0) {
            $userpass = $_POST["userpass"];
            $sql = "INSERT INTO user_info (user, userpass) VALUES (:user, :userpass)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':user', $user, PDO::PARAM_STR);
            $stmt->bindParam(':userpass', $userpass, PDO::PARAM_STR);
            $stmt->execute();
            $_SESSION['userId'] = $user;
                header('Location:https://tech-base.net/tb-250435/mission6/logged.php');
        } else {
            $userdouble = "同じユーザーネームが既に存在します。";
        }
    }

    
    //ユーザーログイン機能
    if(!empty($_POST["loginUser"]) && isset($_POST["loginPass"])){
        if(!empty($_POST["loginPass"]) || $_POST["loginPass"]=="0"){
            $loginPass = $_POST["loginPass"];
            $user=$_POST["loginUser"];
            $sql = 'SELECT * FROM user_info WHERE user=:user';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':user', $user, PDO::PARAM_INT);
            $stmt->execute();
            $row=$stmt->fetch();
            if($row && $loginPass == $row['userpass']){
                $_SESSION['userId'] = $user;
                header('Location:https://tech-base.net/tb-250435/mission6/logged.php');
            }else{
                $userwrong = "ユーザーネームまたはパスワードが一致しません。";
            }
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
            echo '<div class="comment">' . nl2br($row['comment']) . '</div>';
        echo "<hr>";
        }
        ?>
        <br>
        <p>※投稿にはユーザー登録/ログインが必要です。</p>
    </div>
    <div class="left-section">
    </div>
    <div class="right-section">
        <div class= "right-form">
            <h3>ユーザー登録</h3>
            <form action="" method="post">
                <input type="text" name="user" placeholder="ユーザーネーム"><br>
                <input type="password" name="userpass" placeholder="パスワード">
                <input type="submit" name="submit" value="登録"><br>
                <?php if(isset ($userdouble)){echo $userdouble;}?>
            </form>
            <h3>ログイン</h3>
            <form action="" method="post">
                <input type="text" name="loginUser" placeholder="ユーザーネーム"><br>
                <input type="password" name="loginPass" placeholder="パスワード">
                <input type="submit" name="submit" value="ログイン"><br>
                <?php if(isset ($userwrong)){echo $userwrong;}?>
            </form>
        </div>
    </div>
</body>
</html>
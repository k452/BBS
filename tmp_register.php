<!DOCTYPE html>
<html lang = “ja”>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <title>ユーザー仮登録ページ</title>
</head>
<body>
<?php
    //データベースの変数定義
    $servername = '';
    $username = '';
    $password = '';
    $dbname = '';

    //変数定義
    $time = date("Y/m/d H:i:s");
    $name = ( isset( $_POST["name"] ) === true ) ?$_POST["name"]: "";
    $pass = ( isset( $_POST["pass"] ) === true ) ?$_POST["pass"]: "";
    $err_msg1 = "";
    $err_msg2 = "";
    $flag = 0;

    try{
        //PDO
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); 

        //id生成、ユーザー情報の登録及びパスワードの入力確認
        if(isset($_POST["btn1"]) ===  true){
            if($name   === "" ){
                $err_msg1 = "名前を入力してください"; 
                echo $err_msg1.'<br>';
                echo '<br>';
            }elseif($pass === ""){
                $err_msg2 = "パスワードを入力してください";
                echo $err_msg2.'<br>';
                echo '<br>';
            }elseif($err_msg1 === "" && $err_msg2 ===""){
                //id生成
                $id = uniqid();

                //ユーザー情報の登録
                $sql =  $conn->prepare("INSERT INTO userinfo(name, id, pass, flag, time)
                VALUES(:name, :id, :pass, :flag, :time)");
                $sql->bindParam(':name', $name, PDO::PARAM_STR);
                $sql->bindParam(':id', $id, PDO::PARAM_STR);
                $sql->bindParam(':pass', $pass, PDO::PARAM_STR);
                $sql->bindValue(':flag', $flag, PDO::PARAM_INT);
                $sql->bindValue(':time', $time, PDO::PARAM_INT);
                $sql->execute();

                //URL生成
                $para_0 = array(
                    'id' => $id
                );
                $para = http_build_query($para_0);
                $url = "~~~/register.php"."?".$para;

                //登録されたユーザー情報の表示
                 //sqlが作用した行の表示
                $x = $sql->rowCount();
                echo $x.'<br>';
                
                echo "あなたの登録したIDとパスワードは以下の通りです".'<br>';
                echo "ID:$id".'<br>';
                echo "URL:$url".'<br>';
                echo "パスワード:$pass".'<br>';

                //メール送信
                mb_language("Japanese");
                mb_internal_encoding("UTF-8");
                $to = $_POST["mail"];
                $title = "本登録用メール";
                $content = "仮登録が完了しました!\n
                            ID:$id\n
                            パスワード:$pass\n
                            本登録用URL:$url
                            ";
                if(mb_send_mail($to, $title, $content)){
                    echo "本登録用メールを送信しました".'<br>';
                }else{
                    echo "メールの送信に失敗しました".'<br>';
                }
            }
        }
        
        
        //読み込みと表示
        $stmt = $conn->prepare("SELECT name, id, pass, flag, time FROM userinfo ORDER BY time ASC"); 
        $stmt->execute();

        $result = $stmt->fetchall();
        foreach($result as $value){
            echo 'name:'.$value[0].'</br>';
            echo 'id:'.$value[1].'</br>';
            echo 'pass:'.$value[2].'</br>';
            echo 'flag:'.$value[3].'</br>';
            echo '書き込み時刻:'.$value[4].'</br>';
            echo "<hr>";
        
    }

    }catch(PDOException $e){
        echo $sql . "<br>" . $e->getMessage();
    }
    $conn = null;
?>

<h1>ユーザー登録用フォーム</h1>
<form action="tmp_register.php" method="post" id ="submit">
    <div>名前</div>
    <input type="text" name="name"><br></br>
    <div>メールアドレス</div>
    <input type="text" name="mail"><br></br>
    <div>パスワード</div>
    <input type ="password" name="pass" ><br></br>
    <input type="submit" name="btn1" value="登録する">
</form>

</body>
</html>
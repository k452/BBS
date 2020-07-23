<!DOCTYPE html>
<html lang = “ja”>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <title>ユーザー本登録ページ</title>
</head>
<body>
<?php
    //データベースの変数定義
    $servername = '';
    $username = '';
    $password = '';
    $dbname = '';

    //変数定義
    $id = ( isset( $_POST["id"] ) === true ) ?$_POST["id"]: "";
    $pass = ( isset( $_POST["pass"] ) === true ) ?$_POST["pass"]: "";
    $err_msg1 = "";
    $err_msg2 = "";

    //セッション
    session_start();

    try{
        //PDO
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

            //URLからのパラメータIDの取得
            $param_0 = strstr($_SERVER["REQUEST_URI"], '?');  
            $param = str_replace('?id=', '', $param_0);

            if($param !== ""){
            //セッション変数にパラメータを代入
                $_SESSION[PARAM] = $param;
            }

        //ユーザー情報の照合及びパスワードの入力確認
        if(isset($_POST["btn"]) ===  true){
            if($id   === "" ){
                $err_msg1 = "IDを入力してください"; 
                echo $err_msg1.'<br>';
                echo '<br>';
            }elseif($loginpass === ""){
                $err_msg2 = "パスワードを入力してください";
                echo $err_msg2.'<br>';
                echo '<br>';
            }elseif($err_msg1 === "" && $err_msg2 ===""){
                //ユーザー情報の取得
                $get_pass_0 = $conn->prepare("SELECT pass FROM userinfo WHERE id = :id ");
                $get_pass_0->bindValue(':id', $id, PDO::PARAM_INT);
                $get_pass_0->execute();
                $get_pass = $get_pass_0->fetch();

                if($get_pass[0] === $pass and $id === $_SESSION[PARAM]){
                    //フラグの書き換え
                    $flag = 1;
                    $sql_2 = $conn->prepare("UPDATE userinfo SET flag = :flag WHERE id = :id ");
                    $sql_2->bindValue(':flag', $flag, PDO::PARAM_INT);
                    $sql_2->bindValue(':id', $id, PDO::PARAM_INT);
                    $sql_2->execute();
                    
                    //フラグの取得
                    $sql_3 = $conn->prepare("SELECT flag FROM userinfo WHERE id = :id ");
                    $sql_3->bindValue(':id', $id, PDO::PARAM_INT);
                    $sql_3->execute();
                    $get_flag = $sql_3->fetch();
                    
                    if($get_flag[0] == 1){
                        //ログインページへ移動
                        header("location: login.php");
                    }elseif($get_flag[0] == 0){
                        echo "本登録されていません!";
                    }
                }else{
                    echo "パスワードが間違っています"."<br>";
                }
            }
        }
        
        //読み込みと表示
        $stmt = $conn->prepare("SELECT name, id, pass, flag, time FROM userinfo "); 
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

<h1>ユーザー本登録フォーム</h1>
<form action="register.php" method="post" id ="submit">
    <div>ID</div>   
    <input type="text" name="id"><br></br>
    <div>パスワード</div>
    <input type ="password" name="pass" ><br></br>
    <input type="submit" name="btn" value="登録">
</form>

</body>
</html>
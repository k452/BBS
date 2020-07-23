<!DOCTYPE html>
<html lang = “ja”>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <title>ユーザーログインページ</title>
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

        //ユーザー情報の照合及びパスワードの入力確認
        if(isset($_POST["btn"]) ===  true){
            if($id === "" ){
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

                $get_name_0 = $conn->prepare("SELECT name FROM userinfo WHERE id = :id ");
                $get_name_0->bindValue(':id', $id, PDO::PARAM_INT);
                $get_name_0->execute();
                $get_name = $get_name_0->fetch();

                $get_flag_0 = $conn->prepare("SELECT flag FROM userinfo WHERE id = :id ");
                $get_flag_0->bindValue(':id', $id, PDO::PARAM_INT);
                $get_flag_0->execute();
                $get_flag = $get_flag_0->fetch();

                //セッションIDの登録
                $_SESSION[ID] = $id;

                if($get_pass[0] == $pass and $get_flag[0] == 1){
                    $_SESSION[NAME] = $get_name[0];
                    header("Location: cache_lite.php");
                }else{
                    echo "パスワードが間違っています";
                }
            }
        }
        
    }catch(PDOException $e){
        echo $sql . "<br>" . $e->getMessage();
    }
    $conn = null;
?>

<h1>ユーザーログインフォーム</h1>
<form action="login.php" method="post" id ="submit">
    <div>ID</div>
    <input type="text" name="id" value="<?php echo $_SESSION[ID]; ?>"><br></br>
    <div>パスワード</div>
    <input type ="password" name="pass" ><br></br>
    <input type="submit" name="btn" value="ログイン">
</form>

</body>
</html>
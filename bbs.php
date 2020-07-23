<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>

<?php
    ob_start();

    //UA関連
    function getUserAgent(){
        $userAgent = isset($_SERVER['HTTP_USER_AGENT'])? $_SERVER['HTTP_USER_AGENT'] : '';
        return $userAgent;
    }

    function isSmartPhone(){
        $ua = getuserAgent();

        if (stripos($ua, 'iphone') !== false || // iphone
        stripos($ua, 'ipod') !== false || // ipod
        (stripos($ua, 'android') !== false && stripos($ua, 'mobile') !== false) || // android
        (stripos($ua, 'windows') !== false && stripos($ua, 'mobile') !== false) || // windows phone
        (stripos($ua, 'firefox') !== false && stripos($ua, 'mobile') !== false) || // firefox phone
        (stripos($ua, 'bb10') !== false && stripos($ua, 'mobile') !== false) || // blackberry 10
        (stripos($ua, 'blackberry') !== false) // blackberry
        ) {
        $isSmartPhone = true;
        } else {
        $isSmartPhone = false;
        }

        return $isSmartPhone;
    }
    
    function isMobilePhone(){
        $ua = getuserAgent();

        if (stripos($ua, 'DoCoMo') !== false ||
        stripos($ua, 'UP.Browser') !== false || 
        stripos($ua, 'SoftBank') !== false  || 
        stripos($ua, 'Vodafone') !== false || 
        stripos($ua, 'J-PHONE') !== false || 
        stripos($ua, 'MOT-') !== false || 
        stripos($ua, 'WILLCOM') !== false || 
        stripos($ua, 'emobile') !== false
        ) {
            $isMobilePhone = true;
        } else {
            $isMobilePhone = false;
        }
        return $isMobilePhone;
    }

    //smarty関連
    define('SMARTY_DIR', '/パス/Smarty/libs/');
    require_once(SMARTY_DIR . 'Smarty.class.php');
    $smarty = new SmartyBC();
    $smarty->php_handling = Smarty::PHP_ALLOW;

    //データベースの変数定義
    $servername = '';
    $username = '';
    $password = '';
    $dbname = '';

    //変数定義
    $time = date("Y/m/d H:i:s");
    $err_msg1 = "";
    $err_msg2 = "";
    $err_msg3 = "";
    $err_msg4 = "";
    $err_msg5 = "";
    $message ="";
    $name = ( isset( $_POST["name"] ) === true ) ?$_POST["name"]: "";
    $comment  = ( isset( $_POST["comment"] )  === true ) ?  trim($_POST["comment"])  : "";
    $deletenumber = ( isset( $_POST["deletenumber"] ) === true ) ?$_POST["deletenumber"]: "";
    $editnumber = ( isset( $_POST["editnumber"] ) === true ) ?$_POST["editnumber"]: "";
    $inputpass = ( isset( $_POST["inputpass"] ) === true ) ?$_POST["inputpass"]: "";
    $deletepass = ( isset( $_POST["deletepass"] ) === true ) ?$_POST["deletepass"]: "";
    $editpass = ( isset( $_POST["editpass"] ) === true ) ?$_POST["editpass"]: "";
    $enum = ( isset( $_POST["enum"] ) === true ) ?$_POST["enum"]: "";
    $deletemessage = 'この書き込みは削除されました';
    $null = NULL;

    try{
        //PDO
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

        //掲示板処理
        if(isset($_POST["btn1"]) ===  true){
            //書き込み処理
            if($_POST["state"] != "editmode"){
                if($name   === "" ){
                    $err_msg1 = "名前を入力してください"; 
                    echo $err_msg1.'<br>';
                    echo '<br>';
                }elseif($comment  === "" ){
                    $err_msg2 = "コメントを入力してください";
                    echo $err_msg2.'<br>';
                    echo '<br>';
                }elseif($pass === ""){
                    $err_msg3 = "パスワードを入力してください";
                    echo $err_msg3.'<br>';
                    echo '<br>';
                }elseif($err_msg1 === "" && $err_msg2 ==="" && $err_msg3 ===""){
                    //一時ファイル名の抽出
                    $tmp_name_0 = $_FILES['file']['tmp_name'];
                    $tmp_name_00 = pathinfo($tmp_name_0);
                    $tmp_name = $tmp_name_00['filename'];

                    //ファイル名抽出
                    $title = $_FILES['file']['name'];

                    //拡張子抽出
                    $extension_0 = pathinfo($title);
                    $extension = $extension_0["extension"];

                    //拡張子の判定
                    if(isset($_POST['file'])){
                        if($extension == "jpg" || $extension == "JPG" || $extension == "jpeg"){
                            $extension = "jpeg";
                        }else if($extension == "png" || $extension == "PNG"){
                            $extension = "png";
                        }else if($extension == "gif" || $extension == " GIF"){
                            $extension = "gif";
                        }else if($extension == "mp4" || $extension == "MP4"){
                            $extension = "mp4";
                        }else{
                            echo "対応のファイル形式はjpeg,png,gif,mp4のみです".'</br>';
                        }
                    }

                    //保存先の指定
                    $destination = sprintf('%s/%s.%s'
                    , 'upfile'
                    , $tmp_name
                    , $extension
                    );

                    //専用ディレクトリへファイルを移動
                    move_uploaded_file($tmp_name_0, $destination);
                    
                    //書き込み保存
                    $sql = $conn->prepare("INSERT INTO bbs(name, comment, time, password, title, path, tmp_name)
                            VALUES(:name, :comment, :time, :inputpass, :title, :destination, :tmp_name)");
                    $sql->bindParam(':name', $name, PDO::PARAM_STR);
                    $sql->bindParam(':comment', $comment, PDO::PARAM_STR);
                    $sql->bindValue(':time', $time, PDO::PARAM_INT);
                    $sql->bindParam(':inputpass', $inputpass, PDO::PARAM_STR);
                    $sql->bindParam(':title', $title, PDO::PARAM_STR);
                    $sql->bindParam(':destination', $destination, PDO::PARAM_STR);
                    $sql->bindParam(':tmp_name', $tmp_name, PDO::PARAM_STR);
                    $sql->execute();
                }
            }elseif($_POST["state"] == "editmode"){
                //編集モードでの書き込み(更新)処理
                if($name   === "" ){
                    $err_msg1 = "名前を入力してください"; 
                    echo $err_msg1.'<br>';
                    echo '<br>';
                }elseif($comment  === "" ){
                    $err_msg2 = "コメントを入力してください";
                    echo $err_msg2.'<br>';
                    echo '<br>';
                }elseif($pass === ""){
                    $err_msg3 = "パスワードを入力してください";
                    echo $err_msg3.'<br>';
                    echo '<br>';
                }elseif($err_msg1 === "" && $err_msg2 ==="" && $err_msg3 ===""){    
                    //一時ファイル名の抽出
                    $tmp_name_0 = $_FILES['file']['tmp_name'];
                    $tmp_name_00 = pathinfo($tmp_name_0);
                    $tmp_name = $tmp_name_00['filename'];

                    //ファイル名抽出
                    $title = $_FILES['file']['name'];

                    //拡張子抽出
                    $extension_0 = pathinfo($title);
                    $extension = $extension_0["extension"];

                    //拡張子の判定
                    if(isset($_POST['file'])){
                        if($extension == "jpg" || $extension == "JPG" || $extension == "jpeg"){
                            $extension = "jpeg";
                        }else if($extension == "png" || $extension == "PNG"){
                            $extension = "png";
                        }else if($extension == "gif" || $extension == " GIF"){
                            $extension = "gif";
                        }else if($extension == "mp4" || $extension == "MP4"){
                            $extension = "mp4";
                        }else{
                            echo "対応のファイル形式はjpeg,png,gif,mp4のみです".'</br>';
                        }
                    }

                    //保存先の指定
                    $destination = sprintf('%s/%s.%s'
                    , 'upfile'
                    , $tmp_name
                    , $extension
                    );

                    //専用ディレクトリへファイルを移動
                    move_uploaded_file($tmp_name_0, $destination);
                    
                    //データベースの更新
                    $sql_2 = $conn->prepare("UPDATE bbs SET name = :name, comment = :comment, time = :time, password = :inputpass, title = :title, path = :destination, tmp_name = :tmp_name  WHERE number = :editnumber ");
                    $sql_2->bindParam(':name', $name, PDO::PARAM_INT);
                    $sql_2->bindParam(':comment', $comment, PDO::PARAM_INT);
                    $sql_2->bindValue(':time', $time, PDO::PARAM_INT);
                    $sql_2->bindParam(':inputpass', $inputpass, PDO::PARAM_INT);
                    $sql_2->bindParam(':title', $title, PDO::PARAM_INT);
                    $sql_2->bindParam(':destination', $destination, PDO::PARAM_INT);
                    $sql_2->bindParam(':tmp_name', $tmp_name, PDO::PARAM_INT);
                    $sql_2->bindParam(':editnumber', $enum, PDO::PARAM_INT);
                    $sql_2->execute();
                    
                }
            }

        }elseif(isset($_POST["btn2"]) ===  true){
            //削除処理
            if($deletepass === ""){ 
                $err_msg4 = "パスワードが入力されていません!".'<br>';
                echo $err_msg4;                   
            }elseif($err_msg4 === ""){
                $sql_delete = $conn->prepare("SELECT password FROM bbs WHERE number = :deletenumber;");
                $sql_delete->bindValue(':deletenumber', $deletenumber, PDO::PARAM_INT);
                $sql_delete->execute(); 
                $registeredpass = $sql_delete->fetch();

                if($deletepass === $registeredpass[0]){
                    $sql_3 = $conn->prepare("UPDATE bbs SET name = :name, comment = :comment, time = :time, title = :title, path = :destination WHERE number = :deletenumber ");
                    $sql_3->bindParam(':name', $deletemessage, PDO::PARAM_STR);
                    $sql_3->bindParam(':comment', $deletemessage, PDO::PARAM_STR);
                    $sql_3->bindValue(':time', $null, PDO::PARAM_INT);
                    $sql_3->bindParam(':title', $null, PDO::PARAM_STR);
                    $sql_3->bindParam(':destination', $null, PDO::PARAM_STR);
                    $sql_3->bindValue(':deletenumber', $deletenumber, PDO::PARAM_INT);
                    $sql_3->execute();
                    echo "書き込みは無事に削除されました".'<br>';
                    echo '<br>';

                }elseif($deletepass !== $registeredpass[0]){
                    echo "パスワードが間違っています!".'<br>';
                    echo '<br>';
                }
            }
        
            
        }elseif(isset($_POST["btn3"]) ===  true){
            //編集ボタンが押された場合の処理
            if($editpass === ""){ 
                $err_msg5 = "パスワードが入力されていません!".'<br>';
                echo $err_msg5;
            }elseif($err_msg5 === ""){
                $sql_edit = $conn->prepare("SELECT password FROM bbs WHERE number = :editnumber ");
                $sql_edit->bindValue(':editnumber', $editnumber, PDO::PARAM_INT);
                $sql_edit->execute();
                $registeredpass_2 = $sql_edit->fetch();

                if($editpass === $registeredpass_2[0]){
                    $get_name_0 = $conn->prepare("SELECT name FROM bbs WHERE number = :editnumber ");
                    $get_name_0->bindValue(':editnumber', $editnumber, PDO::PARAM_INT);
                    $get_name_0->execute();
                    $get_name = $get_name_0->fetch();

                    $get_comment_0 = $conn->prepare("SELECT comment FROM bbs WHERE number = '$editnumber' ");  
                    $get_comment_0->bindValue(':editnumber', $editnumber, PDO::PARAM_INT);
                    $get_comment_0->execute();
                    $get_comment = $get_comment_0->fetch();
                }elseif($editpass !== $registeredpass_2[0]){
                    echo "パスワードが間違っています!".'<br>';
                    echo '<br>';
                } 
            }
        }

        //読み込みと表示
        $stmt = $conn->prepare("SELECT number, name, comment, time, title, path FROM bbs ORDER BY number ASC"); 
        $stmt->execute();

        echo '<hr>';
        $result = $stmt->fetchall();
        foreach($result as $value){
            echo '書き込み番号:'.$value[0].'</br>';
            echo '名前:'.$value[1].'</br>';
            echo 'コメント:'.$value[2].'</br>';
            echo '書き込み時刻:'.$value[3].'</br>';
            if($value[5] !== "upfile/."){
                $extension2_0 = pathinfo($value[4]);
                $extension2 = $extension2_0["extension"];
                if($extension2 == "jpeg" || $extension2 == "png" || $extension2 == "gif"){
                    echo "<img src = \"$value[5]\">";
                }elseif($extension2 == "mp4"){
                    echo "<video width=320px height=240px src = \"$value[5]\" controls></video>";
                }
            }
            echo "<hr>";
        }

    }catch(PDOException $e){
        echo $sql . "<br>" . $e->getMessage();
    }
    
    $conn = null;       
    
    //テンプレート変数を代入
    $smarty->assign("get_name",$get_name);
    $smarty->assign("get_comment",$get_comment);
    $smarty->assign("editnumber",(int)$editnumber);

    //テンプレートを表示
    $smarty->display('bbs.tpl');

    $htmldata = ob_get_contents();
    ob_get_clean();
    echo $htmldata;
    return $htmldata;
?>

</body>
</html>
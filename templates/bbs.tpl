{* Smarty *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head>
    {php} if(isSmartPhone()) :{/php}
        <link rel="stylesheet" media="all" type="text/css" href="smartphone.css"/>
    {php} elseif(isMobilePhone()) :{/php}
        <link rel="stylesheet" media="all" type="text/css" href="mobilephone.css"/>
    {php} else :{/php}
        <link rel="stylesheet" media="all" type="text/css" href="pc.css"/>
    {php} endif; {/php}

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>簡易掲示板</title>
</head>
<body>
    <a href = "tmp_register.php">ユーザー登録はこちらのページから</a><br></br>
    <form action="session_delete.php" method="post">
        <input type="submit" value="ログアウト">
    </form>
    <br></br>

    <h1>書き込みフォーム</h1>
    <form action="bbs.php" method="post" enctype="multipart/form-data">
        {php} if(isset($_POST["btn3"]) and $editpass === $registeredpass_2[0] ):{/php}
            <h3>現在編集モードです</h3><br></br>
            <div>名前</div>
            <input type="text" name="name" value= {$get_name[0]}><br></br>
            <div>内容</div>
            <input type="text" name="comment" value= {$get_comment[0]}><br></br>
            <div>画像・動画</div>
            <input type="file" name="file" size="30"><br></br>
            <div>パスワード</div>
            <input type ="password" name="inputpass"><br></br>
            <input type="hidden" name="state" value ="editmode">
            <input type="hidden" name="enum" value = {$editnumber}><br></br>
            <input type="submit" name="btn1" value="書き込む">
        {php} else:{/php}
            <div>名前</div>
            <input type="text" name="name" value="{php} echo $_SESSION[NAME]; {/php}"><br></br>
            <div>内容</div>
            <input type="text" name="comment"><br></br>
            <div>画像・動画</div>
            <input type="file" name="file" size="30"><br></br>
            <div>パスワード</div>
            <input type ="password" name="inputpass"><br></br>
            <input type="submit" name="btn1" value="書き込む">
        {php} endif;{/php}
    </form>

    <h1>削除用フォーム</h1>
    <form action="bbs.php" method="post" onsubmit="return check()">
        <div>削除番号</div>
        <input type="text" name="deletenumber"><br></br>
        <div>パスワード</div>
        <input type ="password" name="deletepass" ><br></br>
        <input type="submit" name="btn2" value="削除する">
    </form>

    <h1>編集用フォーム</h1>
    <form action="bbs.php" method="post">
        <div>編集番号</div>
        <input type="text" name="editnumber"><br></br>
        <div>パスワード</div>
        <input type ="password" name="editpass" ><br></br>
        <input type="submit" name="btn3" value="編集する">
    </form>
    <br></br>

    <script language="JavaScript" type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script>
        function check(){
            if(window.confirm('送信してよろしいですか？')){ // 確認ダイアログを表示
                return true; // 「OK」時は送信を実行
            }
            else{ // 「キャンセル」時の処理
                window.alert('キャンセルされました'); // 警告ダイアログを表示
                return false; // 送信を中止
            }
        } 
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang = “ja”>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <title>crondelete</title>
</head>
<body>
<?php

   //データベースの変数定義
   $servername = '';
   $username = '';
   $password = '';
   $dbname = '';

   //変数定義
   $time_now_0 = date("Y/m/d H:i:s");

   try{
      //PDO
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
      $conn->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

      $stmt = $conn->prepare("SELECT name, id, pass, flag, time FROM userinfo ORDER BY time ASC"); 
      $stmt->execute();
      $result = $stmt->fetchall();

      foreach($result as $value){
         if($value[3] == 0){
            $time_now = strtotime($time_now_0);
            $time_regi = strtotime($value[4]);
            $timedif = ($time_now - $time_regi)/60/60;

            if($timedif >= 24){
               $name = NULL;
               $id = NULL;
               $pass = NULL;
               $sql_2 = $conn->prepare("UPDATE userinfo SET name = :name, id = :id, pass = :pass  WHERE id = :id_2 ");
               $sql_2->bindParam(':name', $name, PDO::PARAM_STR);
               $sql_2->bindParam(':id', $id, PDO::PARAM_STR);
               $sql_2->bindParam(':pass', $pass, PDO::PARAM_STR);
               $sql_2->bindParam(':id_2', $value[1], PDO::PARAM_STR);
               $sql_2->execute();
            }
         }
      }
   }catch(PDOException $e){
      echo $sql . "<br>" . $e->getMessage();
   }
   $conn = null;
?>
</body>
</html>
   
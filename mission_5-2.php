
<?php

//DB接続
	try{
		$dsn='mysql:dbname=データベース名;host=MYSQLホスト名;charset=utf8mb4';
		$user='ユーザー名';
		$password='パスワード';
		$pdo=new PDO($dsn,$user,$password,
			array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING,
				 PDO::ATTR_EMULATE_PREPARES => false));
		}
			catch (PDOException $e) {
			$error = $e->getMessage();
			}

//DB接続完了




//テーブル作成
	$sql="CREATE TABLE IF NOT EXISTS mission5"
	."("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "date DATETIME,"
	. "pass char(10)"
	.");";
	$stmt=$pdo->query($sql);
//テーブル作成完了



//データ入力

	if(!empty($_POST['name'])&&($_POST['comment']&&($_POST['pass']))){

		$name = $_POST['name'];
		$comment = $_POST['comment']; 
		$pass=$_POST['pass'];
		$date= new DateTime();
		$date=$date->format("Y/m/d H:i:s");


			echo $comment."を受け取りました";

		if(empty($_POST['editNO'])){//もし編集番号が選択されていなかったら


		//テーブルにデータ入力

			$sql="INSERT INTO mission5 (id, name, comment, date, pass ) SELECT COALESCE(MAX(id)+1,1), :name, :comment, :date, :pass from mission5"; 
			$stmt = $pdo -> prepare($sql);
			$stmt -> bindParam(':name', $name, PDO::PARAM_STR);
			$stmt  -> bindParam(':comment', $comment, PDO::PARAM_STR);
			$stmt  -> bindParam(':date', $date,  PDO::PARAM_STR);
			$stmt  -> bindParam(':pass', $pass, PDO::PARAM_STR);
			$stmt  -> execute();
		
		}
	}//データ入力完了

//データ編集機能
	if(!empty($_POST['editNO'])&&($_POST['pass'])){
		$editNO=$_POST['editNO'];
		$epass=$_POST['pass'];

		
		

				$sql = 'update mission5 set name=:name,comment=:comment where id=:id';
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':name', $name, PDO::PARAM_STR);
				$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
				$stmt->bindParam(':id', $editNO, PDO::PARAM_INT);
				$stmt->execute();

				

		}//データ編集完了


//削除番号取得機能
	if(!empty($_POST['deleten'])&&($_POST['delpass'])){

		$deleteid=$_POST['deleten'];
		$deletepass=$_POST['delpass'];

				$sql = 'select * from mission5 where id=:id ';//データの呼び出し
				$stmt = $pdo->prepare($sql);

				$stmt->bindParam(':id', $deleteid, PDO::PARAM_STR);
				$stmt->execute();
				$result = $stmt -> fetch(PDO::FETCH_ASSOC);//データの取得

				$delnumber = $result['id'];
				$delpass = $result['pass'];

//削除機能
			if($deletepass==$delpass){

				$sql = 'delete from mission5 where id=:deleteid';
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':deleteid', $deleteid, PDO::PARAM_INT);
				$stmt->execute();

			}
	}//削除終了




//編集選択機能
	if(!empty($_POST['edit'])&&($_POST['editpass'])){

		$editid=$_POST['edit'];
		$edpass=$_POST['editpass'];


		$sql = 'select * from mission5 where id=:id ';//データの呼び出し

		$stmt = $pdo->prepare($sql);

		$stmt->bindParam(':id', $editid, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt -> fetch(PDO::FETCH_ASSOC);//データの取得
	
			if($result['id']==$editid && $result['pass']==$edpass){

				$editnumber = $result['id'];
				$editname = $result['name'];
				$editcomment = $result['comment'];
				$editpass = $result['pass'];
				}
		
	}





?>




<html>
<head>
	<meta charset="utf-8">
	<title>5-1</title>
</head>

<form method="POST" action="mission_5-2.php">
[投稿]
<p><label>名前：</label><input type="text" name="name" 
	placeholder="名前" value="<?php if(!empty($editname)) {echo $editname;} ?>"></p>

<p><label>コメント：</label><input type="text"  
	name="comment"placeholder="コメント" value="<?php if(!empty($editcomment)) {echo $editcomment;} ?>">
<input type="submit" value="送信する"></p>


<p><label>パスワード：</label><input type="password"  
	name="pass"placeholder="password" value="<?php if(!empty($editpass)){echo $editpass;}?>"></p>

<p><input type="hidden" name="editNO"placeholder="編集番号" value="<?php if(!empty($editnumber)) {echo $editnumber;} ?>">
<input type = "hidden" name = "editnum" value ="編集"></p>

[削除]
<p><label>削除対象番号:</label><input type = "text" name = "deleten"  placeholder="削除対象番号" >
<input type="submit" name="delete"  value="削除" ></p>

<p><label>パスワード：</label><input type="password"  
	name="delpass"placeholder="password" > </p>

[編集]
<p><label>編集番号:<label><input type ="text" name="edit" placeholder="編集対象番号" >
<input type = "submit" name = "editnum" value ="編集"></p>

<p><label>パスワード：</label><input type="password"  
	name="editpass"placeholder="password"> </p>
</form>
</html>

<?php
echo"<br>";
echo "<strong>コメント一覧<br></strong>";
//データ表示
$sql = 'SELECT * FROM mission5';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['date'].',';
		echo $row['pass'].'<br>';
	echo "<hr>";
	}
?>
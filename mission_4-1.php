<?php
	$dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn,$user,$password);
//基本の接続

	$sql= "CREATE TABLE IF NOT EXISTS Board" //bbというテーブルがなかったら作成
	." ("
	. "id INT NOT NULL AUTO_INCREMENT,"
	. "name TEXT,"
	. "comment TEXT,"
	. "pass TEXT,"
	. "time CURRENT_TIMESTAMP,"
	. "primary key(id)"
	.");"; //idはnullを格納せず自動採番
	$stmt = $pdo->query($sql);

			$name = $_POST['name'];
			$comment = $_POST['comment'];
			$pass = $_POST['pass'];
			$ed_num = $_POST['ed_num'];

//フォーム1
	if(!empty($name)and($comment)and($pass)){
			if(empty($ed_num)){
					$sql = $pdo -> prepare("INSERT INTO Board (name, comment,pass,time) VALUES (:name,:comment,:pass,cast(now() as datetime))");
					$sql -> bindParam(':name', $name, PDO::PARAM_STR);
					$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
					$sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
					$sql -> execute();
					echo "新しい投稿をしました。";
				}else{
				//書き込みを上書き編集 これが働いてなさげ
					$nm = $_POST['name'];
					$kome = $_POST['comment'];
					$pasu = $_POST['pass'];
					$taimu = date("Y-m-d H:i:s");//timestamp使う方法あるのか
					$sql = "update Board set name='$nm', comment='$kome', pass='$pasu', time='$taimu' where id = $ed_num"; //updateで上書き
					$result = $pdo->query($sql);
					echo "投稿".$ed_num."を編集しました。";
				}
	}

//フォーム2
		$delete = $_POST['delete'];
		$del_pass = $_POST['del_pass'];
	if(!empty($delete)){
			$sql = "SELECT pass FROM Board WHERE id = $delete ";
			$plans = $pdo->query($sql); //該当パスワードの呼び出し
			$result = $plans -> fetch(PDO::FETCH_ASSOC); //取り出したものを仮想配列に変換
			foreach($result as $row){
				if($del_pass == $row){//パスワードが正しい時
					$sql = "delete from Board where id= $delete";
					$result = $pdo->query($sql);
					echo "投稿".$delete."を削除しました。";
				}else{
					echo "パスワードが違います。";
				}
			}
	}

//フォーム3
		$edit = $_POST['edit'];
		$ed_pass = $_POST['ed_pass'];
	if(!empty($edit)){
			$sql = "SELECT pass FROM Board WHERE id = $edit";
			$plans = $pdo->query($sql); //該当パスワードの呼び出し
			$result = $plans -> fetch(PDO::FETCH_ASSOC); //取り出したものを仮想配列に変換
		foreach($result as $row){
			if($ed_pass == $row){//パスワードが正しい時
				echo "投稿".$edit."を編集します。";
			 	//この後フォーム1に指定された内容を再表示する処理
				$editname = "SELECT name from Board where id = $edit";
				$enplans = $pdo->query($editname);//この時点では名前そのものは格納されていない
				$enresult = implode('',$enplans -> fetch(PDO::FETCH_ASSOC));//ここで名前そのものが入った配列になる それをさらに文字列に変換
				$editcomment = "SELECT comment FROM Board where id = $edit";
				$ecplans = $pdo->query($editcomment);
				$ecresult = implode('',$ecplans -> fetch(PDO::FETCH_ASSOC));
			}else{
				echo "パスワードが違います。";
			}
		}
	}

?>

<!DOCTYPE html>
<html lang = "ja">
	<head>
	<meta charset="UTF-8">
	</head>
	<body>
			<form action="mission_4-1.php" method="post">
				<input type="text" name="name" placeholder="名前" value= <?php echo $enresult; ?> >
					<!-- 入力フォーム(名前) -->
					<br />
				<input type="text" name="comment" "コメント" placeholder="コメント" value= <?php echo $ecresult; ?> >
					<!-入力フォーム(コメント) ->
				<input type="hidden" name="ed_num" value= <?php echo $edit; ?> ><br />
				<input type="text" name="pass" placeholder="パスワード">
					<!-入力フォーム(パスワード)->
				<input type="submit" name="submit"> <!-- 送信ボタン -->
					<br /><br />
				<input type="text" name="delete" placeholder="削除対象番号"><br />
				<input type="text" name="del_pass" placeholder="パスワード">
				<input type="submit" name="delete_sub" value="削除"><!-削除ボタン->
					<br /><br />
				<input type="text" name="edit" placeholder="編集対象番号"><br />
				<input type="text" name="ed_pass" placeholder="パスワード">
				<input type="submit" name="ed_sub" value="編集"><!-編集ボタン->
					<br />
			</form>
			<p>投稿</p>
	</body>
</html>

<?php //ブラウザに表示
	$sql = 'SELECT * FROM Board ORDER BY id ASC';
	$results = $pdo -> query($sql);
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].' ';
		echo $row['name'].' ';
		echo $row['time'].'<br>';
		echo '　'.$row['comment'].'<br>';
	}
?>

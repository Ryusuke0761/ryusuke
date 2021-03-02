<!--INSERT文：データの入力(データレコードの挿入)-->
<?php
$dsn=データベース名;
$user=ユーザー名;
$password=パスワード;
$pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));
$sql = "CREATE TABLE IF NOT EXISTS tbtest"
." ("
. "id INT AUTO_INCREMENT PRIMARY KEY,"
. "name char(32),"
. "comment TEXT,"
. "time TIMESTAMP"
.");";
$stmt = $pdo->query($sql);

$passcode="pass";#パスコード
$time=date("Y-m-d h:i:s");

if(isset($_POST["d_num"])){
    $d_num=$_POST["d_num"];
}
if(isset($_POST["e_num"])){
    $e_num=$_POST["e_num"];
}
if(isset($_POST["submit"])){
    $submit=$_POST["submit"];
}
if(isset($_POST["delete"])){
    $delete=$_POST["delete"];
}
if(isset($_POST["edit"])){
    $edit=$_POST["edit"];
}
#送信or編集
if(isset($submit)){
    if(!empty($_POST["name"])&&!empty($_POST["str"])){
        $name=$_POST["name"];
        $comment=$_POST["str"];
        if($passcode==$_POST["pass_tex"]){
            if(empty($e_num)){
                $sql=$pdo->prepare("INSERT INTO tbtest(name,comment,time)VALUES(:name,:comment,:time)");
                $sql->bindParam(":name",$name,PDO::PARAM_STR);
                $sql->bindParam(":comment",$comment,PDO::PARAM_STR);
                $sql->bindParam(":time",$time,PDO::PARAM_STR);                                               
                $sql->execute();
            }
            else{
                $id=$e_num;
                $sql = "UPDATE tbtest SET name=:name,comment=:comment WHERE id=:id";
                #prepare:SQL文の一部を変数のように記述しておき,その部分に当てはめる値を後から指定できる
                #引数に指定したSQl文をDBに対して発行する
                $stmt = $pdo->prepare($sql);
                #プレースホルダーに値をバインドさせる
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
        else{
            echo "パスワードが間違っています<br>";
        }
    }
    else{
        echo "文字列を入力してください<br>";
    }
}
#削除処理
if(isset($delete)){
    if($passcode==$_POST["pass_del"]){
        $id = $d_num;#削除するデータレコード
        #SQL実行時のデータベース命令文
        $sql = 'delete from tbtest where id=:id';
        $stmt = $pdo->prepare($sql);#引数に指定したSQL文をDb指定する
        #プレースホルダーに値をバインドする
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }   
}
#編集準備
if(isset($edit)){
    if(!empty($e_num)){
        if($passcode==$_POST["pass_ed"]){#パスワード
            //$id=$e_num;
            $sql="SELECT * FROM tbtest WHERE id='$e_num'";
            #SQL実行時のデータベース命令文
            $stmt=$pdo->query($sql);
            #ftechAll:結果セットに残っている全ての行を含む配列を返す
            $results=$stmt->fetch();
            
            $editName=$results["name"];
            $editText=$results["comment"];
            
        }
        else{
            echo "パスワードが間違っています<br>";
        }
    }
    else{
        echo "編集数字を入力してください<br>";
    }
}
?>
<form action="" method="post">
    <!--送信フォーム-->
    名前<br>
    <input type="text"name="name"value="<?=isset($editName) ? $editName:' ';?>"><br>
    コメント<br>
    <input type="text"name="str"value="<?=isset($editText) ? $editText:' ';?>"><br>
    <input type="text"name="pass_tex"placeholder="パスワード">
    <input type="submit"name="submit"><br><br>
    <!--削除フォーム-->
    <input type="number"name="d_num"placeholder="削除番号"><br>
    <input type="text"name="pass_del"placeholder="パスワード">
    <input type="submit"name="delete"value="削除"><br><br>
    <!--編集フォーム-->
    <input type="number"name="e_num"placeholder="編集番号"value="<?=$e_num?>"><br>
    <input type="text"name="pass_ed"placeholder="パスワード">
    <input type="submit"name="edit"value="編集"><br>
</form>

<?php
#データを選択
$sql="SELECT * FROM tbtest";
#SQL実行時のデータベース命令文
$stmt=$pdo->query($sql);
#ftechAll:結果セットに残っている全ての行を含む配列を返す
$results=$stmt->fetchAll();
foreach($results as $row){
    echo $row["id"].",";
    echo $row["name"].",";
    echo $row["comment"].",";
    echo $row["time"]."<br>";
    echo "<hr>";
}
?>
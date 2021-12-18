 <?php
header('Access-Control-Allow-Origin: *');
header('Vary: Origin');
require('./Files.php');

//var_dump($_POST);

$db = new MysqlDatabase();
$pdo = $db->getPDO();
//var_dump($db, $pdo);

if ($_GET['p'] === 'post'){
  $image = $_POST['pictureName'];
    var_dump($_POST, $_FILES);
    $Files = new Picture();
    $Files->post(
        $_POST['imageObject'], 
    $_POST['pictureName'], 
    $_POST['PostUserId'], 
    $_POST['title'],$_POST['content'],
    $_POST['PostUserName'],'articles');
   
}else if ($_GET['p'] === 'getPost'){
    $id = $_GET['id'];
    $res = $pdo->query("SELECT * FROM articles WHERE id = $id LIMIT 1")->fetch();
   echo json_encode($res);

}else if ($_GET['p'] === 'getPosts'){
    if (isset($_GET['userId']) && !empty($_GET['userId'])){
        $id = $_GET['userId'];
        $res = $pdo->query("SELECT * FROM articles WHERE userId = $id ORDER BY dateTable DESC")->fetchAll();
        echo json_encode($res);
    }else {
        $res = $pdo->query("SELECT * FROM articles ORDER BY dateTable DESC")->fetchAll();
        echo json_encode($res);
    }
   
}
else if ($_GET['p'] === 'getUser'){
    $res = $pdo->query("SELECT * FROM members  ORDER BY dateTable DESC")->fetchAll();
    echo json_encode($res);
}
else if ($_GET['p'] === 'getOneUser'){
    $id = $_GET['id'];
    $res = $pdo->query("SELECT * FROM members WHERE id = $id LIMIT 1")->fetch();
    echo json_encode($res);
}
 else if ($_GET['p'] === 'postUser'){
     $name = $_POST['first_name'];
     $password = $_POST['password'];

    $user = $pdo->query("SELECT * FROM members WHERE first_name LIKE '%{$name}%'  LIMIT 1")->fetch();
    if ($user){
        echo "Ce nom existe deja !";
    }else {
        $pdo->prepare("INSERT INTO members SET first_name = ?, last_name = ?,dateTable = NOW(), 
        country = ?, phone =?, password = ?, genre = ?")->execute([
            $name,
            $_POST['last_name'],
            243,
            $_POST['phone'],
            $password,
            $_POST['genre']
        ]);
        echo "Compte bien cree !";

    }

    
 }
 else if ($_GET['p'] === 'postChat'){
     $content = $_POST['content'];
     $sendId = $_POST['sendId'];
     $receveId = $_POST['receiveId'];
    $pdo->prepare("INSERT INTO chat SET content = ?, sendId = ?,receiveId = ?, dateTable = NOW()
    ")->execute([
        $content,$sendId,$receveId
    ]);
 } else if ($_GET['p'] === 'getChat'){
    $sendId = $_GET['sendId'];
    $receveId = $_GET['receiveId'];
  $res = $pdo->query("SELECT * FROM chat WHERE (sendId = $sendId AND receiveId = $receveId) 
                    OR (sendId = $receveId AND receiveId = $sendId)  ORDER BY dateTable
   ")->fetchAll();
   echo json_encode($res);
}else if ($_GET['p'] === 'login'){
     $first_name = $_POST['first_name'];
     $password = $_POST['password'];
     $res = $pdo->query("SELECT * FROM members WHERE first_name = '$first_name' AND password = '$password' LIMIT 1")->fetch();
 
    if ($res){
     $pdo->prepare("UPDATE members SET inLine = NOW()  WHERE id = ?")->execute([$res->id]);

        echo json_encode($res);
    }else  {
        echo "User not found";
    }
 }

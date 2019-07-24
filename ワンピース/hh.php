<?php
ini_set("display_errors", On);
error_reporting(E_ALL);
ini_set('log_errors','on');  //ログを取るか
ini_set('error_log','php.log');
session_start();


$monsters = array();



abstract class Creature{
    protected $name;
    protected $hp;
    protected $attackMin;
    protected $attackMax;

    public function setName($str){
      $this->name = $str;
    }
    public function getName(){
      return $this->name;
    }
    public function setHp($num){
      $this->hp = $num;
    }
    public function getHp(){
      return $this->hp;
    }

  }

class Human extends Creature{

    public function __construct($name, $hp, $attackMin, $attackMax){
        $this->name = $name;
        $this->hp = $hp;
        $this->attackMin = $attackMin;
        $this->attackMax = $attackMax;
    }

    public function attack($targetObj){
      $attackPoint = mt_rand($this->attackMin, $this->attackMax);
      $attackSelect = mt_rand(0,9);
      if($attackSelect <= 2){
        History::set('JET銃!!');
        $attackPoint = $attackPoint * 2;
        $attackPoint = (int)$attackPoint;
        }elseif($attackSelect == 9){
          History::set('ギガントライフル!!');
          $attackPoint = $attackPoint * 4;
          $attackPoint = (int)$attackPoint;
      }else{
        History::set('ゴムゴムのバズーカ!!');
      }

      $targetObj->setHp($targetObj->getHp()-$attackPoint);
      History::set($attackPoint.'ポイントのダメージ！');
    }
}

class Monster extends Creature{
    protected $img;
    public function __construct($name, $hp, $img, $attackMin, $attackMax){
        $this->name =$name;
        $this->hp = $hp;
        $this->img = $img;
        $this->attackMin = $attackMin;
        $this->attackMax = $attackMax;
    }
    public function getImg(){
        return $this->img;
    }
}
class History {
    public static function set($str){
        if(empty($_SESSION['history'])) $_SESSION['History'] = "";
        $_SESSION['history'] .= $str."<br>";
    }
    public static function clear(){
        unset($_SESSION['history']);
    }
}

$human = new Human('ルフィ', 30000, 1000, 3000);
$monsters[] = new Monster( 'クロコダイル', 8100, 'img/monster01.png',1000 , 10000);
$monsters[] = new Monster( 'クロ', 1600, 'img/monster02.png', 200, 700);
$monsters[] = new Monster( 'クリーク', 1700, 'img/monster03.png', 300, 1000 );
$monsters[] = new Monster( 'エネル', 10000, 'img/monster04.png', 0, 0);
$monsters[] = new Monster( 'アーロン', 2000, 'img/monster05.png', 500, 1500 );
$monsters[] = new Monster( 'バギー', 1500, 'img/monster06.png', 150, 450 );
$monsters[] = new Monster( '黄猿', 150000, 'img/monster07.png', 10000, 30000 );
$monsters[] = new Monster( 'ルッチ', 30000, 'img/monster08.png', 2000, 5000);

function createMonster(){
    global $monsters;
    $monster = $monsters[mt_rand(0, 7)];
    $monster2 = $monsters[mt_rand(0, 7)];
    History::set($monster->getName());
    History::set($monster2->getName());
    $_SESSION['monster'] = $monster;
    $_SESSION['monster2'] = $monster2;
}
function createHuman(){
    global $human;
      $_SESSION['human'] =  $human;
}
function init(){
    History::clear();
    History::set('初期化します');

    createHuman();
    createMonster();
}
function gameOver(){
    $_SESSION = array();
}



if(!empty($_POST)){
    $attackFlg = (!empty($_POST['attack'])) ? true : false;
    $startFlg = (!empty($_POST['start'])) ? true : false;
    error_log('POSTされた');

    if($startFlg){
        History::set('ゲームスターと');
        init();
    }else{

        if($attackFlg){


            History::set($_SESSION['human']->getName().'の攻撃');
            $att = mt_rand(0,2)
            if($att == 0){
                $_SESSION['human']->attack($_SESSION['monster']);

                History::set($_SESSION['monster']->getName().'の攻撃');
                $_SESSION['monster']->attack($_SESSION['human']);

            }elseif($att == 1){
                $_SESSION['human']->attack($_SESSION['monster2']);
                History::set($_SESSION['monster2']->getName().'の攻撃');
                $_SESSION['monster2']->attack($_SESSION['human']);

            }else{

            }

            if($_SESSION['human']->getHp() <= 0){
                gameOver();
            }else{

                if($_SESSION['monster']->getHp() <= 0){
                    History::set($_SESSION['monster']->getName().'を倒した');
                    createMonster();

                }
            }
        }else{
            History::set('逃げた');
            createMonster();
        }
    }
    $_POST = array();
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ホームページのタイトル</title>
    <style>
    body{
        margin: 0 auto;
        padding: 150px;
        width: 25%;
        background: #fbfbfa;
        color: white;
    }
    h1{ color: white; font-size: 20px; text-align: center;}
    h2{ color: white; font-size: 16px; text-align: center;}
    form{
        overflow: hidden;
    }
    input[type="text"]{
        color: #545454;
        height: 60px;
        width: 100%;
        padding: 5px 10px;
        font-size: 16px;
        display: block;
        margin-bottom: 10px;
        box-sizing: border-box;
    }
    input[type="password"]{
        color: #545454;
        height: 60px;
        width: 100%;
        padding: 5px 10px;
        font-size: 16px;
        display: block;
        margin-bottom: 10px;
        box-sizing: border-box;
    }
    input[type="submit"]{
        border: none;
        padding: 15px 30px;
        margin-bottom: 15px;
        background: black;
        color: white;
        float: right;
    }
    input[type="submit"]:hover{
        background: #3d3938;
        cursor: pointer;
    }
    a{
        color: #545454;
        display: block;
    }
    a:hover{
        text-decoration: none;
    }
    img {
        float: left;
    }
    </style>
</head>
<body>
    <h1 style="text-align:center; color:#333;">ワンピース！</h1>
    <div style="background:black; padding:15px; position:relative;">
        <?php if(empty($_SESSION)){ ?>
            <h2 style="margin-top:60px;">START?</h2>
            <form method="post">
                <input type="submit" name="start" value="▶︎ゲームスタート">
            </form>
        <?php }else{ ?>
            <div style="text-align: center;">
            <h2 style='display:inline; margin:10px;'><?php echo $_SESSION['monster']->getName(); ?></h2>
            <h2 style='display:inline; margin-left:115px;'><?php echo $_SESSION['monster2']->getName(); ?></h2>
            </div>
            <div style="height: 150px;">
                <img class="m1" src="<?php echo $_SESSION['monster']->getImg(); ?>" style="width:150px; height:150px; margin-right: 30px; display:block;">
                <img class="m2" src="<?php echo $_SESSION['monster2']->getImg(); ?>" style="width:150px; height:150px; display:block;">
            </div>
            <div style="text-align: center;">
            <p style="font-size:8px; margin-right: 45px; display:inline"><?php echo $_SESSION['monster']->getName()?>のHP:<?php echo $_SESSION['monster']->getHp(); ?></p>
            <p style="font-size:8px; margin-left: 60px; display:inline"><?php echo $_SESSION['monster2']->getName()?>のHP:<?php echo $_SESSION['monster2']->getHp(); ?></p>
            </div>
            <p style="text-align:center; margin-bottom: 0px;">ルフィのHP:<?php echo $_SESSION['human']->getHp(); ?></p>
            <form style="margin-bottom: 0;" method="post">
                <input type="submit" name="attack" value="▶︎攻撃">
                <input type="submit" name="escape" value="▶︎逃げ">
                <input type="submit" name="start" value="▶︎リスタート">
            </form>
        <?php } ?>
        <div style="position:absolute; right:-350px; top:0; color:black; width: 300px;">
            <p><?php echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : ''; ?></p>
        </div>
    </div>

</body>
</html>

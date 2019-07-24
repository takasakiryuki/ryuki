<?php
ini_set("display_errors", On);
error_reporting(E_ALL);
ini_set('log_errors','on');  //ログを取るか
ini_set('error_log','php.log');  //ログの出力ファイルを指定
session_start(); //セッション使う

// モンスター達格納用
$monsters = array();

// 抽象クラス（生き物クラス）
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
  public function attack($targetObj){
    $attackPoint = mt_rand($this->attackMin, $this->attackMax);
    if(!mt_rand(0,9)){ //10分の1の確率でクリティカル
      $attackPoint = $attackPoint * 1.5;
      $attackPoint = (int)$attackPoint;
      History::set($this->getName().'のクリティカルヒット!!');
    }
    $targetObj->setHp($targetObj->getHp()-$attackPoint);
    History::set($attackPoint.'ポイントのダメージ！');
  }
  public function att($targetObj){
    $attackPoint = mt_rand($this->attackMin, $this->attackMax);
    $rand = mt_rand(0,9);
    if($rand < 4){ //10分の1の確率でクリティカル
      $attackPoint = (int)$attackPoint;
      History::set('ゴムゴムのピストル!!');
    }elseif($rand > 6){ //10分の1の確率でクリティカル
      $attackPoint = (int)$attackPoint;
      $attackPoint *= 1.5;
      History::set('ゴムゴムのジェットピストル!!');
    }else{ //10分の1の確率でクリティカル
        $attackPoint = (int)$attackPoint;
        $attackPoint *= 3;
        History::set('ゴムゴムのギガントピストル!!');
      }
     if($_SESSION['monster']->getName() === 'エネル'){
         $attackPoint *= 10;
         $targetObj->setHp($targetObj->getHp()-$attackPoint);
         History::set($attackPoint.'ポイントのダメージ！');
     }else{
    $targetObj->setHp($targetObj->getHp()-$attackPoint);
    History::set($attackPoint.'ポイントのダメージ！');
    }
  }
}
// 人クラス
class Human extends Creature{
  public function __construct($name, $hp, $attackMin, $attackMax) {
    $this->name = $name;
    $this->hp = $hp;
    $this->attackMin = $attackMin;
    $this->attackMax = $attackMax;
  }

}
// モンスタークラス
class Monster extends Creature{
  // プロパティ
  protected $img;
  // コンストラクタ
  public function __construct($name, $hp, $img, $attackMin, $attackMax) {
    $this->name = $name;
    $this->hp = $hp;
    $this->img = $img;
    $this->attackMin = $attackMin;
    $this->attackMax = $attackMax;
  }
  // ゲッター
  public function getImg(){
    return $this->img;
  }

}

// 履歴管理クラス（インスタンス化して複数に増殖させる必要性がないクラスなので、staticにする）
class History{
  public static function set($str){
    // セッションhistoryが作られてなければ作る

    if(empty($_SESSION['history'])) $_SESSION['history'] = '';
    // 文字列をセッションhistoryへ格納
    $_SESSION['history'] .= $str.'<br>';
  }
  public static function clear(){
    unset($_SESSION['history']);
  }
}

// インスタンス生成
$human = new Human('ルフィ', 30000, 200, 700);
$monsters[] = new Monster( 'クロコダイル', 8100, 'img/monster01.png', 300, 1000 );
$monsters[] = new Monster( 'クロ', 1600, 'img/monster02.png', 50, 100);
$monsters[] = new Monster( 'ドンクリーク', 1700, 'img/monster03.png', 70, 150 );
$monsters[] = new Monster( 'エネル', 50000, 'img/monster04.png', 0, 100);
$monsters[] = new Monster( 'アーロン', 2000, 'img/monster05.png', 100, 200 );
$monsters[] = new Monster( 'バギー', 1500, 'img/monster06.png', 30, 80 );
$monsters[] = new Monster( '黄猿', 200000, 'img/monster07.png', 15000, 50000 );
$monsters[] = new Monster( 'ルッチ', 30000, 'img/monster08.png', 300, 800 );

function createMonster(){
  global $monsters;
  $monster =  $monsters[mt_rand(0, 7)];
  $_SESSION['monster'] =  $monster;
}
function createHuman(){
  global $human;
  $_SESSION['human'] =  $human;
}
function init(){
  History::clear();
  $_SESSION['knockDownCount'] = 0;
  createHuman();
  createMonster();
}
function gameOver(){
  $_SESSION = array();

}


//1.post送信されていた場合
if(!empty($_POST)){
  $attackFlg = (!empty($_POST['attack'])) ? true : false;
  $startFlg = (!empty($_POST['start'])) ? true : false;
  error_log('POSTされた！');

  if($startFlg){
    History::set('ゲームスタート！');
    init();
  }else{
    // 攻撃するを押した場合
    if($attackFlg){
    History::clear();
      // モンスターに攻撃を与える
      History::set($_SESSION['human']->getName().'の攻撃！');
      $_SESSION['human']->att($_SESSION['monster']);


      // モンスターが攻撃をする
      History::set($_SESSION['monster']->getName().'の攻撃！');
      $_SESSION['monster']->attack($_SESSION['human']);


      // 自分のhpが0以下になったらゲームオーバー
      if($_SESSION['human']->getHp() <= 0){
        gameOver();
      }else{
        // hpが0以下になったら、別のモンスターを出現させる
        if($_SESSION['monster']->getHp() <= 0){
          History::set($_SESSION['monster']->getName().'を倒した！');
          createMonster();
          $_SESSION['knockDownCount'] = $_SESSION['knockDownCount']+1;
        }
      }
    }else{ //逃げるを押した場合
        if(!mt_rand(0,2)){
            History::set('逃げられなかった！');
            History::set($_SESSION['monster']->getName().'の攻撃！');
            $_SESSION['monster']->attack($_SESSION['human']);
            if($_SESSION['human']->getHp() <= 0){
              gameOver();
            }

        }else{
            History::set('逃げた！');
            createMonster();
        }
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
	    	text-align:center;
    	}
        input[name="attack"]{
	    	border: none;
	    	padding: 15px 30px;
	    	margin-bottom: 5px;
	    	background: black;
	    	color: white;
	    	float: left;
            margin-left: 20px;
    	}
        input[name="escape"]{
            border: none;
            padding: 15px 30px;
            margin-bottom: 5px;
            background: black;
            color: white;
            float: right;
            margin-right: 20px;
        }
        input[name="start"]{
            border: none;
            padding: 15px 30px;
            margin-bottom: 15px;
            background: black;
            color: white;
            float: left;
            margin-left: 80px;
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
    </style>
  </head>
  <body>
   <h1 style="text-align:center; color:#333;">ワンピース!!</h1>
    <div style="background:black; padding:15px; position:relative;">
      <?php if(empty($_SESSION)){ ?>
        <h2 style="margin-top:60px;">GAME START?</h2>
        <form method="post">
          <input type="submit" name="start" value="▶ゲームスタート">
        </form>
      <?php }else{ ?>
        <h2><?php echo $_SESSION['monster']->getName().'が現れた!!'; ?></h2>
        <div style="height: 150px;">
          <img src="<?php echo $_SESSION['monster']->getImg(); ?>" style="width:120px; height:150px; margin:40px auto 0 auto; display:block;">
        </div>
        <p style="font-size:14px; text-align:center;"><?= $_SESSION['monster']->getName() ?>のHP：<?php echo $_SESSION['monster']->getHp(); ?></p>
        <p style="margin-top: 30px; font-size:14px; text-align:center;">ルフィの残りHP：<?php echo $_SESSION['human']->getHp(); ?></p>
        <form method="post">
          <input style="font-size:14px; text-align:center;" type="submit" name="attack" value="▶攻撃する">
          <input style="font-size:14px; text-align:center;" type="submit" name="escape" value="▶逃げる">
          <input style="font-size:14px; text-align:center;" type="submit" name="start" value="▶ゲームリスタート">
        </form>
      <?php } ?>
      <div style="position:absolute; right:-350px; top:0; color:black; width: 300px;">
        <p><?php echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : ''; ?></p>
      </div>
    </div>

  </body>
</html>

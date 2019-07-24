<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    </head>
    <body>
    <center>
        <font size="6" >High&Lowゲーム</font>

        <?php
            $leftCard = $_POST['leftCard'];
            $select = $_POST['select'];

            $cards=
             array("JK.png","01.png","02.png","03.png","04.png","05.png","06.png","7.png","08.png","09.png","10.png","11.png","12.png","13.png");

            echo '<hr>';
            $rightCard = mt_rand(0,13);

            echo '<img src="../cards/',$cards[$leftCard],'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            echo '<img src="../cards/',$cards[$rightCard],'">';
            echo '<br>';

            echo "「",$select,"」を選択しました。";

            $result = "";

            if($leftCard < $rightCard){
                $result = "High";
            }elseif($leftCard > $rightCard){
                $result = "Low";
            }else{
                $result = $select;
            }

            echo '<br>';
            if($select == $result){
                echo "You Win!";
            }else{
                echo "You Lose...!";
            }
            echo '<br>';

        ?>
        <a href="index.php">もう一度挑戦</a>
    </center>
    </body>
</html>

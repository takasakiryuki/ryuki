<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    </head>
    <body>
    <center>
        <font size="6" >High&Lowゲーム</font>
    <form action="highAndLowS07.php" method="post">
        <?php
            $cards = 
            array("JK.png","01.png","02.png","03.png","04.png","05.png","06.png","7.png","08.png","09.png","10.png","11.png","12.png","13.png");
            
            echo '<hr>';
            $leftCard = mt_rand(0,13);
            echo '<img src="../cards/',$cards[$leftCard],'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            echo '<img src="../cards/bg.png">';
            echo '<br>';
        ?>
            
        <input type="radio" name="select" value="High">High
        <input type="radio" name="select" value="Low" style="margin-left: 60px;">Low
        <br>
        <input type="submit" value="決定" style="width:70px;">
        <input type="hidden" name="leftCard" value="<?= $leftCard;?>">        
        
    </form>
    </center>
    </body>
</html>
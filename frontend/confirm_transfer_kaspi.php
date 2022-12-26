<?php	
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "final";

            $my_user = array();

            $conn = mysqli_connect($servername, $username, $password, $dbname);
           
            if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
            }
            
            try { 
                $sql = "SELECT users.first_name, 
                users.last_name,
                users.card_account
                FROM users
                INNER JOIN my_user
                ON my_user.user_id = users.id AND my_user.id = 1";
               $result = mysqli_query($conn, $sql); 
            } catch (mysqli_sql_exception $e) { 
               var_dump($e);
               exit; 
            } 
            
            while ($row = mysqli_fetch_array($result)) {
                $my_user['card_account'] = $row['card_account'];
                $my_user['first_name'] = $row['first_name'];
                $my_user['last_name'] = $row['last_name'];
             }

            $total = $_POST['total'];
            $cardnumber = $_POST['cardnumber'];
            $card_account = $my_user['card_account'];

            try { 
                $sql = "SELECT id,
                first_name, 
                last_name,
                card_account
                FROM users
                WHERE card_number = $cardnumber";
               $result = mysqli_query($conn, $sql); 
            } catch (mysqli_sql_exception $e) { 
               var_dump($e);
               exit; 
            } 
            
            $recipient_card = array();

            while ($row = mysqli_fetch_array($result)) {
                $recipient_card['id'] = $row['id'];
                $recipient_card['card_account'] = $row['card_account'];
                $recipient_card['first_name'] = $row['first_name'];
                $recipient_card['last_name'] = $row['last_name'];
             }

            $notsuccess = empty($total) || empty($cardnumber);

            function card($cardnumber){
                $pattern = "/^\d+$/";
                if (!preg_match($pattern, $cardnumber)) return False;
                if (strlen($cardnumber)!=16) return False;
                if ($cardnumber[0]!=4) return False;
                $sum = 0;
                for ($i = 0;$i < 16; $i++) {
                    if (($i % 2) == 0) {
                        $val = $cardnumber[$i]*2;
                        if ($val > 9)  $val -= 9;
                    } else {
                        $val = $cardnumber[$i] ;
                    }
                    $sum += $val;
                }
                return (($sum % 10) == 0);
            }

    if ($notsuccess){
    $ans='<h1>Sorry</h1>
			<p>You didn\'t fill out the form completely.  
            <a href="transfer_to_kaspi_card.php">Try again?</a></p>';
    }else
    if (!card($cardnumber)){
        $ans='<h1>Sorry</h1>
                <p>You didn\'t provide a valid card number.  
                <a href="transfer_to_kaspi_card.php">Try again?</a></p>';
    }else
        if (empty($recipient_card)){
            $ans='<h1>Sorry</h1>
            <p>You didn\'t provide a Kaspi card number.  
            <a href="transfer_to_kaspi_card.php">Try again?</a></p>';
        }
    else
        if ($recipient_card['card_account'] < $total){
        $ans='<h1>Sorry</h1>
                <p>You have insufficient funds.  
                <a href="transfer_to_kaspi_card.php">Try again?</a></p>';
        }
        else{
        session_start();
       
        $first_name = $recipient_card['first_name'];
        $last_name = $recipient_card['last_name'];
        $id = $recipient_card['id'];
        $_SESSION['transfer_amount'] = $total;

        $ans = "<div class=\"card white\">
            <img src=\"./image/gold.png\" align=\"left\">
            <span class=\"top-text\">Kaspi Gold</span>
            <span class=\"sum\">$card_account ₸</span>
        </div>

        <div class=\"card white\">
            <img src=\"./image/gold.png\" align=\"left\">
            <span class=\"top-text\">$first_name $last_name</span>
        </div>
        
        &nbsp;

        <div class=\"card white gray-color\">
            <span class=\"top-text\">Сумма перевода</span>
            <span class=\"sum\">$total ₸</span>
        </div>
        <div class=\"card white gray-color\">
            <span class=\"top-text\">Комиссия</span>
            <span class=\"sum\">0 ₸</span>
        </div>
        <div class=\"card white gray-color\">
            <span class=\"top-text\">Сумма списания</span>
            <span class=\"sum\">$total ₸</span>
        </div>

        &nbsp;

       <a href=\"success_transfer.php?id=$id\"><div id=\"button\">Подтвердить и перевести</div></a>";}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/Anel & Manshuk/confirm.css">
    <title>Подтверждение</title>
</head>
<body>
    <div id="head">
        Клиенту Kaspi
    </div>
    <div id="main">
        <?php
        echo $ans;
        ?>
    </div>
</body>
</html>
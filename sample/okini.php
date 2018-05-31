<?php

require_once dirname(__FILE__).'/../autoload.php';

require_once dirname(__FILE__).'/config.php';
require_once dirname(__FILE__).'/helper.php';

//require_once '/func.php';
//var_dump($_POST);
$botan = "";
if(isset($_POST)==true)
{
	//どのボタンを押したかチェック
	for($i=0;$i<100;$i++)
		{
			if(isset($_POST['btn_'.$i])==true)
			{
				$botan = $i;
/*					  	print '<br>';
        print '$botan:'.$botan;
        print '<br>';
*/
				break;
			}		
		}
}

//var_dump($botan);
//$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
//$ac_code = isset($_POST['ac_code']) ? $_POST['ac_code'] : 1;

$response = null;
$keyword  = "";
$page     = 1;
if (isset($_POST['keyword'])) {
    $keyword   = $_POST['keyword'];
    $page      = isset($_POST['page']) ? $_POST['page'] : 1;

    // Clientインスタンスを生成 Make client instance
    $rwsclient = new RakutenRws_Client();
    // アプリIDをセット Set Application ID
    $rwsclient->setApplicationId(1031677992385383126);
    // アフィリエイトIDをセット (任意) Set Affiliate ID (Optional)
    $rwsclient->setAffiliateId(RAKUTEN_APP_AFFILITE_ID);

    // プロキシの設定が必要な場合は、ここで設定します。
    // If you want to set proxy, please set here.
    // $rwsclient->setProxy('proxy');

    // 楽天市場商品検索API2 で商品を検索します
    // Search by IchibaItemSearch (http://webservice.rakuten.co.jp/api/ichibaitemsearch/)
    $response = $rwsclient->execute('IchibaItemSearch', array(
        'keyword' => $keyword,
        'page'    => $page,
        'hits'    => 9
    ));
}

?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Rakuten Web Service SDK - Sample</title>
<link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
<header>
<h1><a href="index.php">Rakuten Web Service SDK お気に入り一覧</a></h1>
</header>





<?php 
$okis = array();
if ($response && $response->isOk()): ?>



<?php
 $i = 0;
 $ites = array();
foreach ($response as $item): 
	$ites[$i]['affiliateUrl'] = $item['affiliateUrl'];
	$ites[$i]['itemName'] = $item['itemName'];
	$ites[$i]['imageUrl'] = $item['smallImageUrls'][0]['imageUrl'];
	$ites[$i]['itemPrice'] = $item['itemPrice'];
	$ites[$i]['itemCaption'] = $item['itemCaption'];

if ($i == 1){
	//var_dump($item['itemName']);
	//var_dump($item['itemUrl']);
	//$item->name = $rws_item['Item']['itemName'];
                //$item->url = $rws_item['Item']['itemUrl'];
}
$i += 1;

 endforeach; 
 
 
//*************************************
//お気に入り
//2. DB接続します


require_once __DIR__ . '../../../conf/database_conf.php';
					
					  
					# MySQLデータベースに接続します。
					  $db = new PDO($dsn, $dbUser, $dbPass);
					  
			  
					  
$sql = 'INSERT gs_bm_table(id,book_name,book_url,affiliateurl,imageurl,itemprice,book_coment,datetime) VALUES (NULL,:book_name,:book_url,:affiliateurl,:imageurl,:itemprice,:book_coment,sysdate() )';

$prepare = $db->prepare($sql);

								  $prepare->bindValue(':book_name', $ites[$botan]['itemName'], PDO::PARAM_STR);
								  $prepare->bindValue(':book_url', $ites[$botan]['affiliateUrl'], PDO::PARAM_STR);
								  $prepare->bindValue(':affiliateurl', $ites[$botan]['affiliateUrl'], PDO::PARAM_STR);
								  $prepare->bindValue(':imageurl', $ites[$botan]['imageUrl'], PDO::PARAM_STR);
								  $prepare->bindValue(':itemprice', $ites[$botan]['itemPrice'], PDO::PARAM_STR);
								  $prepare->bindValue(':book_coment', $ites[$botan]['itemCaption'], PDO::PARAM_STR);
								  
								  
$prepare->execute();

					  
 
 
 //$i = 1;
 //var_dump($ites[$i]);
//var_dump($ites[$i]);
$okis[0]= $ites[$i];
//var_dump($okis);
endif; ?>
<?php 

//***************************
//お気に入りデータを呼び出し

	
$sql='SELECT * FROM gs_bm_table';
	$prepare = $db->prepare($sql);
	$prepare->execute();					  
					  
					  
					  
$result = $prepare->fetch(PDO::FETCH_ASSOC);
	
	//$prepare = null;


//３．データ表示
$view="";
if($result==false) {
    //execute（SQL実行時にエラーがある場合）
  $error = $prepare->errorInfo();
  exit("ErrorQuery:".$error[2]); //配列index[2]にエラーコメントあり 

}else{
?>



<div id="itemarea">

<ul id="itemlist">
<?php	
	
	
  //Selectデータの数だけ自動でループしてくれる
  //FETCH_ASSOC=http://php.net/manual/ja/pdostatement.fetch.php
  while( $result = $prepare->fetch(PDO::FETCH_ASSOC)){ 
    //$view  .= '<p>';           
	//$view  .= $result["indate"] ."：". $result["name"] ;           
	//$view .= '</p>'; 
	//var_dump($result);
?>	
	<li class="item">

<a href="<?php echo h($result['affiliateurl']) ?>" class="itemname" title="<?php echo h($result['book_name']) ?>">
<?php echo h(mb_strimwidth($result['book_name'], 0, 80, '...', 'UTF-8')) ?></a>

<ul>
<?php if (!empty($result['imageurl'])): ?>
<li class="image"><img src="<?php echo h($result['imageurl']) ?>"></li>
<?php endif; ?>

<li class="price"><?php echo h(number_format($result['itemprice'])) ?>円</li>
<li class="description"><?php echo h($result['book_coment']) ?></li>
</ul>

</li>
<?php
  }




}			

 ?>













</body>
</html>

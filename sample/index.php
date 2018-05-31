<?php

require_once dirname(__FILE__).'/../autoload.php';

require_once dirname(__FILE__).'/config.php';
require_once dirname(__FILE__).'/helper.php';

//require_once '/func.php';

$response = null;
$keyword  = "";
$page     = 1;
if (isset($_GET['keyword'])) {
    $keyword   = $_GET['keyword'];
    $page      = isset($_GET['page']) ? $_GET['page'] : 1;

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
<title>Rakuten Web Service SDK</title>
<link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
<header>
<h1><a href="index.php">Rakuten Web Service SDK</a></h1>
</header>

<nav class="search-form">

<form action="index.php" method="GET">
<input id="keyword" class="keyword" name="keyword" type="text" value="<?php echo h($keyword) ?>">
<input type="submit" class="search-button" value="検索">
</form>
</nav>



<?php if ($response && $response->isOk()): ?>

<div class="pager"><?php echo $pager = pager(
    (int)$page,
    (int)$response['pageCount'],
    '?keyword=%s&amp;page=%d',
    $keyword
) ?></div>

<div id="itemarea">
<form action="okini.php" method="post">
<ul id="itemlist">
<?php
 $i = 0;
 $ites = array();
foreach ($response as $item): 
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

?>
<li class="item">

<a href="<?php echo h($item['affiliateUrl']) ?>" class="itemname" title="<?php echo h($item['itemName']) ?>">
<?php echo h(mb_strimwidth($item['itemName'], 0, 80, '...', 'UTF-8')) ?></a>

<ul>
<?php if (!empty($item['smallImageUrls'][0]['imageUrl'])): ?>
<li class="image"><img src="<?php echo h($item['smallImageUrls'][0]['imageUrl']) ?>"></li>
<?php endif; ?>
<li class=""><button class="button" type="submit" name ="btn_<?php echo $i;?>">お気に入りボタン</button>
</li>
<li class="price"><?php echo h(number_format($item['itemPrice'])) ?>円</li>
<li class="description"><?php echo h($item['itemCaption']) ?></li>
</ul>

</li>
<?php endforeach; 
//var_dump($ites);
?>
</ul>
<input type="hidden" name="keyword" value="<?php echo $keyword;?>">
<input type="hidden" name="page" value="<?php echo $page;?>">

</form>
</div>
<div class="pager"><?php echo $pager ?></div>
<?php endif; ?>

</body>
</html>

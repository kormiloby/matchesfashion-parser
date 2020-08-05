<?php
error_reporting(E_ALL);

include 'vendor/autoload.php';

use Clue\React\Buzz\Browser;
use Clue\React\Socks\Client as SocksClient;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;
use React\Socket\Connector;
use React\Filesystem\Filesystem;
use Matchesfashion\Scraper;
use Matchesfashion\Builder\CategoryUrlBuilder;
use Matchesfashion\Builder\SubCategoryUrlBuilder;
use Matchesfashion\Builder\ItemBuilder;
use Matchesfashion\Printer\JsonFilePrinter;

$headers = [
  'cookie' => 'SESSION_TID=KDBITX0B39EOZ-17BILZZ; plpLayoutMobile=2; plpLayoutTablet=2; plpLayoutDesktop=3; plpLayoutLargeDesktop=4; _pxhd=0cfc57b2c3c8e7205b808d40acfbd689a12cfdcdc40b3aa4310907dcfac06cc0:207752c1-d340-11ea-a965-81422cf9161e; sizeTaxonomy=""; gender=mens; loggedIn=false; saleRegion=ROW; _dy_csc_ses=t; _dy_c_exps=; _dycnst=dg; AMCVS_62C33A485B0EB69A0A495D19%40AdobeOrg=1; _ga=GA1.2.945445821.1596278090; _gid=GA1.2.1939215135.1596278090; s_cc=true; _dyid=-5093570872026935207; _dyjsession=e64060d237cc73ec6069d5d30e721a2e; _dycst=dk.l.c.ws.; _dy_c_att_exps=; _dyid_server=-5093570872026935207; cb-enabled=enabled; cb-shown=true; _fbp=fb.1.1596278163000.726853539; _cs_c=0; rskxRunCookie=0; rCookie=jx2alh9fngssdr0hb9xk5dkdbit19l; _gcl_au=1.1.795096869.1596278170; _pin_unauth=dWlkPU5qVmpZekE0WkRNdE56UXhaUzAwT0RKbExXSTJPR1l0TWpFMk1ERXpOVE5oWkdVMQ; _pxvid=207752c1-d340-11ea-a965-81422cf9161e; country=RUS; language=en; billingCurrency=USD; indicativeCurrency=""; fsm_uid=713b23bf-115b-44e9-fc92-164d9998dab3; _dyfs=1596278211268; signed-up-for-updates=true; defaultSizeTaxonomy=MENSSHOESEUITSEARCH; _dy_geo=BY.EU.BY_HM.BY_HM_Minsk; _dy_df_geo=Belarus..Minsk; s_sq=%5B%5BB%5D%5D; JSESSIONID=s4~B4BDDD52C0AF71A3F27EEAF9F646D6FC; gpv_pn=mens%3Asale; dy_fs_page=www.matchesfashion.com%2Fintl%2Fmens%2Fsale%3Fpage%3D6%26noofrecordsperpage%3D240%26sort%3D; AMCV_62C33A485B0EB69A0A495D19%40AdobeOrg=1075005958%7CMCIDTS%7C18476%7CMCMID%7C10960796723112666223370556962831819765%7CMCAAMLH-1596976519%7C6%7CMCAAMB-1596976519%7CRKhpRz8krg2tLO6pguXWp5olkAcUniQYPHaMWWgdJ3xzPWQmdj0y%7CMCOPTOUT-1596378919s%7CNONE%7CvVersion%7C4.4.1; _dy_toffset=-1; _dy_ses_load_seq=65991%3A1596372981866; _dy_soct=1003595.1005104.1596371717*1011332.1019298.1596372981*1022774.1040843.1596372981*1001485.1001871.1596372981; _dy_lu_ses=e64060d237cc73ec6069d5d30e721a2e%3A1596372982575; _cs_id=78aa5941-feac-a6be-8bd1-a2f53451eec3.1596278167.10.1596372985.1596371721.1.1630442167919.Lax.0; _cs_s=3.1; sailthru_pageviews=3; lastRskxRun=1596372985613; _derived_epik=dj0yJnU9bUNpYWxpclJITEZpREQ2OUIwQ3dESDZyOHQ5bkl1UDUmbj1vNXJSZXJWd1l3cmllU0R0eVFBbU1nJm09MSZ0PUFBQUFBRjhtdF9rJnJtPTEmcnQ9QUFBQUFGOG10X2s; sailthru_content=52e4286b833222bb7b374590cd5ba50528c1649dccb2f4dee2b98e249aa5be36b61cfd15c8501b89cadc787f42f079a8; sailthru_visitor=50f055a5-9068-4034-8535-2588891f22bd; _uetsid=8565c806456bfabf196c45e290c284cd; _uetvid=87050758bb1ef31d6f625e7fe6b7ad3c; AWSALB=oR5HGpcpDaWEtefQL2naem7OlG3Wi4WRysdD4qWgGFEFZkwMASBbjRjTsQecBJ8Txl9wb8XkjoLbjGH3MiD/s/Exl8o8ivWBSajc8LjLuENbee3pgwfB8SdZeRdP; AWSALBCORS=oR5HGpcpDaWEtefQL2naem7OlG3Wi4WRysdD4qWgGFEFZkwMASBbjRjTsQecBJ8Txl9wb8XkjoLbjGH3MiD/s/Exl8o8ivWBSajc8LjLuENbee3pgwfB8SdZeRdP; _px2=eyJ1IjoiOTQyNjQ3MDAtZDRiZi0xMWVhLThmMmYtMmZmODAyNDFhOWM1IiwidiI6IjIwNzc1MmMxLWQzNDAtMTFlYS1hOTY1LTgxNDIyY2Y5MTYxZSIsInQiOjE1OTYzNzQ2OTc0NzUsImgiOiI1OWFkZTYxN2JkMDM3ODlhODhmMTcwMTZlMjBjOTcwODdjYzQ4NzUyZjBjMzAwNmYzMDJlZjljOGNhNDE2NzE0In0='
];
// $proxy = new SocksClient('176.108.47.38:3128', new Connector($loop));
// $connector = new Connector($loop, ['tcp' => $proxy]);
// $client = new Browser($loop, $connector);


$loop = React\EventLoop\Factory::create();
$client = new Browser($loop);

$сategoryUrlBuilder = new CategoryUrlBuilder();
$scraperCategory = new Scraper($client, $сategoryUrlBuilder);

$subCategoryUrlBuilder = new SubCategoryUrlBuilder();
$scraperSubCategory = new Scraper($client, $subCategoryUrlBuilder);

$itemBuilder = new ItemBuilder();
$scraperItems = new Scraper($client, $itemBuilder);

$scraperCategory->scrape([
    'https://www.matchesfashion.com/intl/mens/sale/',
    'https://www.matchesfashion.com/intl/womens/sale/'
], $headers, 2);
$loop->run();
// print_r($scraperCategory->getData());

$scraperSubCategory->scrape($scraperCategory->getData(), $headers, 2);
$loop->run();
// print_r($scraperSubCategory->getData());

$urls = $scraperSubCategory->getData();
$paramsString = '?noOfRecordsPerPage=1000';
$urlsParam = [];
foreach ($urls as $url) {
    $urlsParam[] = $url . $paramsString;
}
// print_r($urlsParam);

$scraperItems->scrape($urlsParam, $headers, 2);
$loop->run();

$data = $scraperItems->getData();

$filesystem = \React\Filesystem\Filesystem::create($loop);
$printer = new JsonFilePrinter($filesystem);

$printer->print($data);

$loop->run();

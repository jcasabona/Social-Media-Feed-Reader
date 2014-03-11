<!html> 

<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US"> 
 
<head> 
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700' rel='stylesheet' type='text/css'>
	<title>Social Media Hub</title>

	<style type="text/css">
		body{
			font-family: "Open Sans Condensed", Arial, sans-serif;
			font-size: 62.5%;
			background: #EFEFEF;
			color: #999999;
			margin: 0;
			}

		a, a:visited{
			color: #880088;
			text-decoration: none;
			}

		a:hover{
			color: #CFCFCF;
			}

		header{
			background: #660066;
			padding: 1em;
			color: #FFFFFF;
			position: fixed;
			z-index: 2;
			width: 100%;
			top: 0px;
			}
		header h1{
			font-size: 2.5em;
			margin: 0;
			}

		.container{
			margin-top: 8em;
			}

		h3{
			font-weight: bold;
			font-size: 1.7em;
			padding-top: 0;
			margin-top: 0;
		}

		p{
			font-size: 1.65em;	
		}

		@media screen and (min-width: 50em){
			.column{
				width: 32%; 
				float: left;
			}
		}

		.item{
			padding: 0.6em;
			margin: 1em;
			background: #FFFFFF;
			border: 1px solid #CFCFCF;
		}

		.item p{
				margin-top: 0.6em;	
		}


		.item img{
			display: block;
			margin: 0 auto;
		}

		.item img.icon{
			display: inline-block;
			float: left;
			max-width: 10%;
			padding-right: 0.5em;
			}

	</style>

</head>
<body>

<header>
	<h1>The Social Media Feed Reader</h1>
</header>

<div class="container">
<?php
define('DEFAULT_FEED', 'http://casabona.org/feed/'); 
		
function get_rss($feed){
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $feed); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	$output = curl_exec($ch); 
	curl_close($ch);

	return $output;    
}  

function parse_rss($feed){
	$stuff= "";

    $rss = simplexml_load_string(get_rss($feed));
    foreach ($rss->channel->item as $item) {
        $link = $item->link; 
        $title = $item->title; 
        $desc= $item->description;

        $stuff.= '<div class="item">';
        $stuff.= '<img src="img/rss.png" alt="RSS Icon" class="icon" />';
      	$stuff.= '<h3><a href="'. $link .'">'. $title .'</a></h3>';
      	$stuff.= '<p>'.$desc.'</p>';
      	$stuff.= '</div>';
    }

    return $stuff;
}

function parse_twitter($feed){
	$stuff= "";

    $rss = simplexml_load_string(get_rss($feed));

    $name= explode(" ", $rss->title);
    $name= $name[0];
    $title= '<h3><a href='. $rss->id.'>@'. $name .'</a></h3>';
    foreach ($rss->entry as $item) {
    	//print_array($item);

        $link = $item->link['href']; 
        $desc= $item->summary;

        $stuff.= '<div class="item">';
        $stuff.= '<img src="img/tw.png" alt="Twitter Icon" class="icon" />';
      	$stuff.= $title;
      	$stuff.= '<p>'.$desc.' <a href='. $link .'>link</a></p>';
      	$stuff.= '</div>';
    }

    return $stuff;
}
	


function fb_parse_feed( $feed, $no = 10 ) {
	
	// URL to the Facebook page's RSS feed.
	$rss_url = $feed;
	
	$curl = curl_init();
 
    // You need to query the feed as a browser.
    $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
	$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
	$header[] = "Cache-Control: max-age=0";
	$header[] = "Connection: keep-alive";
	$header[] = "Keep-Alive: 300";
	$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
	$header[] = "Accept-Language: en-us,en;q=0.5";
	$header[] = "Pragma: "; // browsers keep this blank.
 
	curl_setopt($curl, CURLOPT_URL, $rss_url);
	curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla');
	curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
	curl_setopt($curl, CURLOPT_REFERER, '');
	curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
	curl_setopt($curl, CURLOPT_AUTOREFERER, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_TIMEOUT, 10);
 
	$raw_xml = curl_exec($curl); // execute the curl command
	curl_close($curl); // close the connection
	
	$xml = simplexml_load_string( $raw_xml );
  
	$out = ''; 
	$i = 1;
	foreach( $xml->channel->item as $item ){

		$link = $item->link; 
        $title = $item->title; 
        $desc= $item->description;

        $out.= '<div class="item">';
        $out.= '<img src="img/fb.png" alt="Facebook Icon" class="icon" />';
      	$out.= '<h3><a href="'. $link .'">'. $title .'</a></h3>';
      	$out.= '<p>'.$desc.'</p>';
      	$out.= '</div>';
		
		if( $i == $no ) break;
		$i++;
	}
	
	return $out;

}


function print_column($content){
?>
	<div class="column">
	<?php print $content; ?>
	</div>

<?php
}

function print_array($a){
	print "<pre>";
	var_dump($a);
	print "</pre>";
}
$fbid= '693078330713147'; //23985062429 | univofscranton
$fb_feed= 'http://www.facebook.com/feeds/page.php?id='.$fbid.'&format=rss20';

print_column(parse_rss(DEFAULT_FEED)); 
print_column(fb_parse_feed($fb_feed));
print_column(parse_twitter('http://www.rssitfor.me/getrss?name=jcasabona'));

?>
</div>
</body>
</html>
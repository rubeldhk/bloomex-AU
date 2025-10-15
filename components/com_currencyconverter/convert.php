<?php
	function get_conversion($cur_from,$cur_to){
			if(strlen($cur_from)==0){
				$cur_from = "USD";
			}
			if(strlen($cur_to)==0){
				$cur_from = "PHP";
			}
			$host="finance.yahoo.com";
			$fp = @fsockopen($host, 80, $errno, $errstr, 30);
			if (!$fp)
			{
				$errorstr="$errstr ($errno)<br />\n";
				return false;
			}
			else
			{
				$file="/d/quotes.csv";
				$str = "?s=".$cur_from.$cur_to."=X&f=sl1d1t1ba&e=.csv";
				$out = "GET ".$file.$str." HTTP/1.0\r\n";
			    $out .= "Host: www.yahoo.com\r\n";
				$out .= "Connection: Close\r\n\r\n";
				@fputs($fp, $out);
				while (!@feof($fp))
				{
					$data .= @fgets($fp, 128);
				}
				@fclose($fp);
				@preg_match("/^(.*?)\r?\n\r?\n(.*)/s", $data, $match);
				$data =$match[2];
				$search = array ("'<script[^>]*?>.*?</script>'si","'<[\/\!]*?[^<>]*?>'si","'([\r\n])[\s]+'","'&(quot|#34);'i","'&(amp|#38);'i","'&(lt|#60);'i","'&(gt|#62);'i","'&(nbsp|#160);'i","'&(iexcl|#161);'i","'&(cent|#162);'i","'&(pound|#163);'i","'&(copy|#169);'i","'&#(\d+);'e");
				$replace = array ("","","\\1","\"","&","<",">"," ",chr(161),chr(162),chr(163),chr(169),"chr(\\1)");
				$data = @preg_replace($search, $replace, $data);
				$result =explode(",",$data);
				return $result[1];
			}//else
	}//end get_conversion
	
	$x = get_conversion($_GET['from'],$_GET['to']);
	$x = $x * $_GET['val'];
	echo "Conversion Result : <strong>".$x."</strong>";
?>
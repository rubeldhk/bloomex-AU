<?php
$html = ''; 
$referer = ModLanding_Helper::getReferer();
if ($referer) {
   $searchTerm = ModLanding_Helper::getSearchTerm($referer);
   if ($searchTerm && !empty($searchTerm)) {
      $results = ModLanding_Helper::getResults($searchTerm);
      $html = ModLanding_Helper::putHtmlOnResults($results);
   }
}
if (!empty($html)) {
   $host = ModLanding_Helper::getHost($referer);
   $html = "
<b id='ls_title'>" . htmlspecialchars(strtoupper($searchTerm)) . "</b>
<br>
You came here from <a href='http://$host'>$host</a> searching for <i>" . htmlspecialchars($searchTerm) . "</i>.
<br>
These posts might be of interest:   
<br>
<br>
   $html
   ";
}
echo $html;

class ModLanding_Helper {

   function getReferer() {
      $return = false;
      if (array_key_exists('HTTP_REFERER', $_SERVER) && !empty($_SERVER['HTTP_REFERER'])) {
         $return = $_SERVER['HTTP_REFERER'];
      }
//      $return = 'http://www.google.com/search?q=joomla+&start=0&ie=utf-8&oe=utf-8&client=firefox-a&rls=org.mozilla:en-US:official';
      return $return;
   }

   function getHost($str) {
      $return = false;
      $arr = parse_url($str);
      if (array_key_exists('host', $arr)) {
         $host = $arr['host'];
         $return = $host;
      }
      return $return;
   }
   
   function getSearchTerm($str) {
      $return = false;
      $arr = parse_url($str);
      if (array_key_exists('host', $arr) && array_key_exists('query', $arr)) {
         $host = $arr['host'];
         if(substr($host, 0, 4) == 'www.') {
            $host = substr($host, 4);
         }
         $queryString = $arr['query'];
         if (!empty($host) && !empty($queryString)) {
            $param = ModLanding_Helper::getSearchTermParameterName($host);
            if ($param) {
               parse_str($queryString, $arr);
               if (array_key_exists($param, $arr)) {
                  $searchTerm = $arr[$param];
                  $searchTerm = urldecode($searchTerm);
                  $outCharset = explode('=', _ISO);
                  $outCharset = $outCharset[1];
                  $searchTerm = @iconv('UTF-8', $outCharset, $searchTerm);
                  $return = $searchTerm;  
               }
            }
         }
      }
      return $return;
   }

   function getResults($searchTerm) {
      global $database, $my;
      $gid = $my->gid;
      $nullDate = $database->getNullDate();
      $now = _CURRENT_SERVER_TIME;
      
      $tokens = ModLanding_Helper::tokenizeQuoted($searchTerm);
      
      $fields = array('a.title', 'a.metadesc', 'a.introtext', 'a.metakey', 'a.fulltext');
      $where = '';
      foreach ($fields as $field) {
         $tmp = '';
         foreach ($tokens as $token) {
            $token = $database->getEscaped($token);
            $t = "LOWER($field) LIKE LOWER('%$token%')";
            if (!empty($tmp)) {
               $tmp .= " OR $t";
            } else {
               $tmp = "$t";
            }
         }
         $tmp = "($tmp)";
         if (!empty($where)) {
            $where .= " OR $tmp";    
         } else {
            $where = " $tmp";  
         }
      }
      
      $query = "
         SELECT DISTINCT a.id, a.title 
           FROM #__content AS a
     INNER JOIN #__categories AS b ON b.id=a.catid
     INNER JOIN #__sections AS u ON u.id = a.sectionid
      LEFT JOIN #__menu AS m ON m.componentid = a.id
          WHERE $where      
            AND (a.state = 1 OR a.state = -1)
            AND a.access <= $gid
            AND ((u.published = 1 AND b.published = 1 AND b.access <= $gid AND u.access <= $gid) OR m.type = 'content_typed')
            AND ( a.publish_up = " . $database->Quote( $nullDate ) . " OR a.publish_up <= " . $database->Quote( $now ) . " )
            AND ( a.publish_down = " . $database->Quote( $nullDate ) . " OR a.publish_down >= " . $database->Quote( $now ) . " )
       ORDER BY a.hits DESC
          LIMIT 5";
      $database->setQuery($query);
//      echo $database->getQuery();
      $database->query();
      $list = $database->loadObjectList();
      return $list;
   }

   function putHtmlOnResults($results) {
      $return = '';
      foreach ($results as $result) {
         $title = htmlspecialchars($result->title);
         $id = $result->id;
         $return .= "<li>";
         $return .= "<a href='index.php?option=com_content&task=view&id=$id'>$title</a>";
         $return .= "</li>";
//         $return .= "\n<br>\n";
      }
      if (!empty($return)) {
         $return = "<ul>$return</ul>";
      }
      return $return;
   }

   function getSearchTermParameterName($str) {
      $search_engines = array('google.com' => 'q',
         'go.google.com' => 'q',
         'maps.google.com' => 'q',
         'local.google.com' => 'q',
         'search.yahoo.com' => 'p',
         'search.msn.com' => 'q',
         'msxml.excite.com' => 'qkw',
         'search.lycos.com' => 'query',
         'alltheweb.com' => 'q',
         'search.aol.com' => 'query',
         'search.iwon.com' => 'searchfor',
         'ask.com' => 'q',
         'ask.co.uk' => 'ask',
         'search.cometsystems.com' => 'qry',
         'hotbot.com' => 'query',
         'overture.com' => 'Keywords',
         'metacrawler.com' => 'qkw',
         'search.netscape.com' => 'query',
         'looksmart.com' => 'key',
         'dpxml.webcrawler.com' => 'qkw',
         'search.earthlink.net' => 'q',
         'search.viewpoint.com' => 'k',
         'mamma.com' => 'query');

      $delim = false;

      // Check to see if we have a host match in our lookup array
      if (isset($search_engines[$str])) {
         $delim = $search_engines[$str];
      } else {
         // Lets check for referrals for international TLDs and sites with strange formats

         // Optimizations
         $sub13 = substr($str, 0, 13);

         // Search string for engine
         if(substr($str, 0, 7) == 'google.')
         $delim = "q";
         elseif($sub13 == 'search.atomz.')
         $delim = "sp-q";
         elseif(substr($str, 0, 11) == 'search.msn.')
         $delim = "q";
         elseif($sub13 == 'search.yahoo.')
         $delim = "p";
         elseif(preg_match('/home\.bellsouth\.net\/s\/s\.dll/i', $str))
         $delim = "bellsouth";
      }
      return $delim;
   }
   
   function tokenizeQuoted($string) {
      $invalid = "\40\41\43\44\45\46\47\48\49\50\51\52\53 \54\55\56\57\72\73\74\75\76 \77\100\133\134\135\136\137\138\139\140 \173\174\175\176\n\r\t";
      for($tokens=array(), $nextToken=strtok($string, $invalid); $nextToken!==false; $nextToken=strtok($invalid)) {
         if($nextToken{0}=='"') {
            $nextToken = $nextToken{strlen($nextToken)-1}=='"' ? substr($nextToken, 1, -1) : substr($nextToken, 1) . ' ' . strtok('"');
         }
         $tokens[] = $nextToken;
      }
      return $tokens;
   }
}
?>
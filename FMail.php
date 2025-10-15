<?php
###########################################################################################################
#   Software: FMail                                                                                       #
#   Version:  2.12                                                                                        #
#   Date:     2005-04-21                                                                                  #
#   Author:   Yuri Frantsevich (fyn@tut.by)                                                               #
#   License:  Freeware                                                                                    #
#                                                                                                         #
#   You may use and modify this software as you wish.                                                     #
###########################################################################################################
#   $mymail = new FMail ('Encoding', 'ContentType', 'Charset');                                           #
#                                                                                                         #
#   Encoding:                                                                                             #
#    0 = '8bit'                                                                                           #
#    1 = 'base64'                                                                                         #
#    2 = '7bit'                                                                                           #
#    3 = 'quoted-printable'                                                                               #
#                                                                                                         #
#   ContentType:                                                                                          #
#    0 = 'text/plain'                                                                                     #
#    1 = 'multipart/mixed'                                                                                #
#    2 = 'application/octet-stream'                                                                       #
#    3 = 'text/html'                                                                                      #
#    4 = 'multipart/alternative'                                                                          #
#                                                                                                         #
#   Charset:                                                                                              #
#    0 = 'iso-8859-1'                                                                                     #
#    1 = 'utf-8'                                                                                          #
#    2 = 'windows-1251'                                                                                   #
#    3 = 'koi8-r'                                                                                         #
#                                                                                                         #
#   $mymail->mailsend('to','message','subject','from','path_to_file','filename','type');                  #
#                                                                                                         #
#   type = '' or type = 'HTML' or type = 'HTML_FROM_FILE'                                                 #
#                                                                                                         #
###########################################################################################################
#   You can use this so:                                                                                  #
#                                                                                                         #
#   First:                                                                                                #
#   Change default mail addresses                                                                         #
#    $TO (default 'to' address),                                                                          #
#    $FROM (default 'from' address),                                                                      #
#    $MESSAGE (default message),                                                                          #
#    $SUBJ (default subject) and                                                                          #
#    $AdminMail (default administrator mail address)                                                      #
#                                                                                                         #
#   EXAMPLE:                                                                                              #
###########################################################################################################
#   initialization                                                                                        #
#                                                                                                         #
#   $mymail = new FMail();                                                                                #
#   or                                                                                                    #
#   $mymail = new FMail ('1', '0', '2');                                                                  #
###########################################################################################################
#   if you send the simple message                                                                        #
#                                                                                                         #
#   $mymail->mailsend(); send all by default                                                              #
#   or                                                                                                    #
#   $mymail->mailsend('to@mail.com');                                                                     #
#   or                                                                                                    #
#   $mymail->mailsend('to@mail.com', 'message');                                                          #
#   or                                                                                                    #
#   $mymail->mailsend('to@mail.com', 'message', 'subject');                                               #
#   or                                                                                                    #
#   $mymail->mailsend('to@mail.com', 'message', 'subject', 'from@message');                               #
###########################################################################################################
#   if you wish to send a file                                                                            #
#                                                                                                         #
#   $mymail->mailsend('to@mail.com', 'message', 'subject', 'from@message', 'path/to/file');               #
#   or                                                                                                    #
#   $mymail->mailsend('to@mail.com', 'message', 'subject', 'from@message', 'path/to/file', 'filename');   #
###########################################################################################################
#   if you send the message in HTML format                                                                #
#                                                                                                         #
#   $mymail->mailsend('to@mail.com', 'message', 'subject', 'from@message','','','HTML');                  #
#   or with file                                                                                          #
#   $mymail->mailsend('to@mail.com', 'message', 'subject', 'from@message', 'file', 'filename', 'HTML');   #
###########################################################################################################
#   if you wish to send the text from HTML file                                                           #
#                                                                                                         #
#   $mymail->mailsend('to@mail.com','','from@message', 'subject','file', 'filename', 'HTML_FROM_FILE');   #
###########################################################################################################
#   if you wish check email address                                                                       #
#                                                                                                         #
#   $mymail->mailcheck($to,1); //return TRUE or FALSE                                                     #
###########################################################################################################

if (!defined("FMail")) {
	define("FMail", true);
	class FMail {
		######## --> Private properties <-- ########
		var $TO = 'to@mail.com';                      #default to adress
		var $SUBJ;                                    #default subject
		var $MESSAGE;                                 #default message
		var $FROM = 'from@mail.com';                  #default from adress
		var $AdminMail = 'admin@mail.com';            #default admin adress
		var $bagshow = FALSE;                         #show error on page
		var $adminsend = TRUE;                        #send error to admin
		var $decod = FALSE;                           #convert special characters from HTML entities
		############################################
		
		####### --> Do not change this <-- #########
		var $Encoding = array ('8bit', 'base64', '7bit', 'quoted-printable');
		var $ContentType = array('text/plain', 'multipart/mixed', 'application/octet-stream', 'text/html', 'multipart/alternative');
		var $Charset = array('iso-8859-1', 'utf-8', 'windows-1251', 'koi8-r');
		var $MimeVersion = '2.12';
		var $XPriority = '3 (Normal)';
		var $XMailer = 'PHPMail - FMail';
		
		var $encoding;
		var $charset;
		var $ctype;
		var $char;
		var $to;
		var $subj;
		var $from;
		var $message;
		var $mailerror;
		var $mailerrornum;
		var $enc;
		var $ct;
		var $files;
		var $bcc;
		
		function FMail ($encoding = 0, $ctype = 0, $char = 0) {
			global $dbAdminMail, $bccmail, $aut;
			if ($bccmail && $this->mailcheck($bccmail)) $this->bcc = $bccmail;
			if ($dbAdminMail) $this->AdminMail = $dbAdminMail;
			$this->ct = $ctype;
			$this->ctype = $this->ContentType[$ctype];
			if ($this->ct == 1 && $encoding != 1) {
				$encoding = 1;
			}
			$this->encoding = $this->Encoding[$encoding];
			$this->charset = $this->Charset[$char];
			$this->enc = $encoding;
			if ($aut) $this->auth = $aut;
			return TRUE;
		}
		
		function mailsend ($to = FALSE, $message = FALSE, $subj = FALSE, $from = FALSE, $file = FALSE, $filename = FALSE, $type = FALSE) {
			$this->to = ($to)?$to:$this->TO;
			$this->subj = ($subj)?$subj:$this->SUBJ;
			$this->message = ($message)?$message:$this->MESSAGE;
			$this->from = ($from)?$from:$this->FROM;
			$this->message = preg_replace("/{HOST}/",$_SERVER['HTTP_HOST'],$this->message);
			if ($type) $type = strtoupper($type);
			if ($type == 'HTML' || $type == 'HTML_FILE') {
				$this->enc = 1;
				$this->ct = 1;
				$this->encoding = $this->Encoding[$this->enc];
				$this->ctype = $this->ContentType[$this->ct];
				$this->message = nl2br($this->message);
			}
			if ($file) {
				$this->enc = 1;
				$this->ct = 1;
				$this->encoding = $this->Encoding[$this->enc];
				$this->ctype = $this->ContentType[$this->ct];
				if (file_exists($file)) {
					if (!$filename) {
						$files = preg_replace("/\//", '\\', $file);
						$files = preg_replace("/\\\/", "||", $files);
						$filen = split("\|\|", $files);
						$cz = sizeof($filen) - 1;
						$filename = $filen[$cz];
					}
					$f = fopen($file, "rb");
					$filet = fread($f, filesize($file));
					fclose($f);
					$file = $filet;
				}
				if (!$filename && $type != 'HTML') $filename = 'message.txt';
				if (!$filename && $type == 'HTML') $filename = 'message.htm';
				if ($type == 'HTML_FROM_FILE' && !$this->message && $this->decod) $file = $this->html_decode($file);
				$this->files = chunk_split(base64_encode($file));
				$encod = $this->encoding;
			}
			else $encod = $this->encoding;
			if ($type == 'HTML_FROM_FILE' && $this->message) $type = 'HTML';

			$un = strtoupper(uniqid(time()));
			if ($this->mailcheck($this->to) && $this->mailcheck($this->from)) {
				if (preg_match("/(;|,)/", $this->to)) {
					$this->to = preg_replace("/,/", ';', $this->to);
					$to_mail = split(';', $this->to);
					$sz = sizeof($to_mail);
				}
				$head = "From: ".$this->from."\n";
				if ($this->bcc) {
					$head .= "Bcc: ".$this->bcc."\n";
				}
				$head .= "Subject: ".$this->subj."\n";
				$head .= "X-Priority: ".$this->XPriority."\n";
				$head .= "X-Mailer: ".$this->XMailer."\n";
				$head .= "Reply-To: ".$this->from."\n";
				$head .= "Mime-Version: ".$this->MimeVersion."\n";
				if ($this->enc == 1) $this->message = chunk_split(base64_encode($this->message));
				if ($this->ct == 1 && $type != 'HTML' && $type != 'HTML_FROM_FILE' && $file) {
					$head .= "Content-Type: ".$this->ctype."; boundary=\"----=_NextPart_000_0001_FYN$un\"\n";
					$head .= "X-Priority: 3 (Normal)\n";
					$head .= "X-MSMail-Priority: Normal\n";
					$head .= "X-Mailer: Mail::Sendfile version ".$this->XMailer."::".$this->MimeVersion."\n";
					$head .= "Importance: Normal\n";
					
					$head .= "This is a multi-part message in MIME format.\n";
					
					$head .= "\n------=_NextPart_000_0001_FYN$un\n";
					$head .= "Content-Type: ".$this->ContentType[0]."; charset=\"".$this->charset."\"\n";
					$head .= "Content-Transfer-Encoding: ".$encod."\n";
					$head .= "\n".$this->message."\n";
					
					$head .= "\n------=_NextPart_000_0001_FYN$un\n";
					$head .= "Content-Type: ".$this->ContentType[2]."; name=\"".$filename."\"\n";
					$head .= "Content-Transfer-Encoding: ".$this->encoding."\n";
					$head .= "Content-Disposition: attachment; filename=\"".$filename."\"\n";
					$head .= "\n";
					
					$head .= $this->files;
					$head .= ".\n";
				}
				elseif ($type == 'HTML' && $file) {
					$head .= "Content-Type: ".$this->ctype."; boundary=\"----=_NextPart_000_0001_FYN$un\"\n";
					$head .= "X-Priority: 3 (Normal)\n";
					$head .= "X-MSMail-Priority: Normal\n";
					$head .= "X-Mailer: Mail::Sendfile version ".$this->XMailer."::".$this->MimeVersion."\n";
					$head .= "Importance: Normal\n";
					
					$head .= "This is a multi-part message in MIME format.\n";
					
					$head .= "\n------=_NextPart_000_0001_FYN$un\n";
					$head .= "Content-Type: ".$this->ContentType[3]."; charset=\"".$this->charset."\"\n";
					$head .= "Content-Transfer-Encoding: ".$encod."\n";
					$head .= "\n".$this->message."\n";
					
					$head .= "\n------=_NextPart_000_0001_FYN$un\n";
					$head .= "Content-Type: ".$this->ContentType[2]."; name=\"".$filename."\"\n";
					$head .= "Content-Transfer-Encoding: ".$this->encoding."\n";
					$head .= "Content-Disposition: attachment; filename=\"".$filename."\"\n";
					$head .= "\n";
					
					$head .= $this->files;
					$head .= ".\n";
				}
				elseif ($type == 'HTML_FROM_FILE' && !$this->message) {
					$head .= "Content-Type: ".$this->ContentType[3]."; charset=".$this->charset."\n";
					$head .= "Content-Transfer-Encoding: ".$encod."";
					$head .= "\n".$this->files."\n\n";
				}
				elseif ($type == 'HTML') {
					$head .= "Content-Type: ".$this->ContentType[3]."; charset=".$this->charset."\n";
					$head .= "Content-Transfer-Encoding: ".$encod."";
					$zag = $this->message."\n\n";
				}
				else {
					$head .= "Content-Type: ".$this->ctype."; charset=".$this->charset."\n";
					$head .= "Content-Transfer-Encoding: ".$this->encoding."";
					$zag = $this->message."\n\n";
				}
				if ($sz > 2) {
					$num = 0;
					$to = '';
					foreach ($to_mail as $tm) {
						$num++;
						$to = ($to)?"$to;$tm":$tm;
						if ($num == 2) {
							if (@mail($to, $this->subj, $zag, $head)) {
								$to = '';
								$num = 0;
							}
							else {
								$this->mailError('Cann not send mail TO: '.$to);
								$this->mailerrornum = 1;
							}
						}
					}
					if ($to) {
						if (@mail($to, $this->subj, $zag, $head)) {
							$to = '';
							$num = 0;
						}
						else {
							$this->mailError('Cann not send mail TO: '.$to);
							$this->mailerrornum = 1;
						}
					}
					return TRUE;
				}
				else {
					if (@mail($this->to, $this->subj, $zag, $head)) {
						return TRUE;
					}
					else {
						$this->mailError('Cann not send mail TO: '.$this->to);
						$this->mailerrornum = 1;
					}
				}
			}
		}
		
		function mailcheck ($to = FALSE, $ns = FALSE) {
			if (!$to) $to = $this->to;
			if (preg_match("/(;|,)/", $to)) {
				$to = preg_replace("/,/", ';', $to);
				$to_mail = split(';', $to);
				$sz = sizeof($to_mail);
				$err = 0;
				$this->to = '';
				foreach ($to_mail as $tm) {
					if (!(preg_match('/^[A-z0-9&\'\.\-_\+]+@[A-z0-9\-]+\.([A-z0-9\-]+\.)*?[A-z]+$/is', $tm))) { //'/^[A-z0-9_\-]+(([^(\s\(\)<>@,;:\\<>\.\[\])]|\.)[A-z0-9_\-]+)*@[A-z0-9_]+((\-|\.)[A-z0-9_]+)*\.[A-z]{2,}$/'
						$err++;
					}
					else {
						$this->to = ($this->to)?$this->to.";$tm":$tm;
					}
				}
				if ($err == $sz) {
					if (!$ns) $this->mailError("Incorrect mail adress: $to");
					$this->mailerrornum = 2;
					return FALSE;
				}
				else return TRUE;
			}
			else {
				if (!(preg_match('/^[A-z0-9&\'\.\-_\+]+@[A-z0-9\-]+\.([A-z0-9\-]+\.)*?[A-z]+$/is', $to))) {
					if (!$to) $to = 'no address';
					if (!$ns) $this->mailError("Incorrect mail address: $to");
					$this->mailerrornum = 2;
					return FALSE;
				}
				else return TRUE;
			}
		}
		
		function mailError ($errormessage) {
			global $SERVER_NAME;
			$this->mailerror = (empty($this->mailerrormessage)?$errormessage:$this->mailerror);
			$this->mailerror .= "\n"."Link error: ".$_SERVER['REQUEST_URI']."\n";
			$this->mailerror .= "Refferer: ".$_SERVER['HTTP_REFERER']."\n";
			if ($this->bagshow) {
				$this->mailerror = nl2br($this->mailerror);
				echo $this->mailerror;
			}
			if ($this->adminsend) {
				$msubject = "Error from FMail ($SERVER_NAME)";
				@mail( $this->AdminMail, "$msubject", $this->mailerror );
			}
			return FALSE;
		}
		function html_decode ($html) {
			$trans = get_html_translation_table(HTML_ENTITIES);
			$trans = array_flip($trans);
			$html = strtr($html, $trans);
			//supports the most used entity codes
			$html = str_replace("&nbsp;"," ",$html); 
			$html = str_replace("&#380;","Ï",$html);
			$html = str_replace("&amp;","&",$html);
			$html = str_replace("&lt;","<",$html);
			$html = str_replace("&gt;",">",$html);
			$html = str_replace("&#728;","¢",$html); 
			$html = str_replace("&#321;","£",$html); 
			$html = str_replace("&euro;","€",$html);
			$html = str_replace("&#260;","¥",$html); 
			$html = str_replace("&trade;","™",$html);
			$html = str_replace("&copy;","©",$html); 
			$html = str_replace("&reg;","®",$html);
		 	return $html;
		}
		function fmail_info () {
			$inf['Software'] = 'FMail';
			$inf['Version'] = $this->MimeVersion;
			$inf['Date'] = '2005-04-21';
			$inf['Author'] = 'Yuri Frantsevich';
			$inf['License'] = 'Freeware';
			return $inf;
		}
	}
}
?>

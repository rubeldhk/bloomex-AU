<?php
// $Id: editor.htmlarea3_xtd.php, v 1.0 2004/04/19 16:23:28 bpfeifer Exp $
/**
* Advanced Handler for HTMLAarea3 XTD
* @package HTMLAarea3 XTD
* @Copyright © 2004 Bernhard Pfeifer aka novocaine
* @ All rights reserved
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: 1.0 $
**/

defined( '_VALID_MOS' ) or die( 'Direct access to this location is not allowed!' );

$_MAMBOTS->registerFunction( 'onInitEditor', 'botHTMLArea3_XTDEditorInit' );
$_MAMBOTS->registerFunction( 'onGetEditorContents', 'botHTMLArea3_XTDEditorGetContents' );
$_MAMBOTS->registerFunction( 'onEditorArea', 'botHTMLArea3_XTDEditorArea' );

	function botHTMLArea3_XTDEditorInit() {
		global $mosConfig_live_site, $database;
		$query = "SELECT id FROM #__mambots WHERE element = 'htmlarea3_xtd' AND folder = 'editors'";
		$database->setQuery( $query );
		$id = $database->loadResult();
		$mambot = new mosMambot( $database );
		$mambot->load( $id );
		$params =& new mosParameters( $mambot->params );

		?>
		<script type="text/javascript">
		<!--
			_editor_url = "<?php echo $mosConfig_live_site; ?>/mambots/editors/htmlarea3_xtd/";
			<?php
			$language = $params->get( 'language', 'en' );
			?>
			_editor_lang = "<?php echo $language?>";
		//-->
		</script>
		<script type="text/javascript" src="<?php echo $mosConfig_live_site; ?>/mambots/editors/htmlarea3_xtd/htmlarea_xtd.js"></script>
		<script type="text/javascript" src="<?php echo $mosConfig_live_site; ?>/mambots/editors/htmlarea3_xtd/dialog.js"></script>
		<script type="text/javascript" src="<?php echo $mosConfig_live_site; ?>/mambots/editors/htmlarea3_xtd/lang/<?php echo $language?>.js"></script>
		<style type="text/css">@import url(<?php echo $mosConfig_live_site; ?>/mambots/editors/htmlarea3_xtd/htmlarea.css)</style>
		<script type="text/javascript">
		<!--
			// load the plugin files
			<?php
			if( $params->get( 'tableoperations', '0' ) ) {
			?>
			HTMLArea.loadPlugin("TableOperations");
			<?php
			}
			if( $params->get( 'phpspell', '0' ) ) {
			?>
			HTMLArea.loadPlugin("PhpSpell");
			<?php
			}
			if( $params->get( 'enterparagraphs', '0' ) ) {
			?>
			HTMLArea.loadPlugin("EnterParagraphs");
			<?php
			}
			if( $params->get( 'contextmenu', '0' ) ) {
			?>
			HTMLArea.loadPlugin("ContextMenu");
			<?php
			}
			if( $params->get( 'css', '0' ) ) {
			?>
			HTMLArea.loadPlugin("CSS");
			<?php
			}
			if( $params->get( 'insertsmiley', '0' ) ) {
			?>
			HTMLArea.loadPlugin("InsertSmiley");
			<?php
			}
			?>
			var editor = null;
		//-->
		</script>
		<?php
		}

	function botHTMLArea3_XTDEditorArea( $name, $content, $hiddenField, $width, $height, $col, $row ) {
		global $database, $mosConfig_live_site, $mosConfig_absolute_path;
		$database->setQuery( "SELECT template FROM #__templates_menu WHERE client_id='0' AND menuid='0'" );
		$template = $database->loadResult();
		$query = "SELECT id FROM #__mambots WHERE element = 'htmlarea3_xtd' AND folder = 'editors'";
		$database->setQuery( $query );
		$id = $database->loadResult();
		$mambot = new mosMambot( $database );
		$mambot->load( $id );
		$params =& new mosParameters( $mambot->params );
		?>
		<textarea name="<?php echo $hiddenField; ?>" id="<?php echo $hiddenField; ?>" cols="<?php echo $col; ?>" rows="<?php echo $row; ?>" style="width:<?php echo $width; ?>; height:<?php echo $height; ?>"><?php echo $content; ?></textarea>
		<script language="JavaScript1.2" defer="defer">
		<!--
			// build a style for the spellchecker
		_phpspell_mystyle = "<link rel=\"stylesheet\" href=\"<?php echo $mosConfig_live_site; ?>/templates/<?php echo $template; ?>/css/template_css.css\" type=\"text/css\" />";
			// create the editor
		var editor<?php echo $name ?> = new HTMLArea("<?php echo $hiddenField ?>");

			// retrieve the config object
		var config<?php echo $name ?> = editor<?php echo $name ?>.config;
		config<?php echo $name ?>.pageStyle='@import url(<?php echo $mosConfig_live_site ."/templates/". $template ."/css/template_css.css";?>);'
		config<?php echo $name ?>.sizeIncludesToolbar = true;
		config<?php echo $name ?>.height = "300px";
		config<?php echo $name ?>.width = "550px";

		config<?php echo $name ?>.registerButton({
		  id        : "mosimage",
		  tooltip   : "Insert {mosimage} tag",
		  image     : _editor_url + "images/ed_mos_image.gif",
		  textMode  : false,
		  action    : function(editor<?php echo $name ?>, id) {
						editor<?php echo $name ?>.focusEditor();
		                editor<?php echo $name ?>.insertHTML('{mosimage}');
		              }
		});

		config<?php echo $name ?>.registerButton({
		  id        : "mospagebreak",
		  tooltip   : "Insert {mospagebreak} tag",
		  image     : _editor_url + "images/ed_mos_pagebreak.gif",
		  textMode  : false,
		  action    : function(editor<?php echo $name ?>, id) {
						editor<?php echo $name ?>.focusEditor();
		                editor<?php echo $name ?>.insertHTML('{mospagebreak}');
		              }
		});
<?php if( file_exists( $mosConfig_absolute_path. "/components/com_virtuemart/virtuemart_parser.php" )) { ?>

		// *********MosProductSnap ******************************
        config<?php echo $name ?>.registerButton({
          id        : "product_snapshot",
          tooltip   : "Insert a Product Snapshot",
          image     : _editor_url + "images/ed_productsnap.gif",
          textMode  : false,
          action    :  function(editor<?php echo $name ?>, id) {
                            var editor = editor<?php echo $name ?>;
                            var outparam = null;
                            
                            // Take the mosbot tag named "{product_snapshot....}"
                            var mosproductsnap =  editor.getSelectedHTML();
                            
                            // and convert it to string
                            mosproductsnap = mosproductsnap.toString();
                              if (mosproductsnap.indexOf("{product_snapshot")!=-1) {
                                  // let's get the parameters
                                  var params = mosproductsnap.slice(20,mosproductsnap.length-1);
                                  var param_array = new Array();
                            
                                  param_array = params.split(",");
                                  /* now the param_array contains 5 fields:
                                  1.product_id
                                  2.showprice
                                  3.showdesc
                                  4.showaddtocart
                                  5.table align
                                  */
                              }
                              else 
                                mosproductsnap = null; 
                            
                            if (mosproductsnap) outparam = {
                              i_product_id  : param_array[0],
                              i_showprice  : param_array[1],
                              i_showdesc : param_array[2],
                              i_showaddtocart : param_array[3],
                              i_align : param_array[4]
                            };
                            editor._popupDialog("product_snapshot.php", function(param) {
                              if (!param)
                                return false;
                              var html = "{product_snapshot:id="+param.i_product_id+",";
                              html += (param.i_showprice.toString()=="1") ? "true" : "false";
                              html +=",";
                              html += (param.i_showdesc.toString()=="1") ? "true" : "false";
                              html +=",";
                              html += (param.i_showaddtocart.toString()=="1") ? "true" : "false";
                              html +=",";
                              html +=param.i_align;
                              html +="}";
                              editor.insertHTML(html);
                            }, outparam);
                          }
        });
        // *********End MosProductSnap ******************************
<?php } ?>
		config<?php echo $name ?>.toolbar = [
		[ "fontname", "space",
		  "fontsize", "space",
		  "formatblock", "space",
		  "bold", "italic", "underline", "separator",
		  "strikethrough", "subscript", "superscript", "separator", "htmlmode" ],

		[ "justifyleft", "justifycenter", "justifyright", "justifyfull", "separator",
		  "insertorderedlist", "insertunorderedlist", "outdent", "indent", "separator",
		  "forecolor", "hilitecolor", "space", "textindicator", "space", "removeformat", "separator", "undo", "redo" ],

		[ "product_snapshot", "separator", "createlink", "mosimage", "mospagebreak", "separator", "inserthorizontalrule", "insertcharacter", "insertimage", "insertfile", "separator",
		  "inserttable", "toggleborders", "separator", "cut", "copy", "paste", "separator",
		  "killword", "separator", "popupeditor" ],
		];

		<?php
			if( $params->get( 'tableoperations', '0' ) ) {
		?>
			editor<?php echo $name ?>.registerPlugin(TableOperations);
		<?php
			}
			if( $params->get( 'phpspell', '0' ) ) {
		?>
			editor<?php echo $name ?>.registerPlugin(PhpSpell);
		<?php
			}
			if( $params->get( 'enterparagraphs', '0' ) ) {
		?>
			editor<?php echo $name ?>.registerPlugin(EnterParagraphs);
		<?php
			}
			if( $params->get( 'contextmenu', '0' ) ) {
		?>
			editor<?php echo $name ?>.registerPlugin(ContextMenu);
		<?php
			}
			if( $params->get( 'css', '0' ) ) {
		?>
			editor<?php echo $name ?>.registerPlugin(CSS, {
				combos : [ { label: "CSS Styles:",
					// 6 standard Mambo CSS template classes contained
					// add your own CSS classes like this (but leave [None selected] for removal of classes)
					// "Class name to be shown in the drop down": "name of the class like typed in your CSS file",
					// Note: you mustn't put a comma to the last line!
						options: { "[None selected]": "",
							"Small": "small",
							"Small Dark": "smalldark",
							"Contentheading": "contentheading",
							"Componentheading": "componentheading",
		 					"Moscode": "moscode",
		 					"Message": "message"
						}
					} ]
				}
			);
		<?php
			}
			if( $params->get( 'insertsmiley', '0' ) ) {
		?>
			editor<?php echo $name ?>.registerPlugin(InsertSmiley);
		<?php
			}
			if ($name != "editor2") {
		?>
			HTMLArea.agt = navigator.userAgent.toLowerCase();
			HTMLArea.is_gecko  = (navigator.product == "Gecko");
			if (HTMLArea.is_gecko) {
				setTimeout('editor<?php echo $name ?>.generate("<?php echo $hiddenField ?>")', 3000); // Mozilla needs a rest here, especially on Mac OS
			} else {
				editor<?php echo $name ?>.generate('<?php echo $hiddenField ?>');
			}
		<?php
			} else if ($name == "editor2") {
		?>
				editor<?php echo $name ?>.generate('<?php echo $hiddenField ?>');
		<?php
			}
		?>

		//-->
		</script>
		<?php
		}
		function botHTMLArea3_XTDEditorGetContents( $editorArea, $hiddenField ) {
		}

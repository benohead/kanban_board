<?php 
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])) {
	header('Location: login.php?return='.urlencode($_SERVER["REQUEST_URI"]));	
	die();
}
require_once("functions.php");
require_once("models/header.php");
if (!isUserLoggedIn()){
	header('Location: login.php?return='.urlencode($_SERVER["REQUEST_URI"]));
	die();
}
else {
?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<title><?php echo translate('Kanban Board'); ?></title>
		<link type="text/css" rel="stylesheet" href="styles/jquery.qtip.min.css" />
<?php		
	include "includes/board_request_data.php";
?>
		<style>
<?php
			echo get_board_css($current_board);
			echo get_card_css($current_board);
?>	
		</style>
		<script type="text/JavaScript">
		<!--
			function AutoRefresh(interval) {
				setTimeout("location.reload(true);",interval);
			}
		//   -->
		</script>
	</head>
<?php
	if ($readonly==0) {
?>
	<body id="board-body">
<?php
}
else {
?>
	<body id="board-body" class="user-<?php echo $logged_in_user->user_id; ?>" onload="JavaScript: AutoRefresh(5000);">
<?php
}
?>
		<div id="header" class="contextMenu">
			<ul>
				<li class="context-logout header-part"><a href="logout.php"><?php echo translate('Logout'); ?></a></li>
				<li class="context-admin header-part"><a href="admin_projects.php"><?php echo translate('Settings'); ?></a></li>
<?php 
	if (isset($projects) && count($projects) > 0) { 
?>
				<li class="header-part">
					<form name='changeProject' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>
						<select name='projectid' id='projectid' onchange="this.form.submit()">
<?php
		foreach ($projects as $project){
			if ($current_project == $project['id']){
?>
							<option value='<?php echo $project['id']; ?>' selected='selected'><?php echo $project['display_name']; ?></option>
<?php
			}
			else {
?>
							<option value='<?php echo $project['id']; ?>'><?php echo $project['display_name']; ?></option>
<?php
			}
		}
?>
						</select>
						<input type='hidden' name='readonly' value=<?php echo $readonly; ?> />
					</form>
				</li>
<?php 
	}

	if (isset($boards) && count($boards) > 0) {
?>
				<li class="header-part">
					<form name='changeBoard' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>
						<select name='boardid' id='boardid' onchange="this.form.submit()">
<?php
		foreach ($boards as $board){
			if ($current_board == $board['id']){
?>
							<option value='<?php echo $board['id']; ?>' selected='selected'><?php echo $board['display_name']; ?></option>
<?php
			}
			else {
?>
							<option value='<?php echo $board['id']; ?>'><?php echo $board['display_name']; ?></option>
<?php			
			}
		}
?>
						</select>
						<input type='hidden' name='readonly' value=<?php echo $readonly; ?> />
					</form>
				</li>
<?php 
	}
?>
				</ul>
			</div>
			<div id="board">
<?php 
	include 'includes/not_set_current_board.php';

	$boardcolumns = get_board_columns($current_board);
	$cardattributes = getCompleteCardAttributes($current_board);
?>
				<style type="text/css">
<?php 
	foreach ($cardattributes as $attribute_id => $attribute_data) {
		if (isset($attribute_data['hideoncolumns'])) { 
			$hideoncolumns = $attribute_data['hideoncolumns'];
		}
		else {
			unset($hideoncolumns);
		}
		if (isset($hideoncolumns)) {
			foreach($hideoncolumns as $hideoncolumn) {
?>
					html body#board-body div#board div#<?php echo $hideoncolumn; ?>.boardcolumn div.card div.<?php echo $attribute_id?> {
						display: none !important;
					}
<?php 
			}
		}
	}
?>
				</style>
<?php 
	$cards = get_cards_on_board($current_board);
	$rules = unserialize_or_empty_array(get_board_rules($current_board));
	$generators = unserialize_or_empty_array(get_board_generators($current_board));
	$ruleTypes = getActiveRuleTypes();
	$generatorTypes = getActiveGeneratorTypes();
	$rulesPerAction = getRulesPerAction($rules, $ruleTypes);
	$generatorsPerTrigger = getGeneratorsPerTrigger($generators);
	
	if (!isset($cards)) {
		$cards = array();
	}
	if (!isset($wip_limits)) {
			$wip_limits = array();
	}

	$first = true;
	foreach ($boardcolumns as $column_name => $column_data) {
		$wip_limit_text = "";
		$wip_limit = -1;
		if (isset($column_data['wip_limit']) && ($column_data['wip_limit'] > 0)) {
			$wip_limit_text = " - WIP: </span><span class='wip-limit'>".$column_data['wip_limit'];
			$wip_limit = $column_data['wip_limit'];
		}
?>		
				<div id="<?php echo $column_name; ?>" class="boardcolumn <?php echo ($first ? "first" : ""); ?>" data-wip-limit="<?php echo $wip_limit; ?>">
					<div class="title">
						<span class='column-name' title='<?php echo htmlentities(str_replace(array ("\r", "\n"), array("", ""), nl2br($column_data['description'])), ENT_QUOTES); ?>'><?php echo $column_data['display_name']; ?></span>
						<span class='wip-prefix'><?php echo $wip_limit_text; ?></span>
						<a class='addcard' title='<?php echo translate('Add a new card to this column'); ?>'></a>
					</div>
<?php 
		$first = false;
		
		foreach ($cards as $card_id => $card_data) {
			if ($card_data['board']==$column_name) {
?>
					<div id="<?php echo $card_id; ?>" draggable="true" class="card<?php
						foreach ($cardattributes as $attribute_id => $attribute_data) {
							if (!isset($card_data[$attribute_id])) {
								$card_data[$attribute_id] = getDefaultAttributeValue($attribute_data['sourceType']);
							}
							echo ' '.$attribute_id.'-'.preg_replace("/[^a-zA-Z0-9]+/", "", $card_data[$attribute_id]);
						}
					?>">
<?php					
						/* <a title='<?php echo translate('Click here to copy the URL of this card so that you can share it'); ?>' href='#' class='card_url' data-url='<?php echo currentPageURL().'?boardid='.$current_board.'&cardid='.$card_id; ?>'></a>*/
						foreach ($cardattributes as $attribute_id => $attribute_data) {
?>
						<div title="<?php echo (isset($attribute_data['tooltip']) && $attribute_data['tooltip'] != 'DEFAULT') ? $card_data[$attribute_data['tooltip']] : $attribute_data['name']; ?>" class="<?php echo $attribute_id; ?>"><span><?php echo $card_data[$attribute_id]; ?></span></div>
<?php 
				}
?>
					</div>
<?php 
			}
		}
?>
				</div>
<?php
	}
	if ($readonly == 0) {
?>
				<div id="delete-area"></div>
<?php
	}
?>
				<div style="clear:both"></div>
			</div>

		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		<script type="text/javascript" src="scripts/jquery.ui.touch-punch.min.js"></script>
		<script type="text/javascript" src="scripts/jquery.bpopup.min.js"></script>
		<script type="text/javascript" src="scripts/jquery.contextmenu.r2.js"></script>
		<script type="text/javascript" src="scripts/jquery.sidecontent.js"></script>
		<script type="text/javascript" src="scripts/jquery.zclip.min.js"></script>
		<script type="text/javascript" src="scripts/jquery.qtip.min.js"></script> 
		<script type="text/javascript" src="scripts/date.js"></script>
		<script type="text/javascript" src="scripts/jquery.numeric.js"></script> 
		<!-- script type="text/javascript" src="scripts/LZW.min.js"></script  -->
		<script type="text/javascript">
<?php
	foreach ($ruleTypes as $ruleType) {
		echo $ruleType['rule_js'];
	}
	if ($readonly == 0) {
?>
			$('document').ready(init);
<?php
	}
?>
			updateBoardHook();
			updateWipReached();
			
			var $hoverElem = null;
			
			function init(){
				$("#sidebar").sidecontent({
				    classmodifier: "sidecontent",
				    attachto: "rightside",
				    width: "84px",
					title: '<?php echo translate('Tools'); ?>',
				    opacity: "1",
				    pulloutpadding: "5",
				    textdirection: "vertical"
				});
		   
				setupCards($('.card'));
				
				$('.addcard').click(function() {
					var cardid = addCard(1, $(this).parents('.boardcolumn').attr('id'));
					if (cardid == "ERROR") {
						return;
					}
					sendCard(cardid, false);
					popupCardPopup(cardid, true, true);
				});
	
				$('[title!=""]').qtip({
					style: {
						classes: 'qtip-dark qtip-shadow qtip-rounded qtip-youtube'
					},					
					position: { 
						my: 'top center'
					}
				});
				
				$('html').keyup(function(e){
					var $thisHoverElem = $hoverElem;
					if ((e.keyCode == 46) && ($thisHoverElem != null)){
						deleteCard($('#'+$thisHoverElem));
					}
				}); 
				
				$('html').keypress(function(e) {
					var $thisHoverElem = $hoverElem;
					var charCode = e.which;
					if (charCode) {
						key = String.fromCharCode(charCode).toLowerCase();
						if ((key == '+') && ($thisHoverElem != null)) {
							cloneCard($thisHoverElem);
							return false;
						}
					}
				});

				$('.boardcolumn .title').dblclick(function(e) {
					if ( !this._toggle ) {
						$(this).parent().stop().animate({
					        "width": "155px",
					        overflow: "hidden" 
					    }, 500).addClass("hide-cards");
						this._toggle = true;
					}
					else {
						$(this).parent().stop().animate({
					        "width": "1000px",
					        overflow: "auto" 
					    }, 500).removeClass("hide-cards");
						this._toggle = false;
					}
					e.preventDefault();
					return false;
				});
				
				$('.boardcolumn').droppable( {
					accept: '.card, #plus-sign',
					tolerance: 'pointer',
					drop: function( event, ui ) {
						if (!$(ui.draggable).hasClass('card')) {
							var cardid = addCard(1, $(this).attr('id'));
							if (cardid == "ERROR") {
								return;
							}
							sendCard(cardid, false);
							popupCardPopup(cardid, true, true);
							return false;
						}
						var $column_id = $(this).attr('id');
						if (checkWip($column_id, false, true, ui) == "ERROR") {
							return "ERROR"; 
						}
						var $parent_column_id = $(ui.draggable).parent().attr('id');
						var $action_data={};
						$action_data['from'] = $parent_column_id;
						$action_data['to'] = $column_id;
						var $attribute_matching = 1;
						var $cardForRules = $(ui.draggable);
						var cardid = $cardForRules.attr('id');
<?php
	if (isset($rulesPerAction['moved'])) {
		printRules($rulesPerAction['moved'], '$cardForRules.draggable("option","revert",true);');
	}
	if (isset($generatorsPerTrigger['move_from'])) {
		foreach($generatorsPerTrigger['move_from'] as $generatorid) {
			$generatordata = $generators[$generatorid];
?>
						if ($parent_column_id == '<?php echo $generatordata['column']; ?>') {
							<?php echo $generatorTypes[$generatordata['generatorType']]['generator_js']; ?>
							updateCardAttribute(cardid, '<?php echo $generatordata['attribute']; ?>', value);
						}
<?php
		}
	}
	if (isset($generatorsPerTrigger['move_to'])) {
		foreach($generatorsPerTrigger['move_to'] as $generatorid) {
			$generatordata = $generators[$generatorid];
?>
						if ($column_id == '<?php echo $generatordata['column']; ?>') {
							<?php echo $generatorTypes[$generatordata['generatorType']]['generator_js']; ?>
							updateCardAttribute(cardid, '<?php echo $generatordata['attribute']; ?>', value);
						}
<?php
		}
	}
?>
						$(ui.draggable).detach().appendTo(this).css({top: 0,left: 0});
						updateAttrTooltips(cardid);
						updateCardClasses(cardid);
						commitUpdatedCard(cardid);
					}
				} ).sortable();
				
				$('#delete-area').droppable( {
					accept: '.card',
					drop: function( event, ui ) {
						if (!deleteCard($(ui.draggable))) {
							ui.draggable.draggable('option','revert',true);
						}
					}
				} );
				
				$('#delete-icon').droppable( {
					accept: '.card',
					tolerance: 'touch',
					drop: function( event, ui ) {
						if (!deleteCard($(ui.draggable))) {
							ui.draggable.draggable('option','revert',true);
						}
					}
				} ).draggable({
					appendTo: "body",
					cursor: 'move',
					revert: true, 
					helper: function(event, ui) {        
						return $("<div></div>").append( $(this).clone().html()).addClass('dragged-trash-can').appendTo("body");
					}
				}).dblclick(function(e) {
					popupArchivePopup('<?php echo $current_board; ?>');
				});
				
				$('#plus-sign').draggable({
					appendTo: "body",
					cursor: 'move',
					revert: true, 
					helper: function(event, ui) {        
						return $("<div></div>").append( $(this).clone().html()).addClass('dragged-plus-sign').appendTo("body");
					}
				});
				
				$('#home-button').contextMenu('header', {
					menuStyle: {
						width: '200px'
					},
					shadow : false,
					triggerEvent : 'click'
				});
<?php
	if (isset($cardid)) {
?>
				edit_card($('#<?php echo $cardid; ?>'));
<?php 		
	}
?>
			}
<?php
	$boardjs = get_board_javascript($current_board);
	echo $boardjs;
	if (!isset($boardjs) || (strpos($boardjs,"updateBoardHook()")==0)) {
?>
			function updateBoardHook() {
/*				var maxHeight = 0;
				$('#board').children('.boardcolumn').css('height', 'auto');
				$('#board').children('.boardcolumn').each(function() {
					var height = $(this).outerHeight();
					if ( height > maxHeight ) {
						maxHeight = height;
					}
				});
				$('.boardcolumn').css('height', maxHeight);
				$('#delete-area').css('height', maxHeight);*/
			}		
<?php
	}
?>		
			function cloneCard(cloneid) {
				var id = addCard(0, '<?php reset($boardcolumns); echo key($boardcolumns); ?>');
				if (id != "ERROR") {
<?php
	foreach ($cardattributes as $attribute_id => $attribute_data) {
		if ($attribute_id != 'cardNumber' && $attribute_id != 'cardCreationDate') {
?>
					var attributeHtml = getAttributeValue(cloneid, "<?php echo $attribute_id; ?>");
					updateCardAttributes(id, '<?php echo $attribute_id; ?>', attributeHtml);
					if (attributeHtml && (attributeHtml.length != 0)) {
						$('#'+id).addClass("<?php echo $attribute_id; ?>-"+attributeHtml.replace(/[^a-zA-Z0-9]+/g, ""));
					}
<?php
		}
	}
?>
				}
				updateAttrTooltips(id);
				updateCardClasses(id);
				commitUpdatedCard(id);
				return id;
			}

			function addCard($addAttributesValues, $boardColumn) {
				var $column_id = $boardColumn;
				if (checkWip($column_id, false, true, false) == "ERROR") {
					return "ERROR"; 
				}
				
				var $newcard = $('<div class="card" draggable="true"></div>');
				var date = new Date();
				var components = [date.getYear(),date.getMonth(),date.getDate(),date.getHours(),date.getMinutes(),date.getSeconds(),date.getMilliseconds()];
				var id = components.join("");
				$newcard.attr('id', id);
				//$newcard.append("<a href='#' class='card_url' data-url='data-url='<?php echo currentPageURL().'?boardid='.$current_board.'&cardid='; ?>"+id+"'></a>");
				setupCards($newcard);
<?php
	foreach ($cardattributes as $attribute_id => $attribute_data) {
?>
				var $<?php echo $attribute_id; ?>="";
<?php
		if ($attribute_id == "cardCreationDate") {
?>
				$<?php echo $attribute_id; ?>=formatDate(date, "yyyy-MM-dd");
<?php
		}
		else if ($attribute_id == "cardNumber") {
?>
				var maxNumber = 0;
				$('.cardNumber span').each(function() {
					if (parseInt($(this).html()) > maxNumber) {
						maxNumber = parseInt($(this).html());
					}
				});
				maxNumber++;
				$<?php echo $attribute_id; ?>= ""+maxNumber;
<?php
		}
		else {
?>
				if ($addAttributesValues != 0) {
					var $val = $("#<?php echo $attribute_id; ?>").val();
					if ($val) {
						$<?php echo $attribute_id; ?>=$val.replace(/\r?\n/g, "<br />");
					}
				}
<?php 
		}
	}
	foreach ($cardattributes as $attribute_id => $attribute_data) {
?>
				if ($<?php echo $attribute_id; ?>.length != 0) {
					$newcard.addClass("<?php echo $attribute_id; ?>-"+$<?php echo $attribute_id; ?>.replace(/[^a-zA-Z0-9]+/g, ""));
				}

				var $new_<?php echo $attribute_id; ?> = $('<div title=\'<?php echo (isset($attribute_data['tooltip']) && $attribute_data['tooltip'] != 'DEFAULT') ? '\'+$'.$attribute_data['tooltip'].'+\'' : $attribute_data['name']; ?>\' class=\'<?php echo $attribute_id; ?>\'><span>'+$<?php echo $attribute_id; ?>+'</span></div>');
				$newcard.append($new_<?php echo $attribute_id; ?>);
<?php
	}
?>
				if ($addAttributesValues != 0) {
<?php
	foreach ($cardattributes as $attribute_id => $attribute_data) {
?>
					$("#<?php echo $attribute_id; ?>").val("");
<?php
	}
?>
				}
				$('#'+$column_id).append($newcard);
				return id;
			}
			
			function sendCard(cardid, $async) {
				if($async==null) $async = true;
				var $cards = {};
				$("#"+cardid+".card").each(function(){
					var $id = $(this).attr('id');
					$cards[$id] = {};
					var $board = $(this).parent().attr('id');
					$cards[$id]['board']=$board;
<?php
	foreach ($cardattributes as $attribute_id => $attribute_data) {
?>
					$attribute_value = $(this).children(".<?php echo $attribute_id; ?>").find("span").html();
					//if ($attribute_value != '' && $attribute_value != '0' && $attribute_value != 'false') {
						$cards[$id]["<?php echo $attribute_id; ?>"]= $attribute_value;
					//}
<?php
	}
?>
				});
				$.ajax({ type: "POST", async: $async, url: "add_card_to_board.php", data: {cards: JSON.stringify($cards), boardid: <?php echo $current_board; ?>} });
			}

			function commitUpdatedCard($cardid) {
				updateBoardHook();					
				sendCard($cardid);
				updateWipReached();
			}
			
			function commitDeletedCard($cardid) {
				updateBoardHook();					
				$.ajax({ type: "POST", url: "remove_card_from_board.php", data: {cardid: $cardid, boardid: <?php echo $current_board; ?>} });
				updateWipReached();
			}
				
			function exitFullScreen() {
				var elem = document.getElementById("board");
				if (document.cancelFullscreen) {
					document.cancelFullscreen();
				} 
				else if (document.mozCancelFullScreen) {
					document.mozCancelFullScreen();
				} 
				else if (document.webkitCancelFullscreen) {
					document.webkitCancelFullscreen();
				}				
				else {
					$('#board').removeClass('fullscreen');
					$('#fullscreen').removeClass('fullscreen');
				}
			}
			
			function simulateDblClickTouchEvent(oo) {
				var $oo = !oo?{}:$(oo);
				if( !$oo[0] ) { 
					return false; 
				}
		
				$oo.bind('touchend', function(e) {
					var ot = this,
					ev = e.originalEvent;
		
					if( ev && typeof ev.touches == 'object' && ev.touches.length > 1 ) { 
						return; 
					}
		
					ot.__taps = (!ot.__taps)?1:++ot.__taps;
		
					// don't start it twice
					if( !ot.__tabstm ) {
						ot.__tabstm = setTimeout( function() {
							if( ot.__taps >= 2 ) {
								ot.__taps = 0;
								$(ot).trigger('dblclick');
								return false; 
							}
							ot.__tabstm = 0;
							ot.__taps = 0;
						},800);
					}
				});
				return true;
			};
	
			function updateWipReached() {
<?php
	foreach ($boardcolumns as $column_name => $column_data) {
?>
				$column_id='<?php echo $column_name; ?>';
				$('#'+$column_id).removeClass("wip-reached");
				var $wipCheck = checkWip($column_id, true, false, false);
<?php
	}
?>
			}
			
			function edit_card($cardForRules) {
				exitFullScreen();
				cardid=$cardForRules.attr('id');
				var $column_id = $cardForRules.parent().attr('id');
				var $action_data={};
				$action_data['column'] = $column_id;
				var $attribute_matching = 1;
<?php
	if (isset($rulesPerAction['editcard'])) {
		printRules($rulesPerAction['editcard'], '');
	}
?>
				popupCardPopup(cardid, false, false);
			}
			
			function popupCardPopup(cardid, doCommit, deleteOnCancel) {
				$('#card_popup').bPopup({
					loadUrl: 'edit_card.php?boardid=<?php echo $current_board; ?>&cardid='+cardid,
					onClose: function(){
						if ($('#saved').val() == 'yes') { 	
							var attrValues={};					
<?php
	foreach ($cardattributes as $attribute_id_edit => $attribute_data_edit) {
?>
							var value = "false";
							var field = $('#edit-card .attribute-value #<?php echo $attribute_id_edit; ?>');
							if (field.attr('type') == "checkbox") {
								$('#edit-card .attribute-value #<?php echo $attribute_id_edit; ?>:checked').each(function() { value = "true"; });  
							}
							else {
								value = field.val();
							}
							attrValues['<?php echo $attribute_id_edit; ?>'] = value;
							//updateCardAttribute(cardid, '<?php echo $attribute_id_edit; ?>', value)
<?php
	}
?>							
							updateCardAttributes(cardid, attrValues);
							updateAttrTooltips(cardid);
							updateCardClasses(cardid);
							if (doCommit) {
								commitUpdatedCard(cardid);							
							}
							else {
								sendCard(cardid);
							}
						}
						else if (deleteOnCancel) {
							$('#'+cardid).fadeOut(300, function(){ 
								$('#'+cardid).remove();
								commitDeletedCard(cardid);
							});
						}
					}
				});				
			}
			
			function popupArchivePopup(boardid) {
				$('#card_popup').bPopup({
					loadUrl: 'retrieve_from_archive_dialog.php?boardid='+boardid 
				});				
			}
			
			function popupColumnsPopup(boardid) {
				$('#card_popup').bPopup({
					loadUrl: 'customize_columns_dialog.php?boardid='+boardid 
				});
/*				var tag = $("<div></div>");
				$.ajax({
					url: 'customize_columns_dialog.php?boardid='+boardid,
					success: function(data) {
						tag.html(data).dialog({modal: true}).dialog('<?php echo translate('Customize columns'); ?>');
					}
				});*/
			}

			function updateCardAttribute(cardid, attributeid, value) {
				if (!value) {
					value = "";
				}
				$('#'+cardid+' .'+attributeid+' span').html(value.replace(/\r?\n/g, "<br />"));
			}

			function updateCardAttributes(cardid, attrValues) {
				for (var attributeid in attrValues) {
					value = attrValues[attributeid];
					updateCardAttribute(cardid, attributeid, value);
				}
			}

			function updateAttrTooltips(cardid) {
<?php
foreach ($cardattributes as $attribute_id_edit => $attribute_data_edit) {
	if (isset($attribute_data_edit['tooltip']) && $attribute_data_edit['tooltip'] != 'DEFAULT') {
?>
				var $tooltip = getAttributeValue(cardid, "<?php echo $attribute_data_edit['tooltip']; ?>");
<?php		
	}
	else {
?>
				var $tooltip = '<?php echo $attribute_data_edit['name']; ?>';
<?php
	}
?>
				$("#"+cardid+" .<?php echo $attribute_id_edit; ?>").attr('title', $tooltip);
				$("#"+cardid+" .<?php echo $attribute_id_edit; ?>").qtip({
					style: {
						classes: 'qtip-dark qtip-shadow qtip-rounded qtip-youtube'
					},					
					position: { 
						my: 'top center'
					}
				});
<?php
}
?>							
			}
			
			function updateCardClasses(cardid) {
				$('#'+cardid).removeClass();
				$('#'+cardid).addClass('card');
				$('#'+cardid).addClass('ui-draggable');
<?php
foreach ($cardattributes as $attribute_id_edit => $attribute_data_edit) {
?>
				var attributeHtml = getAttributeValue(cardid, "<?php echo $attribute_id_edit; ?>");
				if (attributeHtml && attributeHtml.length != 0) {
					$('#'+cardid).addClass("<?php echo $attribute_id_edit; ?>-"+attributeHtml.replace(/[^a-zA-Z0-9]+/g, ""));
				}
<?php
}
?>							
			}
			
			function setupCardContextMenu(element) {
				element.contextMenu('myMenu1', {
					triggerEvent : 'contextmenu',
					bindings: {
					    'delete': function(t) {
							deleteCard($('#'+t.id));
					    },
					    'clone': function(t) {
					      cloneCard(t.id);
					    },
					    'edit': function(t) {
					      edit_card($(t));
					    },
					}
				});
			}
			
			function setupCards(element) {
				element.draggable({
	                appendTo: 'body',
	                helper: 'clone',
	                zIndex: 100,
	                scroll: false,
	                start: function() { $(this).css("visibility","hidden"); },
	                stop: function() { $(this).css("visibility","visible"); }, 
                	cursor: 'move',
                	revert: 'invalid'
				}).droppable( {
					accept: '#delete-icon',
					drop: function( event, ui ) {
						deleteCard($(this));
					}
				});				
				element.mouseenter(function() {
					$hoverElem = $(this).attr('id');
				});
				element.mouseleave(function() {
					$hoverElem = null;
				});
				setupCardContextMenu(element);
				simulateDblClickTouchEvent(element);
				element.dblclick(function(e) {
					var $cardForRules = $(this);
					edit_card($cardForRules);
					e.stopPropagation();
				});	
/*		        element.find('.card_url').each(function() {		        
		        	$(this).zclip({
						path: 'http://bitlite.de/kanban/ZeroClipboard.swf',
						copy: function(){ return $(this).data('url'); },
						afterCopy: function() {
							$(this).parent().draggable( 'option',  'revert', true ).trigger( 'mouseup' );
	                        alert("<?php echo translate('The URL of this card was copied to you clipboard and is ready to be pasted.'); ?>");
						},
		        	});
				});*/			
			}
	
			function checkWip($column_id, $setReached, $doAlert, $doRevert) {
				var $wip_limit = $('#'+$column_id).data('wip-limit');
				if ($wip_limit > 0) {
					var $current_no_of_cards = $('#'+$column_id+' .card').length;
					if ($current_no_of_cards >= $wip_limit) {
						if ($setReached) {
							$('#'+$column_id).addClass("wip-reached");
						}
						if ($doAlert) {
							alert('<?php echo translate('WIP Limit reached !'); ?>');
						}
						if ($doRevert) {
							$doRevert.draggable.draggable('option','revert',true);
						}
						return "ERROR";
					}
				}
			}
		
			function deleteCard($cardForRules) {
				var $column_id = $cardForRules.parent().attr('id');
				var $action_data={};
				$action_data['column'] = $column_id;
				var $attribute_matching = 1;
<?php
	if (isset($rulesPerAction['deletecard'])) {
		printRules($rulesPerAction['deletecard'], '');
	}
?>
				var moveToTrash=confirm('<?php echo translate('Do you really want to delete this card?'); ?>');
				if (moveToTrash) {
					$cardForRules.fadeOut(300, function(){
						archiveDeletedCard($cardForRules);
						var id = $cardForRules.attr('id');
						$cardForRules.remove();
						commitDeletedCard(id);
					});
					return true;
				}
				return false;
			}
			
			function archiveDeletedCard($cardForRules) {
				$cards = {};
				var $id = $cardForRules.attr('id');
				$cards[$id] = {};
				var $board = $cardForRules.parent().attr('id');
				$cards[$id]['board']=$board;
<?php
	foreach ($cardattributes as $attribute_id => $attribute_data) {
?>
				$cards[$id]["<?php echo $attribute_id; ?>"]= $cardForRules.children(".<?php echo $attribute_id; ?>").find("span").html();
<?php
	}
?>
				$.ajax({ type: "POST", async: false, url: "archive_cards.php", data: {cards: $cards, boardid: <?php echo $current_board; ?>}});				
			}
			
			function undo() {
				$.ajax({
					type: "POST", 
					url: "undo.php", 
					data: {boardid: <?php echo $current_board; ?>},
					success: function() {
						document.location.reload(true);
					}
				});
			}
			
			function redo() {
				$.ajax({ 	
					type: "POST", 
					url: "redo.php", 
					data: {boardid: <?php echo $current_board; ?>},
					success: function() {
						document.location.reload(true);
					}
				});				
			}
			
			function fullscreen() {
				var elem = document.getElementById("board");
				if (elem.requestFullscreen) {
				  elem.requestFullscreen();
				} 
				else if (elem.mozRequestFullScreen) {
				  elem.mozRequestFullScreen();
				} 
				else if (elem.webkitRequestFullscreen) {
				  elem.webkitRequestFullscreen();
				}				
				else {
					$('#board').toggleClass('fullscreen');
					$('#fullscreen').toggleClass('fullscreen');
				}
			}

			function getAttributeValue($cardid, $attributeid) {
				return $("#"+$cardid+" ."+$attributeid+" span").html();
			}
		</script>
		<div id='sidebar'>
			<div class="sidebar-action" id="delete-icon" title="<?php echo translate('Drag a card here to delete it or drag this icon to a card to delete it'); ?>"></div>
			<div class="sidebar-action" id="plus-sign" title="<?php echo translate('Drag this icon to a board column to add a new card'); ?>"></div>
			<a class="sidebar-action" id="columns-sign" href="#" onclick="popupColumnsPopup('<?php echo $current_board; ?>');" title="<?php echo translate('Customize columns'); ?>"></a>
<?php
	if ($readonly == 0) {
?>
			<a class="sidebar-action" id="undo" href="#" onclick="undo();" title="<?php echo translate('Undo'); ?>"></a>
			<a class="sidebar-action" id="redo" href="#" onclick="redo();" title="<?php echo translate('Redo'); ?>"></a>
<?php
	}
?>
			<a class="sidebar-action" id="fullscreen" href="#" onclick="fullscreen();" title="<?php echo translate('Fullscreen'); ?>"></a>
			<a class="sidebar-action" id="list-view-link" href="generic_list_view.php?type=board&boardid=<?php echo $current_board; ?>&readonly=<?php echo $readonly; ?>" title="<?php echo translate('List view'); ?>"></a>
			<div class="sidebar-action" id="home-button" title="<?php echo translate('More tools...'); ?>"></div>
		</div>
		<div id="card_popup"></div>
<?php
	if ($readonly == 0) {
?>
		<div class="contextMenu" id="myMenu1">
			<ul>
				<li id="delete" title="<?php echo translate('Delete this card'); ?>"><?php echo translate('Delete'); ?></li>
				<li id="clone" title="<?php echo translate('Create a clone of this card'); ?>"><?php echo translate('Clone'); ?></li>
				<li id="edit" title="<?php echo translate('Edit this card'); ?>"><?php echo translate('Edit'); ?></li>
			</ul>
	    </div>
<?php
	}
?>
		<div id="copyright">&copy;2013 <a href='http://amazingweb.de'>amazingweb Gmbh</a></div>
	</body>
</html>
<?php
}
?>
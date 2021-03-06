<?php
require_once('auth.php');
require_once('app/config.php');

// help
function has_help_file() {
	global $ne2_config_info;
	$help_file = $ne2_config_info['help_path'] .'website_editor'. $ne2_config_info['help_filesuffix'] ;
	return file_exists($help_file);
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Logo bearbeiten - <?php echo($ne2_config_info['app_titleplain']); ?></title>
<link rel="stylesheet" type="text/css" href="css/bootstrap.css?<?php echo date('Ymdis'); ?>" />
<link rel="stylesheet" type="text/css" href="css/bootstrap-responsive.css?<?php echo date('Ymdis'); ?>" />
<link rel="stylesheet" type="text/css" href="css/style.css?<?php echo date('Ymdis'); ?>" />
<script src="js/jquery-1.10.1.js" type="text/javascript"></script>
<script src="js/bootstrap.js" type="text/javascript"></script>
<script type="text/javascript" src="js/json2.js"></script>
<script type="text/javascript">
var imgObj, imgW, imgH, jsonArray;

var helpText = "";
var thisConf = "<?php echo($ne2_config_info['website']); ?>";
function loadConf(confFileName) {
	$.getJSON("app/edit_conf.php?r=" + Math.random(), {
		"oper": "get_conf",
		"conf_file_name": confFileName
	}, loadContentCallback);
}
// function getValueByName(jdata, wert){
	// var tmpRet = "parameter nicht gefunden";
	// $.each(jdata, function(intInd, value){
		// if (value.opt_name == wert){
			// tmpRet = value.opt_value;
			// return false;
		// }
	// });
	// return tmpRet;
// }
	
	 
function loadContentCallback(data) {
	jsonArray = data;
	
	$.each(data, function( intIndex, obj){
		$("#"+obj.opt_name).val(obj.opt_value);
	});
	
	imgH = $("#logo-Height").val();
	imgW = $("#logo-Width").val();
	
	if($("#logo-URL").val() != "") {
		imgPreLoad();
	}
}

function previewImageLoadCallback() {
	if(imgObj.complete) {
		if(imgW ==""){
		imgW = imgObj.width;
		}
		if(imgH ==""){
		imgH = imgObj.height;
		
		}
	}
}

function saveContentCallback(data) {
	//alert(data);
	saveVars(data);
//	location.reload();
}

//saveVars function, for every button
function saveVars(datab4){
	$.each(jsonArray, function(ind, obj){
		if($("#"+obj.opt_name).length){
			jsonArray[ind].opt_value = $("#"+obj.opt_name).val();
		}
	});
	$.post("app/edit_conf.php", {
			"oper": "set_conf",
			"conf_file_name": thisConf,
			"jsonData": JSON.stringify(jsonArray)
		}, function(rdata) {
			alert(datab4);
			loading(false);
			loadConf(thisConf);
		});
}


function imgPreLoad(){
	if($("#logo-URL").val() != ""){
		var imgsrctmp = $("#logo-URL").val();
		var imgalttmp = $("#logo-Alt").val();
		imgH = $("#logo-Height").val();
		imgW = $("#logo-Width").val();
		imgObj = new Image();
		imgObj.src = imgsrctmp;
		imgObj.onload = previewImageLoadCallback;
		var imgStr = "<img alt=\"" + imgalttmp + "\" height=\"" + imgH + "\" width=\"" + imgW + "\" src=\"" + imgsrctmp + "\" />";
	}
	else{var imgStr = "";}
	$("#imgPrev").html(imgStr);
}

function loading(yesNo){
	if (yesNo) {
		if ($('div.tmpLoadingOverlay').length){
			$('div.tmpLoadingOverlay').fadeIn(100);
		}else{
			$('body').append('<div class="tmpLoadingOverlay"><div style="z-index:1000;position:fixed;top:0;bottom:0;left:0;width:100%;height=100%;background:#000;opacity:0.35;-moz-opacity:0.35;filter:alpha(opacity=35);visibility:visible;"><p class="p_tmp_class_loading"  style="position:fixed;top:50%;width:100%;z-index:1001;color:#fff;font-size:20px;font-weight:bold;text-align:center;">Loading...</p></div></div>');
		}
		$("body").css("overflow", "hidden");
	}else if(!yesNo){
		$('div.tmpLoadingOverlay').fadeOut(1000);
		$("body").css("overflow", "auto");
		}
}


$(document).ready(function() {
	loadConf(thisConf);
	//on change any param of logo, reload it (preview)
	// $("#bildBlock :input").bind($.browser.msie ? 'change':'input', function() {
	// setTimeout(imgPreLoad, 500);
	// });
	//all inputs add class 'textBox'
	$('input[type="text"]').attr('class', 'textBox');
	
	//save all inputs to conf.
	$("#btnSaveall").click(function saveAll() {
		$.each(jsonArray, function(ind, obj){
			if($("#"+obj.opt_name).length){
				jsonArray[ind].opt_value = $("#"+obj.opt_name).val();
			}
		});
		loading(true);
		btnTmp = $(this);
		btnTmpVal = $(this).val();
		$(this).val("Moment...");
			$(this).attr("disabled", "disabled");
			$.post("app/edit_conf.php", {
				"oper": "set_conf",
				"conf_file_name": thisConf,
				"jsonData": JSON.stringify(jsonArray)
			}, function(rdata) {
				alert(rdata);
				btnTmp.val(btnTmpVal);
				btnTmp.removeAttr("disabled");
				loading(false);
				loadConf(thisConf);
			});
	});



	$("#chkAllowHtml").click(function(){
		if ($("#chkAllowHtml:checked").length > 0){
		alert("HTML auf eigene Gefahr verwenden");
		}
	});
	$("#btnUpdate").click(function() {
		var text = $("#name-des-Webauftritts").val();
		var desc = $("#kurzbeschreibung-zum-Webauftritt").val();
		var imgUrl = $("#logo-URL").val();
		var imgAlt = $("#logo-Alt").val();
		var siteTitle = $("#titel-des-Webauftritts").val();
		
		// if image specified, then alt-text cannot be empty!
		if(imgUrl != "") {
			if(imgAlt == "") {
				alert("Bitte geben Sie die Beschreibung zu Ihrem Logo an!");
				return false;
			}
		}
		
		if(confirm("Wollen Sie wirklich speichern?")) {
			loading(true);
			if(imgUrl != "") {
				img = "<img alt=\"" + imgAlt + "\" src=\"" + imgUrl + "\" width=\"" + imgW + "\" height=\"" + imgH + "\" border=\"0\" />";
			}else{
				img = "";
			}
			var templname = $("#selTempl").val();
			var pdata = {
				"content_text": text,
				"content_desc": desc,
				"content_img": img,
				"content_img_alt": imgAlt,
				"site_title_text": siteTitle,
				"content_allow_html": $("#chkAllowHtml:checked").length > 0 ? true : false
			};
			$.post("app/edit_logo.php", {
				"json_oper": "update_content",
				"json_content": JSON.stringify(pdata),
				"template_name": templname
			}, saveContentCallback);
		}
	});
	
	$("#btnLoadLogo").click(function() {
		$.getJSON("app/edit_logo.php?r=" + Math.random(), {
			"json_oper": "get_content",
			"template_name": $("#selTempl").val()
		}, loadContentCallback);
	});

	
	$("#btnUpdateExisted").click(function() {
		if(confirm("Wollen Sie wirklich alle Seiten aktualisieren und mit dem neuen Titel und/oder Logo versehen?")) {
			loading(true);
			var text = $("#name-des-Webauftritts").val();
			var desc = $("#kurzbeschreibung-zum-Webauftritt").val();
			var imgUrl = $("#logo-URL").val();
			var imgAlt = $("#logo-Alt").val();
			var siteTitle = $("#titel-des-Webauftritts").val();
			var img = '';
			if(imgUrl != "") {
				img = "<img alt=\"" + imgAlt + "\" src=\"" + imgUrl + "\" width=\"" + imgW + "\" height=\"" + imgH + "\" border=\"0\" />";
			}
			var templname = $("#selTempl").val();
			var pdata = {
				"content_text": text,
				"content_desc": desc,
				"content_img": img,
				"content_img_alt": imgAlt,
				"site_title_text": siteTitle,
				"content_allow_html": $("#chkAllowHtml:checked").length > 0 ? true : false
			};
			$.post("app/edit_logo.php", {
				"json_oper": "update_content_all",
				"json_content": JSON.stringify(pdata),
				"template_name": templname
			}, saveContentCallback);
		}
	});
	
	$("#btnCopySiteName").click(function() {
		$("#titel-des-Webauftritts").val($("#name-des-Webauftritts").val());
	});
	
	
	
	// initial load	
	// $.getJSON("app/edit_logo.php?r=" + Math.random(), {
		// "json_oper": "get_content",
		// "template_name": $("#selTempl").val()
	// }, loadContentCallback);
	
	// help
	$(".help-container .fetch").click(function() {
		var $this = $(this),
			content = $this.siblings(".hover-popover").find("content").html(),
			showContent = function(content) {
				$this.siblings(".hover-popover").show().find(".content").html(content);
			};

		if(content === undefined || content == "") {
			$.get("app/get_help.php?r=" + Math.random(), {
				"page_name": "website_editor"
			}, showContent);
		} else {
			showContent(content);
		}
	});

	$(".hover-popover .dismiss").click(function() {
		$(this).closest(".hover-popover").hide();
	});
	
	
});


//wenn neue conf Dateien fehlen, die neu generieren.
		<?php
		if(!file_exists("../../".$ne2_config_info['website']) || !file_exists("../../".$ne2_config_info['variables'])){
		?>
			//only for loading /beginn------------
			var loaded = new Object;
			var website = "<?php echo $ne2_config_info['website']; ?>";
			var variables = "<?php echo $ne2_config_info['variables']; ?>";
			loaded[website] = false;
			loaded[variables] = false;
			$(document).ready(function() {
				loading(true);
			});
			
			function loadingCheck(){
				if (loaded[website] && loaded[variables]){
					loading(false);
					clearInterval(loadingAktive);
					loadConf(thisConf);
				}
			}
			var loadingAktive = setInterval("loadingCheck()", 500);
			//only for loading /end--------------
			
			function create_conf(confName, confData){
				$.post("app/create_conf.php", {
						"oper": "create_conf",
						"name": confName,
						"jsonData": JSON.stringify(confData)
				}, function(rdata) {
						loaded[confName] = true;
				});
			}
			
				alert('Hinweis: Eine oder mehrere Konfigurationsdateien fehlen. Diese werden nun automatisch neu erstellt.');
				var json_data = [];
				//load kontakt daten von contactdata.conf save to json_data
				$.get("app/load_osm.php", function(data) {
					var arrValues = data.split('\\:\\');
					var valueNames = new Array("name", "strasse", "plz", "ort", "kontakt1-name", "kontakt1-vorname", "telefon", "fax", "email");
					for(i=0; i<11; i++){
						var item = {
						"opt_name": valueNames[i],
						"opt_value": arrValues[i]
						};
						json_data.push(item);
					}
					//load logo daten von vorlage. save to json_data
					$.getJSON("app/edit_logo.php?r=" + Math.random(), {
					"json_oper": "get_content",
					"template_name": "seitenvorlage.html"
					}, function(data){
						json_data.push({"opt_name": "name-des-Webauftritts","opt_value": data.content_text});
						json_data.push({"opt_name": "titel-des-Webauftritts","opt_value": data.site_title_text});
						json_data.push({"opt_name": "kurzbeschreibung-zum-Webauftritt","opt_value": data.content_desc});
						json_data.push({"opt_name": "logo-URL","opt_value": data.content_img});
						json_data.push({"opt_name": "logo-Alt","opt_value": data.content_img_alt});
						json_data.push({"opt_name": "logo-Width","opt_value": ""});
						json_data.push({"opt_name": "logo-Height","opt_value": ""});
						create_conf(website, json_data);
						create_conf(variables, "");
					});
				});
			
		<?php
		}
		?>

</script>
</head>

<body id="bd_Logo">

	<?php require('common_nav_menu.php'); ?>

	<div class="container page" id="wrapper">
		
		<div class="page-header">
            <h2 class="page-header">Webseitendaten bearbeiten</h2>
            <div class="pull-right">
				
				 <?php
	            	// help
	            	if (has_help_file()) {
	            ?>
	            	<div class="help-container">
						<a class="fetch btn btn btn-primary btn-light" href="javascript:void(0);"><i class="icon-white">?</i> Hilfe</a>
						<div class="hover-popover">
							<div class="header clearfix">
								<h4>Hilfe</h4>
								<div class="pull-right">
									<a class="dismiss btn btn-black-white" href="javascript:void(0);">Ok</a>

								</div>
							</div>

							<div class="content"></div>
						</div>
					</div>
				<?php
	            	} 
	            ?>
	        </div>
        </div>


        <div class="tabbable"> <!-- Only required for left/right tabs -->
		  <ul class="nav nav-tabs nav-tabs-custom">
		    <li class="active"><a href="#logo" data-toggle="tab">Logo bearbeiten</a></li>
		    <li><a href="#osm" data-toggle="tab">OpenStreetMap-Kontaktseite erstellen</a></li>
		  </ul>
		  <div class="tab-content">
		    <div class="tab-pane active" id="logo">
				<form action="" method="post" name="frmEdit" id="frmEdit">
					<div class="row">
						<fieldset>
							<div class="span6">
								<div class="content">
									<input type="hidden" id="selTempl" name="selTempl" value="seitenvorlage.html">
									<div class="control-group">
										<label for="name-des-Webauftritts">Name des Webauftritts:</label>
										<input type="text" id="name-des-Webauftritts" name="name-des-Webauftritts" size="40" />
										<label class="checkbox">
									      <input type="checkbox" id="chkAllowHtml" name="chkAllowHtml" value="ja"> Eigene HTML-Anweisungen zulassen
									    </label>
									</div>
									<div class="control-group">
										<label for="titel-des-Webauftritts">Titel des Webauftritts (auf dem Browser-Titelbar gezeigt wird):</label>
										
										<div class="input-append">
											<input type="text" id="titel-des-Webauftritts" name="titel-des-Webauftritts" size="40" />
											<input type="button" id="btnCopySiteName" class="btn" value="Aus Name Kopieren" />
										</div>
									</div>
									<div class="control-group">
										<label for="kurzbeschreibung-zum-Webauftritt">Kurzbeschreibung zum Webauftritt:</label>
										<input type="text" id="kurzbeschreibung-zum-Webauftritt" name="kurzbeschreibung-zum-Webauftritt" size="60" />
									</div>
								</div>
							</div>
							<div class="span6">
								<div class="content">
									<div class="control-group">
										<label>Bild f&uuml;r das Logo (Optional):</label>
										<div class="input-append">
											<input type="text" id="logo-URL" name="logo-URL" size="40" />
											<a class="btn" href="file_editor.php" target="_blank">Bild hochladen</a>
										</div>
										<div id="imgPrev"></div>
									</div>
									<div class="control-group">
										<label>Alternativer Text f&uuml;r das Bildlogo (falls die Grafik nicht angezeigt oder angesehen werden kann):</label>
										<input type="text" id="logo-Alt" name="logo-Alt" size="40" />
									</div>
									
									<div class="control-group">
										<label>Bild H&ouml;he (optional):</label>
										<input type="text" id="logo-Height" name="logo-Height" size="40" />
										<label>Bild Breite (optional):</label>
										<input type="text" id="logo-Width" name="logo-Width" size="40" />
									</div>
								
									
									
								</div>
							</div>
						</fieldset>
						<div class="span12 form-footer">
							<input type="button" id="btnUpdate" name="btnUpdate" value="Seitenvorlage aktualisieren" class="btn btn-light btn-large btn-success" />
							<input type="button" id="btnUpdateExisted" name="btnUpdateExisted" value="Existierende Seiten aktualisieren" class="btn btn-light btn-large btn-success" />
							<img id="ajaxLoader" alt="please wait..." src="ajax-loader.gif" border="0" width="16" height="16" style="display:none;" />
						</div>		
					</div>
				</form>
		    </div>

		    <div class="tab-pane" id="osm">
		      <!-- Kontakt Block ab hier -->
				<script type="text/javascript">
				<!--
				var helpText = "";

				$(document).ready(function() {
					// $("#btnLoadConf").click(function() {
						// $.get("app/load_osm.php", function(data) {
							// var arrValues = data.split('\\:\\');
							// var n = 1; 
							// $.each(
								// arrValues, function( intIndex, objValue){
									// $("#" + n).attr("value", objValue);
									// n++;
								// }
							// );
							// var lat = arrValues[9];
							// var lon = arrValues[10];
							// setCenter(lat, lon);
						// });
					// });
					
					$("#save_osm").click(function() {
						loading(true);
						var inst = $("#name").attr("value");
						var street = $("#strasse").attr("value");
						var plz = $("#plz").attr("value");
						var city = $("#ort").attr("value");
						var personname = $("#kontakt1-name").attr("value");
						var personvorname = $("#kontakt1-vorname").attr("value");
						var telefon = $("#telefon").attr("value");
						var fax = $("#fax").attr("value");
						var email = $("#email").attr("value");

						$.post("app/save_osm.php", { inst: inst, street: street, plz: plz, city: city, personname: personname, personvorname: personvorname, telefon: telefon, fax: fax, email: email}, function(resp) {
							saveVars("kontakt.shtml wurde erstellt");
						});
					});
				});
				-->
				</script>
				<form action="" method="post" name="frmEdit" id="frmEdit">
					<fieldset>
						
					</fieldset>
				</form>
				<form action="" method="" id="suche">
					<fieldset style="min-width:880px;">
						<legend>Kontaktdaten</legend>
						<div style="float:left;clear:none;">
							<p>
								<label>Institut</label><br />
								<input type="text" id="name" name="inst" />
							</p>
							<p>
								<label>Stra&szlig;e</label><br />
								<input type="text" id="strasse" name="street" />
							</p>
							<p>
								<label>PLZ, Stadt</label><br />
								<input type="text" id="plz" name="plz" />
								<input type="text" id="ort" name="city" />
							</p>
							<p>
								<label>Kontaktperson (Name, Vorname)</label><br />
								<input type="text" id="kontakt1-name" name="person-name" />
								<input type="text" id="kontakt1-vorname" name="person-vorname" />
							</p>
							<p>
								<label>Telefon</label><br />
								<input type="text" id="telefon" name="telefon" />
							</p>
							<p>
								<label>Fax</label><br />
								<input type="text" id="fax" name="fax" />
							</p>
							<p>
								<label>Email</label><br />
								<input type="text" id="email" name="email" />
							</p>
							<p>
								<input id="save_osm" type="button" class="submit btn btn-large btn-success btn-light" name="submit" value="Kontaktseite erstellen" />
							</p>
							<br>
							<p>
								<input id="btnSaveall" type="button" class="submit btn btn-large btn-primary btn-light" name="submit" value="Nur Variablen speichern" />
							</p>
						</div>

					</fieldset>
				</form>
		    </div>
		  </div>
		</div>
	</div>
	
<?php require('common_footer.php'); ?>	

</body>

</html>

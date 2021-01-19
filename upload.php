<?php
$filename = "";
if ($_FILES["file"]["error"] > 0 || count($_FILES["file"]["name"]) == 0){
	$filename = $_POST["example"];
} else {	
	move_uploaded_file($_FILES["file"]["tmp_name"], "upload/".$_FILES["file"]["name"]);
	$filename = "upload/".$_FILES["file"]["name"];
	$_type = $_FILES["file"]["type"];
}
?>
<html>
<head>

<style type="text/css">
html, body {
	width: 100%;
	height: 100%;
	
	overflow: none;
}

a:link {
	color: #888888;
	text-decoration: none;
}

a:visited {
	color: #888888;
	text-decoration: none;
}

a:hover {
	color: #888888;
	text-decoration: none;
}

a:active {
	color: #888888;
	text-decoration: none;
}

.showbox {
	margin: 0 auto;
	width: 700px;
	height: 400px;
	border: 2px solid #1dd;
	vertical-align: middle;
}
.abgne-block-20120106 {
	margin: 10px auto;
	width: 680px;
	overflow-x: scroll;
	white-space: nowrap;
}
.abgne-block-20120106 a {
	margin-right: 10px;
}
.abgne-block-20120106 a img {
	width: 140px;
	height: 92px;
	border: 2px solid #1dd;
}
</style>

<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.Jcrop.js"></script>
<script type="text/javascript" src="js/jquery.blockUI.js"></script>
<script type="text/javascript" src="config.js"></script>
<script type="text/javascript">
var jcrop_api;

jQuery(function($){
	var type = "<?php echo $_type;?>";
	if (!"<?php echo $filename;?>".startsWith("example/") && !(type == "image/jpeg" || type == "image/png")) {
		alert("check image format, please.");
		location.href = "suggest.html";
	}
	
	for (var i=1; i<=100; i++) {
		var pad = "000000";
		var n = String(i);
		var result = (pad+n).slice(-pad.length);
		$(".abgne-block-20120106").append("<a href=\"example/" + result + ".jpg\" onclick=\"ex2(this)\"><img src=\"example/" + result + ".jpg\" /></a>");
	}

	// 用來顯示大圖片用
	var $showImage = $('#show-image');

	// 當滑鼠移到 .abgne-block-20120106 中的某一個超連結時
	$('.abgne-block-20120106 a').mouseover(function(){
		// 把 #show-image 的 src 改成被移到的超連結的位置
		$showImage.attr('src', $(this).attr('href'));
	}).click(function(){
		// 如果超連結被點擊時, 取消連結動作
		return false;
	});

	setTimeout(function() {
		jcrop_api = $.Jcrop("#cropbox", {
		  onSelect: updateCoords
		});	
	}, 400);

	var filename = "<?php echo $filename;?>";
	if (filename.startsWith("example")) {
		$("#select_tab").css("background-color", "#d6e6f6");
		$("#select_tab").css("color", "black");
		$("#example_tab").css("background-color", "#4690bd");
		$("#example_tab").css("color", "white");
	}
});

function updateCoords(c) {
	console.log(c.x);
	console.log(c.y);
	console.log(c.w);
	console.log(c.h);
	$('#x').val(c.x);
	$('#y').val(c.y);
	$('#w').val(c.w);
	$('#h').val(c.h);
};

function checkCoords() {
	$("#analysis").attr("disabled", "disabled");
	 
	if (!parseInt($('#w').val())) {
		$('#x').val(0);
		$('#y').val(0);
		$('#w').val($("#cropbox").width());
		$('#h').val($("#cropbox").height());
	}

	$("#msg").css("color", "Black");
	$("#msg").html("Processing...");

	setTimeout(function(){
		
		imgWidth = $("#cropbox").width();
		imgHeight = $("#cropbox").height();
		testWidth = $("#test").width();
		testHeight = $("#test").height();
		rateWidth = testWidth / imgWidth;
		rateHeight = testHeight / imgHeight; 
		
		$.ajax({
		  type: "POST",
		  url:'crop.php',
		  data: {model:$('#model').val(), rotate:$('#rotate').val(), x:$('#x').val() * rateWidth, y:$('#y').val() * rateHeight, w:$('#w').val() * rateWidth, h:$('#h').val() * rateHeight, filename:$('#filename').val()},
		  success: function(data){
			var form = new FormData();		
			form.append("photo", data);	
			form.append("model", $("#model").val());
			
			jQuery.ajax({
				url: predAPI,
				type: 'POST',
				method: 'POST',
				data: form,
				cache: false,
				contentType: false,
				processData: false,
				success: function(result){
					fields = result.split("<br>");
					ans = fields[1];
					result = fields[0];					
					
					if (ans.includes("Active Wet") > 0) {
						$("#ans").css("color", "OrangeRed");
						// ans = "Wet type - age related macular degeneration with active choroidal neovascularization. recommandation: immediate ophthalmic medical checkup and treatment was suggested, treatment choices includes intravitreal injection, photodynamic therapy, or surgery.";
						// ans = "Wet type age-related macular degeneration with active choroidal neovascularization. Recommendation: immediate ophthalmic medical checkup and treatment are suggested. Treatment choices include intravitreal injection, photodynamic therapy, or surgery.";
						ans = "Wet type age-related macular degeneration with active choroidal neovascularization. Recommendation: immediate ophthalmic medical checkup and treatment are suggested. Treatment choices include intravitreal injection, photodynamic therapy, or surgery.";
					} else if (ans.includes("Inactive Wet") > 0) {
						$("#ans").css("color", "Gold");
						// ans = "Wet type - age related macular degeneration with inactive choroidal neovascularization. Recommandation: follow up at ophthalmic clinic every one to three months (according to history and clinicians judgement). Nutritional support with lutein is suggested. Please seek for medical help as soon as the progression of visual blurring or distortion is noticed.";
						// ans = "Wet type age-related macular degeneration with inactive choroidal neovascularization. Recommendation: follow up at ophthalmic clinic every one to three months (according to history and clinician’s judgement). Nutritional support with lutein-rich diet is suggested. Please seek medical help as soon as the progression of visual blurring or distortion is noticed.";
						ans = "Wet type age-related macular degeneration with inactive choroidal neovascularization. Recommendation: follow up at ophthalmic clinic every one to three months (according to history and clinician’s judgement). Nutritional support with lutein-rich diet is suggested. Please seek medical help as soon as the progression of visual blurring or distortion is noticed.";
					} else if (ans.includes("Drusen") > 0) {
						$("#ans").css("color", "Gold");
						// ans = "Drusen (dry type age-related macular degeneration). recommandation: self-checkup of monocular vision with Amsler grid, and nutritional support with lutein are suggested, please follow up at ophthalmic clinic every year and seel for medical help when vision was decreased.";
						// ans = "Drusen (dry type age-related macular degeneration). Recommendation: self-exam of monocular vision with Amsler grid, and nutritional support with lutein-rich diet are suggested. Please follow up at ophthalmic clinic every year and seek medical help when vision is decreased.";
						ans = "Drusen (dry type age-related macular degeneration). Recommendation: self-exam of monocular vision with Amsler grid, and nutritional support with lutein-rich diet are suggested. Please follow up at ophthalmic clinic every year and seek medical help when vision is decreased.";
					} else if (ans.includes("Normal") > 0) {
						$("#ans").css("color", "Chartreuse");
						// ans = "no age-related macular degeneration. recommandation: self-checkup of monocular vision with Amsler grid is suggested, please seek for medical help when your vision was decreased.";
						// ans = "No age-related macular degeneration. Recommendation: self-exam of monocular vision with Amsler grid is suggested. Please seek medical help when vision is decreased.";
						ans = "No age-related macular degeneration. Recommendation: self-exam of monocular vision with Amsler grid is suggested. Please seek medical help when vision is decreased.";
					}
					$("#ans").html(ans);
					
					$("#result").attr("src", data);
					$("#msg").html(result);
					
					$("#analysis").removeAttr("disabled");					
					
					$("#cropbox_view").css("display", "none");	
					$("#result_view").css("display", "block");	

					setTimeout(function() {
						parent.resizeIframe($("#my_iframe", window.parent.document)[0]);
					}, 400);					
				}
			});
		  }
		});		  
	}, 1000);

	return false;
};
  
function analysis() {
	$("#cropbox_view").css("overflow", "hidden");
	$("#cropbox_view").block({ message: "<div style=\"background-color: #ecf0f1;\"><img src=\"images/loading.gif\" style=\"width: 100px; margin: 0 auto;\"><br>PROCESSING...</div>"});
	$("#analysis").click();
}

function ex() {
	//$("#example_form").submit();
	$("#select_example").css("display", "block");
	
	jcrop_api.destroy();
}
	
function cls() {
	$("#select_example").css("display", "none");
	
	jcrop_api = $("#cropbox").Jcrop({
	  onSelect: updateCoords
	});	
}

function ex2(target) {
	$("#example").val($(target).attr("href"));
	$("#example_form").submit();
}
</script>

</head>

<body style="margin: 0;">

	<div style="width: 1024px; margin: 0 auto;">
		
		<div style="width: 100%; height: 60px;">
			<div style="width: 50%; float: left;">
				<div style="float: left; width: 50px;">&nbsp;</div>
				<a href="home.html">
					<img src="images/logo.svg" style="float: left; height: 90%;">
					<img src="images/line3.jpg" style="float: left; height: 60%; margin-top: 10px;">				
					<img src="images/contactus_logo.svg" style="float: left; height: 70%; margin-top: 8px; margin-left: -8px;">
				</a>
			</div>
			<div style="width: 50%; float: left; color: #888888; margin-top: 17px;">
				<div style="float: left; width: 50px;">&nbsp;</div>
				<a href="main.html" style="float: left; margin-right: 50px;">Main</a>
				<a href="tutorial.html" style="float: left; margin-right: 50px;">Tutorial</a>
				<a href="suggest.html" style="float: left; margin-right: 50px;">OCT Suggest</a>
				<a href="contactus.html" style="float: left; margin-right: 50px;">Contact Us</a>
			</div>
		</div>
		
		<div style="width: 100%; height: 475px; background:#FFFFFF url(images/bg.jpg) no-repeat center center; background-size: cover;">
			<div style="padding: 20px 70px 20px 70px; height: 100%;">	
				<div style="width: 904px; height: 100%; background-color: rgba(183, 211, 233, 0.5); margin: 0 auto;">
					<a href="suggest.html"><div id="select_tab" style="float: left; width: 450px; height: 26px; padding-top: 8px; background-color: #4690bd; color: white; text-align: center; border: 1px solid #d8dde1; cursor: pointer; user-select: none;">Select OCT Record</div></a>
					<div id="example_tab" style="float: left; width: 450px; height: 26px; padding-top: 8px; background-color: #d6e6f6; color: black; text-align: center; border: 1px solid #d8dde1; cursor: pointer; user-select: none;" onclick="ex()">Use Example OCT Record</div>
					<div style="float: left; width: 100%;">
						<div style="width: 880px; margin: 10px auto;">							
							<div id="cropbox_view">
								<div style="width: 840px; background-color: #276d8f; padding: 20px 0 20px 20px;">
									<img src="<?php echo $filename;?>" id="cropbox" style="width: 820px;" />  
									<img src="<?php echo $filename;?>" id="test" style="display: none;" />  
								</div>

								<form action="crop.php" method="post" onsubmit="return checkCoords();">
									<div style="width: 95%; margin: 0 auto;">
										<div style="float:left; width: 50%;">
											<div style="margin-top: 10px;">
												Model:
												<select id="model" name="model">
												  <option value="inception_v3">Inception V3</option>
												  <option value="resnet50">ResNet50</option>
												  <option value="vgg16_bn">VGG16</option>
												</select>
											</div>
											<div style="margin-top: 10px;">
												OCT Rotate:
												<select id="rotate" name=rotate>
												  <option value="0">0&deg;</option>
												  <option value="90">Right 90&deg;</option>
												  <option value="-90">Left 90&deg;</option>
												</select>
											</div>
											<input type="hidden" id="x" name="x" />
											<input type="hidden" id="y" name="y" />
											<input type="hidden" id="w" name="w" />
											<input type="hidden" id="h" name="h" />
											<input type="hidden" id="filename" name="filename" value="<?php echo $filename;?>" />
											<input type="submit" id="analysis" value="Analysis OCT" style="display: none;" />
										</div>
										
										<div style="float:left; width: 50%; margin-top: 15px;">								
<a href="javascript:analysis();">
											<div style="float: right; width: 120px; height: 30px; padding-top: 8px; background-color: #4690bd; color: white; text-align: center; border: 1px solid #d8dde1; cursor: pointer; border-radius: 10px; user-select: none;">Analysis OCT</div>
</a>
										</div>
									</div>
								</form>
							</div>

							<div id="result_view" style="width: 840px; background-color: #276d8f; padding: 20px 0 20px 20px; display: none; color: white;">
								<div style="margin: 0 auto;">
									Analysis Result:<div id="ans" style="display: inline;">&nbsp;</div><br>
									<div style="color: red; font-size: 12px; font-weight: bold;">(This website is only for academic purposes. The diagnosis and recommendations are for reference only. It still needｓ specialistｓ to confirm the judgement.)</div>
									<img id="result" style="width: 820px;" />
									<div id="msg">&nbsp;</div>
								</div>
							</div>
							
						</div>
					</div>
				</div>

			</div>
		</div>
		
		<div style="width: 100%; height: 60px; background-color: #f7f8f8; padding: 15px 0 15px 0;">
			<div style="width: 49%; display: inline; float: left;">
				<img src="images/logo2.png" style="width: 60px; vertical-align: middle; float: left; margin-right: 15px;">
				<div style="float: left; color: #898989;">
					<div style="margin-bottom: 8px;">National Yang-Ming University</div>
					<div style="font-size: 12px; margin-bottom: 2px;">+886-2-2826-7000</div>
					<div style="font-size: 12px;">No.155, Sec. 2, Linong St., Beitou Dist., Taipei City 112, Taiwan</div>
				</div>
			</div>
			<img src="images/line2.png" style="width: 2%; height: 100%; float: left;">
			<div style="width: 49%; display: inline; float: left;">
				<img src="images/logo1.png" style="width: 60px; vertical-align: middle; float: left; margin-left: 5px; margin-right: 15px;">
				<div style="float: left; color: #898989;">
					<div style="margin-bottom: 8px;">Taipei Veterans General Hospital</div>
					<div style="font-size: 12px; margin-bottom: 2px;">+886-2-28712121</div>
					<div style="font-size: 12px;">No.201, Sec. 2, Shipai Rd., Beitou Dist., Taipei City 112, Taiwan</div>
				</div>
			</div>
		</div>
		
		<img src="images/bottom.png" style="width: 100%; height: 10px;">
		
	</div>	
	
	<div id="select_example" style="width: 740px; height: 600px; position: absolute; top: calc(50% - 300px); left: calc(50% - 370px); background-color: rgba(183, 211, 233, 1.0); border: 1px solid black; display: none; z-index: 999;">
		<div style="color: white; background-color: #4690bd; text-align: center; height: 26px; padding-top: 8px; margin-bottom: 8px;">Select Example</div>
		<div class="showbox"><img id="show-image" src="example/000001.jpg" width="100%" height="100%" /></div>
		<div class="abgne-block-20120106">
		</div>
		<div style="width: 100%; text-align: center;"><a href="#" onclick="cls()">Close</a></div>
	</div>
	
	<form id="example_form" action="upload.php" method="post" style="margin: 0; display: inline;">
		<input type="hidden" name="example" id="example">
	</form>

</body>

</html>


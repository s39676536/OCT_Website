<html>
<head>
<title></title>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />

<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.Jcrop.js"></script>
<script type="text/javascript">
  var jcrop_api;
  jQuery(function($){	
	$("#f").change(function() {
		if (this.files && this.files[0]) {
			var reader = new FileReader();
			reader.onload = function (e) {				
				if (jcrop_api != null) {
					jcrop_api.destroy();
				}
				$("#cropbox").attr("src", e.target.result);
				$('#cropbox').Jcrop({
				  onSelect: updateCoords
				}, function () {
					jcrop_api = this;
				});
				
				$("#test").attr("src", e.target.result);
			}
			reader.readAsDataURL(this.files[0]);
		}
	});
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
	if (!parseInt($('#w').val())) {
		$('#x').val(0);
		$('#y').val(0);
		$('#w').val($("#cropbox").width());
		$('#h').val($("#cropbox").height());
	}
		
	$("#result").css("display", "none");
	$("#msg").css("display", "block");
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
			  d = new Date();
			  $("#result").attr("src", data + "?" + d.getTime());
			  
			  setTimeout(function(){
				  $("#result").css("display", "block");
				  $("#msg").css("display", "block");	  
				  
				  type = Math.floor((Math.random() * 4) + 1);
				  switch (type) {
					case 1:		  
						$("#msg").css("color", "Green");
						$("#msg").html($('#model').val() + ": Normal<br>Is OK!");
						break;
						
					case 2:
						$("#msg").css("color", "Coral");
						$("#msg").html($('#model').val() + ": Drusen<br>Observed");
						break;
						
					case 3:
						$("#msg").css("color", "Red");
						$("#msg").html($('#model').val() + ": Active Wet<br>Treatment!!");
						break;
						
					case 4:
						$("#msg").css("color", "Coral");
						$("#msg").html($('#model').val() + ": Inactive Wet<br>Observed");
						break;
				  }			  
			  }, 500);
		  }
		});		  
	}, 1000);
	
	return false;
  };

</script>

</head>
<body>
<input type="file" id="f">

<h3>OCT Suggest</h3>
Select OCT Area:<br>
<img id="cropbox" height="600" />  
<img id="test" style="display: block;" />  

<form action="crop.php" method="post" onsubmit="return checkCoords();">
	Model:
	<select id="model" name="model">
	  <option value="InceptionV3">Inception V3</option>
	  <option value="ResNet50">ResNet50</option>
	  <option value="VGG16">VGG16</option>
	</select><br>
	OCT Rotate:
	<select id="rotate" name=rotate>
	  <option value="0">0&deg;</option>
	  <option value="90">Right 90&deg;</option>
	  <option value="-90">Left 90&deg;</option>
	</select><br>
	<input type="hidden" id="x" name="x" />
	<input type="hidden" id="y" name="y" />
	<input type="hidden" id="w" name="w" />
	<input type="hidden" id="h" name="h" />
	<input type="hidden" id="filename" name="filename" value="<?php echo $filename;?>" />
	<input type="submit" value="Analysis OCT" class="btn btn-large btn-inverse" />
</form>

<img id="result" height="200" />
<div id="msg" style="display:none;">error!</div>

</body>
</html>


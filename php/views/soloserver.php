<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Minecraft Control Panel</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="/css/bootstrap.min.css" rel="stylesheet">
		<link href="/css/flat-ui.css" rel="stylesheet">
		<link href="/css/style2.css" rel="stylesheet">
	</head>
	<body>
		<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				<a class="navbar-brand" href=".">MinecraftControlPanel</a>
			</div>
			<div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
					<li><a href=".">Dashboard</a></li>
					<li class="active"><a href="/servers">My Servers</a></li>
					<li><a href="/plugins">Plugins</a></li>
					<li><a href="/account">My Account</a></li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
          			<li><a>Signed in as <?=$_SESSION['userToken'];?></a></li>
		  			<li><a href="/php/logout/logout.php">Logout</a></li>
		  		</ul>
		  	</div>
		</nav>

		<div id="content">
			<div class="container">
				<div class="row">
					<div class="col-sm-12 col-lg-12">
						<?=$ControlPanel->ServerInfo($_GET['id']);?>
					</div>
				</div>
			</div>
		</div>
	</body>
	
	<script type="text/javascript" src="/js/jquery.js"></script>
	<script type="text/javascript" src="/js/bootstrap.js"></script>
	<script type="text/javascript" src="/js/jquery.form.js"></script>
	<script>
		$("#start").click(function(){
			$('#serverControls').ajaxForm();
			alert("This page will refresh automagically when the server is started");
			setTimeout(function(){ location.reload() }, 8000);
		});
		$("#stop").click(function(){
			$('#serverControls').ajaxForm();
			alert("This page will refresh automagically when the server is stopped");
			setTimeout(function(){ location.reload() }, 8000);
		});
		$(function(){
			setTimeout(function(){
				$.post('/php/server/operators/getlist.php', {id:"<?=$_GET['id'];?>"}, function(data){
					$("#opsReturn").html(data).show();
				});
			}, 2000);
		})
	</script>
	<div id="return"></div>
</html>

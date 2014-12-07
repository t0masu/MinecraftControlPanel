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
					<li><a href="/servers">My Servers</a></li>
					<li class="active"><a href="/plugins">Plugins</a></li>
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
						<div class="jumbotron">
							<h3>Search for bukkit plugins</h3>
							<div class="input-group">
								<input type="text" name="pluginName" class="form-control">
								<span class="input-group-btn">
									<button class="btn btn-default" type="button">Search</button>
								</span>
							</div>
						</div>
						<?=$ControlPanel->searchPluginDB("worldedit");?>
					</div>
				</div>
			</div>
		</div>
	</body>
	
	<script type="text/javascript" src="/js/jquery.js"></script>
	<script type="text/javascript" src="/js/bootstrap.js"></script>
	
</html>

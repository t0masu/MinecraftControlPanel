<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Minecraft Control Panel</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="/css/bootstrap.min.css" rel="stylesheet">
		<link href="/css/flat-ui.css" rel="stylesheet">
		<link href="/css/style.css" rel="stylesheet">
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-sm-6 col-lg-4 col-center">
					<div class="panel panel-default box">
					<div class="panel-heading">
						<h3 class="panel-title">Login to MinecraftControlPanel</h3>
					</div>
					<div class="panel panel-body">
					<div class="alert alert-danger" id="error" style="display: none;">
						<strong>Error!</strong> incorrect username or password
					</div>
					
					<form role="form" id="loginForm" action="#" method="POST">
						<div class="form-group">
							<label>Email/Username</label>
							<input type="text" id="username" name="password" class="form-control" placeholder="Email/Username">
						</div>
						<div class="form-group">
							<label>Password</label>
							<input type="password" id="password" name="password" class="form-control" placeholder="Password">
						</div>
						<button type="submit" class="btn btn-info btn-embossed btn-block">Login</button>
					</form>
					</div>
					<div class="panel-footer">
						<center>Made by t0masu</center>
					</div>
					</div>
				</div>
			</div>
		</div>
	</body>

	<!-- Le scripts -->
	<script src="/js/jquery.js"></script>
	<script src="/js/bootstrap.js"></script>
    <script src="/js/jquery.form.js"></script>
    <script>
    	$(document).ready(function() {
	    	$("#loginForm").ajaxForm(function(){
	    		var username = $("#username").fieldValue();
	    		var password = $("#password").fieldValue();
		    	$.post('/php/login/auth.php', {username:username, password:password}, function(data){
			    	if(data == 1){
				    	window.location = ".";
			    	}else if(data == 0){
				    	$("#error").fadeIn(1200);
				    	setTimeout(function(){
				    		$("#error").fadeOut(1200);
				    	}, 3000);
						$("#password").val('');
			    	}
			    	
		    	});
	    	});
    	});
    </script>

</html>
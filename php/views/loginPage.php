<!DOCTYPE html>
<html class="bg-black">
    <head>
        <meta charset="UTF-8">
        <title>MinecraftControlPanel | Log in</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="../../css/AdminLTE.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="bg-black">
        <div class="form-box" id="login-box">
            <div class="header">Sign In</div>
            <form action="/php/login/auth.php" method="POST" id="loginForm">
                <div class="body bg-gray">
                	<div id="success" class="alert alert-success alert-dismissable" style="display: none">
	            	<i class="fa fa-check"></i>
	            	<b>Authentication Successful!</b> You will be transferred shortly.
            	</div>
            	<div id="error" class="alert alert-danger alert-dismissable" style="display: none">
	            	<i class="fa fa-ban"></i>
	            	<b>Authentication Unsuccessful!</b> Your username or password was not correct. Please try again
            	</div>
                    <div class="form-group">
                        <input type="text" name="username" id="username" class="form-control" placeholder="User ID"/>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password"/>
                    </div>          
                    <div class="form-group">
                        <input type="checkbox" name="remember_me"/> Remember me
                    </div>
                </div>
                <div class="footer">                                                               
                    <input type="submit" class="btn bg-olive btn-block" value="Sign me in">  
                    
                    <p><a href="#">I forgot my password</a></p>
                    
                    <a href="register.html" class="text-center">Register a new membership</a>
                </div>
            </form>

            <div class="margin text-center">
                <span>MinecraftControlPanel &middot; t0masu</span>
            </div>
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script src="/js/plugins/jquery.form.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js" type="text/javascript"></script>
		<script>
    	$(document).ready(function() {
	    	$("#loginForm").ajaxForm(function(){
	    		var username = $("#username").fieldValue();
	    		var password = $("#password").fieldValue();
		    	$.post('/php/login/auth.php', {username:username, password:password}, function(data){
			    	if(data == 1){
			    		$("#success").fadeIn(1200);
				    	setTimeout(function(){
				    		window.location = ".";
				    	}, 4500);
			    	}else if(data == 0){
				    	$("#error").fadeIn(1200);
				    	setTimeout(function(){
				    		$("#error").fadeOut(2500);
				    	}, 5000);
						$("#password").val('');
			    	}
			    	
		    	});
	    	});
    	});
    </script>
    </body>
</html>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>MinecraftControlPanel | Dashboard</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="//code.ionicframework.com/ionicons/1.5.2/css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- Morris chart -->
        <link href="css/morris/morris.css" rel="stylesheet" type="text/css" />
        <!-- jvectormap -->
        <link href="css/jvectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />
        <!-- Date Picker -->
        <link href="css/datepicker/datepicker3.css" rel="stylesheet" type="text/css" />
        <!-- Daterange picker -->
        <link href="css/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
        <!-- bootstrap wysihtml5 - text editor -->
        <link href="css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="css/AdminLTE.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="skin-blue">
        <!-- header logo: style can be found in header.less -->
        <header class="header">
            <a href="." class="logo">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->
                MinecraftControlPanel
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-right">
                    <ul class="nav navbar-nav">
                        <?php $row = $ControlPanel->accountInfo();?>
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="glyphicon glyphicon-user"></i>
                                <span><?=$row["first_name"] . " " . $row["last_name"];?> <i class="caret"></i></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header bg-light-blue">
                                    <img src="img/user-default.png" class="img-circle" alt="User Image" />
                                    <p>
                                    	<?=$row["first_name"] . " " . $row["last_name"];?>
                                        <small>
                                        	<?php
												
												switch($row["auth_level"]) {
													case 0:
														?><?="Guest Account";?><?php
														break;
													case 1:
														?><?="Standard Account";?><?php
														break;
													case 2:
														?><?="Manager Account";?><?php
														break;
													case 3:
														?><?="Administrator Account";?><?php
														break;
												} //end switch
											?>
                                        </small>
                                    </p>
                                </li>
                                <!-- Menu Body -->
                                <li class="user-body">
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Followers</a>
                                    </div>
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Sales</a>
                                    </div>
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Friends</a>
                                    </div>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="/account" class="btn btn-default btn-flat">Account Settings</a>
                                    </div>
                                    <div class="pull-right">
                                        <a href="/php/logout/logout.php" class="btn btn-default btn-flat">Sign out</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="left-side sidebar-offcanvas">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="img/user-default.png" class="img-circle" alt="User Image" />
                        </div>
                        <div class="pull-left info">
                            <p>Hello, <?=$row["first_name"];?></p>
                            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                        </div>
                    </div>
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <ul class="sidebar-menu">
                        <li>
                            <a href=".">
                                <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="/hosts">
                                <i class="fa fa-th"></i> <span>My Hosts</span>
                            </a>
                        </li>
                        <li class="active">
							<a href="/servers">
								<i class="fa fa-hdd-o"></i> <span>My Servers</span>
							</a>
						</li>
						<li>
							<a href="/backups">
								<i class="fa fa-copy"></i> <span>My Backups</span>
							</a>
						</li>
						<li>
							<a href="/plugins">
								<i class="fa fa-magic"></i> <span>Plugins</span>
							</a>
						</li>
                    </ul>
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        My Server
                        <small>Control panel</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">My Server(<?=$_GET["id"];?>)</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <!-- Main row -->
                    <div class="row">
                       <?=$ControlPanel->ServerInfo($_GET['id']);?>
                    </div><!-- /.row (main row) -->

                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->

        <!-- add new calendar event modal -->


        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="//code.jquery.com/ui/1.11.1/jquery-ui.min.js" type="text/javascript"></script>
        <script src="/js/plugins/jquery.form.js"></script>
        <!-- Morris.js charts -->
        <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
        <script src="js/plugins/morris/morris.min.js" type="text/javascript"></script>
        <!-- Sparkline -->
        <script src="js/plugins/sparkline/jquery.sparkline.min.js" type="text/javascript"></script>
        <!-- jvectormap -->
        <script src="js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js" type="text/javascript"></script>
        <script src="js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js" type="text/javascript"></script>
        <!-- jQuery Knob Chart -->
        <script src="js/plugins/jqueryKnob/jquery.knob.js" type="text/javascript"></script>
        <!-- daterangepicker -->
        <script src="js/plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
        <!-- datepicker -->
        <script src="js/plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
        <!-- Bootstrap WYSIHTML5 -->
        <script src="js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
        <!-- iCheck -->
        <script src="js/plugins/iCheck/icheck.min.js" type="text/javascript"></script>

        <!-- AdminLTE App -->
        <script src="js/AdminLTE/app.js" type="text/javascript"></script>
        <script>
		$("#start").click(function(){
			$('#serverControls').ajaxForm();
			alert("This page will refresh automagically when the server is started");
			setTimeout(function(){ 
				$.post('/php/server/status/getStatus.php', {id:"<?=$_GET['id'];?>"}, function(data){
					$("#status").fadeOut(600);
					$("#status").hide();
					$("#status").html(data).fadeIn(1200);
				});
			}, 10000);
		});
		$("#stop").click(function(){
			$('#serverControls').ajaxForm();
			alert("This page will refresh automagically when the server is stopped");
			setTimeout(function(){ 
				$.post('/php/server/status/getStatus.php', {id:"<?=$_GET['id'];?>"}, function(data){
					$("#status").fadeOut(600);
					$("#status").hide();
					$("#status").html(data).fadeIn(1200);
				}); 
			}, 10000);
		});
		
		$("#createBackup").ajaxForm();
		$("#downloadBackup").ajaxForm();
		
		
		$("#crBK").click(function() {
			alert("Creating Backup on server now!");
			setTimeout(function() {
				$.post('/php/server/backups/getBackups.php', {id:"<?=$_GET['id'];?>"}, function(data){
					$("#backups").html(data).fadeIn(1200);
				});
			}, 15000);
		});
		
		
		$(function(){
			$("#ops").hide();
			$("#plugins").hide();
			$("#backups").hide();
			$("#console").hide();
			
			$("#refresh").click(function(){
				$.post("/php/server/console/getViewer.php", {id:"<?=$_GET['id'];?>"}, function(data){
					$("#console").html(data).fadeIn(1200);
				});
			});
			
			setTimeout(function(){
				$.post('/php/server/operators/getPlugins.php', {id:"<?=$_GET['id'];?>"}, function(data){
					$("#plugins").hide();
					$("#plugins").html(data).fadeIn(1200);
				});
			}, 5);
			
			setTimeout(function(){
				$.post('/php/server/operators/getlist.php', {id:"<?=$_GET['id'];?>"}, function(data){
					$("#ops").html(data).fadeIn(1200);
				});
			}, 10);
			
			setTimeout(function(){
				$.post('/php/server/backups/getBackups.php', {id:"<?=$_GET['id'];?>"}, function(data){
					$("#backups").html(data).fadeIn(1200);
				});
			}, 15);
			
			setTimeout(function(){
				$.post('/php/server/console/getViewer.php', {id:"<?=$_GET['id'];?>"}, function(data){
					$("#console").html(data).fadeIn(1200);
				});
			}, 20);
		})
	</script>
	</body>
</html>

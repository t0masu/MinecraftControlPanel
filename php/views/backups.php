<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<title>Minecraft Control Panel &middot; Beta v0.1</title>
		<meta name="generator" content="Minecraft Control Panel - Beta v0.1" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<!--[if lt IE 9]>
			<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<link href="css/styles.css" rel="stylesheet">
	</head>
	<body>
<nav class="navbar navbar-fixed-top header">
 	<div class="col-md-12">
        <div class="navbar-header">
          
          <a href="." class="navbar-brand">Control Panel</a>
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse1">
          <i class="glyphicon glyphicon-search"></i>
          </button>
      
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse1">
          <form class="navbar-form pull-left">
              <div class="input-group" style="max-width:470px;">
                <input type="text" class="form-control" placeholder="Search" name="srch-term" id="srch-term">
                <div class="input-group-btn">
                  <button class="btn btn-default btn-primary" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                </div>
              </div>
          </form>
          <ul class="nav navbar-nav navbar-right">
             <li>
             	<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?=$_SESSION['userToken'];?></a>
			 	<ul class="dropdown-menu">
			 	  <li><a href="#"><span class="glyphicon glyphicon-cog"></span> Account Setings</a></li>
                  <li><a href="php/logout/logout.php">Logout</a></li>
                </ul>
             </li>
             <li>
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-bell"></i></a>
                <ul class="dropdown-menu">
                  <li><a href="#">Server <span class="label label-warning">%id%</span> has been <span class="label label-success">Started!</span></a></li>
                  <li><a href="#">Server <span class="label label-warning">%id%</span> has been <span class="label label-danger">Stopped!</span></a></li>
                  <li><a href="#">Server <span class="label label-warning">%id%</span> got <span class="label label-info">Rebooted!</span></a></li>
                  <li><a href="#">Plugin <span class="label label-warning">%plugin%</span> has been installed on <span class="label label-info">%id%!</span></a></li>
                </ul>
             </li>
             <li><a href="#"><i class="glyphicon glyphicon-user"></i></a></li>
           </ul>
        </div>	
     </div>	
</nav>
<div class="navbar navbar-default" id="subnav">
    <div class="col-md-12">
        <div class="navbar-header">
          
          <a href="/backups" style="margin-left:15px;" class="navbar-btn btn btn-default btn-plus dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-hdd" style="color:#dd1111;"></i> Backups <small><i class="glyphicon glyphicon-chevron-down"></i></small></a>
          <ul class="nav dropdown-menu">
	          <li><a href="/"><i class="glyphicon glyphicon-home"></i> Home</a></li>
	          <li class="nav-divider"></li>
              <li><a href="/csj"><i class="glyphicon glyphicon-upload"></i> Custom Server Jars</a></li>
              <li><a href="/plugins"><i class="glyphicon glyphicon-flash"></i> Plugins</a></li>
              <li><a href="/servers"><i class="glyphicon glyphicon-tasks"></i> Servers</a></li>
              <li class="nav-divider"></li>
              <li><a href="/settings"><i class="glyphicon glyphicon-cog" style="color:#dd1111;"></i> Settings</a></li>
          </ul>
          
          
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse2">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          </button>
      
        </div>
     </div>	
</div>

<!--main-->
<div class="container" id="main">
   <div class="row">
   <div class="col-md-6 col-sm-6">
        <div class="panel panel-default">
          <div class="panel-heading"><h4>Backups completed</h4></div>
   			<div class="panel-body">
              <table class="table" id="myTable">
              	<thead>
              		<th>File</th>
              		<th>Size</th>
              		<th>Download</th>
              	</thead>
              	<tbody>
              		<tr>
              			<td rel='{"id":$id, "field":$field}'>tomvps.no-ip.org</td>
              			<td>25565</td>
              			<td>CraftBukkit</td>
              			<td>2048M</td>
              		</tr>
              	</tbody>
              </table>
            </div>
   		</div>
        <div class="well"> 
             <form class="form-horizontal" role="form">
              <h4>Server Flavours</h4>
               <div class="form-group" style="padding:14px;">
			   </div>
              <button class="btn btn-success pull-right" type="button">Post</button><ul class="list-inline"><li><a href="#"><i class="glyphicon glyphicon-align-left"></i></a></li><li><a href="#"><i class="glyphicon glyphicon-align-center"></i></a></li><li><a href="#"><i class="glyphicon glyphicon-align-right"></i></a></li></ul>
            </form>
        </div>
     
        <div class="panel panel-default">
           <div class="panel-heading"><h4>Cron Jobs</h4></div>
   			<div class="panel-body">
              <img src="//placehold.it/150x150" class="img-circle pull-right"> <a href="#">Free @Bootply</a>
              <div class="clearfix"></div>
              There a load of new free Bootstrap 3 ready templates at Bootply. All of these templates are free and don't require extensive customization to the Bootstrap baseline.
              <hr>
              <ul class="list-unstyled"><li><a href="http://www.bootply.com/templates">Dashboard</a></li><li><a href="http://www.bootply.com/templates">Darkside</a></li><li><a href="http://www.bootply.com/templates">Greenfield</a></li></ul>
            </div>
         </div> 

	</div>
  	<div class="col-md-6 col-sm-6">
      	 
          <div class="well"> 
          		<form action="#" method="" class="form">
          			<h4>Custom server jars</h4>
          			<div class="form-group">
          				<input type="file" name="jarfile" class="form-control" />
          			</div>
          			<button type="submit" name="submit" class="btn btn-primary">Upload to cache</button>
          		</form>
          </div>

      	 <div class="panel panel-default">
           <div class="panel-heading"><h4>Latest Server &amp; World Backups</h4></div>
   			<div class="panel-body">
              <p><img src="//placehold.it/150x150" class="img-circle pull-right"> <a href="#">The Bootstrap Playground</a></p>
              <div class="clearfix"></div>
              <hr>
              Design, build, test, and prototype using Bootstrap in real-time from your Web browser. Bootply combines the power of hand-coded HTML, CSS and JavaScript with the benefits of responsive design using Bootstrap. Find and showcase Bootstrap-ready snippets in the 100% free Bootply.com code repository.
            </div>
         </div>
      
      	 <div class="panel panel-default">
           <div class="panel-heading"><h4>Reminders / Notepad</h4></div>
   			<div class="panel-body">
              <ul class="list-group">
              <li class="list-group-item">Modals</li>
              <li class="list-group-item">Sliders / Carousel</li>
              <li class="list-group-item">Thumbnails</li>
              </ul>
            </div>
   		 </div>
      
  	</div>
 </div><!--/row-->
</div><!--/main-->
	<!-- script references -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/scripts.js"></script>
	</body>
</html>
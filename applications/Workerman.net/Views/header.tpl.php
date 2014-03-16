<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="utf-8">
  <title><?php if(isset($html_title))echo $html_title;else echo 'workerman 一个高性能的php socket 服务器框架';?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?php if(isset($html_desc))echo $html_desc;?>">
  <meta name="author" content="">

	<!--link rel="stylesheet/less" href="/less/bootstrap.less" type="text/css" /-->
	<!--link rel="stylesheet/less" href="/less/responsive.less" type="text/css" /-->
	<!--script src="/js/less-1.3.3.min.js"></script-->
	<!--append ‘#!watch’ to the browser URL, then refresh the page. -->
	
	<link href="/css/bootstrap.min.css" rel="stylesheet">
	<link href="/css/style.css" rel="stylesheet">

  <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
  <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
  <![endif]-->

  <!-- Fav and touch icons -->
  <link rel="apple-touch-icon-precomposed" sizes="144x144" href="img/apple-touch-icon-144-precomposed.png">
  <link rel="apple-touch-icon-precomposed" sizes="114x114" href="img/apple-touch-icon-114-precomposed.png">
  <link rel="apple-touch-icon-precomposed" sizes="72x72" href="img/apple-touch-icon-72-precomposed.png">
  <link rel="apple-touch-icon-precomposed" href="img/apple-touch-icon-57-precomposed.png">
  <link rel="shortcut icon" href="img/favicon.png">
  
	<script type="text/javascript" src="/js/jquery.min.js"></script>
	<script type="text/javascript" src="/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="/js/scripts.js"></script>
	 <script type="text/javascript" src="/js/jquery.min.js"></script>
	 <script type="text/javascript" src="/js/highcharts.js"></script>
</head>
<body>
<div class="container">
	<div class="row clearfix">
		<div class="col-md-12 column">
			<nav class="navbar navbar-default" role="navigation">
				<div class="navbar-header">
					 <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					  <span class="sr-only">Toggle navigation</span>
					  <span class="icon-bar"></span>
					  <span class="icon-bar"></span>
					  <span class="icon-bar"></span>
					  </button>
					  <img src="/img/the-workerman-logo.png" />
				</div>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li<?php if($html_nav == 'home')echo ' class="active" ';?>>
							<a href="/">首页</a>
						</li>
						<li<?php if($html_nav == 'applications')echo ' class="active" ';?>>
							<a href="/applications">相关应用</a>
						</li>
						<li<?php if($html_nav == 'download')echo ' class="active" ';?>>
							<a href="/download">下载</a>
						</li>
						<li<?php if($html_nav == 'documentation')echo ' class="active" ';?>>
							<a href="/documentation">文档</a>
						</li>
						<li<?php if($html_nav == 'group')echo ' class="active" ';?>>
							<a href="/group">社区</a>
						</li>
					</ul>
				</div>
			</nav>
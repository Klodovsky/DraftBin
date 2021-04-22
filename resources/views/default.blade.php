<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<link rel="stylesheet" media="screen" href="/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Oswald">
	<link rel=stylesheet type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">

	<script src="/jquery.js"></script>
	<script src="/bootstrap.min.js"></script>
	<!-- Salutations jeune fouineur ! :) -->
	<title>@yield('pagetitle')</title>
	<style>
	.navbar-brand, .nav-link{
		font-family:Cabin;
	}
	body {
		margin-top:75px;
	}
	.alert {
		margin-bottom: 10px;
	}
	</style>
	@yield('style')
	@yield('script')
	<nav class="navbar navbar-fixed-top navbar-default">
		<div class="container">
			<div class="navbar-header">
				<a class="navbar-brand" href="/">DraftBin</a>
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>
			<div class="collapse navbar-collapse" id="navbar" style="max-height:500px;">
				<ul class="nav navbar-nav navbar-right">
					@yield('navbar')
				</ul>
			</div>
		</div>
	</nav>
</head>

<body>
	@yield('content')
</body>
<footer>
<div class="container" style="position:fixed;bottom:0;height:auto;margin-top:40px;width:100%;text-align:center">
<div class="row">
	<h5 class="text-center"><small><i>Made by Khaled BEN HASSEN - <a href="https://github.com/Klodovskyy" target="_blank">Klodov$ky</a>, 2021</i></small></h5>
</div>
</div><br />
</footer>

</html>

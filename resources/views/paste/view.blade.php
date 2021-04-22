
@extends('default')

@section('pagetitle') {{ $title }} | DraftBin @endsection

@section('navbar')
<li class="nav-item"><a href="/" class="nav-link">Home</a></li>
@if (Auth::check())
<li class="nav-item"><a href="/users/dashboard" class="nav-link">Dashboard</a></li>
<li class="nav-item"><a href="/users/account" class="nav-link">My Account</a></li>
<li class="nav-item"><a href=" /logout" class="nav-link">Logout <i>({{ Auth::user()->name }})</i></a></li>
@else
<li class="nav-item"><a href="/login" class="nav-link">Login</a></li>
<li class="nav-item"><a href="/register" class="nav-link">Register</a></li>
@endif
@endsection

@section('style')
<link rel="stylesheet" href="/highlight_styles/tomorrow.css">

<style>
	@if ($noSyntax == false)
	pre {
		overflow: auto;
		word-wrap: normal;
		background:none;
		padding:0px;
		font-size: 75%;
		word-break: normal;
	}
	pre code {
		white-space: pre;
	}
	.hljs-line-numbers {
		text-align: right;
		border-right: 1px solid #ccc;
		color: #999;
		-webkit-touch-callout: none;
		-webkit-user-select: none;
		-khtml-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
	}
	@else
	pre {
		color: #000;
		word-break: normal;
	}
	@endif
</style>
	<style>

		.widget .panel-body { padding:0px; }
		.widget .list-group { margin-bottom: 0; }
		.widget .panel-title { display:inline }
		.widget .label-info { float: right; }
		.widget li.list-group-item {border-radius: 0;border: 0;border-top: 1px solid #ddd;}
		.widget li.list-group-item:hover { background-color: rgba(86,61,124,.1); }
		.widget .mic-info { color: #666666;font-size: 16px; }
		.widget .action { margin-top:5px; }
		.widget .comment-text { font-size: 12px; }
		.widget .btn-block { border-top-left-radius:0px;border-top-right-radius:0px; }
	</style>
@endsection

@section('script')
@if ($noSyntax == false)
<script src="highlight.pack.js"></script>
<script src="highlightjs-line-numbers.min.js"></script>
<script>
	hljs.initHighlightingOnLoad();
	hljs.initLineNumbersOnLoad();
</script>
@endif
@endsection

@section('content')
<div class="container">
	@if ($expiration == "Expired")
	<div class="alert alert-info" role="alert">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		<i>This paste has expired, however since you've wrote it you may view it whenever you want.</i>
	</div>
	@elseif ($expiration == "Burn after reading (next time)")
	<div class="alert alert-warning" role="alert">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		<i>This paste is in burn after reading. From now, it could be viewed only one time.</i>
	</div>
	@elseif ($expiration == "Burn after reading")
	<div class="alert alert-danger" role="alert">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		<i><strong>Be careful!</strong> This paste is in burn after reading mode and you won't be able to see it again.</i>
	</div>
	@endif

	<div class="row">
		<div class="col-sm-11">
			<h3 style="margin-top:0px; word-wrap: break-word;">{{ $title }}</h3>
		</div>
		{{-- Ici le petit panel de gestion --}}
		@if ($sameUser == true)
		<div class="col-sm-1 hidden-xs">
			<button class="btn btn-danger btn-sm pull-right" type="button" data-toggle="modal" data-target="#delete" aria-expanded="false" aria-controls="collapse}"><i class="fa fa-trash-o"></i></button>
		</div>
		<div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="preview" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title" id="preview" style="word-wrap: break-word;">Delete "<i>{{ $title }}</i>" ?</h4>
					</div>
					<div class="modal-body">Are you sure ? You <b>cannot</b> undo this !</div>
					<div class="modal-footer">
						<a class="btn btn-danger btn-sm" href="/users/delete/{{ $link }}" role="button">Yes</a>
						<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">No</button>
					</div>
				</div>
			</div>
		</div>
		@endif
	</div>

	<div class="row">
		<div class="col-xs-12">
			<ul class="list-inline" style="color:#999FA4;">
				<script>
					$(function () {
						$('[data-toggle="tooltip"]').tooltip()
					})
				</script>
				<li><i class="fa fa-user" data-toggle="tooltip" data-placement="bottom" title="Username"></i> <i>{{ $username }}</i></li>
				<li><i class="fa fa-calendar" data-toggle="tooltip" data-placement="bottom" title="Date of creation"></i> <i data-toggle="tooltip" data-placement="bottom" title="{{ $fulldate }}">{{ $date }}</i></li>
				<li><i class="fa fa-eye" data-toggle="tooltip" data-placement="bottom" title="Times viewed"></i> <i>{{ $views }} view(s)</i></li>
				{{-- Expiration cachée si xs --}}
				@if ($expiration == "Never")
				<li class="hidden-xs"><i class="fa fa-clock-o" data-toggle="tooltip" data-placement="bottom" title="Expiration"></i> <i>{{ $expiration }}</i></li>
				@else
				<li><i class="fa fa-clock-o" data-toggle="tooltip" data-placement="bottom" title="Expiration"></i> <i>{{ $expiration }}</i></li>
				@endif
				
				{{-- Privacy cachée si xs --}}
				@if ($privacy == "Public")
				<li class="hidden-xs"><i class="fa fa-lock" data-toggle="tooltip" data-placement="bottom" title="Privacy"></i> <i>{{ $privacy }}</i></li>
				@else
				<li><i class="fa fa-lock" data-toggle="tooltip" data-placement="bottom" title="Privacy"></i> <i>{{ $privacy }}</i></li>
				@endif
			</ul>
		</div>
	</div>

	@if (Session::has('success_message'))
		<div class="alert alert-success m-t-sm">{{ Session::get('success_message') }}</div>
	@endif
	@if (Session::has('error_message'))
		<div class="alert alert-danger m-t-sm"><?php echo html_entity_decode(Session::get('error_message')); ?></div>
	@endif
	
	{{-- N'est formaté que si le SH est activé --}}
	<div class="row" @if ($noSyntax == true) style="margin-bottom:20px;" @endif>
		<div class="col-sm-12">
			<label for="paste"><i>@if ($noSyntax == false) Syntax-highlighted @else Plain-text @endif</i></label>@if ($privacy != "Password-protected") <i class="pull-right"><a href="/{{ $link }}/raw">Raw paste</a> @endif </i>
			<pre id="paste"><code>@if ($noSyntax == true)<i>@endif{{ $content }} @if ($noSyntax == true)</i>@endif</code></pre>
		</div>
	</div>
	
	{{-- N'apparaît que si le SH est activé --}}
	@if ($noSyntax == false)
	<div class="row" style="margin-bottom:20px;">
		<div class="col-sm-12">
			<label for="noFormatPaste"><i>Plain-text</i></label>
			<i><textarea class="form-control input-sm" id="noFormatPaste" rows="15" readonly="true">{{ $content }}</textarea></i>
		</div>
	</div>
	@endif

	<!--  comment list area -->
		<div class="row">
			<div class="col-lg-12">
				<div class="comment-area">

					<div class="container col-lg-12">
						<div class="row">
							<div class="panel panel-default widget">
								<div class="panel-heading">

									<h3 class="panel-title">
										 Comments</h3>
									<span class="label label-info"> @if(!empty($total_comment)) {{$total_comment}} @else 0 @endif </span>
								</div>
								<div class="panel-body">
									<ul class="list-group">
										@if(!empty($comment_lists))
											@foreach($comment_lists as $comment_list)
												<li class="list-group-item">
													<div class="row">

														<div class="col-xs-10 col-md-11">
																	<div>
{{--																		<a href="http://www.jquery2dotnet.com/2013/10/google-style-login-page-desing-usign.html">--}}
{{--																			Google Style Login Page Design Using Bootstrap--}}
{{--																		</a>--}}

																		<div class="mic-info">
																			By: <a href="#">@if(!empty($comment_list->comment_user_name)) {{$comment_list->comment_user_name}} @endif</a>  @if(!empty($comment_list->created_at)) {{\Carbon\Carbon::parse($comment_list->created_at)->diffForhumans()}} @endif
																		</div>
																	</div>
																	<div class="comment-text">
																		@if(!empty($comment_list->comment_body)) {{$comment_list->comment_body}} @endif
																	</div>
																	{{--													<div class="action">--}}
																	{{--														<button type="button" class="btn btn-primary btn-xs" title="Edit">--}}
																	{{--															<span class="glyphicon glyphicon-pencil"></span>--}}
																	{{--														</button>--}}
																	{{--														<button type="button" class="btn btn-success btn-xs" title="Approved">--}}
																	{{--															<span class="glyphicon glyphicon-ok"></span>--}}
																	{{--														</button>--}}
																	{{--														<button type="button" class="btn btn-danger btn-xs" title="Delete">--}}
																	{{--															<span class="glyphicon glyphicon-trash"></span>--}}
																	{{--														</button>--}}
																	{{--													</div>--}}
																</div>


													</div>
												</li>
											@endforeach
										@endif

									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	<!-- start comment area -->
		<div style="margin-top:15px" class="row">
			<div class="col-lg-12">
				<div class="comment-area">
					<h5>Leave A Comment</h5>
					<form action="{{url('comment_save_by_user')}}" onsubmit="document.getElementById('comment_submit').disabled =1;" method="post">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="paster_id" value="{{ \Illuminate\Support\Facades\Crypt::encrypt($link) }}">
						<div class="form-group row mb-30">
							<div class="col-lg-6">
								<input type="text" class="form-control form-control-input" placeholder="Your Name" name="comment_user_name">
							</div>
							<div class="col-lg-6">
								<input type="email" class="form-control form-control-input" placeholder="Your Mail" name="comment_user_mail">
							</div>
						</div>
						<div class="form-group row">
							<div class="col-lg-12">
								<textarea class="form-control" placeholder="Your Comment" name="comment_body" rows="8"></textarea>
							</div>
						</div>
						<div class="form-group row mt-30">
							<div class="col-lg-6">
								<button type="submit" id="comment_submit" class="btn btn-primary comment-submit-btn">Submit</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>

</div>
@endsection

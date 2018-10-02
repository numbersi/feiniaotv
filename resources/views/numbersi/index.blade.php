@extends('template.cangbai.layout')
@section('body','index')
@section('title','影视首页')
@section('content')
<script>
$("#影视首页").attr("class","active");
</script>
  <div class="container">
   <div class="row">
            <marquee style="width: 100%;color: red;font-size: 20px;padding-top: 5px;padding-bottom: 5px;">{{config('webset.notice')}}</marquee>
        </div>
        <div class="row">
            {!! config('adconfig.index_ad') !!}
        </div>
   <div class="row">
	<div class="stui-pannel stui-pannel-bg clearfix">
     <div class="stui-pannel-box clearfix">
      <div class="stui-pannel-bd">
       <div class="carousel carousel_default flickity-page">
	   @if($bannerlist)
       @foreach($bannerlist as $v)
        <div class="col-md-2 col-xs-1">
         <a href="{{$v['banner_link']}}" class="stui-vodlist__thumb img-shadow" title="{{$v['banner_title']}}" style="background: url({{$v['banner_img']}}) no-repeat; background-position:50% 50%; background-size: cover; padding-top: 40%;">
             <span class="pic-text text-center">{{$v['banner_title']}}</span>
         </a>
        </div>
	   @endforeach
       @else
       @endif
       </div>
      </div>
     </div>
    </div> 
    <!--电视剧-->
	<div class="stui-pannel stui-pannel-bg clearfix">
     <div class="stui-pannel-box clearfix">
      <div class="col-lg-wide-75 col-xs-1 padding-0">
       <div class="stui-pannel_hd">
        <div class="stui-pannel__head clearfix">
         <a class="more text-muted pull-right" href="/tvlist/all/1.html">更多<i class="icon iconfont icon-more"></i></a>
         <h3 class="title"><img src="/public/static/lc/icon/icon_2.png" />电视剧</h3>
		 @foreach($videotype['tv'] as $key=>$type)
		 <ul class="nav nav-text pull-right @if($loop->index>4) hidden-xs hidden-md @endif">
			<li><a href="/tvlist/{{$type}}/1.html" class="text-muted">{{$key}}片</a> <span class="split-line"></span></li>
		 </ul>
		 @break($loop->index==10)
         @endforeach
        </div>
       </div>
       <div class="stui-pannel_bd clearfix">
        <ul class="stui-vodlist clearfix">
        @foreach($tvs as $tv)
		 <li class="col-md-5 col-sm-4 col-xs-3 @if($loop->index>4) hidden-lg hidden-md @endif" id="vodlist-{{$tv->id}}">
          <div class="stui-vodlist__box">
           <a class="stui-vodlist__thumb lazyload img-shadow" href="/play/tv/{{$tv->id}}.html" onclick="jilu(this)" title="{{$tv->title}}" style="background-image: url({{$tv->cover}});">
		   {{--<span class="play hidden-xs"></span><span class="pic-text text-right">{{$tv->upinfo}}</span>--}}
           </a>
           <div class="stui-vodlist__detail">
            <h4 class="title text-overflow"><a href="/play/{{$tv->id}}.html" onclick="jilu(this)" title="{{$tv->title}}">{{$tv->title}}</a></h4>
			<p class="text text-overflow text-muted hidden-xs">
                @foreach($tv->actor as $actor)
                    {{$actor}},
                    @endforeach
            </p>
           </div>
          </div>
		 </li>
		 @break($loop->index==5)
         @endforeach
        </ul>
       </div>
      </div>
      <div class="col-lg-wide-25 hidden-md hidden-sm hidden-xs">
       <div class="stui-pannel_hd">
        <div class="stui-pannel__head clearfix">
         <a class="more text-muted pull-right" href="/tvlist/all/1.html">更多 <i class="icon iconfont icon-more"></i></a>
         <h3 class="title"><img src="/public/static/lc/icon/icon_12.png" />热播榜</h3>
        </div>
       </div>
       <div class="stui-pannel_bd">
        <ul class="stui-vodlist__rank col-pd clearfix">
		 @foreach($tvs as $tv)
         <li class="@if($loop->index>4) hidden-lg hidden-md @endif"><a href="/play/{{$tv->id}}.html" onclick="jilu(this)" title="{{$tv->title}}"><span class="text-muted pull-right">{{$tv->upinfo}}</span><span class="badge badge-second">*</span>{{$tv->title}}</a></li>
        @break($loop->index==4)
        @endforeach
        </ul>
       </div>
       </div>
      </div>
     </div>

	</div>
</div>
        <script>
            $(function () {
                $('#button').click(function () {
                    var key = $('#sos').val();
                    if (key != '' && key != null) {
                        window.location = '/search/' + key + '.html'
                    }
                });

                $('input').keyup(function () {
                    if (event.keyCode == 13) {
                        $("#button").trigger("click");
                    }
                })
            })
        </script>
@endsection
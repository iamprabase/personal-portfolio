<section class="content-header">  <h1>    @php      $routename=explode('.',Route::currentRouteName());      $rn1=ucfirst($routename[0]);      $rn2=ucfirst($routename[1]);    @endphp  </h1>  <ol class="breadcrumb">    <li><a href="#"><i class="fa fa-dashboard"></i> DashBoard</a></li>    <li><a href="{{ $routename[0] }}">{{ $rn1 }}</a></li>    <li class="active">{{ ($rn2==='Index') ? 'Home' : $rn2 }}</li>  </ol></section>
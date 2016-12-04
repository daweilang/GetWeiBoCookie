@extends('layouts/app')

@section('content')
<div class="container">  
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">登录新浪微博</div>
                <div class="panel-body">

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>编辑失败</strong> 输入不符合要求<br><br>
                            {!! implode('<br>', $errors->all()) !!}
                        </div>
                    @endif

                    <form action="{{ url('admin/authorize/getCookie') }}" method="POST">
                        {!! csrf_field() !!}                      
                            <input id='sp' name='sp' value='{{ $sp }}' type='hidden'>  
                            <br>
                            <input id='servertime' name='servertime' value='{{ $servertime }}' type='hidden'>  
                            <br>
                            <input id='nonce' name='nonce' value='{{ $nonce }}' type='hidden'> 
                            <br> 
                            <input id='rsakv' name='rsakv' value='{{ $rsakv }}' type='hidden'>
                            <br> 
                            <input id='pcid' name='pcid' value='{{ $pcid }}' type='hidden'> 
                            <br> 
                            <img src='{{ $doorImg }}'>
                            <br> 
                            <input id='door' name='door' value='' type='text'> 
                            <br> 
                        <button class="btn btn-lg btn-info">提交验证码</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>  
@endsection

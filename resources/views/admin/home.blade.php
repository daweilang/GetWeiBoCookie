@extends('layouts/app')

@section('content')
<div class="container">  
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">模拟weibo登录</div>

                <div class="panel-body">
					设计说明：<br>
                    新浪通行证需要先请求prelogin.php，进行预登陆，获得登录所需要的参数和加密算法，<br>
					密码采用rsa算法，php实现比较复杂，所以使用预登陆返回的js算法(该算法有升级可能)，<br>
					由于是js加密，所以需要通过页面跳转方式将生成的密码传递到最后的登录提交页，<br>
					页面多次跳转，所需参数储存到临时文件，以便重复使用，<br>
					预登陆后会返回是否需要验证码的参数，根据参数，最后提交页分为自动提交和人工输入验证码提交两种流程<br>

                </div>
            </div>
        </div>
    </div>
</div>  
@endsection
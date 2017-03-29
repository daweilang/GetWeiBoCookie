@extends('layouts/app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">填写需要登录才能访问的微博地址</div>
                <div class="panel-heading">
                	获得的微博Cookie储存于项目目录下"storage/app/wbcookie/cookie_weibo.txt"中<br>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/admin/authorize/getTestContent') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('wb_url') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">微博页面地址</label>

                            <div class="col-md-6">
                                <input id="wb_url" type="text" class="form-control" name="wb_url" value="">
								@if ($errors->has('wb_url'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('wb_url') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-sign-in"></i> 测试
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

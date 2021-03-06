@extends('layouts.app')

@section('content')

    <div id="create-chat" class="container" style="margin-top:40px">
        <div class="row">
            <div class="col-sm-12 col-md-6 col-md-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong>Fill the form to continue</strong>
                    </div>
                    <div class="panel-body">
                        <form role="form" action="{{route('thread.resume.post')}}" method="POST">
                            {{csrf_field()}}
                            <fieldset>
                                <div class="row">
                                    <div class="center-block avatar">
                                        <span class="chat-img1">
                                        <span class="chat_label">Chat with: </span>
                                            <div class="circle">
                                                <div class="initials">RV</div>
                                            </div>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 col-md-10  col-md-offset-1 ">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="glyphicon glyphicon-envelope"></i>
                                                </span> 
                                                <input class="form-control" placeholder="Emailaddress" name="emailaddress" type="text" autofocus>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="glyphicon glyphicon-user"></i>
                                                </span> 
                                                <input class="form-control" placeholder="Your personal code" name="code">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-lg btn-primary btn-block" value="Create chat!">
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                    <div class="panel-footer ">Dont have a chat? <a href="{{route('index')}}"> Create chat</a> <a class="pull-right" href="{{route('login')}}"> Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">Chats</div>

                <div class="panel-body">
                    <thread-chats :threads="threads"></thread-chats>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">Chats</div>

                <div class="panel-body">
                    <chat-messages :messages="messages"></chat-messages>    
                </div>
                <div class="panel-footer">
                    <chat-form
                        v-on:messagesent="addMessageToThread"
                        :thread-id="{{ rand(0, 10000) }}"
                    ></chat-form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
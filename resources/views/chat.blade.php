<!-- resources/views/chat.blade.php -->

@extends('layouts.app')

@section('content')

<div class="main_section">
      <div class="chat_container">
        <div class="row">
            <div class="col-sm-12 message_section">
                 <div class="row">
                    <div class="new_message_head">
                       <div class="pull-left">
                          <button><i class="fa fa-plus-square-o" aria-hidden="true"></i> New Message</button>
                       </div>
                       <div class="pull-right">
                          <div class="dropdown">
                             <button class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-cogs" aria-hidden="true"></i>  Setting
                             <span class="caret"></span>
                             </button>
                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
                              <li><a href="#">Action</a></li>
                              <li><a href="#">Profile</a></li>
                              <li><a href="{{ url('/logout') }}"
                                    onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                    Logout
                                </a>
                                </li>
                              <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                            </ul>
                         </div>
                      </div>
                 </div><!--new_message_head-->
                 
                 <div class="chat_area" v-chat-scroll="{always: false}">
                    <chat-messages :messages="messages"></chat-messages>
                 </div><!--chat_area-->
                    <chat-form
                        v-on:messagesent="addMessageToThread"
                    ></chat-form>
                 </div> <!--message_section-->
              </div>
            </div>
        </div>
    </div>
</div>
@endsection
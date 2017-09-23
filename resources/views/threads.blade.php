@extends('layouts.app')

@section('content')

<div class="main_section">
  <div class="chat_container">
     <div class="col-sm-3 chat_sidebar">
     <div class="row">
        <div id="custom-search-input">
           <div class="input-group col-md-12">
              <input type="text" class="  search-query form-control" placeholder="Conversation" />
              <button class="btn btn-danger" type="button">
              <span class=" glyphicon glyphicon-search"></span>
              </button>
           </div>
        </div>
        <div class="dropdown all_conversation">
           <button class="dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
           <i class="fa fa-weixin" aria-hidden="true"></i>
           All Conversations
           <span class="caret pull-right"></span>
           </button>
              <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                 <li><a href="#"> All Conversation </a>  <ul class="sub_menu_ list-unstyled">
                 <li><a href="#"> All Conversation </a> </li>
                 <li><a href="#">Another action</a></li>
                 <li><a href="#">Something else here</a></li>
                 <li><a href="#">Separated link</a></li>
                 </ul>
                 </li>
                 <li><a href="#">Another action</a></li>
                 <li><a href="#">Something else here</a></li>
                 <li><a href="#">Separated link</a></li>
              </ul>
           </div>
           <div class="member_list">
              <thread-chats :threads="threads"></thread-chats>
           </div>
        </div>
     </div>
  </div>
     <!--chat_sidebar-->
  <div class="col-sm-9 message_section">
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
                  <li><a href="#">Logout</a></li>
                </ul>
             </div>
          </div>
     </div><!--new_message_head-->
     
     <div class="chat_area" v-chat-scroll="{always: false}">
        <chat-messages :user="{{ Auth::user() }}" :messages="messages"></chat-messages>    
     </div><!--chat_area-->
        <chat-form
        v-on:messagesent="addMessageToThread"
        :user="{{ Auth::user() }}"
        ></chat-form>
     </div> <!--message_section-->
  </div>
</div>
@endsection
//フォロー、アンフォローボタンの実装

@if (Auth::id() != $user->id)
//ログインしているuserのidを呼び出し、そのuserのidと異なる場合以下のボタンを表示する。

    @if (Auth::user()->is_following($user->id))
    　　//ログインしているuserのidは、そのuserがfollowしているuserのidが
        {!! Form::open(['route' => ['user.unfollow', $user->id], 'method' => 'delete']) !!}
            {!! Form::submit('アンフォロー', ['class' => "btn btn-danger btn-block"]) !!}
        {!! Form::close() !!}
        
    @else
        {!! Form::open(['route' => ['user.follow', $user->id]]) !!}
            {!! Form::submit('フォロー', ['class' => "btn btn-primary btn-block"]) !!}
        {!! Form::close() !!}
        
    @endif
    
@endif

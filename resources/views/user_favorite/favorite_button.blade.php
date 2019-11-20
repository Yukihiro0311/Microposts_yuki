@if (Auth::user()->check_favorite($micropost->id))
    {!! Form::open(['route' => ['favorites.unfavorite', $micropost->id], 'method' => 'delete']) !!}
    {!! Form::submit('そうでもないね', ['class' => "btn-danger btn-sm"]) !!}
    {!! Form::close() !!}
        
    @else
    {!! Form::open(['route' => ['favorites.favorite', $micropost->id]]) !!}
    {!! Form::submit('いいね', ['class' => "btn btn-success btn-sm"]) !!}
    {!! Form::close() !!}
         
@endif


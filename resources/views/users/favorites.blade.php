@extends('layouts.app')

@section('content')
    <div class="row">
        
        <aside class="col-sm-4">
            @include('users.card', ['user' => $user])
        </aside>
           
        <div class="col-sm-8">
            @include('users.navtabs',['user' => $user])

            @if (count($favorites) > 0)
                <ul class="media-list">
                    @foreach ($favorites as $favorite)
                        <li class="media mb-3">
                            <img class="mr-2 rounded" src="{{ Gravatar::src($favorite->user->email, 50) }}" alt="">
                            <div class="media-body">
                                <div>
                                     {!! link_to_route('users.show', $favorite->user->name, ['id' => $favorite->user->id]) !!} 
                                </div>
                                <div>
                                    <p class="mb-0">{!! nl2br(e($favorite->content)) !!}</p>
                                </div>
                                <div>
                                    @include('user_favorite.favorite_button', ['micropost' => $favorite])
                                </div>
                            </div> 
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
    
@endsection
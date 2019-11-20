@extends('layouts.app')

@section('content')
    <div class="row">
        <aside class="col-sm-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $user->name }}</h3>
                </div>
                <div class="card-body">
                    <img class="rounded img-fluid" src="{{ Gravatar::src($user->email, 500) }}" alt="">
                </div>
            </div>
            @include('user_follow.follow_button', ['user' => $user])
        </aside>
        
        <div class="col-sm-8">
            <ul class="nav nav-tabs nav-justified mb-3">
                <li class="nav-item"><a href="{{ route('users.show', ['id' => $user->id]) }}" class="nav-link {{ Request::is('users/*/$user->id') ? 'active' : '' }}">タイムライン <span class="badge badge-secondary">{{ $count_microposts }}</span></a></li>
                <li class="nav-item"><a href="{{ route('users.followings', ['id' => $user->id]) }}" class="nav-link {{ Request::is('users/*/followings') ? 'active' : '' }}">フォロー<span class="badge badge-secondary">{{ $count_followings }}</span></a></li>
                <li class="nav-item"><a href="{{ route('users.followers', ['id' => $user->id]) }}" class="nav-link {{ Request::is('users/*/followers') ? 'active' : '' }}">フォロワー<span class="badge badge-secondary">{{ $count_followers }}</span></a></li>
                <li class="nav-item"><a href="{{ route('my_favorite', ['id' => $user->id]) }}" class="nav-link {{ Request::is('microposts/*/$micropost->id') ? 'active' : '' }}">お気に入り<span class="badge badge-secondary">{{ $count_favorites }}</span></a></li>
            </ul>
            @include('users.users', ['users' => $users])
        </div>
    </div>
@endsection
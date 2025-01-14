@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
            @endif
            <form  action="{{ route('quiz.question-set', ['id' => 1]) }}" method="GET">
                @csrf
                <x-adminlte-button type="submit" label="Luyện tập" theme="primary" icon="fas fa-key"/>
            </form> 
        </div>
    </div>
</div>
@endsection

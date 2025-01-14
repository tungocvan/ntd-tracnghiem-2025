@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Kết quả trắc nghiệm') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    @foreach($results as $index => $result)
                        <div class="mb-4">
                            <h4>Câu {{ $index + 1 }}: {{ $result['question'] }}</h4>

                            @foreach($result['answers'] as $answerIndex => $answer)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="answers[{{ $index }}]" id="answer{{ $index }}_{{ $answerIndex }}" value="{{ $answerIndex }}" disabled
                                        @if($result['user_answer'] == $answerIndex) checked @endif>
                                    <label class="form-check-label" for="answer{{ $index }}_{{ $answerIndex }}">
                                        {{ $answer }}
                                        @if($answerIndex == $result['correct_answer'])
                                            <span class="text-success">✔</span>
                                        @elseif($result['user_answer'] == $answerIndex)
                                            <span class="text-danger">✘</span>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
                <div class="card-footer">
                    <a href="{{ route('student.index') }}">Quay lại</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

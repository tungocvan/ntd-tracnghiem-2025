@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Kết quả trắc nghiệm') }} - <a href="{{ route('student.index') }}">Quay lại</a></div>
                <div class="card-body">
                    <p>Tổng số câu: <span> {{$total['total']}}</span></p>
                    <p>Số câu trả lời đúng:<span> {{$total['right']}}</span></p>
                    <p>Số câu trả lời sai: <span> {{$total['wrong']}}</span></p>
                    <p>Chưa trả lời: <span> {{$total['noAnswer']}}</span></p>
                    <p>Đạt tỉ lệ: <span> {{$total['tile']}}%</span></p>
                </div>
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
                                        @if($result['user_answer'] == $answerIndex && $result['user_answer']!==null) checked @endif>
                                    <label class="form-check-label" for="answer{{ $index }}_{{ $answerIndex }}">
                                        {{ $answer }}
                                        @if($answerIndex == $result['correct_answer'] && $result['user_answer']!==null)
                                            <span class="text-success">✔</span>
                                        @elseif($result['user_answer'] == $answerIndex && $result['user_answer']!==null)
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

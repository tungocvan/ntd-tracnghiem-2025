@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    {{ __('Danh sách câu hỏi trắc nghiệm: - ') }}
                    <p>Thời gian làm bài: {{ $timeRemaining }} phút</p>
                    Thời gian còn lại: <span id="timer"></span>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form id="quiz-form"  action="{{ route('quiz.submit-set', ['id' => $id]) }}" method="POST">
                        @csrf

                        @foreach($questions as $index => $question)
                            <div class="mb-4">
                                <h4>Câu {{ $index + 1 }}: {{ $question['content'] }}</h4>

                                @foreach($question['answers'] as $answerIndex => $answer)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answers[{{ $index }}]" id="answer{{ $index }}_{{ $answerIndex }}" value="{{ $answerIndex }}">
                                        <label class="form-check-label" for="answer{{ $index }}_{{ $answerIndex }}">
                                            {{ $answer }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                        <button type="submit" class="btn btn-primary">Nộp bài</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
{{-- https://www.daterangepicker.com/#examples  --}}
@stop
<script>
    // Thời gian làm bài tính bằng giây (ví dụ: 10 phút = 60 giây)
    let timeRemaining = {{ $timeRemaining * 60 }};

    // Hàm cập nhật thời gian trên giao diện
    function updateTimer() {
        let minutes = Math.floor(timeRemaining / 60);
        let seconds = timeRemaining % 60;
        document.getElementById('timer').textContent = `${minutes}:${seconds < 10 ? '0' + seconds : seconds}`;

        // Khi hết thời gian, tự động nộp bài
        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            document.getElementById('quiz-form').submit();
        }
        timeRemaining--;
    }

    // Cập nhật đồng hồ đếm ngược mỗi giây
    let timerInterval = setInterval(updateTimer, 1000);

    // window.onload = function() {
    //     updateTimer();
    // };
    document.addEventListener('DOMContentLoaded', function(event) {

        updateTimer();
    });
</script>

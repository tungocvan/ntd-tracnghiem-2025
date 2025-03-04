@extends('layouts.app')

@section('content')
<div class="container" x-data="exportPdf">
    <div id="result" class="row justify-content-center">
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
    <button id="exportPdf" class="btn btn-primary">Export PDF</button>
</div>
@endsection


@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const exportPdfButton = document.getElementById('exportPdf');
    
            if (exportPdfButton) {
                exportPdfButton.addEventListener('click', function (event) {
                    event.preventDefault(); // Ngăn chặn hành động mặc định của thẻ <a>
    
                    const element = document.getElementById('result'); // Lấy phần tử có id="result"
    
                    // Sử dụng html2canvas để chụp nội dung
                    html2canvas(element).then(canvas => {
                        const imgData = canvas.toDataURL('image/png'); // Chuyển đổi canvas thành hình ảnh PNG
    
                        // Tạo PDF với khổ A4, hướng dọc
                        const pdf = new jspdf.jsPDF('p', 'mm', 'a4'); // Hướng dọc, khổ A4
    
                        const imgWidth = 210; // Chiều rộng của khổ A4 (mm)
                        const imgHeight = (canvas.height * imgWidth) / canvas.width; // Tính chiều cao dựa trên tỷ lệ
    
                        const a4Height = 297; // Chiều cao khổ A4 (mm)
                        const marginTop = 2.64583; // Margin top 10px (2.64583mm)
                        const marginBottom = 4.64583; // Margin bottom 10px (2.64583mm)
                        const contentHeight = a4Height - marginTop - marginBottom; // Chiều cao có thể sử dụng cho nội dung
    
                        let position = 0; // Vị trí bắt đầu của hình ảnh
    
                        // Chia nội dung thành nhiều trang nếu cần
                        while (position < imgHeight) {
                            if (position > 0) {
                                pdf.addPage(); // Thêm trang mới nếu cần
                            }
    
                            // Cắt hình ảnh và thêm vào PDF với margin top và bottom
                            pdf.addImage(
                                imgData, // Dữ liệu hình ảnh
                                'PNG', // Định dạng hình ảnh
                                0, // Vị trí x (ngang)
                                -position + marginTop, // Vị trí y (dọc) với margin top
                                imgWidth, // Chiều rộng hình ảnh
                                imgHeight // Chiều cao hình ảnh
                            );
    
                            position += contentHeight; // Di chuyển vị trí cho trang tiếp theo
                        }
    
                        pdf.save('result.pdf'); // Lưu file PDF với tên "result.pdf"
                    });
                });
            }
        });
        </script>
@stop
@extends('adminlte::page')
{{-- @section('plugins.Select2', true) --}}
@section('plugins.Summernote', true)
@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <style>
        .alert a{
            color: black;
            text-decoration:none
        }
    </style>
@stop
@section('title', 'QUẢN TRỊ CÂU HỎI')
@php
    //dd($data);
    $config = [
        "height" => "200",
    ];

@endphp
@section('content_header')
<x-adminlte-alert>
    <form  action="{{ route('quiz.quiz-edit') }}" method="POST">
        @csrf
    <h5>SỬA CÂU HỎI</h5>
    @if(session('question'))
                <div class="alert alert-success">
                    {{ session('question') }}
                </div>
    @endif
    <input type="hidden" name="user_id" value="{{$data['user_id']}}"  />
    <input type="hidden" name="id" value="{{$data['id']}}"  />
    <div class="row gx-4">
        <div class="col">
            <x-adminlte-select name="monhoc">
                <x-adminlte-options :options="$data['monhoc']"  :selected="old('monhoc') ?? $questions->category_topic_id"  igroup-size="md" label-class="text-lightblue"  placeholder="Chọn Môn học..." />
            </x-adminlte-select>
        </div>
        <div class="col">
            <x-adminlte-select name="khoilop">
                <x-adminlte-options :options="$data['khoilop']"  :selected="old('khoilop') ?? $questions->category_class_id"  igroup-size="md" label-class="text-lightblue"  placeholder="Chọn Khối lớp..." />
            </x-adminlte-select>
        </div>
        <div class="col">
            <x-adminlte-select name="capdo">
                <x-adminlte-options :options="$data['capdo']"  :selected="old('capdo') ?? $questions->question_level"  igroup-size="md" label-class="text-lightblue"  placeholder="Chọn Cấp độ..." />
            </x-adminlte-select>
        </div>
        <div class="col">
            <x-adminlte-select name="loaicau">
                <x-adminlte-options :options="$data['loaicau']" :selected="old('loaicau') ?? $questions->question_type"  igroup-size="md" label-class="text-lightblue"  placeholder="Loại đáp án..." />
            </x-adminlte-select>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h5>Nội dung câu hỏi</h5>
            <x-adminlte-text-editor name="content" :config="$config" >
                {{old('content') ?? $questions->parsed_details['content'] }}
            </x-adminlte-text-editor>
        </div>
    </div>

    <h5>Nội dung trả lời</h5>
    <div class="row g-2">

        <div class="col-6">
            <h5>Đáp án 1</h5>
            <x-adminlte-text-editor name="dapan1" >
                {{old('dapan1') ?? $questions->parsed_details['answers'][0] }}
            </x-adminlte-text-editor>
        </div>
        <div class="col-6">
            <h5>Đáp án 2</h5>
            <x-adminlte-text-editor name="dapan2" :selected="old('dapan2')">
                {{old('dapan2') ?? $questions->parsed_details['answers'][1] }}
            </x-adminlte-text-editor>
        </div>
        <div class="col-6">
            <h5>Đáp án 3</h5>
            <x-adminlte-text-editor name="dapan3" :selected="old('dapan3')">
                {{old('dapan3') ?? $questions->parsed_details['answers'][2] }}
            </x-adminlte-text-editor>
        </div>
        <div class="col-6">
            <h5>Đáp án 4</h5>
            <x-adminlte-text-editor name="dapan4" :selected="old('dapan4')">
                {{old('dapan4') ?? $questions->parsed_details['answers'][3] }}
            </x-adminlte-text-editor>
        </div>
    </div>

    <div class="row">
        <div class="col-4">
            <h5>Chọn câu trả lời</h5>
            <x-adminlte-select name="dapan">
                <x-adminlte-options :options="$data['dapan']" :selected="old('dapan') ?? $questions->correct_answers"  igroup-size="md" label-class="text-lightblue"  placeholder="Chọn câu trả lời" />
            </x-adminlte-select>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <a href="{{ route('quiz.quiz-list') }}" class="btn  btn-outline-primary">Quay về</a>
            <button type="submit" class="btn  btn-outline-primary">Cập nhật</button>
        </div>
    </div>
    </form>
</x-adminlte-alert>
@stop




@section('content')
<x-adminlte-alert>
    <div class="row">
        <div class="col-md-12">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>
</x-adminlte-alert>
<script>
    document.addEventListener('DOMContentLoaded', function(event) {
             // Bắt sự kiện chọn tất cả

    });
</script>
@stop


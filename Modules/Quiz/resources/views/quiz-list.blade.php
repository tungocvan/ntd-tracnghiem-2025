@extends('adminlte::page')
{{-- @section('plugins.Select2', true) --}}
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
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
@section('content_header')
<x-adminlte-alert>
    <h5>DANH SÁCH CÂU HỎI</h5>
    <div class="row">
        <div class="col-3">
            <a href="{{ route('quiz.quiz-add') }}" class="btn  btn-outline-primary ">Thêm mới</a>
            <a href="{{ route('quiz.quiz-add') }}" class="btn  btn-outline-danger">Xóa tất cả</a>
        </div>
        <div class="col-4 d-flex">
            <form id="importForm"  action="{{ route('quiz.quiz-import') }}" method="POST" enctype="multipart/form-data" class="d-flex">
                @csrf
                <x-adminlte-input-file name="file" igroup-size="sm" placeholder="Import File...">
                    <x-slot name="prependSlot">
                        <div class="input-group-text bg-lightblue">
                            <i class="fas fa-upload"></i>
                        </div>
                    </x-slot>

                </x-adminlte-input-file>
                {{-- <button type="submit" class="btn  btn-outline-danger ml-2">Import Danh sách Câu hỏi</button> --}}
            </form>
        </div>
    </div>
</x-adminlte-alert>
@stop


@php
    $heads = [
        'ID',
        'Nội dung câu hỏi',
        'Môn học',
        'Khối lớp',
        'Cấp độ',
        'Loại câu hỏi',
        ['label' => 'Actions', 'no-export' => true, 'width' => 15],
    ];

    $config = [
        'data' => $questions,
        'order' => [[0, 'desc']],
        'columns' => [
            ['data' => 'id','name' => 'id'],
            ['data' => 'content','name' => 'content'],
            ['data' => 'name_topic'],
            ['data' => 'name_class'],
            ['data' => 'question_level'],
            ['data' => 'question_type'],
            ['orderable' => false, 'data' => 'action'],
        ],
        'fixedHeader' => true,  
    ];
  // dd($config['data']);
@endphp

@section('content')
<x-adminlte-alert>
    <div class="row">
        <div class="col-md-12">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <form  action="{{ route('quiz.submit-list') }}" method="POST">
                @csrf
                <x-adminlte-datatable id="table7" :heads="$heads" theme="info" class="bg-teal" :config="$config"
                striped beautify hoverable bordered  with-buttons />
            </form>
        </div>
    </div>
</x-adminlte-alert>
<script>
    document.addEventListener('DOMContentLoaded', function(event) {
             // Bắt sự kiện chọn tất cả
             document.getElementById('file').addEventListener('change', function() {
                    document.getElementById('importForm').submit();
             });
    });
</script>
@stop


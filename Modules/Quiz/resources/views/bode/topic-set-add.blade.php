@extends('adminlte::page')
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('title', 'QUẢN TRỊ BỘ ĐỀ')
@section('content_header')
    <h1>THÊM MỚI BỘ ĐỀ</h1>
@stop
@section('plugins.Select2', true)
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
@section('content')
@php
  $questions =  $data['questions'] ?? [];
  $heads = [
        'ID',
        'Nội dung câu hỏi',
        'Môn học',
        'Khối lớp',
        'Cấp độ',
        'Loại câu hỏi',
        ['label' => '<input type="checkbox" onclick="toggleSelectAll(this)">', 'no-export' => true, 'width' => 15],
    ];
    $colSelector = ':not([dt-no-export])';
    $lengthBtn = [
        'extend' => 'pageLength',
        'className' => 'btn-default',
    ];
    $excelBtn = [
        'action' => "alert('Activated!')",
        'className' => 'btn-default',
        'text' => 'Save',
        'titleAttr' => 'Tạo bộ đề',
        'exportOptions' => ['columns' => $colSelector],
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
        'lengthMenu' => [[10, 25, 50, -1], ['10 rows', '25 rows', '50 rows', 'Show all']],

        'buttons' => [$lengthBtn, $excelBtn]
    ];
@endphp
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
    <form  action="{{ route('quiz.submit-list') }}" method="POST">
        @csrf
    <div class="row">
        <div class="col">
            <x-adminlte-select name="monhoc">
                <x-adminlte-options :options="$data['monhoc']"  :selected="old('monhoc')"  igroup-size="md" label-class="text-lightblue"  placeholder="Chọn Môn học..." />
            </x-adminlte-select>
        </div>
        <div class="col">
            <x-adminlte-select name="khoilop">
                <x-adminlte-options :options="$data['khoilop']"  :selected="old('khoilop')"  igroup-size="md" label-class="text-lightblue"  placeholder="Chọn Khối lớp..." />
            </x-adminlte-select>
        </div>
        <div class="col">
            <x-adminlte-select name="capdo">
                <x-adminlte-options :options="$data['capdo']"  :selected="old('capdo')"  igroup-size="md" label-class="text-lightblue"  placeholder="Chọn Cấp độ..." />
            </x-adminlte-select>
        </div>
        <div class="col">
            <x-adminlte-select name="loaicau">
                <x-adminlte-options :options="$data['loaicau']" :selected="old('loaicau') ?? 0"  igroup-size="md" label-class="text-lightblue"  placeholder="Loại đáp án..." />
            </x-adminlte-select>
        </div>
        <div class="col">
            <button type="submit" name="loc-cau-hoi" class="btn btn-block btn-outline-success" value="true">Submit</button>
        </div>
    </div>
    </form>
</x-adminlte-alert>
<x-adminlte-alert>
    <div class="row">
        <div class="col">
            <h2>Tạo ngẫu nhiên câu hỏi</h2>
        </div>
    </div>
</x-adminlte-alert>
<x-adminlte-alert>
    <div class="row">
   
        <div class="col-2">
            Đã chọn <strong id="socau">0</strong> câu.
        </div>
        <div class="col-5">
            <form  action="{{ route('quiz.create-setquiz') }}" method="POST">
                @csrf
            <div class="row">
                <div class="col-6">
                    <x-adminlte-input name="ten_bode" type="text" placeholder="Tên Bộ Đề..."/>            
                </div>
                <div class="col-3">                 
                    <x-adminlte-input name="tg_bode" type="text" placeholder="Thời gian làm bài"/>
                </div>
                <div class="col-3">                 
                    <button type="submit" name="tao-bo-de" class="btn btn-block btn-outline-success" value="true">Tạo bộ đề</button>
                </div>
            </div>    
            
            <input type="hidden" id="data_bo_de" name="bo_de" />
            <input type="hidden"  name="user_id" value="{{$data['user_id']}}" />
            <input type="hidden"  name="bd_monhoc" value="{{$category_topic_id ?? ''}}" />
            <input type="hidden"  name="bd_khoilop" value="{{$category_class_id ?? ''}}" />
            <input type="hidden"  name="bd_capdo" value="{{$question_level ?? ''}}" />
            <input type="hidden"  name="bd_loaicau" value="{{$question_type ?? ''}}" />
            
            </form>
        </div>
    </div>
</x-adminlte-alert>
@if(count($questions) > 0 )
<x-adminlte-alert>
    <div class="row">
        <div class="col-md-12">
            <form  action="{{ route('quiz.submit-list') }}" method="POST">
                @csrf
                <x-adminlte-datatable id="table7" :heads="$heads" theme="info" class="bg-teal" :config="$config"
                striped beautify hoverable bordered  with-buttons />
            </form>
        </div>
    </div>
</x-adminlte-alert>
@endif

<script>
    var bode = [];
    var socau = document.getElementById('socau');
    var dataBode = document.getElementById('data_bo_de');
    document.addEventListener('DOMContentLoaded', function(event) {
        document.getElementById('monhoc').addEventListener('change', function(e) {
                 console.log(e.target.value);
        });
        document.getElementById('khoilop').addEventListener('change', function(e) {
                 console.log(e.target.value);
        });
        document.getElementById('capdo').addEventListener('change', function(e) {
                 console.log(e.target.value);
        });
        document.getElementById('loaicau').addEventListener('change', function(e) {
                 console.log(e.target.value);
        });
    });
    function toggleSelectAll(source) {
            const checkboxes = document.querySelectorAll('#table7 tbody input[type="checkbox"]');
            bode=[];
            checkboxes.forEach((checkbox) => {
                checkbox.checked = source.checked
                if(checkbox.checked == true){
                    //console.log(checkbox.value);
                    bode.push(parseInt(checkbox.value))
                }else{
                    bode=[];
                }

            });
            console.log('bode:',bode);
            dataBode.value = JSON.stringify(bode)
            socau.innerText = bode.length;
    }
    function HanlderCheck(cb,id) {
        // console.log('Clicked:',cb.checked);
        // console.log('id:',id);
        toggleElement(bode,id);
        console.log('bode:',bode);
        socau.innerText = bode.length;
        dataBode.value = JSON.stringify(bode)
    }
    function toggleElement(array, id) {
        const index = array.indexOf(id);
        if (index === -1) {
            // Nếu không tìm thấy phần tử, thêm vào mảng
            array.push(id);
        } else {
            // Nếu tìm thấy phần tử, xóa khỏi mảng
            array.splice(index, 1);
        }
        return array;
     }
</script>
@stop


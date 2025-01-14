@extends('adminlte::page')
@section('title', 'QUẢN TRỊ BỘ ĐỀ')
@section('content_header')
    <h1>DANH SÁCH BỘ ĐỀ</h1>
@stop
@section('plugins.Select2', true)
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
    <div class="row gx-4">        
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
    </div>
</x-adminlte-alert>
<script>
    document.addEventListener('DOMContentLoaded', function(event) {
             // Bắt sự kiện chọn tất cả
   
    });
</script>
@stop


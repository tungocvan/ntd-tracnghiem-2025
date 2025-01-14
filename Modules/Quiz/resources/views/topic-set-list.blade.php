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
</x-adminlte-alert>
<script>
    document.addEventListener('DOMContentLoaded', function(event) {
             // Bắt sự kiện chọn tất cả
   
    });
</script>
@stop


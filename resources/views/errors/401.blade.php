@extends('layouts.master') 
@section('main')
<style>
    .container {
        text-align: center;
        display: table-cell;
        vertical-align: middle;
    }

    .content {
        text-align: center;
        display: inline-block;
    }

    .title {
        font-size: 72px;
        margin-bottom: 40px;
    }
</style>
<main class="pt-8">
  <div class="container">
        <div class="content">
            <div class="title">{{$error['code']}}</div>
            <div class="title">{{$error['description']}}</div>
        </div>
    </div>
</main>
@stop
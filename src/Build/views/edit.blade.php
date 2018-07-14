@extends('admin::layouts.master')
@section('content')
    <div class="card" id="app">
        <div class="card-header">{MODEL_TITLE}管理</div>
        <ul role="tablist" class="nav nav-tabs">
            <li class="nav-item"><a href="/{ROUTE_ROOT}" class="nav-link">{MODEL_TITLE}列表</a></li>
            <li class="nav-item"><a href="#" class="nav-link active">修改{MODEL_TITLE}</a></li>
        </ul>
        <form action="/{ROUTE_ROOT}/{{${SMODEL}['id']}}" method="post">
            <div class="card-body card-body-contrast">
                @csrf @method('PUT')
                {HTML}
            </div>
            <div class="card-footer text-muted">
                <button class="btn btn-primary offset-sm-2">保存更新</button>
            </div>
        </form>
    </div>
@endsection

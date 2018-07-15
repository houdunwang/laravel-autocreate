<?php
namespace {NAMESPACE_CONTROLLER};

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use {NAMESPACE_MODEL};
use {NAMESPACE_REQUEST};
class {CONTROLLE_NAME} extends Controller
{
    //显示列表
    public function index()
    {
        $data = {MODEL}::paginate(10);
        return view('{VIEW_NAME}.index', compact('data'));
    }

    //创建视图
    public function create({MODEL} ${SMODEL})
    {
        return view('{VIEW_NAME}.create',compact('{SMODEL}'));
    }

    //保存数据
    public function store({MODEL}Request $request,{MODEL} ${SMODEL})
    {
        ${SMODEL}->fill($request->all());
        ${SMODEL}->save();

        return redirect('/{ROUTE_ROOT}')->with('success', '保存成功');
    }

    //显示记录
    public function show({MODEL} $field)
    {
        return view('{VIEW_NAME}.show', compact('field'));
    }

    //编辑视图
    public function edit({MODEL} ${SMODEL})
    {
        return view('{VIEW_NAME}.edit', compact('{SMODEL}'));
    }

    //更新数据
    public function update({MODEL}Request $request, {MODEL} ${SMODEL})
    {
        ${SMODEL}->update($request->all());
        return redirect('/{ROUTE_ROOT}')->with('success','更新成功');
    }

    //删除模型
    public function destroy({MODEL} ${SMODEL})
    {
        ${SMODEL}->delete();
        return redirect('{ROUTE_ROOT}')->with('success','删除成功');
    }
}

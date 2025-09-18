<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Tag;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TagController extends Controller
{
    private string $dashboard_title = 'Управление компонентами для блоков';
    private string $dashboard = 'tags';
    private string $err = '';

    public static function getTags($params = []){

        $tags = $tagList = [];

        if (empty($params['tag_id'])){
            $tags = Tag::all();
        } else {
            $tags = Tag::where('id','=', $params['tag_id'])->get();
        }

        return $tags->keyBy('id');
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        return Inertia::render('Dashboard', [
            'dashboard' => $this->dashboard,
            'dashboard_title' => $this->dashboard_title,
            'tags' => self::getTags(),
            'err' => $this->err,
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->show();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        Tag::create([
            'name' => $request->name,
            'class' => $request->class,
        ]);
        return $this->show();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function update(Request $request)
    {
        foreach ($request->tags as $key => $val){
            if (empty($val['del'])){
                Tag::where('id', '=', $val['id'])
                    ->update([
                        'name' => $val['name'],
                        'class' => $val['class'],
                    ]);
            } else {
                $this->delete($val['id']);
            }
        }
        return $this->show();
    }

    /**
     * Удаление тега
     */
    private function delete(int $tag_id)
    {
        Tag::destroy($tag_id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $this->delete($request->tag_id);
        return $this->show();
    }
}

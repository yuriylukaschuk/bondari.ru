<?php

namespace App\Http\Controllers;

use App\Models\Razdel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RazdelController extends Controller
{
    public string $menu = '<nav><ul class="topmenu">';
    public string $razdelsTree = '';
    private string $dashboard = 'razdels';
    private string $dashboard_title = 'Редактирование форм';
    private string $err = '';
    private $razdels = [];
    private $first_parent = '';

    /**
     * Show the form for creating a new resource.
     */
    public function show(int $parent_id = 0, int $lvl = 1)
    {
        $razdels = Razdel::where('lvl','=', $lvl)
            ->where('parent_id','=', $parent_id)
            ->orderBy('parent_id')
            ->orderBy('lvl')
            ->orderBy('npp')
            ->get()
            ->keyBy('id');

        return Inertia::render('Dashboard', [
            'dashboard' => $this->dashboard,
            'dashboard_title' => $this->dashboard_title,
            'err' => $this->err,
            'parent_id' => $parent_id,
            'lvl' => $lvl,
            'razdels' => $razdels,
        ]);
    }

    /**
     * Показать все пункты раздела
     */
    public function index(Request $request)
    {
        if ($request->has('razdel_id')){
            $razdel = Razdel::where('id', $request->get('razdel_id'))->first();
            $parent_id = $razdel->id;
            $lvl = $razdel->lvl + 1;
        } elseif ($request->has('parent_id')){
            $parent_id = $request->parent_id;
            $lvl = $request->lvl;
        } else {
            $parent_id = 0;
            $lvl = 1;
        }
        return $this->show($parent_id, $lvl);
    }

    private function getParent(int $parent_id, int $id = 0)
    {
        if (empty($this->razdels[$parent_id])){
            $this->first_parent = $this->razdels[$id]['url'];
            return;
        }
        $this->getParent($this->razdels[$parent_id]['parent_id'], $parent_id);
    }

    public function changeUrl($parent_id, $url)
    {
        $sep = '/';
        $this->getParent($parent_id);
        // Если арес страницы не содержит разделители
        if (stripos($url, $sep) === false){
            return $this->first_parent . $sep . $url;
        // В противном случае удаляем и формируем заново
        } else {
            $arr = explode($sep, $url);
            return $this->first_parent . $sep . end($arr);
        }
    }

    /**
     * Добавить новый раздел
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'npp' => 'required|integer',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'keywords' => 'nullable|string|max:255',
            'url' => 'required|string|unique:razdels,url',
            'feedback' => 'boolean',
            'feedback_title' => 'nullable|string|max:64',
        ]);
        $razdels = Razdel::all();
        // Добавляем parent_id и lvl из URL параметров
        $validated['parent_id'] = $request->parent_id;
        $validated['lvl'] = $request->lvl;
        if ( $request->lvl > 1){
            $this->razdels = Razdel::all()->keyBy('id');
            $validated['url'] = $this->changeUrl($request->parent_id, $request->url);
        }
        $razdel = Razdel::create($validated);
        return $this->show($request->parent_id, $request->lvl);
    }

    /**
     * Добавить новый раздел
     */
    public function update(Request $request)
    {
        $this->razdels = Razdel::all()->keyBy('id');
        foreach ($request->razdels as $razdel_id => $razdel){
            $parent_id = $razdel['parent_id'];
            $lvl = $razdel['lvl'];
            if (empty($razdel['del'])){
                Razdel::where('id', '=', $razdel_id)
                ->update([
                    'npp' => $razdel['npp'],
                    'title' => $razdel['title'],
                    'description' => $razdel['description'],
                    'keywords' => $razdel['keywords'],
                    'url' => ($lvl == 1) ? $razdel['url'] : $this->changeUrl($parent_id, $razdel['url']),
                    'feedback' => $razdel['feedback'],
                    'feedback_title' => $razdel['feedback_title'],
                ]);
            } else {
                $childrens = Razdel::allChildrens($razdel['id']);
                $children_id = [];
                foreach ($childrens as $key => $val){
                    $children_id[] = $val->id;
                }
                Razdel::destroy($children_id);
            }
        }
        return $this->show($parent_id, $lvl);
    }
}

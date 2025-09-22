<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Component;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\RazdelController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\PositionController;

use App\Traits\RazdelTrait;

class BlockController extends Controller
{
    use RazdelTrait;

    public string $dashboard_title = 'Сборка каркаса страницы';
    private string $dashboard = 'blocks';
    private string $err = '';

    public static function getBlocks($params = [])
    {
        if (!empty($params['id'])){
            return Block::with(['components' => function($query) {
                    $query->orderBy('number', 'asc');
                }])
                ->where('id', $params['id'])
                ->orderBy('npp')
                ->get()
                ->keyBy('npp');
        } elseif (!empty($params['block_id'])){
            return Block::with(['components' => function($query) {
                    $query->orderBy('number', 'asc');
                }])
                ->where('id', '=', $params['block_id'])
                ->first();
        } elseif (!empty($params['npp'])){
            return Block::with(['components' => function($query) {
                    $query->orderBy('number', 'asc');
                }])
                ->where('razdel_id', $params['razdel_id'])
                ->where('npp', '=', $params['npp'])
                ->get()
                ->keyBy('npp');
        } elseif (!empty($params['withoutComponenets'])){
            // Получаем все блоки из раздела
            $result = Block::where('razdel_id','=',$params['razdel_id'])
                ->orderBy('npp')
                ->get();

            foreach ($result as $block){
                $blocks[$block->id] = [
                    'id' => $block->id,
                    'npp' => $block->npp,
                    'images' => $block->images,
                    'imageset' => $block->imageset,
                    'positions' => $block->positions,
                ];
            }
            return $blocks;
        } else {
            return Block::with(['components' => function($query) {
                    $query->orderBy('number', 'asc');
                }])
                ->where('razdel_id', $params['razdel_id'])
                ->orderBy('npp')
                ->get()
                ->keyBy('npp');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $razdel_id = 1)
    {
        return Inertia::render('Dashboard', [
            'dashboard' => $this->dashboard,
            'dashboard_title' => $this->dashboard_title,
            'err' => $this->err,
            'razdel_id' => $razdel_id,
            'razdels' => $this->getRazdelsList($razdel_id),
            'tags' => TagController::getTags(),
            'positions' => PositionController::getPositions(),
            'blocks' => self::getBlocks([
                'razdel_id' => $razdel_id
            ])
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $razdel_id = $request->filled('razdel_id') ? $request->razdel_id : 1;
        return $this->show($razdel_id);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->isNotFilled('razdel_id')) {
            return $this->show(1);
        }

        // Проверяем существование блока с указанным номером в разделе
        $blocks = self::getBlocks([
            'razdel_id' => $request->razdel_id,
            'npp' => $request->block,
        ]);

        // Если на стринце блока с указанным номером не существует, добавляем его в раздел
        if ($blocks->isEmpty()){
            $blocks = Block::create([
                'razdel_id' => $request->razdel_id,
                'npp' => $request->block,
            ]);
            $block_id = $blocks->id;

            if ($request->tag_id == 8) {
                Block::incrementBlocks($block_id, [
                    'imageset' => 1
                ]);
                for ($npp=1; $npp<=$request->photo_count; $npp++){
                    Component::addComponents([
                        'block_id' => $block_id,
                        'tag_id' => $request->tag_id,
                        'position_id' => 10, // Всегда центрируем
                        'number' => $npp,
                    ]);
                    Block::incrementBlocks($block_id, [
                        'positions' => 1,
                        'images' => 1
                    ]);
                }
            } else {
                Component::addComponents([
                    'block_id' => $block_id,
                    'tag_id' => $request->tag_id,
                    'position_id' => $request->position_id,
                    'number' => $request->number,
                ]);
                if ($request->tag_id == 7){
                    Block::incrementBlocks($block_id, [
                        'positions' => 1,
                        'images' => 1
                    ]);
                } else {
                    Block::incrementBlocks($block_id, [
                        'positions' => 1,
                    ]);
                }
            }
        } else {
            $block_id = $blocks[$request->block]['id'];

            if ($blocks[$request->block]['imageset'] || $request->tag_id == 8){
                $this->err = 'В блок из ряда фотографий нельзя добавлять другие компоненты';
            } elseif ($request->tag_id == 7){ // Если производится попытка загрузить фотографию
                // Если при загрузке фотографии уже есть другие фотографии
                if ($blocks[$request->block]['images']){
                    $this->err = 'В одном блоке не может быть более одной фотографии';
                    // Если при загрузке фотографии уже есть другие компоненты
                } elseif ($blocks[$request->block]['positions']){
                    // Проферяем позиционирование фотографии
                    $pos = PositionController::getPositions([
                        'positions_id' => $request->position_id
                    ]);
                    $position = $pos[$request->position_id]['position'];
                    if ($position === 'photo-center'){
                        $this->err = 'При наличии в блоке других компонент фотография должна располагаться слева либо справа';
                    } else {
                        if ($position === 'photo-left'){
                            $npp = 1;
                            // Вставляем фото первым по счету
                            Component::addComponents([
                                'block_id' => $block_id,
                                'tag_id' => $request->tag_id,
                                'position_id' => $request->position_id,
                                'number' => $npp++,
                            ]);
                            // У остальных компонент увеличиваем номер следования на 1
                            foreach ($blocks[$request->block]['components'] as $key => $val){
                                Component::updateComponents($val['id'], [
                                    'number' => $npp++
                                ]);
                            }
                        } else {
                            $npp = $blocks[$request->block]['positions'] + 1;
                            // Вставляем компонент последним
                            Component::addComponents([
                                'block_id' => $block_id,
                                'tag_id' => $request->tag_id,
                                'position_id' => $request->position_id,
                                'number' => $npp,
                            ]);
                        }
                        Block::where('id', $block_id)
                            ->increment('images', 1, [
                                'image_position' => $position
                            ]);
                        Block::incrementBlocks($block_id, [
                            'positions' => 1,
                        ]);
                    }
                }
            } else {
                $number_matched = false;
                $setup_npp = $request->number;
                // Проверяем на совпадение очередности. Если есть компоненты под таким же номером, сдвигаем остальные
                foreach ($blocks[$request->block]['components'] as $key => $val){
                    if ($request->number == $val['number'] || $number_matched){
                        Component::updateComponents($val['id'], [
                            'number' => ++$setup_npp
                        ]);
                        $number_matched = true;
                    }
                }
                Component::addComponents([
                    'block_id' => $block_id,
                    'tag_id' => $request->tag_id,
                    'position_id' => $request->position_id,
                    'number' => $request->number,
                ]);
                Block::incrementBlocks($block_id, [
                    'positions' => 1
                ]);
            }
        }
        return $this->show($request->razdel_id);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function delete(Request $request)
    {
        $block = self::getBlocks([
            'block_id' => $request->block_id,
        ]);
        if ($block->positions == 1){
            Block::destroy($request->block_id);
        } else {
            $number_change = false;
            // Проверяем на совпадение очередности. Если есть компоненты под таким же номером, сдвигаем остальные
            foreach ($block->components as $key => $val){
                if ($number_change){
                    Component::updateComponents($val['id'], [
                        'number' => $setup_npp++
                    ]);
                }
                if ($val['id'] == $request->component_id){
                    Component::destroy($val['id']);
                    $setup_npp = $val['number'];
                    if ($val['tag_id'] == 7) {
                        Block::decrementBlocks($request->block_id, [
                            'images' => 1
                        ]);
                    }
                    Block::decrementBlocks($request->block_id, [
                        'positions' => 1
                    ]);
                    $number_change = true;
                }
            }
        }
        return $this->show($request->razdel_id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        file_put_contents('/var/www/bondari.com/tests/block_update', print_r($request->all(), true), FILE_APPEND);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $setup_npp = 1;
        $blocks = self::getBlocks([
            'razdel_id' => $request->razdel_id
        ]);
        $number_change = false;
        // Проверяем на совпадение очередности. Если есть компоненты под таким же номером, сдвигаем остальные
        foreach ($blocks as $npp => $block){
            if ($number_change){
                Block::updateBlocks($block['id'], [
                    'npp' => $setup_npp++
                ]);
            }
            if ($block['id'] == $request->block_id){
                Block::destroy($block['id']);
                $setup_npp = $request->npp;
                $number_change = true;
            }
        }
        return $this->show($request->razdel_id);
    }
}

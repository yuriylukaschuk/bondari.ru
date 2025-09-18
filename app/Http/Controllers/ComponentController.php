<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Component;
use App\Models\Block;
use App\Models\ComponentPhoto;

use Illuminate\Http\Request;
use App\Http\Controllers\RazdelController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\TagController;
use Inertia\Inertia;

use App\Traits\RazdelTrait;

class ComponentController extends Controller
{
    use RazdelTrait;

    private string $err = '';
    public string $dashboard_title = 'Заполнение сайта содержимым';
    private string $dashboard = 'components';

    public static function getComponents($params = [])
    {
        $components = [];

        if (!empty($params['id'])){
            return Component::one($params['id']);
        } elseif (!empty($params['block_id'])){
            $result = Block::with([
                    'components.photos',
                    'components.contents',
                    'razdel'
                ])
                ->where('id', $params['block_id'])
                ->where('razdel_id', $params['razdel_id'])
                ->first();
        } else {
            $result = Block::with([
                    'components.photos',
                    'components.contents',
                    'razdel'
                ])
                ->where('razdel_id', $params['razdel_id'])
                ->getContent()
                ->first();
        }
        if (!empty($result)){
            foreach ($result->components as $component) {
                $components[$component->id] = [
                    'id' => $component->id,
                    'number' => $component->number,
                    'tag_id' => $component->tag_id,
                    'tag_class' => $component->tags->class,
                    'position_id' => $component->position_id,
                ];
                if ($component->isPhoto()) {
                    $photo = $component->getContent();
                    if ($photo === null){
                        $components[$component->id]['description'] = '';
                        $components[$component->id]['image_id'] = 0;
                        $components[$component->id]['image'] = '../images/nophoto.png';
                        $components[$component->id]['imagePresent'] = false;
                    } else {
                        $components[$component->id]['description'] = $photo->title;
                        $components[$component->id]['image_id'] = $photo->id;
                        $components[$component->id]['image'] = '/storage/'.$photo->thumbnail;
                        $components[$component->id]['imagePresent'] = true;
                    }
                } elseif ($component->isPhotoSet()){
                    $photoSet = $component->getContent();
                    if (empty($photoSet)){
                        $components[$component->id]['description'] = '';
                        $components[$component->id]['image_id'] = 0;
                        $components[$component->id]['image'] = '../images/nophoto.png';
                        $components[$component->id]['imagePresent'] = false;
                    } else {
                        $components[$component->id]['description'] = $photoSet->title;
                        $components[$component->id]['image_id'] = $photoSet->id;
                        $components[$component->id]['image'] = '/storage/'.$photoSet->thumbnail;
                        $components[$component->id]['imagePresent'] = true;
                    }
                } elseif ($component->isText()) {
                    $text = $component->getContent();
                    $components[$component->id]['text'] = ($text === null) ? '' : $text->content;
                }
            }
        }
        return $components;
    }

    /**
     * Display the specified resource.
     */
    public function show(int $razdel_id = 1, int $block_id = 0)
    {
        if ($block_id){
            $components = self::getComponents([
                'razdel_id' => $razdel_id,
                'block_id' => $block_id,
            ]);
            $photos = PhotoController::getPhotoGallery($razdel_id);
            $tags = TagController::getTags();
            $positions = PositionController::getPositions();
        } else {
            $components = $photos = $tags = $positions = [];
        }

        return Inertia::render('Dashboard', [
            'dashboard' => $this->dashboard,
            'dashboard_title' => $this->dashboard_title,
            'razdel_id' => $razdel_id,
            'razdels' => $this->getRazdelsList($razdel_id),
            'block_id' => $block_id,
            'blocks' => BlockController::getBlocks([
                'withoutComponenets' => true,
                'razdel_id' => $razdel_id,
            ]),
            'tags' => $tags,
            'positions' => $positions,
            'components' => $components,
            'photos' => $photos,
            'err' => $this->err,
        ]);
    }

    /**
     * Показать форму создания нового ресурса
     */
    public function create(Request $request)
    {
        $razdel_id = $request->filled('razdel_id') ? $request->razdel_id : 1;
        $block_id = $request->filled('block_id') ? $request->block_id : 0;
        return $this->show($razdel_id, $block_id);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->component_id && $request->photo_id){
            $componentPhoto = ComponentPhoto::where('component_id', $request->component_id)
                ->where('photo_id', $request->image_id)
                ->first();
            if ($componentPhoto) {
                $componentPhoto->where('component_id', $request->component_id)
                ->where('photo_id', $request->image_id)
                ->update(['photo_id' => $request->photo_id]);
            } else {
                ComponentPhoto::create([
                    'component_id' => $request->component_id,
                    'photo_id' => $request->photo_id
                ]);
            }
        }
        return $this->show($request->razdel_id, $request->block_id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        foreach ($request->components as $component_id => $component){
            if (empty($component['imagePresent'])){
                $componentContent = Content::where('component_id', $component_id)->first();
                $content = $this->processTextareaContent($component['text']);
                if ($componentContent) {
                    // Обработка текста из textarea
                    $componentContent->where('component_id', $component_id)
                    ->update(['content' => $content]);
                } elseif (!empty($component['text'])) {
                    Content::create([
                        'component_id' => $component_id,
                        'content' => $content
                    ]);
                }
            }
        }
        return $this->show($request->razdel_id, $request->block_id);
    }

    /**
     * Обработка содержимого textarea
     * Разбивает текст на предложения по переносам строк
     */
    private function processTextareaContent(array $contents): array
    {
        // Фильтруем пустые строки и убираем пробелы
        $sentences = array_filter(array_map('trim', $contents), function($content) {
            return !empty($content);
        });
        
        // Сбрасываем ключи массива (чтобы был числовой индекс)
        return array_values($sentences);
    }

}

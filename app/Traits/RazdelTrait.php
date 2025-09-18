<?php

namespace App\Traits;
use App\Models\Razdel;

trait RazdelTrait
{
    private $razdels = [];

    private function buildTree($data, $parentId = 0)
    {
        $tree = [];
        foreach ($data as $item) {
            if ($item['parent_id'] == $parentId) {
                $item['children'] = $this->buildTree($data, $item['id']);
                $tree[] = $item;
            }
        }
        return $tree;
    }
    
    private function getMenuItems(){
        $this->razdels = Razdel::where('lvl','>',0)
            ->orderBy('parent_id')
            ->orderBy('lvl')
            ->orderBy('npp')
            ->get()
            ->keyBy('id');
        return $this->buildTree($this->razdels);
    }

    public function getRazdelsList()
    {
        $this->razdels = Razdel::orderBy('parent_id')
            ->orderBy('lvl')
            ->orderBy('npp')
            ->get()
            ->keyBy('id');
        return $this->buildTree($this->razdels);
    }

    protected function getPageData($url)
    {
        $blocks = [];
        $razdel = Razdel::with([
            'blocks' => function($query) {
                $query->with([
                    'components' => function($query) {
                        $query->with(['contents', 'photos', 'tags', 'positions']);
                    }
                ]);
            }
        ])
        ->where('url', $url)
        ->first();

        foreach ($razdel->blocks as $block) {
            $blocks[$block->id] = [
                'npp' => $block->npp,
                'images' => $block->images,
                'imageset' => $block->imageset,
                'image_position' => $block->image_position,
                'positions' => $block->positions,
                'components' => [],
            ];
            foreach ($block->components as $component) {
                $blocks[$block->id]['components'][$component->id] = [
                    'tag_id' => $component->tag_id,
                    'class' => $component->tags->class,
                    'position_id' => $component->position_id,
                    'position_name' => $component->positions->name,
                    'position' => $component->positions->position,
                    'number_id' => $component->number,
                ];
                if ($block->images){
                    foreach ($component->photos as $photo) {
                        $blocks[$block->id]['components'][$component->id]['photo'] = [
                            'id' => $photo->id,
                            'filename' => '/storage/'.$photo->filename,
                            'thumbnail' => '/storage/'.$photo->thumbnail,
                            'title' => $photo->title,
                            'description' => $photo->description,
                        ];
                    }
                }
                if (!empty($component->contents->content)){
                    $blocks[$block->id]['components'][$component->id]['content'] = $component->contents->content;
                }
            }
        }

        return (object)[
            'title' => $razdel->title,
            'description' => $razdel->description,
            'keywords' => $razdel->keywords,
            'url' => $razdel->url,
            'feedback' => $razdel->feedback,
            'feedback_title' => $razdel->feedback_title,
            'blocks' => $blocks
        ];
    }
}

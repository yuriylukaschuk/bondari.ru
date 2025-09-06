<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Traits\RazdelTrait;

class OutputController extends Controller
{
    use RazdelTrait;

    private function show($url)
    {
        $page = ($url === 'welcome') ? 'Welcome' : 'Bondari';
        $pageData = $this->getPageData($url);

        return Inertia::render($page, [
            'blocks' => $pageData->blocks,
            'menuItems' => $this->getMenuItems(),
            'feedback' => [
                'present' => $pageData->feedback,
                'title' => $pageData->feedback_title
            ],
            'meta' => [
                'title' => $pageData->title,
                'keywords' => $pageData->keywords,
                'description' => $pageData->description,
            ],
        ]);
    }

    public function index()
    {
        return $this->show('welcome');
    }

    public function priceList()
    {
        return $this->show('price-list');
    }

    public function outerClient()
    {
        return $this->show('outer-client');
    }

    public function reviews()
    {
        return $this->show('reviews');
    }

    public function production($slug)
    {
        return $this->show('production/'.$slug);
    }

    public function directory($slug)
    {
        return $this->show('directory/'.$slug);
    }

    public function feedbackSend(Request $request)
    {
    }
}

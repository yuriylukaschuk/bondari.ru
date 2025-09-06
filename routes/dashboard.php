<?php

use App\Http\Controllers\RazdelController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\ComponentController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/**
 * Работа с разделами
 */
Route::prefix('dashboard')->
    middleware(['auth', 'verified', 'permission:dashboard-login'])->group(function () {

    /* ---------------------------------- Разделы ----------------------------------*/
    /**
     * Показать все разделы уровня
     */
    Route::get('/', [
        RazdelController::class, 'index'
    ])->name('razdels.list');

    Route::get('/razdels/{page?}', [
        RazdelController::class, 'index'
    ])->name('razdels.list');

    Route::get('/razdels', [
        RazdelController::class, 'index'
    ])->name('razdels.list');

    /**
     * Добавить новый раздел
     */
    Route::put('/razdels/{parent_id}/{lvl}', [
        RazdelController::class, 'store'
    ])->middleware(['permission:dashboard-changes'])->name('razdels.store');

    /**
     * Сохранить изменения в разделах уровня
     */
    Route::post('/razdels', [
        RazdelController::class, 'update'
    ])->middleware(['permission:dashboard-changes'])->name('razdels.update');


    /* ---------------------------------- Фотографии ----------------------------------*/
    /**
     * Показать все загруженные фотографии
     */
    Route::get('photos/{page?}', [
        PhotoController::class,'index'
        ])->name('photos.index');

    /**
     * Загрузить фотографию на сайт
     */
    Route::post('photos/upload', [
        PhotoController::class,'upload'
    ])->middleware(['role:web-developer', 'permission:dashboard-changes'])->name('photos.upload');

    /**
     * Сохранить изменения при редактировании
     */
    Route::put('photos', [
        PhotoController::class,'update'
    ])->middleware(['role:web-developer', 'permission:dashboard-changes'])->name('photos.update');

    /**
     * Создать фотографию
     */
    Route::get('photos/create', [
        PhotoController::class,'create'
    ])->middleware(['role:web-developer', 'permission:dashboard-changes'])->name('photos.create');

    /* ---------------------------------- Компоненты страницы ----------------------------------*/
    /**
     * Показать все элементы
     */
    Route::get('tags/{page?}', [
        TagController::class,'index'
        ])->name('tags.index');

    /**
     * Добавить новый элемент
     */
    Route::put('tags', [
        TagController::class,'create'
    ])->middleware(['role:web-developer', 'permission:dashboard-changes'])->name('tags.create');

    /**
     * Сохранить изменения при редактировании
     */
    Route::post('tags', [
        TagController::class,'update'
    ])->middleware(['role:web-developer', 'permission:dashboard-changes'])->name('tags.update');

    /**
     * Удалить элемент
     */
    Route::delete('tags/{id?}', [
        TagController::class,'destroy'
    ])->middleware(['role:web-developer', 'permission:dashboard-changes'])->name('tags.destroy');

    /* ---------------------------------- Страницы сайта ----------------------------------*/
    /**
     * Показать все блоки страницы (раздела)
     */
    Route::get('blocks/{razdel_id?}', [
        BlockController::class,'index'
        ])->name('blocks.index');

    /**
     * Создать новый блок
     */
    Route::post('blocks/create', [
        BlockController::class,'create'
    ])->middleware(['role:web-developer', 'permission:dashboard-changes'])->name('blocks.create');

    /**
     * Удалить блок
     */
    Route::post('blocks/{block_id?}', [
        BlockController::class,'destroy'
    ])->middleware(['role:web-developer', 'permission:dashboard-changes'])->name('blocks.destroy');

    /**
     * Удалить компонент из блока
     */
    Route::put('blocks/{component_id?}', [
        BlockController::class,'delete'
    ])->middleware(['role:web-developer', 'permission:dashboard-changes'])->name('components.delete');

    /**
     * Изменить порядковый номер компонента в блоке
     */
    Route::post('blocks/{component_id?}', [
        BlockController::class,'update'
    ])->middleware(['role:web-developer', 'permission:dashboard-changes'])->name('blocks.update');

    /* ---------------------------------- Заполнение страницы ----------------------------------*/

    /**
     * Показать содержимое блока в выбранном разделе
     */
    Route::get('components/{razdel_id?}/{block_id?}', [
        ComponentController::class,'create'
        ])->name('components.index');

    /**
     * Сохранить изменения в выбранном фото
     */
    Route::post('components', [
        ComponentController::class,'store'
    ])->middleware(['role:web-developer', 'permission:dashboard-changes'])->name('components.store');

    /**
     * Сохранить изменения при редактировании
     */
    Route::post('components/{razdel_id}/{block_id}', [
        ComponentController::class,'update'
    ])->middleware(['role:web-developer', 'permission:dashboard-changes'])->name('components.update');

    /**
     * Удалить запись
     */
    Route::post('components/{id?}', [
        ComponentController::class,'destroy'
    ])->middleware(['role:web-developer', 'permission:dashboard-changes'])->name('components.destroy');

});

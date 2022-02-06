<?php

declare(strict_types=1);

use App\Http\Controllers\ScraperIndex;
use App\Http\Controllers\ScraperStore;
use App\Http\Controllers\ScraperUpdatePost;
use Illuminate\Support\Facades\Route;

Route::get('/scrape', ScraperIndex::class);
Route::post('/scrape', ScraperStore::class);
Route::patch('/scrape/post/{id}', ScraperUpdatePost::class);

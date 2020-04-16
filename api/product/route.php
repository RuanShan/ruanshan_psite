<?php

use think\facade\Route;

Route::resource('product/categories', 'product/Categories');
Route::get('product/categories/subCategories', 'product/Categories/subCategories');
Route::resource('product/items', 'product/Items');
Route::resource('product/pages', 'product/Pages');
Route::resource('product/userItems', 'product/UserItems');

Route::get('product/search', 'product/Items/search');
Route::get('product/items/my', 'product/Items/my');
Route::get('product/items/relatedItems', 'product/Items/relatedItems');
Route::post('product/items/doLike', 'product/Items/doLike');
Route::post('product/items/cancelLike', 'product/Items/cancelLike');
Route::post('product/items/doFavorite', 'product/Items/doFavorite');
Route::post('product/items/cancelFavorite', 'product/Items/cancelFavorite');
Route::get('product/tags/:id/items', 'product/Tags/items');
Route::get('product/tags', 'product/Tags/index');
Route::get('product/tags/hotTags', 'product/Tags/hotTags');

Route::post('product/userItems/deletes', 'product/UserItems/deletes');
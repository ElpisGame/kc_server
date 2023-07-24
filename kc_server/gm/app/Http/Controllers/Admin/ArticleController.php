<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Requests\ArticleRequest;
use App\Http\Controllers\Admin\Resources\ArticleResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Article;

class ArticleController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['role:超级管理员|管理员']);
    }

    public function index(ArticleRequest $request)
    {
        $query = Article::query();
        #search
        $id = $request->input('id');
        if (isset($id)) {
            $query->where("id", $id);
        }

        $account_id = $request->input('account_id');
        if (isset($account_id)) {
            $query->where("account_id", $account_id);
        }

        $category = $request->input('category');
        if (isset($category)) {
            $query->where("category", 'like', "%{$category}%");
        }

        $title = $request->input('title');
        if (isset($title)) {
            $query->where("title", 'like', "%{$title}%");
        }

        $content = $request->input('content');
        if (isset($content)) {
            $query->where("content", $content);
        }

        $updated_at = $request->input('updated_at');
        if (isset($updated_at) && count($updated_at) == 2) {
            $query->whereBetween("updated_at", [$updated_at[0], $updated_at[1]]);
        }

        $created_at = $request->input('created_at');
        if (isset($created_at) && count($created_at) == 2) {
            $query->whereBetween("created_at", [$created_at[0], $created_at[1]]);
        }

        $deleted_at = $request->input('deleted_at');
        if (isset($deleted_at) && count($deleted_at) == 2) {
            $query->whereBetween("deleted_at", [$deleted_at[0], $deleted_at[1]]);
        }


        #pagination
        $perPage = $request->input('perPage', 10);
        $currentPage = $request->input('currentPage', 1);
        $query->orderBy("id", "desc");
        $query->with("account");
        $articles = $query->paginate($perPage, $columns = ['*'], $pageName = 'page', $page = $currentPage);
        #return
        return ArticleResource::collection($articles);
    }

    public function store(ArticleRequest $request)
    {
        $fields = $request->all();
        $fields['account_id'] = auth('admin')->id();
        $article = Article::create($fields);
        return new ArticleResource($article);
    }

    public function show($id)
    {
        $article = Article::findOrFail($id);
        $article->with("account");
        return new ArticleResource($article);
    }

    public function update(ArticleRequest $request, $id)
    {
        $article = Article::findOrFail($id);
        $fields = $request->all();
        $fields['account_id'] = auth('admin')->id();
        $article->update($fields);
        return new ArticleResource($article);
    }

    public function destroy($id)
    {
        Article::destroy($id);
        return new JsonResource(null);
    }
}

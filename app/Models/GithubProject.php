<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//GithubProject
class GithubProject extends Model
{
    protected $table = 'github_project';

    protected $fillable = [
        'id',
        'query_id',
        'project_id',
        'name',
        'author',
        'stargazers',
        'watchers'
    ];
}

<?php

namespace App\Services;

use App\Models\GithubProject;
use App\Models\Responses\ProjectSearchResponse;
use App\Models\Search;
use App\Services\Interfaces\IGitWrapper;

class GitService implements Interfaces\IGitService
{
    public IGitWrapper $gitWrapper;

    /**
     * @param IGitWrapper $gitWrapper
     */
    public function __construct(IGitWrapper $gitWrapper)
    {
        $this->gitWrapper = $gitWrapper;
    }


    /**
     * @param $query
     * @param $perPage
     * @param $page
     */
    public function getProjects($query, $perPage, $page)
    {
        $projectSearchResponse = new ProjectSearchResponse();
        if ($query) {
            $id = $this->addOrUpdateProjectsFromQuery($query, $perPage, $page);
            $queryEntity = Search::query()->where('id',  $id)->first();
            $items_count = GithubProject::query()->where('search_id', $id)->count();
            $total_count = $queryEntity->total_count;

            if ($items_count < $perPage * $page && $total_count > $perPage * $page) {
                for ($i = intdiv($items_count,$perPage); $i <= $page; $i++) {
                    $this->addOrUpdateProjectsFromQuery($query, $perPage, $i);
                }
            }
            $items = GithubProject::query()->where('search_id', $id)->skip($perPage * ($page - 1))->take($perPage)->get();
            $projectSearchResponse->items = $items;
            $projectSearchResponse->totalCount = $total_count;
            $remain = $total_count % $perPage;
            $projectSearchResponse->totalPages = $remain > 0 ? intdiv($total_count,$perPage) + 1 : $remain;
            $projectSearchResponse->currentPage = $page;
        }

        return $projectSearchResponse;
    }

    public function delete($query)
    {
        $search_data = Search::query()->where('id', $query['id'])->first();
        if (isset($search_data->id)) {
            GithubProject::query()->where('search_id',  $search_data->id)->delete();
            Search::query()->where('id',  $search_data->id)->delete();
        }
    }

    /**
     * @param $query
     * @param $perPage
     * @param int $page
     * @return mixed
     */
    public function addOrUpdateProjectsFromQuery($query, $perPage, $page)
    {
        $id = Search::query()->where('query', $query)->value('id');

        if (!$id) {
            $id = Search::query()->insertGetId([
                'query' => $query,
                'total_count' => 0,
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time())
            ]);
        }

        $projectItems = json_decode($this->gitWrapper->getProjects($query, $perPage, $page));

        Search::query()->where('id', $id)->update(['total_count' => $projectItems->total_count]);

        foreach ($projectItems->items as $item) {
            GithubProject::query()->updateOrInsert(['project_id' => $item->id], [
                'search_id' => $id,
                'project_id' => $item->id,
                'name' => $item->name,
                'author' => $item->owner->login,
                'stargazers' => $item->stargazers_count,
                'watchers' => $item->watchers_count,
                'html_url' => $item->html_url,
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time())
            ]);
        }
        return $id;
    }
}

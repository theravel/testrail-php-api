<?php
/**
 * Created by PhpStorm.
 * User: aappen
 * Date: 04.10.18
 * Time: 11:27
 */

namespace seretos\testrail\api;


class Plans extends AbstractApi
{
    private $cache = null;

    public function all(int $projectId)
    {
        if($this->cache === null) {
            $this->cache = $this->connector->send_get('get_plans/'.$this->encodePath($projectId));
        }
        return $this->cache;
    }

    public function get(int $planId)
    {
        return $this->connector->send_get('get_plan/' . $this->encodePath($planId));
    }

    public function findByName(int $projectId, string $name)
    {
        $allPlans = $this->all($projectId);
        foreach ($allPlans as $plan) {
            if ($plan['name'] === $name) {
                return $plan;
            }
        }
        return [];
    }

    public function create(int $projectId, string $name, string $description = null, int $milestoneId = null)
    {
        $plan = $this->connector->send_post('add_plan/'.$this->encodePath($projectId),
            ['name' => $name,
                'description' => $description,
                'milestone_id' => $milestoneId]);
        $this->cache = null;
        return $plan;
    }

    public function createEntry(int $planId, int $suiteId, string $name, array $configIds = [], array $runs = [], string $description = null, bool $all = true, array $cases = []){
        $entry = $this->connector->send_post('add_plan_entry/'.$this->encodePath($planId),
            ['suite_id' => $suiteId,'name' => $name, 'description' => $description, 'include_all' => $all, 'case_ids' => $cases, 'config_ids' => $configIds, 'runs' => $runs]);
        $this->cache = null;
        return $entry;
    }

    /**
     * @param int $planId
     * @param array $parameters {
     *      @var string       $name
     *      @var string       $description
     *      @var int        $milestone_id
     * }
     */
    public function update(int $planId, array $parameters = []){
        $plan = $this->connector->send_post('update_plan/'.$this->encodePath($planId),$parameters);
        $this->cache = null;
        return $plan;
    }

    public function updateEntry(int $planId, int $entryId, array $parameters = []){
        $entry = $this->connector->send_post('update_plan_entry/'.$this->encodePath($planId).'/'.$this->encodePath($entryId),$parameters);
        $this->cache = null;
        return $entry;
    }

    public function delete(int $planId){
        $this->connector->send_post('delete_plan/'.$this->encodePath($planId),[]);
        $this->cache = null;
    }

    public function deleteEntry(int $planId, int $entryId){
        $this->connector->send_post('delete_plan_entry/'.$this->encodePath($planId).'/'.$this->encodePath($entryId),[]);
        $this->cache = null;
    }

    public function close(int $planId){
        $plan = $this->connector->send_post('close_plan/'.$this->encodePath($planId),[]);
        $this->cache = null;
        return $plan;
    }
}
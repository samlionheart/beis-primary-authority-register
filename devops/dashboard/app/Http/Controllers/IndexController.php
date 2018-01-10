<?php

namespace App\Http\Controllers;
use Cache;

class IndexController extends Controller
{
    private $services;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        \App\Services\CloudFoundryStatsService $cloudFoundryStatsService,
        \App\Services\TestResultsService $testResultsService,
        \App\Services\UptimeRobotStatsService $uptimeRobotStatsService,
        \App\Services\BuildVersionService $buildVersionService,
        \App\Services\TravisStatsService $travisStatsService,
        \App\Services\GitHubStatsService $gitHubStatsService
    )
    {
        $this->services['uptime'] = $uptimeRobotStatsService;
        $this->services['tests'] = $testResultsService;
        $this->services['cf'] = $cloudFoundryStatsService;
        $this->services['travis'] = $travisStatsService;
        $this->services['github'] = $gitHubStatsService;
        $this->services['build_versions'] = $buildVersionService;
    }
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('welcome')->withCloudFoundryAppsToDisplay(4);
    }

    public function cloudFoundryStats()
    {
        return Cache::remember('cf', 1, function () {
            return $this->services['cf']->stats();
        });
    }

    public function testStats()
    {
        return Cache::remember('travis_tests', 120, function () {
            return $this->services['trs']->stats();
        });
    }

    public function uptimeStats()
    {
        return Cache::remember('uptime', 1, function () {
            return $this->services['uptime']->stats();
        });
    }

    public function travisStats()
    {
        return Cache::remember('travis', 1, function () {
            return $this->services['travis']->stats();
        });
    }

    public function gitHubStats()
    {
        return Cache::remember('github', 1, function () {
            return $this->services['github']->stats();
        });
    }

    public function buildVersionStats()
    {
        return Cache::remember('build_versions', 1, function () {
            return $this->services['build_versions']->stats();
        });
    }
}


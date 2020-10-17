<?php

class Collection
{

    public static function getFootballs($dbc)
    {
        $dbc->where('is_visible = 1');
        $dbc->orderBy("rank", "ASC");

        $rows = $dbc->get('cn_season_f');

        $results = array();
        $obj = new cn_season_f($dbc);
        foreach ($rows as $v) {
            if ($obj->load($v['id'])) {
                $results[] = clone $obj;
            }
        }
        return $results;
    }

    public static function getSports($dbc, $category_id = false, $limit = 20)
    {
        $dbc->where('is_visible = 1');
        if (!empty($category_id)) $dbc->where('category_id = ' . $category_id);
        $dbc->orderBy("created_at", "desc");

        if ($limit)
            $rows = $dbc->get('cn_sports', $limit);
        else
            $rows = $dbc->get('cn_sports');

        $results = array();
        $obj = new cn_sports($dbc);
        foreach ($rows as $v) {
            if ($obj->load($v['id'])) {
                $results[] = clone $obj;
            }
        }
        return $results;
    }

    public static function getNews($dbc, $limit = false)
    {
        $dbc->where('is_visible = 1');
        $dbc->orderBy("created_at", "desc");

        if ($limit)
            $rows = $dbc->get('cn_news', $limit);
        else
            $rows = $dbc->get('cn_news');

        $results = array();
        $obj = new cn_news($dbc);
        foreach ($rows as $v) {
            if ($obj->load($v['id'])) {
                $results[] = clone $obj;
            }
        }
        return $results;
    }

    public static function getBanners($dbc, $params)
    {
        $dbc->where('is_visible = 1');
        $dbc->orderBy("show_date", "desc");

        $block = 0;
        
        if(array_key_exists('id', $params)){
            $block = $params['id'];
        }
        
        $dbc->where('block', $block);

        $rows = $dbc->get('cn_banners');

        $results = array();
        $obj = new cn_banners($dbc);
        foreach ($rows as $v) {
            if ($obj->load($v['id'])) {
                $results[] = clone $obj;
            }
        }
        return $results;
    }

    public static function getSportsCategories($dbc, $rank_desc = false)
    {
        $dbc->where('is_visible = 1');
        if ($rank_desc) $dbc->orderBy("rank", "desc");
        $rows = $dbc->get('cn_sports_categories');

        $results = array();
        $obj = new cn_sports_categories($dbc);
        foreach ($rows as $v) {
            if ($obj->load($v['id'])) {
                $results[] = clone $obj;
            }
        }
        return $results;
    }

    public static function getNewsCategories($dbc, $rank_desc = true)
    {
        $dbc->where('is_visible = 1');
        if ($rank_desc) $dbc->orderBy("`rank`", "desc");
        $rows = $dbc->get('cn_sports_categories');

        $results = array();
        $obj = new cn_news_categories($dbc);
        foreach ($rows as $v) {
            if ($obj->load($v['id'])) {
                $results[] = clone $obj;
            }
        }
        return $results;
    }
    
    public static function getShooters($dbc, $params)
    {
        if(array_key_exists('season_id', $params)){
            $dbc->where('season_id', $params['season_id']);
            $dbc->orderBy('api_id', 'desc');
            $season_fd_row = $dbc->getOne('cn_season_fd');
            
            $dbc->where('season_fdid', $season_fd_row['api_id']);
        }else if(array_key_exists('season_fd_id', $params)) {
            $dbc->where('season_fdid', $params['season_fd_id']);
        }
        
        $dbc->where('is_visible', 1);
        $dbc->orderBy('`rating`', 'desc');
                 
        $limit = null;
        $start_count = 0;
        if(array_key_exists('limit', $params)) { 
            $limit = $params['limit'];
            if(array_key_exists('page_number', $params)){
                $pageNumber = $params['page_number'] ? $params['page_number'] : 1;
                $pageLimit = $limit;
                $limit = array(($pageNumber - 1) * $pageLimit, $pageLimit);
                $start_count = ($pageNumber - 1) * $pageLimit;
            }
        }
        
        $copyDb = $dbc->copy();
        $rows = $dbc->get('cn_shooter', $limit);
        
        $totalRecords = $copyDb->getValue("cn_shooter", "count(id)");
 
        foreach($rows as $shooter){
            $item = array();
            
            foreach($shooter as $key => $value){
                $item[$key] = $value;
            }

            $team = self::getTeamById($dbc, $shooter['team_id']);

            $item['team_logo'] = $team->getLogo();
            $item['team_name_en'] = $team->getNameEn();
            $item['team_name_zh'] = $team->getNameZh();
            
            $start_count++;
            $item['sort'] = $start_count;
            
            $results['shooters'][] = $item;
        }
        
        if(array_key_exists('page_number', $params)){
            $results['totalPage']              = ceil($totalRecords / $limit[1]);
            $results['pageNumber']             = $pageNumber;
            $results['totalRecord']            = $totalRecords;
            $results['numRecord']              = $pageLimit;
            $results['fromPage']               = $pageNumber > 1?($pageNumber - 1)*$pageLimit:"1";
            $results['toPage']                 = ($pageNumber *$pageLimit) > $totalRecords?$totalRecords:($pageNumber *$pageLimit);
        }
        return $results;
    }
    
    public static function getRankings($dbc, $params)
    {
        if(array_key_exists('season_id', $params)){
            $dbc->where('is_visible', '1');
            $dbc->where('season_id', $params['season_id']);
            $dbc->orderBy('api_id', 'desc');
            $season_fd_row = $dbc->getOne('cn_season_fd');
            
            $dbc->where('season_fd_id', $season_fd_row['api_id']);
        }else if(array_key_exists('season_fd_id', $params)) {
            $dbc->where('season_fd_id', $params['season_fd_id']);
        }
        
        $dbc->where('is_visible', 1);
        $dbc->orderBy('position', 'asc');
                 
        $limit = null;
        if(array_key_exists('limit', $params)) { 
            $limit = $params['limit'];
        }
        
        $group = array();
        
        $ranking_rows = $dbc->get('cn_ranking', $limit, array('id', 'position', 'won', 'draw', 'loss', 'total', 'points', 'goals', 'goal_diff', 'goals_against', 'team_id', 'group_id'));
        foreach($ranking_rows as $ranking){
            foreach($ranking as $key => $value){
                $item[$key] = $value;
                
                if($key == 'team_id'){
                    $team = self::getTeamById($dbc, $value);

                    $item['team_logo'] = $team->getLogo();
                    $item['team_name_en'] = $team->getNameEn();
                    $item['team_name_zh'] = $team->getNameZh();
                }
            }

            $group[$ranking['group_id']][] = $item;
        }
        
        $dbc->where('api_id', $params['season_id']);
        $season_rule = $dbc->getValue('cn_season_f', 'rule');
        
        $results['rankings'] = $group;
        $results['rule'] = $season_rule;
        
        return $results;
    }
    
    public static function getSchedule($dbc, $params)
    {
        if($params['season_id'] > 0){
            $dbc->where('competition_id', $params['season_id']);
        }
        
        if(array_key_exists('round', $params)){
            if($params['round'] > 0){
                $dbc->where('round', $params['round']);
            }
        }
        
        if(array_key_exists('month', $params)){
            if($params['month'] > 0){
                $dbc->where('MONTH(FROM_UNIXTIME(match_time))', $params['month']);
            }

        }
        
        $dbc->where('is_visible', 1);
        $dbc->orderBy('match_time', 'asc');
                 
        $limit = null;
        if(array_key_exists('limit', $params)) { 
            if($params['limit'] > 0){
                $limit = $params['limit'];
            }
        }
        $rows = $dbc->get('cn_contest_f', $limit);
        
        $results = array();
        $obj = new cn_contest_f($dbc);
        foreach ($rows as $v) {
            if ($obj->load($v)) {
                $results[] = clone $obj;
            }
        }
        return $results;
    }
    
    public static function getSeason($dbc){
        $dbc->where('is_visible', '1');
        $dbc->orderBy("`rank`", 'asc');
        $season_rows = $dbc->get('cn_season_f', null, array('id', 'api_id', 'name_en', 'name_zh', 'title'));
        foreach($season_rows as $season){
            foreach($season as $key => $value){
                $item[$key] = $value;
            }

            $results['season'][] = $item;
        }
        
        return $results;
    }

    
    public static function getSeasonOptions($dbc, $params)
    {
        if(array_key_exists('season_id', $params)){
            $dbc->where('competition_id', $params['season_id']);
        }
        
        $indicator = '轮次';
        
        $dbc->where('is_visible', 1);
        $total_rounds = $dbc->getValue('cn_contest_f', 'MAX(`round`)');
        if($total_rounds > 0){
            for($i = 1; $i <= $total_rounds; $i++){
                $option['display'] = '第'.$i.'轮';
                $option['round'] = $i;
                $options[] = $option;
            }
        }else{
            $indicator = '日期';
            $months = $dbc->rawQuery('SELECT MONTH(FROM_UNIXTIME(match_time)) as month FROM cn_contest_f WHERE competition_id = '.$params['season_id'].' GROUP BY month ORDER BY match_time ASC');
            foreach($months as $month){
                $option['display'] =  getMonthStringZh($month['month']);
                $option['month'] = $month['month'];
                $options[] =$option;
            }
        }

        $dbc->where('api_id', $params['season_id']);
        $season_detail = $dbc->getOne('cn_season_f');
        
        $results['options'] = $options;
        $results['season_detail'] = $season_detail;
        $results['indicator'] = $indicator;
        
        return $results;
    }
    
    public static function getTeamById($dbc, $id)
    {
        $obj = new cn_team($dbc);
        $obj->load($id);
        
        return $obj;
    }

}
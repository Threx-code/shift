<?php

namespace App\Transformers;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class ListTransformer
{
    /**
     * @param $listData
     * @param $listKeywords
     * @param $inProgress
     * @param $uncuratedKeywords
     * @return array
     */
    public function transform($listData, $listKeywords, $inProgress, $uncuratedKeywords): array
    {
        $result = [];
        foreach($listData as $listKey => $list) {
            $result[$listKey]['list_id'] = $list['id'];
            $result[$listKey]['name'] = $list['name'];

            $result[$listKey]['total_keyword'] = 0;
            $result[$listKey]['total_keyword_processing'] = 0;
            if (!empty($listKeywords)) {
                if (in_array($list['id'], array_column($listKeywords, 'list_id'), true)) {
                    $useKey = array_keys(array_column($listKeywords, 'list_id'), $list['id']);
                    $result[$listKey]['total_keyword'] = (int) ($listKeywords[$useKey[0]]['count']);
                    $result[$listKey]['total_keyword_processing'] = (int) ($listKeywords[$useKey[0]]['count_processed']);
                }
            }

            $result[$listKey]['in_progress'] = false;
            $result[$listKey]['in_progress_actions'] = ['action' => 'import', 'action_counter' => 0];
            if (!empty($inProgress[0])) {
                if (in_array($list['id'], array_column($inProgress, 'list_id'), true)) {
                    $useKey = array_keys(array_column($inProgress, 'list_id'), $list['id']);
                    $result[$listKey]['in_progress'] = (bool)($inProgress[$useKey[0]]['in_progress'] ?? 0);
                    $result[$listKey]['in_progress_actions'] = ['action' => 'import', 'action_counter' => (int) ($inProgress[$useKey[0]]['in_progress'] ?? 0)];
                    $result[$listKey]['total_keyword'] += $inProgress[$useKey[0]]['keywords_in_progress'];
                }
            }

            $result[$listKey]['total_uncurated_keywords'] = 0;
            if (!empty($uncuratedKeywords[0])) {
                if (in_array($list['id'], array_column($uncuratedKeywords, 'list_id'), true)) {
                    $useKey = array_keys(array_column($uncuratedKeywords, 'list_id'), $list['id']);
                    $result[$listKey]['total_uncurated_keywords'] =(int) ($uncuratedKeywords[$useKey[0]]['count']);
                }
            }
        }
        return $result;
    }
}

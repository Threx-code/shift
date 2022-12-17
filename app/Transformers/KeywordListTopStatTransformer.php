<?php

namespace App\Transformers;

use Carbon\Carbon;
use Illuminate\Support\Str;
use JsonException;

class KeywordListTopStatTransformer
{

    public function transform($dataArray)
    {
        return [
            'search_data'       => $this->searchData($dataArray['search_data']),
            'serp_data'         => $this->serpData($dataArray['serp_data']),
            'visibility_data'  => $this->visibilityData($dataArray['visibility_data']),
        ];
    }

    /**
     * @param $serpData
     * @return array
     */
    protected function serpData($serpData): array
    {
        $serpData = json_decode($serpData, true);
        $serpFeatures = str_replace(',}', '', $serpData['serp_features']) . '}';

        return [
            'cpc'           => $serpData['cpc'],
            'sum_of_ctr'    => $serpData['sum_of_ctr'],
            'serp_features' => json_decode($serpFeatures, true)
        ];
    }

    /**
     * @param $dataArray
     * @return array
     * @throws JsonException
     */
    protected function searchData($dataArray): array
    {
        $dataArray= json_decode($dataArray, true);
        $searchData = $dataArray['top_stats'];
        $trends = json_decode($dataArray['trend'], true);
        $svPonderated = json_decode($dataArray['search_volume_ponderated'], true);

        $currentYear = $wholeDate = $previousYear = $currentCv = $currentMonth = $oneYearAgo = $recentDate = $svOneYearAgo= null;
        if(!empty($trends)) {
            $thisYear = max($trends);
            $currentYear = $thisYear['year'];
            $currentMonth = $thisYear['month'];
            $currentCv = $thisYear['sv'];
            $wholeDate = $thisYear['year'] . '-' . Str::padLeft($thisYear['month'], 2, 0) . '-01';
            $lastYear = min($trends);
            $previousYear = $lastYear['year'];
            $svOneYearAgo = $lastYear['sv'];
        }
        $searchTrends   = collect();
        $showLatestTrend = false;
        $firstSvPonderated = $svPonderated[0] ?? null;
        foreach ($trends as $key => $trend) {
            if ($key == 0 && $firstSvPonderated) {
                $showLatestTrend = ($firstSvPonderated->month == $trend->month && $firstSvPonderated->year == $trend->year);
            }

            if ($showLatestTrend && $key <= 12) {
                $searchTrends->push((object) [
                    'date'  => $trend['year'].'-'.Str::padLeft($trend['month'], 2, 0).'-01',
                    'value' => $trend['sv']
                ]);
            } else if (!$showLatestTrend && $key > 0) {
                $searchTrends->push((object)[
                    'date' => $trend['year'] . '-' . Str::padLeft($trend['month'], 2, 0) . '-01',
                    'value' => $trend['sv']
                ]);
            }
        }

        $newDate = json_decode(json_encode($searchTrends, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
        if (!empty($newDate)) {
            $oneYearAgo = min(array_column($newDate, 'date'));
            $recentDate = max(array_column($newDate, 'date'));
        }
        $yoy = $searchData['yoy'] ? $searchData['yoy_sign'].$searchData['yoy'] : 0;

        return [
            'search_volume' => $searchData['search_volume'],
            'crawled_on'   => Carbon::parse($searchData['crawled_on'])->format('Y-m-d'),
            'last_update'  => $wholeDate,
            'year_over_year' => (object) [
                'value'         => $yoy,
                'this_year'     => $currentYear,
                'previous_year' => $previousYear,
                'month' => $currentMonth,
                'one_year_ago_date' => $oneYearAgo,
                'recent_date' => $recentDate,
                'month_start' => self::nameOfMonth($currentMonth),
                'sv_one_year_ago' => (int)$svOneYearAgo,
                'sv_recent_month' => (int)$currentCv,
                'avg_percent_diff' =>  round($searchData['percentage_diff'], 0, PHP_ROUND_HALF_EVEN),
            ],
            'search_trend' => $searchTrends->toArray()
        ];
    }

    /**
     * @param $num
     * @return string
     */
    private static function nameOfMonth($num): string
    {
        if($num) {
            $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            return $months[$num - 1];
        }
        return '';
    }

    /**
     * @param $dataArray
     * @return array
     */
    private function visibilityData($dataArray): array
    {
        return (new VisibilityTransformer())->transform($dataArray);
    }

}

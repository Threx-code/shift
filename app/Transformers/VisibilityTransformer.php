<?php

namespace App\Transformers;

class VisibilityTransformer
{
    public static $defaultColors = [
        [
            'hex'  => '#64656F',
            'rgba' => ['r' => 100, 'g' => 101, 'b' => 111]
        ],
        [
            'hex'  => '#BB4E55',
            'rgba' => ['r' => 187, 'g' => 78, 'b' => 85]
        ],
        [
            'hex'  => '#9C60B2',
            'rgba' => ['r' => 156, 'g' => 96, 'b' => 178]
        ],
        [
            'hex'  => '#6269A9',
            'rgba' => ['r' => 98, 'g' => 105, 'b' => 169]
        ],
        [
            'hex'  => '#62A166',
            'rgba' => ['r' => 98, 'g' => 161, 'b' => 102]
        ],
        [
            'hex'  => '#D66FA7',
            'rgba' => ['r' => 214, 'g' => 111, 'b' => 167]
        ],
        [
            'hex'  => '#55B29C',
            'rgba' => ['r' => 85, 'g' => 178, 'b' => 156]
        ],
        [
            'hex'  => '#E19B13',
            'rgba' => ['r' => 225, 'g' => 155, 'b' => 19]
        ],
        [
            'hex'  => '#5DA1DF',
            'rgba' => ['r' => 93, 'g' => 161, 'b' => 223]
        ],
        [
            'hex'  => '#93AA39',
            'rgba' => ['r' => 147, 'g' => 170, 'b' => 57]
        ],
        [
            'hex'  => '#DB6B1B',
            'rgba' => ['r' => 219, 'g' => 107, 'b' => 27]
        ],
        [
            'hex'  => '#dfe6d3',
            'rgba' => ['r' => 223, 'g' => 230, 'b' => 211]
        ],
        [
            'hex'  => '#e6beff',
            'rgba' => ['r' => 230, 'g' => 190, 'b' => 255]
        ],
        [
            'hex'  => '#aa6e28',
            'rgba' => ['r' => 170, 'g' => 110, 'b' => 40]
        ],
        [
            'hex'  => '#fffac8',
            'rgba' => ['r' => 255, 'g' => 250, 'b' => 200]
        ],
        [
            'hex'  => '#800000',
            'rgba' => ['r' => 128, 'g' => 0, 'b' => 0]
        ],
        [
            'hex'  => '#aaffc3',
            'rgba' => ['r' => 170, 'g' => 255, 'b' => 195]
        ],
        [
            'hex'  => '#808000',
            'rgba' => ['r' => 128, 'g' => 128, 'b' => 0]
        ],
        [
            'hex'  => '#ffd8b1',
            'rgba' => ['r' => 255, 'g' => 215, 'b' => 180]
        ],
        [
            'hex'  => '#000080',
            'rgba' => ['r' => 0, 'g' => 0, 'b' => 128]
        ],
        [
            'hex'  => '#808080',
            'rgba' => ['r' => 128, 'g' => 128, 'b' => 128]
        ],
        [
            'hex'  => '#000000',
            'rgba' => ['r' => 0, 'g' => 0, 'b' => 0]
        ]
    ];

    /**
     * @param $arrayData
     * @return array
     */
    public function transform($arrayData): array
    {
        $competitorsData = json_decode($arrayData, true);
        if(empty($competitorsData)){
            return [];
        }
        $competitors = collect();
        foreach ($competitorsData['data'] as $data) {
            $color = current(self::$defaultColors);

            $competitors->push(
                (object)[
                    'domain'      => str_replace(['https://', 'www'], '', $competitorsData['domain']),
                    'color_hex'   => $color['hex'],
                    'color_rgba'  => $color['rgba'],
                    'visibility'  => (object)[
                        'desktop' => (object)[
                            'vscore'       => ((float)$data['vscore'] !== 0.01) ? $data['vscore'] : 0.1,
                            'vscore_before' => $data['vscore_before'],
                            'vscore_trend' => ($data['vscore'] === $data['vscore_change']) ? $data['vscore_before'] : $data['vscore_change']
                        ]
                    ],
                    'sessions'  => (object)[
                        'keywords_list' => $data['visits'],
                        'entire_domain' => $data['total_visits'] ?? 0,
                        'trend'         => ($data['vscore_change'] != '0.00' && $data['vscore_change']) ? (float)$data['vscore_change']/(float)$data['vscore'] : 0.00
                    ]
                ]
            );

            $next = next(self::$defaultColors);
            if ($next === false) {
                reset(self::$defaultColors);
            }
        }

        return $competitors->toArray();
    }
}

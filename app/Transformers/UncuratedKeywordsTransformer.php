<?php

namespace App\Transformers;

use Illuminate\Support\Str;

class UncuratedKeywordsTransformer
{
    CONST TAGS = [
        'aggregated' => 'Aggregated Keyword',
        'brand'      => 'Branded Keyword',
        'low_volume' => 'Extremely Low Search Volume',
        'invalid_keyword' => 'Invalid Keyword',
        'mispell'    => 'Misspelling'
    ];

    /**
     * @param $keywordNames
     * @param $keywords
     * @return array
     */
    public function transform($keywords, $keywordNames): array
    {
        $formattedKeywords = [];
        if (!empty($keywordNames)) {
            foreach ($keywordNames as $key => $keywordName) {
                $listKeyword = array_values(array_filter($keywords, function($keyword) use($keywordName) {
                    return $keyword['keyword_id'] == $keywordName['id'];
                }));

                $formattedKeywords[$key]['name'] = $keywordName['name'];
                $formattedKeywords[$key]['tag']  = Str::ucfirst($listKeyword[0]['junk']);
                if ($listKeyword[0] && $listKeyword[0]['junk']) {
                    $formattedKeywords[$key]['tag'] = self::TAGS[$listKeyword[0]['junk']] ?? Str::ucfirst($listKeyword[0]['junk']);
                }
            }
        }
        return $formattedKeywords;
    }
}

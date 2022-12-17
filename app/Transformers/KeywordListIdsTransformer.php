<?php

namespace App\Transformers;

class KeywordListIdsTransformer
{
    /**
     * @param $data
     * @return array
     */
    public function transform($data): array
    {
        $result = [];
        if(!empty($data)) {
            foreach ($data as $key => $value) {
                $result[$key]['id'] = $value['keyword_id'];
            }
        }
        return $result;
    }
}

<?php

class InABTestingPost
{
    private $postVariationKey = null;
    private $postVariationId = null;

    function __construct($postVariationConfigDbRow=null) {
        if ($postVariationConfigDbRow!=null)
            $this->setPostVariationKey($this->extractDbRowVariationKey($postVariationConfigDbRow), $this->extractDbRowVariationId($postVariationConfigDbRow));
    }

    private function getPostVariationKey(){
        return $this->postVariationKey;
    }

    private function getPostVariationId(){
        return $this->postVariationId;
    }

    private function setPostVariationKey($postVariationKey, $postVariationId){
        $this->postVariationKey = $postVariationKey;
        $this->postVariationId = $postVariationId;
        return $this->getPostVariationKey();
    }

    private function extractDbRowVariationKey($postVariationConfigDbRow){
        return $postVariationConfigDbRow->variation_key;
    }

    private function extractDbRowVariationId($postVariationConfigDbRow){
        return $postVariationConfigDbRow->id;
    }

    public function selectPostVariationIdentifiers($postId, $force=false){

        $postVariationKey = $this->getPostVariationKey();
        $postVariationId = $this->getPostVariationId();
        if ((!$postVariationKey or !$postVariationId) or $force) {
            global $wpdb;
            $global = new InDefineGlobals();
            $TABLE_VARIATION = $global::TABLE_NAME_POST_VARIATIONS;


            $post = $wpdb->get_results("SELECT * FROM $TABLE_VARIATION WHERE post_id=$postId ORDER BY id LIMIT 1");
            $postVariationKey = $this->setPostVariationKey($this->extractDbRowVariationKey($post[0]), $this->extractDbRowVariationId($post[0]));
            $postVariationId = $this->getPostVariationId();
        }

        return ["id"=>$postVariationId, "key"=>$postVariationKey];
    }
}
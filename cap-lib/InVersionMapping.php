<?php

class InVersionMapping
{
    public $scriptVersionsMapping;
    public $variationId;

    function __construct($variationId) {
        global $wpdb;
        $global = new InDefineGlobals();
        $tableScriptVersions = $global::TABLE_SCRIPT_VERSIONS;
        $this->variationId = $variationId;
        $this->scriptVersionsMapping = json_decode(json_encode($wpdb->get_results("SELECT * FROM $tableScriptVersions WHERE variation_id='$variationId'")), true);
    }
    public function searchForVersionAndId($lookupTag, $searchByScriptName = false) {
        $global = new InDefineGlobals();
        $versionsArray = $this->scriptVersionsMapping;
        $indexOfArray = 0;
        foreach ($versionsArray as $val) {
            if((!$searchByScriptName && $val[$global::LOOKUP_TAG] === $lookupTag) || $searchByScriptName && $val['script_name'] === $lookupTag) {
                if(!$searchByScriptName) {
                    return ['version' => $val[$global::VERSION], 'id' => $val['id'], 'fullUrl' => $lookupTag.".js".'?'.$global::SCRIPT_PARAM.'='.$val[$global::VERSION]];//MARK-CONTEXTDOMAIN
                }else{
                    return $lookupTag.'?'.$global::SCRIPT_PARAM.'='.$val[$global::VERSION];//MARK-CONTEXTDOMAIN
                }
            }
            $indexOfArray++;
        }
        return false;
    }
    public function populateScriptVersionsMapping($scriptName, $lookupTag, $onlyVersion = false){
        global $wpdb;
        $global = new InDefineGlobals();
        $tableScriptVersions = $global::TABLE_SCRIPT_VERSIONS;

        $searchForIndexResponse = $this->searchForVersionAndId($lookupTag);
        $indexOfVersion = $searchForIndexResponse['version'];
        if(empty($indexOfVersion)){
            $version = 0.01;
            $wpdb->insert($tableScriptVersions, array(
                'version' => $version,
                'variation_id' => $this->variationId,
                'script_name' => $scriptName,
                'lookup_tag' => $lookupTag
            ));
        }else {
            $version = (($indexOfVersion*100)+1)/100;
            $wpdb->update($tableScriptVersions, array(
                'version' => $version,
            ), array('id' => $searchForIndexResponse['id']));
        }
        if(!$onlyVersion) {
            return $scriptName . ".js";
        }else{
            return $version;
        }
    }
}
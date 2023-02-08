<?php

class InDefineGlobals{

    //MARK-CONTEXTDOMAIN
    const TAG_CONTEXT_DOMAIN = '$context-domain$';
    public $websiteDomain = '';
    function __construct(){
        $this->websiteDomain = substr(get_site_url(), strlen('https://'));
    }

    function getWebsiteDomain(){
        return $this->websiteDomain;
    }
    //MARK-CONTEXTDOMAIN - END

    const ELEM_GROUP_NAME_POST_SLUG = "PS";
    const ELEM_GROUP_NAME_HEAD_SCRIPTS = "HS";
    const ELEM_GROUP_NAME_BODY_SCRIPTS = "BS";
    const ELEM_GROUP_NAME_BODY_PAGES = "BP";
    const ELEM_GROUP_NAME_PAGE_TEMPLATE = "PT";
    const ELEM_GROUP_NAME_POST_ID = "PI";
    const ELEM_GROUP_NAME_TITLE_OF_POST = "TP";
    const ELEM_GROUP_NAME_ADV_ID = "AT";
    const ELEM_GROUP_NAME_IMG = "IMG";
    const ELEM_GROUP_NAME = "NM";

    const KEY_DELIMITER_ELEM_GROUP = ";";//Between element-groups (header-scripts' ids, pages ids, etc'...)
    const KEY_DELIMITER_ELEM_GROUP_NAME_TO_VALUES = ":";//Between a given element-group's identifier (HS/BP/PT...) and its input values
    const KEY_DELIMITER_ELEM_GROUP_ELEMENTS = ",";//Between element-group's elements' identifiers (page1, page2, page3, etc'...)
    const KEY_DELIMITER_ELEM_GROUP_ELEMENT_TO_VALUES = "|";//Between a given element's id and its input values (script parameters, etc'...)
    const KEY_DELIMITER_ELEM_GROUP_ELEMENT_INPUT_VALUES = "//";//Between a given element's input values (script parameters, etc'...)

    const SCRIPT_TAG_BEGINING = '$tag-v';
    const SCRIPT_TAG_ENDING = '$';

    const TAG_TITLE = '$tag-title$';
    const TAG_HEADLINE = '$tag-headline$';
    const TAG_HEADLINE_ID = '$tag-headline-dashed-id$';
    const TAG_OPENING_TEXT = '$tag-text$';
    const TAG_TEXT1 = '$tag-text1$';
    const TAG_TEXT2 = '$tag-text2$';
    const TAG_IMG = '$tag-IMG$';
    const TAG_IMG_ID = '$tag-IMG-ID$';
    const TAG_DASHED_ID = '$tag-dashed-ID$';

    const TAG_DFN_SCROLL_SHOW = '$tag-dfn-show$';

    const AUTOMATED_SCRIPT_FILENAME_PREFIX_PRE_HEADCODE = 'pre-headcode';
    const AUTOMATED_SCRIPT_FILENAME_PREFIX_POST_HEADCODE = 'post-headcode';
    const AUTOMATED_SCRIPT_FILENAME_PREFIX_PRE_FOOTERCODE = 'pre-footercode';
    const AUTOMATED_SCRIPT_FILENAME_PREFIX_POST_FOOTERCODE = 'post-footercode';

    const AUTOMATED_SCRIPT_ROOT = "js/auto/";

    const WRAPPER_VERSION_TAG = '$(wrapper-version)$';

    const TABLE_SCRIPT_VERSIONS = 'in_script_versions';
    const LOOKUP_TAG = 'lookup_tag';
    const VERSION = 'version';


    const SCRIPT_PARAM = 'ver';//MARK-CONTEXTDOMAIN
    const SCRIPT_VERSION_TAG = '?(version-';

    const SCRIPT_TAG = '<script>';

    const OPENING_PAGE_TEMPLATE = '

    <!-- DEBUG: Opening page template - START -->
    <!-- wp:heading -->
    <h3 id="$tag-headline-dashed-id$">$tag-headline$</h3>
    <!-- /wp:heading -->
    <!-- wp:image {"align":"center","id":$tag-IMG-ID$,"sizeSlug":"large","linkDestination":"none"} -->
    <figure class="wp-block-image size-large">
    <img src="$tag-IMG$" alt="" class="wp-image-$tag-IMG-ID$"/>
    </figure>
    <!-- /wp:image -->
    <!-- wp:paragraph -->
    <p>$tag-text$</p>
    <!-- /wp:paragraph -->   
    <!-- DEBUG: Opening page template - END -->
    
    ';

    const TAG_PRE_IMAGE_AD_TEMPLATE = '$pre_image_ad_template$';
    const TAG_PAGE_END_AD_TEMPLATE = '$page_end_ad_template$';

    const TABLE_NAME_POST_VARIATIONS = 'in_post_variation';
    const TABLE_NAME_OPENING_PAGE = 'in_opening_page_content';
    const DEVICE_TYPE_ID_DESKTOP = 1;
    const DEVICE_TYPE_ID_MOBILE = 2;

    const DEVICE_TYPE_NAME_DESKTOP = "desktop";
    const DEVICE_TYPE_NAME_MOBILE = "mobile";

    const TAG_DFN_AD_UNIT_DIV_ID_START = ";dfnTag:adUnitDivId";
    const TAG_DFN_AD_UNIT_DIV_ID_END = ";";

    const DIV_INDEX_KEY = "div_index";
    const AD_UNIT_INFO_KEY = "ad_unit_info";

    const HTML_REMARK_HEAD_SCRIPT_START = '<!-- Intellecto Plugin - Head - Start -->';
    const HTML_REMARK_HEAD_SCRIPT_END = '<!-- Intellecto Plugin - Head - END -->';
    const HTML_REMARK_FOOTER_SCRIPT_START = '<!-- Intellecto Plugin - Footer - Start -->';
    const HTML_REMARK_FOOTER_SCRIPT_END = '<!-- Intellecto Plugin - Footer - END -->';
    const HTML_REMARK_BODY_CONTENT_START = '<!-- Intellecto Plugin - Body - Start -->';
    const HTML_REMARK_BODY_CONTENT_END = '<!-- Intellecto Plugin - Body - END -->';

}

class InPostConfig{

    private $headScripts = [];
    private $footerScripts = [];
    private $bodyPageIds = [];
    private $advTemplateIds = [];
    private $pageTemplateId;
    private $postId;
    private $postSlug;
    private $postTitle;
    private $variationId;
    private $featuredImageId;
    private $postName;

    static $PARAM_HEAD_SCRIPT_ID = 'scriptId';
    static $PARAM_HEAD_PARAMETERS = 'scriptParams';

    public function addHeadScripts($headScripts, $replace = false)
    {
        if($replace) {
            $this->headScripts = $headScripts;
        }
        else {
            $this->headScripts = array_merge($this->headScripts, $headScripts);
        }
    }
    public function addFooterScripts($bodyScripts, $replace = false)
    {
        if($replace) {
            $this->footerScripts = $bodyScripts;
        }
        else {
            $this->footerScripts = array_merge($this->footerScripts, $bodyScripts);
        }
    }

    public function getVariationId(){
        return $this->variationId;
    }
    public function setVariationId($variationId){
        $this->variationId = $variationId;
    }
    public function getHeadScripts(){
        return $this->headScripts;
    }
    public function getFooterScripts(){
        return $this->footerScripts;
    }
    public function setfeaturedImageId($featuredImageId){
        $this->featuredImageId = $featuredImageId;
    }
    public function getfeaturedImageId(){
        return $this->featuredImageId;
    }
    public function setPostName($postName){
        $this->postName = $postName;
    }
    public function getPostName(){
        return $this->postName;
    }

    public function addBodyPages($bodyPageIds, $replace = false)
    {
        if($replace) {
            $this->bodyPageIds=$bodyPageIds;
        }
        else {
            $this->bodyPageIds = array_merge($this->bodyPageIds, $bodyPageIds);
        }
    }

    public function getBodyPages(){
        return $this->bodyPageIds;
    }

    public function setadvTemplateIds($advTemplateIds, $replace = false)
    {
        if($replace) {
            $this->advTemplateIds=$advTemplateIds;
        }
        else {
            $this->advTemplateIds = array_merge($this->advTemplateIds, $advTemplateIds);
        }
    }

    public function getadvTemplateIds(){
        return $this->advTemplateIds;
    }

    public function setPageTemplateId($pageTemplateId)
    {
        $this->pageTemplateId=$pageTemplateId;
    }

    public function getPageTemplateId(){
        return $this->pageTemplateId;
    }
    public function setPostSlug($postSlug)
    {
        $this->postSlug=$postSlug;
    }

    public function getPostSlug(){
        return $this->postSlug;
    }
    public function setPostTitle($postTitle)
    {
        $this->postTitle=$postTitle;
    }

    public function getPostTitle(){
        return $this->postTitle;
    }

    public function setPostId($postId)
    {
        $this->postId=$postId;
    }

    public function getPostId(){
        return $this->postId;
    }

    public function getJsDatatypedVariables($variablesArray){
        $dataTypedScriptParams = [];

        if ($variablesArray != null and !empty($variablesArray)) {
            foreach ($variablesArray as $var) {
                $varType = gettype($var);
                if ($varType == "integer" || $varType == "double")
                    array_push($dataTypedScriptParams, $var);
                else if ($varType == "string")
                    array_push($dataTypedScriptParams, "'$var'");
            }
        }

        return $dataTypedScriptParams;
    }

    private function serializeScriptInfo($headScript){
        $global =new InDefineGlobals();
        $KEY_DELIMITER_ELEM_GROUP_ELEMENT_TO_VALUES = $global::KEY_DELIMITER_ELEM_GROUP_ELEMENT_TO_VALUES;
        $KEY_DELIMITER_ELEM_GROUP_ELEMENT_INPUT_VALUES = $global::KEY_DELIMITER_ELEM_GROUP_ELEMENT_INPUT_VALUES;
        $scriptParams = $headScript[InPostConfig::$PARAM_HEAD_PARAMETERS];
        $scriptParamsStr = "";

        if ($scriptParams != null and !empty($scriptParams)) {
            $scriptParamsStr = $KEY_DELIMITER_ELEM_GROUP_ELEMENT_TO_VALUES . implode($KEY_DELIMITER_ELEM_GROUP_ELEMENT_INPUT_VALUES, $this->getJsDatatypedVariables($scriptParams));
        }

        return $headScript[InPostConfig::$PARAM_HEAD_SCRIPT_ID] . $scriptParamsStr;
    }

    private function generateKeySegmentHeadScripts(){
        $global = new InDefineGlobals();
        $KEY_DELIMITER_ELEM_GROUP_ELEMENTS = $global::KEY_DELIMITER_ELEM_GROUP_ELEMENTS;
        $keySegmentHeadScriptsStr = "";

        $headScriptsArray = $this->getHeadScripts();
        if(is_array($headScriptsArray)){
            foreach ($headScriptsArray as $singleScript){
                $keySegmentHeadScriptsStr .= $this->serializeScriptInfo($singleScript) . $KEY_DELIMITER_ELEM_GROUP_ELEMENTS;
            }
            $keySegmentHeadScriptsStr = substr($keySegmentHeadScriptsStr, 0, -1);
        }
        $keySegmentHeadScriptsStr = str_replace("'", '"', $keySegmentHeadScriptsStr);
        return $keySegmentHeadScriptsStr;
    }
    private function generateKeySegmentFooterScripts(){
        $global = new InDefineGlobals();
        $KEY_DELIMITER_ELEM_GROUP_ELEMENTS = $global::KEY_DELIMITER_ELEM_GROUP_ELEMENTS;
        $keySegmentHeadScriptsStr = "";

        $headScriptsArray = $this->getFooterScripts();
        if(is_array($headScriptsArray)){
            foreach ($headScriptsArray as $singleScript){
                $keySegmentHeadScriptsStr .= $this->serializeScriptInfo($singleScript) . $KEY_DELIMITER_ELEM_GROUP_ELEMENTS;
            }
            $keySegmentHeadScriptsStr = substr($keySegmentHeadScriptsStr, 0, -1);
        }

        return $keySegmentHeadScriptsStr;
    }

    private function generateKeySegmentBodyPages(){
        $global = new InDefineGlobals();
        $KEY_DELIMITER_ELEM_GROUP_ELEMENTS = $global::KEY_DELIMITER_ELEM_GROUP_ELEMENTS;
        $keySegmentBodyPagesStr = "";

        $bodyPagesArray = $this->getBodyPages();
        if(is_array($bodyPagesArray)){
            $keySegmentBodyPagesStr = implode($KEY_DELIMITER_ELEM_GROUP_ELEMENTS, $bodyPagesArray);
        }

        return $keySegmentBodyPagesStr;
    }
    private function generateKeySegmentadvTemplateIds(){
        $global = new InDefineGlobals();
        $KEY_DELIMITER_ELEM_GROUP_ELEMENTS = $global::KEY_DELIMITER_ELEM_GROUP_ELEMENTS;
        $keySegmentadvTemplateIdsStr = "";

//        $advTemplateIdsArray = $this->getadvTemplateIds();
        $keySegmentadvTemplateIdsStr = $this->getadvTemplateIds()['pre_image_ad_template_id'].$KEY_DELIMITER_ELEM_GROUP_ELEMENTS.$this->getadvTemplateIds()['page_end_ad_template_id'].$KEY_DELIMITER_ELEM_GROUP_ELEMENTS.$this->getadvTemplateIds()['pre_image_ad_template_id_mobile'].$KEY_DELIMITER_ELEM_GROUP_ELEMENTS.$this->getadvTemplateIds()['page_end_ad_template_id_mobile'];

        return $keySegmentadvTemplateIdsStr;
    }

    public function generateKey(){

        $global = new InDefineGlobals();
        $ELEM_GROUP_NAME_POST_ID = $global::ELEM_GROUP_NAME_POST_ID;
        $ELEM_GROUP_NAME_POST_SLUG = $global::ELEM_GROUP_NAME_POST_SLUG;
        $ELEM_GROUP_NAME_TITLE_OF_POST = $global::ELEM_GROUP_NAME_TITLE_OF_POST;
        $ELEM_GROUP_NAME_HEAD_SCRIPTS = $global::ELEM_GROUP_NAME_HEAD_SCRIPTS;
        $ELEM_GROUP_NAME_Footer_SCRIPTS = $global::ELEM_GROUP_NAME_BODY_SCRIPTS;
        $ELEM_GROUP_NAME_BODY_PAGES = $global::ELEM_GROUP_NAME_BODY_PAGES;
        $ELEM_GROUP_NAME_PAGE_TEMPLATE = $global::ELEM_GROUP_NAME_PAGE_TEMPLATE;
        $ELEM_GROUP_NAME_ADV_ID = $global::ELEM_GROUP_NAME_ADV_ID;
        $KEY_DELIMITER_ELEM_GROUP = $global::KEY_DELIMITER_ELEM_GROUP;
        $KEY_DELIMITER_ELEM_GROUP_NAME_TO_VALUES = $global::KEY_DELIMITER_ELEM_GROUP_NAME_TO_VALUES;
        $ELEM_GROUP_NAME_IMG = $global::ELEM_GROUP_NAME_IMG;
        $ELEM_GROUP_NAME = $global::ELEM_GROUP_NAME;

        $postTitle = str_replace("'", '', $this->getPostTitle());
        $postTitle = str_replace("\\", '', $postTitle);
        $postName = str_replace("'", '', $this->getPostName());
        $postName = str_replace("\\", '', $postName);

        $PI=$ELEM_GROUP_NAME_POST_ID . $KEY_DELIMITER_ELEM_GROUP_NAME_TO_VALUES . $this->getPostId();
        $PS=$ELEM_GROUP_NAME_POST_SLUG . $KEY_DELIMITER_ELEM_GROUP_NAME_TO_VALUES . $this->getPostSlug();
        $NM=$ELEM_GROUP_NAME . $KEY_DELIMITER_ELEM_GROUP_NAME_TO_VALUES . $postName;
        $AT=$ELEM_GROUP_NAME_ADV_ID . $KEY_DELIMITER_ELEM_GROUP_NAME_TO_VALUES . $this->generateKeySegmentadvTemplateIds();
        $TP=$ELEM_GROUP_NAME_TITLE_OF_POST . $KEY_DELIMITER_ELEM_GROUP_NAME_TO_VALUES . $postTitle;
        $HS=$ELEM_GROUP_NAME_HEAD_SCRIPTS . $KEY_DELIMITER_ELEM_GROUP_NAME_TO_VALUES . $this->generateKeySegmentHeadScripts();
        $BS=$ELEM_GROUP_NAME_Footer_SCRIPTS . $KEY_DELIMITER_ELEM_GROUP_NAME_TO_VALUES . $this->generateKeySegmentFooterScripts();
        $BP=$ELEM_GROUP_NAME_BODY_PAGES . $KEY_DELIMITER_ELEM_GROUP_NAME_TO_VALUES . $this->generateKeySegmentBodyPages();
        $PT=$ELEM_GROUP_NAME_PAGE_TEMPLATE . $KEY_DELIMITER_ELEM_GROUP_NAME_TO_VALUES . $this->getPageTemplateId();
        $IMG=$ELEM_GROUP_NAME_IMG . $KEY_DELIMITER_ELEM_GROUP_NAME_TO_VALUES . $this->getfeaturedImageId();

        return
            $PI . $KEY_DELIMITER_ELEM_GROUP .
            $PS . $KEY_DELIMITER_ELEM_GROUP .
            $NM . $KEY_DELIMITER_ELEM_GROUP .
            $AT . $KEY_DELIMITER_ELEM_GROUP .
            $TP . $KEY_DELIMITER_ELEM_GROUP .
            $HS . $KEY_DELIMITER_ELEM_GROUP .
            $BS . $KEY_DELIMITER_ELEM_GROUP .
            $BP . $KEY_DELIMITER_ELEM_GROUP .
            $IMG . $KEY_DELIMITER_ELEM_GROUP .
            $PT;
    }

}



/*
 * TESTING CODE - SHOULD BE REMOVED
 */

if (false) {
    $InPostConfig = new InPostConfig();

//= HEAD SCRIPTS
    $array = [
        [InPostConfig::$PARAM_HEAD_SCRIPT_ID => 5, InPostConfig::$PARAM_HEAD_PARAMETERS => ['V1', 2, 'V3.1']],
        [InPostConfig::$PARAM_HEAD_SCRIPT_ID => 6, InPostConfig::$PARAM_HEAD_PARAMETERS => ['V1', 'V2', 3.1]]
    ];
    $advArray = ["pre_image_ad_template_id"=>1,"page_end_ad_template_id"=>2];
    $InPostConfig->addHeadScripts($array);

////= BODY PAGES
    $array = array(101, 102, 103);
    $InPostConfig->addBodyPages($array);
    $InPostConfig->setadvTemplateIds($advArray);
    $InPostConfig->setPostTitle('Test');
//    $InPostConfig->getadvTemplateIds();

////= PAGE TEMPLATE ID
    $InPostConfig->setPageTemplateId(3);

////= POST ID
    $InPostConfig->setPostId(80);

////= Generate KEY
    $InPostConfig->generateKey();
}
// END OF TESTING CODE
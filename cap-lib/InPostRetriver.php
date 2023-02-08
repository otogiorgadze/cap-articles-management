<?php

class InPostRetriever{

    private $scriptVersionsMapping = [];

    private $postVariationConfigDbRow = null;
    private $inABTestingPost = null;
    private $globalSharedValues = null;

    function __construct() {
        $this->setInABTestingPost(new InABTestingPost());
        $this->setGlobalSharedValues(new InDefineGlobals());
        $this->populateScriptVersionsMapping();
    }
    private function populateScriptVersionsMapping(){
        $global = $this->getGlobalSharedValues();
        $this->scriptVersionsMapping[$global::AUTOMATED_SCRIPT_FILENAME_PREFIX_PRE_HEADCODE]='0.0.1';
    }
    private function getPostVariationConfigDbRow(){
        return $this->postVariationConfigDbRow;
    }
    private function setPostVariationConfigDbRow($postVariationConfigDbRow){
        $this->postVariationConfigDbRow = $postVariationConfigDbRow;
        return $this->getPostVariationConfigDbRow();
    }
    private function getPostVariationDbContent($postVariationKey, $force=false){
        $postVariationConfigDbRow = $this->getPostVariationConfigDbRow();
        if (!$postVariationConfigDbRow or $force){
            $global = new InDefineGlobals();
            $TABLE_NAME_POST_VARIATIONS = $global::TABLE_NAME_POST_VARIATIONS;

            global $wpdb;
            $post = $wpdb->get_results("SELECT * FROM $TABLE_NAME_POST_VARIATIONS WHERE variation_key='$postVariationKey'");
            $postVariationConfigDbRow = $this->setPostVariationConfigDbRow($post[0]);
        }

        return $postVariationConfigDbRow;
    }
    private function getGlobalSharedValues(){
        return $this->globalSharedValues;
    }
    private function preGlobals_ORIG(){
        global $wpdb, $post, $websiteUrlDomainPrefix, $websiteDomain;

        $websiteUrlDomainPrefix = plugin_dir_url( __FILE__ );
        $websiteDomain = substr(get_site_url(), strlen('https://'));

        $QUERY_PARAM_CAID='ca_id';
        $QUERY_PARAM_FBCLID='fbclid';
        $QUERY_PARAM_GCLID='gclid';
        $DEFAULT_CAID=1;
        $FBCLID_CAID=2;
        $GCLID_CAID=3;
//
        $preGlobal = '
        <script>
        let dconfig ='
            .json_encode([
                "pageId"=> get_the_ID(),
                "mobileDevice"=> json_encode(wp_is_mobile()),
                "loggedUser" => json_encode(is_user_logged_in()),
                "forceAuctionWonBid"=> "true",
            ]);

        $queryStringParamCaId=($_GET[$QUERY_PARAM_CAID] ? $_GET[$QUERY_PARAM_CAID] :
            ($_GET[$QUERY_PARAM_FBCLID] ? $FBCLID_CAID :
                ($_GET[$QUERY_PARAM_GCLID] ? $GCLID_CAID :
                    $DEFAULT_CAID)));
        $inlineCode = "\n let referrerCaid='$queryStringParamCaId';\n let websiteDomain='$websiteDomain';\n let queryStringParamCaId = '$queryStringParamCaId';\n let sessionId = `".intval(microtime(true)*1000).":\${Math.random()*1000000000000000000}`;\n";

        $infiniteScrollInline = $inlineCode."</script>";
        return $preGlobal.$infiniteScrollInline;

    }
    private function preGlobals(){
        global $websiteUrlDomainPrefix, $websiteDomain;

        $websiteUrlDomainPrefix = plugin_dir_url( __FILE__ );
        $websiteDomain = substr(get_site_url(), strlen('https://'));

        $QUERY_PARAM_CAID='ca_id';
        $QUERY_PARAM_FBCLID='fbclid';
        $QUERY_PARAM_GCLID='gclid';
        $DEFAULT_CAID=1;
        $FBCLID_CAID=2;
        $GCLID_CAID=3;

        $queryStringParamCaId=($_GET[$QUERY_PARAM_CAID] ? $_GET[$QUERY_PARAM_CAID] :
            ($_GET[$QUERY_PARAM_FBCLID] ? $FBCLID_CAID :
                ($_GET[$QUERY_PARAM_GCLID] ? $GCLID_CAID :
                    $DEFAULT_CAID)));


        $preGlobal = '
<script>
    var dconfig=JSON.parse(\'{"pageId":'.get_the_ID().',"x":1,"mobileDevice":'.(wp_is_mobile() ? 'true' : 'false').',"loggedUser":'.(is_user_logged_in() ? 'true' : 'false').',"forceAuctionWonBid":true,"referrerCaid":"'.$queryStringParamCaId.'","websiteDomain":"'.$websiteDomain.'","queryStringParamCaId":"'.$queryStringParamCaId.'"}\');
    dconfig.sessionId=`'.intval(microtime(true)*1000).':\${Math.random()*1000000000000000000}`;';
    return $preGlobal;



//        var dconfig =JSON.parse(\"{'
//            ."pageId:".get_the_ID()
//            .",mobileDevice:".(wp_is_mobile() ? 'true' : 'false')
//            .",loggedUser:".(is_user_logged_in() ? 'true' : 'false')
//            .",forceAuctionWonBid:true"
//            .",referrerCaid:'$queryStringParamCaId'"
//            .",websiteDomain:'$websiteDomain'"
//            .",queryStringParamCaId:'$queryStringParamCaId'"
//            .",sessionId:`".intval(microtime(true)*1000).":\${Math.random()*1000000000000000000}`}\");"
//        ;

//        $inlineCode = "\n".
//            "let referrerCaid='$queryStringParamCaId';\n"
//            ."let websiteDomain='$websiteDomain';\n"
//            ."let queryStringParamCaId = '$queryStringParamCaId';\n"/*
//            ."let sessionId = `".intval(microtime(true)*1000).":\${Math.random()*1000000000000000000}`;\n"*/;
//        $infiniteScrollInline = $inlineCode."</script>";
//        return $preGlobal.$infiniteScrollInline;
    }
    private function setGlobalSharedValues($globalSharedValues){
        $this->globalSharedValues = $globalSharedValues;
        return $this->getGlobalSharedValues();
    }
    private function getInABTestingPost(){
        return $this->inABTestingPost;
    }
    private function setInABTestingPost($inABTestingPost){
        $this->inABTestingPost = $inABTestingPost;
        return $this->getInABTestingPost();
    }
    private function buildAutomatedScriptUrl($filenamePrefix, $postVariationId){
        $global = new InDefineGlobals();
        $DEVICE_TYPE_NAME_DESKTOP = $global::DEVICE_TYPE_NAME_DESKTOP;
        $DEVICE_TYPE_NAME_MOBILE = $global::DEVICE_TYPE_NAME_MOBILE;
        $isMobile = wp_is_mobile();

        if($isMobile){
            $deviceTypeName = $DEVICE_TYPE_NAME_MOBILE;
        }else{
            $deviceTypeName = $DEVICE_TYPE_NAME_DESKTOP;
        }

        $VersionMapping = new InVersionMapping($postVariationId);

        return plugin_dir_url( dirname( __FILE__ ) ) .$global::AUTOMATED_SCRIPT_ROOT.$VersionMapping->searchForVersionAndId($filenamePrefix."-".$deviceTypeName."-".$postVariationId)['fullUrl'];

    }
    private function getAutomatedPreHeadScripts($postVariationId){
        $global = new InDefineGlobals();
        $scripts = "<!--tS1--><script src='".$this->buildAutomatedScriptUrl($global::AUTOMATED_SCRIPT_FILENAME_PREFIX_PRE_HEADCODE, $postVariationId)."'></script>";
        return $this->preGlobals().$scripts;
    }
    private function getAutomatedPostHeadScripts($postVariationId){
        $global = new InDefineGlobals();
        $scripts = "<!--tS2--><script src='".$this->buildAutomatedScriptUrl($global::AUTOMATED_SCRIPT_FILENAME_PREFIX_POST_HEADCODE, $postVariationId)."'></script>";
        return $scripts;
    }
    private function getAutomatedHeadScripts($postVariationId){
        return ['preHeadScripts'=>$this->getAutomatedPreHeadScripts($postVariationId), 'postHeadScripts'=>$this->getAutomatedPostHeadScripts($postVariationId)];
    }
    private function getPostVariationHeadScripts($postVariationKey, $postVariationId){
        $automatedHeadScriptsArray = $this->getAutomatedHeadScripts($postVariationId);
        return
            $automatedHeadScriptsArray['preHeadScripts']
            .$this->getPostVariationDbContent($postVariationKey)->head_html
            .$automatedHeadScriptsArray['postHeadScripts'];
    }
    private function getAutomatedPreFooterScripts($postVariationId){
        $global = new InDefineGlobals();
        $scripts = "<!--tSF1--><script src='".$this->buildAutomatedScriptUrl($global::AUTOMATED_SCRIPT_FILENAME_PREFIX_PRE_FOOTERCODE, $postVariationId)."'></script>";
        return $scripts;
    }
    private function getAutomatedPostFooterScripts($postVariationId){
        $global = new InDefineGlobals();
        $scripts = "<!--tSF2--><script src='".$this->buildAutomatedScriptUrl($global::AUTOMATED_SCRIPT_FILENAME_PREFIX_POST_FOOTERCODE, $postVariationId)."'></script>";
        return $scripts;
    }
    private function getAutomatedFooterScripts($postVariationId){
        return ['preFooterScripts'=>$this->getAutomatedPreFooterScripts($postVariationId), 'postFooterScripts'=>$this->getAutomatedPostFooterScripts($postVariationId)];
    }
    private function getPostVariationFooterScripts($postVariationKey, $postVariationId){
        $automatedFooterScriptsArray = $this->getAutomatedFooterScripts($postVariationId);
        return
            $automatedFooterScriptsArray['preFooterScripts']
            .$this->getPostVariationDbContent($postVariationKey)->footer_html
            .$automatedFooterScriptsArray['postFooterScripts'];
    }
    private function getPostVariationBodyContent($postVariationKey, $isMobile){
        if($isMobile) {
            return $this->getPostVariationDbContent($postVariationKey)->body_wp_html_code_mobile;
        }else {
            return $this->getPostVariationDbContent($postVariationKey)->body_wp_html_code;
        }
    }
    private function getPostVariationTitle($postVariationKey){
        return $this->decodePostConfig($postVariationKey)->postTitle;
    }
    public function getPostOpeningPage($postId){
        global $wpdb, $global;
        $TABLE_NAME_OPENING_PAGE = $global::TABLE_NAME_OPENING_PAGE;
        $post = $wpdb->get_results("SELECT * FROM $TABLE_NAME_OPENING_PAGE WHERE post_id='$postId'");
        $postOpeningPage = $post[0];
        return $postOpeningPage;
    }
    private function getPostVariationSlug($postVariationKey){
        return $this->decodePostConfig($postVariationKey)->postSlug;
    }
    private function decodePostConfig($postVariationKey){
        return json_decode($this->getPostVariationDbContent($postVariationKey)->post_configuration);
    }
    public function getPostHead($postId){
        $postVariationsIdentifiers = ($this->getInABTestingPost())->selectPostVariationIdentifiers($postId);
        return $this->getPostVariationHeadScripts($postVariationsIdentifiers['key'], $postVariationsIdentifiers['id']);
    }
    public function getPostFooter($postId){
        $postVariationsIdentifiers = ($this->getInABTestingPost())->selectPostVariationIdentifiers($postId);
        return $this->getPostVariationFooterScripts($postVariationsIdentifiers['key'], $postVariationsIdentifiers['id']);
    }
    public function getPostBody($postId){
        $isMobile = wp_is_mobile();
        $postVariationsIdentifiers = ($this->getInABTestingPost())->selectPostVariationIdentifiers($postId);
        return $this->getPostVariationBodyContent($postVariationsIdentifiers['key'], $isMobile);
    }

    public function getPostTitle($postId){
        $postVariationsIdentifiers = ($this->getInABTestingPost())->selectPostVariationIdentifiers($postId);
        return $this->getPostVariationTitle($postVariationsIdentifiers['key']);
    }
    public function getPostSlug($postId){
        $postVariationsIdentifiers = ($this->getInABTestingPost())->selectPostVariationIdentifiers($postId);
        return $this->getPostVariationSlug($postVariationsIdentifiers['key']);
    }


}
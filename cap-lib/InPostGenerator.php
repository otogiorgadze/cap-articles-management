<?php
include ('InVersionMapping.php');

class InPostGenerator
{

    private $adReplacementIndexArray;

    private function setAdReplacementIndexArray($index, $tag){
        $this->adReplacementIndexArray[$tag] = $index;
    }
    private function getAdReplacementIndexArray($tag){
        return$this->adReplacementIndexArray[$tag];
    }
    private function getStringBetween($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    //MARK-CONTEXTDOMAIN
    private function getAlternativeDomain($origDomain, $alternativeDomain)
    {
        $global = new InDefineGlobals();
        return ($origDomain==$global::TAG_CONTEXT_DOMAIN ? ($alternativeDomain || $global->getWebsiteDomain()) : $origDomain);
    }
    private function unparseUrl($url, $additionalQueryParamsString, $alternativeDomain)
    {
        $strippedUrl = trim(stripslashes($url),'"');
        $parsedUrlParams = parse_url($strippedUrl);

        $queryIsSet = isset($parsedUrlParams['query']);
        $additionalQueryIsSet = isset($additionalQueryParamsString);

        $scheme = isset($parsedUrlParams['scheme']) ? $parsedUrlParams['scheme'] . '://' : '';
        $host = isset($parsedUrlParams['host']) ? $this->getAlternativeDomain($parsedUrlParams['host'], $alternativeDomain) : '';
        $port = isset($parsedUrlParams['port']) ? ':' . $parsedUrlParams['port'] : '';
        $user = isset($parsedUrlParams['user']) ? $parsedUrlParams['user'] : '';
        $pass = isset($parsedUrlParams['pass']) ? ':' . $parsedUrlParams['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsedUrlParams['path']) ? $parsedUrlParams['path'] : '';
        $fragment = isset($parsedUrlParams['fragment']) ? '#' . $parsedUrlParams['fragment'] : '';

        // $query = isset($parsedUrlParams['query']) ? '?' . $parsedUrlParams['query'] : '';
        $query = (($queryIsSet or $additionalQueryIsSet) ? '?' : '')
            . ($queryIsSet ? $parsedUrlParams['query'] : '')
            . ($additionalQueryIsSet ? (($queryIsSet ? '&' : '') . $additionalQueryParamsString) : '');

        return "$scheme$user$pass$host$port$path$query$fragment";
    }
    //MARK-CONTEXTDOMAIN - END

    private function constructPostHeadScriptsHtml($postConfigObject, $scriptType, $postVariationId = null){
        global $wpdb;

        $global = new InDefineGlobals();
        $SCRIPT_TAG_BEGINING = $global::SCRIPT_TAG_BEGINING;
        $SCRIPT_TAG_ENDING = $global::SCRIPT_TAG_ENDING;
        $DEVICE_TYPE_ID_DESKTOP = $global::DEVICE_TYPE_ID_DESKTOP;
        $DEVICE_TYPE_ID_MOBILE = $global::DEVICE_TYPE_ID_MOBILE;
        $WRAPPER_START = "$(";
        $WRAPPER_END = ")$";
        $headHtml = "";
        if($scriptType === $DEVICE_TYPE_ID_DESKTOP) {
            $headScripts = $postConfigObject->getHeadScripts();
        }else{
            $headScripts = $postConfigObject->getFooterScripts();
        }
        foreach ($headScripts as $script) {
            if (!empty($script)) {
                $scriptId = $script[InPostConfig::$PARAM_HEAD_SCRIPT_ID];
                $scriptParamValues = $script[InPostConfig::$PARAM_HEAD_PARAMETERS];

                //DEBUG-VALUES
                $dbResultsArray = $wpdb->get_results("SELECT * FROM in_template_script WHERE id='$scriptId' LIMIT 1");

                $headScript_Replaced = $dbResultsArray[0]->head_script_html_template;
                $verNum = $dbResultsArray[0]->version;

                $script_Version = (($verNum*100)+1)/100;

                $wpdb->update("in_template_script", array('version'=>$script_Version),array('id'=>$scriptId));
                $tagToReplace = $global::SCRIPT_VERSION_TAG;

                $doc = new DOMDocument();
                $doc->loadHTML($headScript_Replaced);
                $scriptTags = $doc->getElementsByTagName('script');


                $scriptsSrcManipulated = "";
                foreach($scriptTags as $tag) {
                    $url = $tag->getAttribute('src');

                    //MARK-CONTEXTDOMAIN
                    if(!empty($url)) {
                        $updatedUrl = $this->unparseUrl($url, $global::SCRIPT_PARAM .'=' . $script_Version, null);
                        $scriptsSrcManipulated .= '<script src="' . $updatedUrl . '"></script>' . "\n";
                    }else{
                        $scriptsSrcManipulated .= "<script>".$tag->textContent."</script>";
                    }
                    //MARK-CONTEXTDOMAIN - END
                }
                if(!empty($scriptsSrcManipulated)) {
                    $headScript_Replaced = $scriptsSrcManipulated;
                }

                if ($scriptParamValues !== null and !empty($scriptParamValues)) {
                    $paramIndex = 1;
                    $varParams = "";

                    foreach ($scriptParamValues as $paramValue) {

                        if(!is_int($paramValue) && !str_contains($paramValue, $WRAPPER_START)){ $paramValue = '"'.$paramValue.'"';}

                        $varParams .= "\nlet v".$paramIndex." = ".$paramValue.";";
                        $paramIndex++;

//                        $headScript_Replaced = str_replace($SCRIPT_TAG_BEGINING . $paramIndex . $SCRIPT_TAG_ENDING, stripslashes($paramValue), $headScript_Replaced);
//                        $headScript_Replaced = str_replace($WRAPPER_START, "", $headScript_Replaced);
//                        $headScript_Replaced = str_replace($WRAPPER_END, "", $headScript_Replaced);

                    }
                    $headScript_Replaced = str_replace($global::SCRIPT_TAG, $global::SCRIPT_TAG.$varParams."\n", $headScript_Replaced);
                }
                $headHtml .= $headScript_Replaced."\n";
            }
        }
        if(substr_count($headHtml, $global::SCRIPT_TAG) > 0 && $postVariationId !== null) {
            $VersionMapping = new InVersionMapping($postVariationId);
            $version = $VersionMapping->populateScriptVersionsMapping('Wrapper', $global::WRAPPER_VERSION_TAG, true);
            $headHtml = str_replace($global::WRAPPER_VERSION_TAG, $global::SCRIPT_PARAM.'='.$version, $headHtml);
        }
        return $headHtml;
    }

    private function replaceTemplateWithAdUnit($template, $adunitObjPerAdDivIndexArray)
    {
        $DFNTAG_ADUNIT_NETWORK_CODE =';dfnTag:networkCode;';
        $DFNTAG_ADUNIT_PARENT_ADUNIT_CODE =';dfnTag:parentAdUnitCode;';
        $DFNTAG_ADUNIT_NAME =';dfnTag:adUnitName;';

        $global = new InDefineGlobals();

        $TAG_START = $global::TAG_DFN_AD_UNIT_DIV_ID_START;
        $TAG_END = $global::TAG_DFN_AD_UNIT_DIV_ID_END;

        $DIV_INDEX_KEY = $global::DIV_INDEX_KEY;
        $AD_UNIT_INFO_KEY = $global::AD_UNIT_INFO_KEY;

        foreach ($adunitObjPerAdDivIndexArray as $adunitObjPerAdDivIndexObject) {
            $divIndex = $adunitObjPerAdDivIndexObject[$DIV_INDEX_KEY];
            $adunitObj = $adunitObjPerAdDivIndexObject[$AD_UNIT_INFO_KEY];
            $adUnitsAd = $adunitObj->ad_unit_div_id;

            $template = str_replace($TAG_START . $divIndex . $TAG_END, $adUnitsAd, $template);
            $template = preg_replace('/' . $DFNTAG_ADUNIT_NETWORK_CODE . '/', $adunitObj->network_code, $template);
            $template = preg_replace('/' . $DFNTAG_ADUNIT_PARENT_ADUNIT_CODE . '/', $adunitObj->parent_ad_unit_code, $template);
            $template = preg_replace('/' . $DFNTAG_ADUNIT_NAME . '/', $adunitObj->ad_unit_name, $template);
        }

        return $template;
    }

    private function adReplacement($bodyPageWpCodeHtml, $adTemplates, $deviceTypeId){
        global $wpdb, $table_name_adUnits;
        $global = new InDefineGlobals();
        $TAG_PRE_IMAGE_AD_TEMPLATE = $global::TAG_PRE_IMAGE_AD_TEMPLATE;
        $TAG_PAGE_END_AD_TEMPLATE = $global::TAG_PAGE_END_AD_TEMPLATE;
        $DEVICE_TYPE_ID_DESKTOP = $global::DEVICE_TYPE_ID_DESKTOP;

        $banner728x90InfoArrayDesktop = 'banner728x90InfoArrayDesktop';
        $banner300x250InfoArrayDesktop = 'banner300x250InfoArrayDesktop';
        $banner300x250InfoArrayMobile = 'banner300x250InfoArrayMobile';
        $banner300x100InfoArrayMobile = 'banner300x100InfoArrayMobile';

        $preImageAdTemplate = $adTemplates['pre_image_ad_template'];
        $pageEndAdTemplate = $adTemplates['page_end_ad_template'];
        $preImageMobileAdTemplate = $adTemplates['pre_image_ad_template_mobile'];
        $pageEndMobileAdTemplate = $adTemplates['page_end_ad_template_mobile'];
        $adTemplateToAdunitObjectsPairArray = [];

        $DIV_INDEX_KEY = $global::DIV_INDEX_KEY;
        $AD_UNIT_INFO_KEY = $global::AD_UNIT_INFO_KEY;

        if($deviceTypeId === $DEVICE_TYPE_ID_DESKTOP){
            $banner728x90AdUnitDbRowsArray = $wpdb->get_results("SELECT * FROM $table_name_adUnits WHERE group_name='$banner728x90InfoArrayDesktop'");
            $banner300x250AdUnitDbRowsArray = $wpdb->get_results("SELECT * FROM $table_name_adUnits WHERE group_name='$banner300x250InfoArrayDesktop'");
            $adTemplateToAdunitObjectsPairArray = [
                ['template'=>$preImageAdTemplate, 'ad_unit_object'=>$banner728x90AdUnitDbRowsArray, 'ad_template_page_tag'=>$TAG_PRE_IMAGE_AD_TEMPLATE],
                ['template'=>$pageEndAdTemplate, 'ad_unit_object'=>$banner300x250AdUnitDbRowsArray, 'ad_template_page_tag'=>$TAG_PAGE_END_AD_TEMPLATE]
            ];
        }
        else{
            $banner300x100AdUnitDbRowsArray = $wpdb->get_results("SELECT * FROM $table_name_adUnits WHERE group_name='$banner300x100InfoArrayMobile'");
            $banner300x250AdUnitDbRowsArray = $wpdb->get_results("SELECT * FROM $table_name_adUnits WHERE group_name='$banner300x250InfoArrayMobile'");

            $adTemplateToAdunitObjectsPairArray = [
                    ['template' => $preImageMobileAdTemplate, 'ad_unit_object' => $banner300x100AdUnitDbRowsArray, 'ad_template_page_tag' => $TAG_PRE_IMAGE_AD_TEMPLATE],// The value of "current_index" attribute should be SHARED between all items that use ad_unit_object:$banner300x250AdUnitDbRowsArray
                    ['template' => $pageEndMobileAdTemplate, 'ad_unit_object' => $banner300x250AdUnitDbRowsArray, 'ad_template_page_tag' => $TAG_PAGE_END_AD_TEMPLATE]// The value of "current_index" attribute should be SHARED between all items that use ad_unit_object:$banner300x250AdUnitDbRowsArray
            ];

        }
        $accumulatedAdunitObjPerAdDivIndexArray = [];

        foreach ($adTemplateToAdunitObjectsPairArray as $adTemplateToAdunitObjectsPair){
            $template = $adTemplateToAdunitObjectsPair['template'];
            $adunitObjArray = $adTemplateToAdunitObjectsPair['ad_unit_object'];
            $adTemplatePageTag = $adTemplateToAdunitObjectsPair['ad_template_page_tag'];

            $TAG_START = $global::TAG_DFN_AD_UNIT_DIV_ID_START;
            $TAG_END = $global::TAG_DFN_AD_UNIT_DIV_ID_END;

            if (preg_match_all('/' . $TAG_START . '(.*?)' . $TAG_END . '/', $template, $match) > 0) {
                $adunitObjPerAdDivIndexArray = [];
                $idIndexofAds = array_unique($match[1]);
                foreach ($idIndexofAds as $idIndexOfAd) {
                    if ((count($adunitObjArray)-1) > $this->getAdReplacementIndexArray($adTemplatePageTag)) {
                        array_push($adunitObjPerAdDivIndexArray,[$DIV_INDEX_KEY=>$idIndexOfAd, $AD_UNIT_INFO_KEY=>$adunitObjArray[$this->getAdReplacementIndexArray($adTemplatePageTag)]]);
                        $this->setAdReplacementIndexArray($this->getAdReplacementIndexArray($adTemplatePageTag) + 1, $adTemplatePageTag);
                    }
                }
            }
            $bodyPageWpCodeHtml = str_replace($adTemplatePageTag, $this->replaceTemplateWithAdUnit($template, $adunitObjPerAdDivIndexArray), $bodyPageWpCodeHtml);
            $accumulatedAdunitObjPerAdDivIndexArray = array_merge($adunitObjPerAdDivIndexArray, $accumulatedAdunitObjPerAdDivIndexArray);

        }
        return ['body_html'=>$bodyPageWpCodeHtml, 'adunit_objects_per_divindex'=>$accumulatedAdunitObjPerAdDivIndexArray];
    }

    private function getBodyScripts(){
        return "<script defer src=\"//static.vidazoo.com/basev/vwpt.js\" data-widget-id=\"63a9eb521dad81c302139b70\"></script>\n\n";
    }

    private function constructPostBodyHtml($postConfigObject, $deviceTypeId = 1){

//        DESKTOP BY DEFAULT

        global $wpdb;

        $global = new InDefineGlobals();
        $TAG_TITLE = $global::TAG_TITLE;
        $TAG_DASHED_ID = $global::TAG_DASHED_ID;
        $OPENING_PAGE_TEMPLATE = $global::OPENING_PAGE_TEMPLATE;
        $TAG_HEADLINE = $global::TAG_HEADLINE;
        $TAG_HEADLINE_ID = $global::TAG_HEADLINE_ID;
        $TAG_OPENING_TEXT = $global::TAG_OPENING_TEXT;
        $TAG_TEXT1 = $global::TAG_TEXT1;
        $TAG_TEXT2 = $global::TAG_TEXT2;
        $TAG_IMG = $global::TAG_IMG;
        $TAG_IMG_ID = $global::TAG_IMG_ID;

        $TAG_PRE_IMAGE_AD_TEMPLATE = $global::TAG_PRE_IMAGE_AD_TEMPLATE;
        $TAG_PAGE_END_AD_TEMPLATE = $global::TAG_PAGE_END_AD_TEMPLATE;

        $postID = $postConfigObject->getPostId();
        $opening_page_results = $wpdb->get_results("SELECT * FROM in_opening_page_content WHERE post_id=$postID");
        $headline_text = $opening_page_results[0]->headline_text;
        $opening_text = $opening_page_results[0]->text;

        $body_wp_html_code = ""/*$this->getBodyScripts()*/;
        $body_wp_html_code .= $OPENING_PAGE_TEMPLATE;
        $body_wp_html_code = str_replace($TAG_HEADLINE, $headline_text, $body_wp_html_code);
        $body_wp_html_code = str_replace($TAG_OPENING_TEXT, $opening_text, $body_wp_html_code);

        $body_wp_html_code = str_replace($TAG_HEADLINE_ID, str_replace(' ', '-', strtolower(str_replace(',', '', $headline_text))), $body_wp_html_code);
        $body_wp_html_code = str_replace($TAG_IMG, wp_get_attachment_image_src($postConfigObject->getfeaturedImageId(), 'full')[0], $body_wp_html_code);
        $body_wp_html_code = str_replace($TAG_IMG_ID, $postConfigObject->getfeaturedImageId(), $body_wp_html_code);

        $templateId = $postConfigObject->getPageTemplateId();
        $bodyPageIdsArray = $postConfigObject->getBodyPages();
//        DESKTOP
        $pre_image_ad_template_id = $postConfigObject->getadvTemplateIds()['pre_image_ad_template_id'];
        $page_end_ad_template_id = $postConfigObject->getadvTemplateIds()['page_end_ad_template_id'];
//        MOBILE
        $pre_image_ad_template_id_mobile = $postConfigObject->getadvTemplateIds()['pre_image_ad_template_id_mobile'];
        $page_end_ad_template_id_mobile = $postConfigObject->getadvTemplateIds()['page_end_ad_template_id_mobile'];
//        DESKTOP
        $pre_image_ad_template_result = $wpdb->get_results("SELECT ad_container_html FROM in_template_ad WHERE id='$pre_image_ad_template_id' LIMIT 1");
        $pre_image_ad_template = $pre_image_ad_template_result[0]->ad_container_html;
        $page_end_ad_template_result = $wpdb->get_results("SELECT ad_container_html FROM in_template_ad WHERE id='$page_end_ad_template_id' LIMIT 1");
        $page_end_ad_template = $page_end_ad_template_result[0]->ad_container_html;
//        MOBILE
        $pre_image_ad_template_result_mobile = $wpdb->get_results("SELECT ad_container_html FROM in_template_ad WHERE id='$pre_image_ad_template_id_mobile' LIMIT 1");
        $pre_image_ad_template_mobile = $pre_image_ad_template_result_mobile[0]->ad_container_html;
        $page_end_ad_template_result_mobile = $wpdb->get_results("SELECT ad_container_html FROM in_template_ad WHERE id='$page_end_ad_template_id_mobile' LIMIT 1");
        $page_end_ad_template_mobile = $page_end_ad_template_result_mobile[0]->ad_container_html;
        $adTemplates = [
            'pre_image_ad_template' => $pre_image_ad_template,
            'page_end_ad_template' => $page_end_ad_template,
            'pre_image_ad_template_mobile' => $pre_image_ad_template_mobile,
            'page_end_ad_template_mobile' => $page_end_ad_template_mobile
        ];

        $automatedHeadscripts = [];
        if ($bodyPageIdsArray !== null and !empty($bodyPageIdsArray)) {
            $bodyTemplateDbResultsArray = $wpdb->get_results("SELECT body_page_html FROM in_template_body_page WHERE id='$templateId' LIMIT 1");


            $this->setAdReplacementIndexArray(0, $TAG_PRE_IMAGE_AD_TEMPLATE);
            $this->setAdReplacementIndexArray(0, $TAG_PAGE_END_AD_TEMPLATE);

            $pageIndex = true;
            foreach ($bodyPageIdsArray as $bodyPageId) {


                $pageHtml = $bodyTemplateDbResultsArray[0]->body_page_html;
                $pageContentItemsDbResultsArray = $wpdb->get_results("SELECT * FROM in_page_content WHERE id='$bodyPageId'");


                foreach ($pageContentItemsDbResultsArray as $pageContentItems) {
                    $TAG_REPLACED_ID = str_replace(' ', '-', strtolower(str_replace(',', '', $pageContentItems->title)));
                    if($pageIndex){
                        $pageHtml = str_replace($global::TAG_DFN_SCROLL_SHOW, 'dfn-inf-scroll-show', $pageHtml);
                    }else{
                        $pageHtml = str_replace($global::TAG_DFN_SCROLL_SHOW, '', $pageHtml);
                    }
                    $pageHtml = str_replace($TAG_TITLE, $pageContentItems->title, $pageHtml);
                    $pageHtml = str_replace($TAG_DASHED_ID, $TAG_REPLACED_ID, $pageHtml);
                    $pageHtml = str_replace($TAG_IMG, wp_get_attachment_image_src($pageContentItems->wp_image_id, 'full')[0], $pageHtml);
                    $pageHtml = str_replace($TAG_IMG_ID, $pageContentItems->wp_image_id, $pageHtml);
                    $pageHtml = str_replace($TAG_TEXT1, $pageContentItems->text1, $pageHtml);
                    $pageHtml = str_replace($TAG_TEXT2, $pageContentItems->text2, $pageHtml);

                    $response = $this->adReplacement($pageHtml, $adTemplates, $deviceTypeId);
                    $pageHtml = $response['body_html'];
                    //TODO: Use $reponse->adunit_objects_per_divindex[]->$AD_UNIT_INFO_KEY - to generate a COMPLETE list of all ad-units that were utilized: "$utilizedAdUnitObjectsArray"
                    $utilizedAdUnitObjectsArray[] = $response['adunit_objects_per_divindex'];
                    $pageIndex = false;
                }
                $body_wp_html_code .= $pageHtml;
            }

            //TODO: Create automated scripts function
            $automatedHeadscripts = $this->generateAutomatedScripts($utilizedAdUnitObjectsArray, $deviceTypeId, $postConfigObject->getVariationId());
        }

        return ['body_html'=>$body_wp_html_code, 'automated_headscripts'=>$automatedHeadscripts];
    }
    public function generateAutomatedScripts($utilizedAdUnitObjectsArray, $deviceType, $postVariationId){
        //TODO: implement - create 2 new javascript files:
        //  name: "headcode-desktop-$postVariationId.js"
        //  name: "headcode-mobile-$postVariationId.js"
        //  location: "https://dailyfeednews.com/wp-content/plugins/dfn/js/automated/"

        $global = new InDefineGlobals();
        $DEVICE_TYPE_ID_DESKTOP = $global::DEVICE_TYPE_ID_DESKTOP;
        $DEVICE_TYPE_NAME_DESKTOP = $global::DEVICE_TYPE_NAME_DESKTOP;
        $DEVICE_TYPE_NAME_MOBILE = $global::DEVICE_TYPE_NAME_MOBILE;

        $jsonStringStart = "addHeadCode(";
        $jsonStringEnd = ", dconfig.queryStringParamCaId);";

        if($deviceType === $DEVICE_TYPE_ID_DESKTOP){
            $deviceName = $DEVICE_TYPE_NAME_DESKTOP;
        }else{
            $deviceName = $DEVICE_TYPE_NAME_MOBILE;
        }

        foreach($utilizedAdUnitObjectsArray as $utilizedAdUnitObjects){
            foreach ($utilizedAdUnitObjects as $utilizedAdUnitObject) {
                $adUnitObjectPageArray[] = [
                    'network_code' => $utilizedAdUnitObject['ad_unit_info']->network_code,
                    'parent_ad_unit_code' => $utilizedAdUnitObject['ad_unit_info']->parent_ad_unit_code,
                    'ad_unit_name' => $utilizedAdUnitObject['ad_unit_info']->ad_unit_name,
                    'ad_unit_name_dimensions' => json_decode($utilizedAdUnitObject['ad_unit_info']->ad_unit_name_dimensions),
                    'ad_unit_div_id' =>$utilizedAdUnitObject['ad_unit_info']->ad_unit_div_id
                ];
            }
        }

        $fileContent = $jsonStringStart.json_encode($adUnitObjectPageArray).$jsonStringEnd;
        $global = new InDefineGlobals();

        $VersionMapping = new InVersionMapping($postVariationId);

        $fileLocationAndName = $global::AUTOMATED_SCRIPT_FILENAME_PREFIX_PRE_HEADCODE."-".$deviceName."-".$postVariationId;
        file_put_contents(plugin_dir_path( dirname( __FILE__ ) ).$global::AUTOMATED_SCRIPT_ROOT.$VersionMapping->populateScriptVersionsMapping($fileLocationAndName, $global::AUTOMATED_SCRIPT_FILENAME_PREFIX_PRE_HEADCODE.'-'.$deviceName."-".$postVariationId), '');
        $fileLocationAndName = $global::AUTOMATED_SCRIPT_FILENAME_PREFIX_POST_HEADCODE."-".$deviceName."-".$postVariationId;
        file_put_contents(plugin_dir_path( dirname( __FILE__ ) ).$global::AUTOMATED_SCRIPT_ROOT.$VersionMapping->populateScriptVersionsMapping($fileLocationAndName, $global::AUTOMATED_SCRIPT_FILENAME_PREFIX_POST_HEADCODE.'-'.$deviceName."-".$postVariationId), $fileContent);
        $fileLocationAndName = $global::AUTOMATED_SCRIPT_FILENAME_PREFIX_PRE_FOOTERCODE."-".$deviceName."-".$postVariationId;
        file_put_contents(plugin_dir_path( dirname( __FILE__ ) ).$global::AUTOMATED_SCRIPT_ROOT.$VersionMapping->populateScriptVersionsMapping($fileLocationAndName, $global::AUTOMATED_SCRIPT_FILENAME_PREFIX_PRE_FOOTERCODE.'-'.$deviceName."-".$postVariationId), '');
        $fileLocationAndName = $global::AUTOMATED_SCRIPT_FILENAME_PREFIX_POST_FOOTERCODE."-".$deviceName."-".$postVariationId;
        file_put_contents(plugin_dir_path( dirname( __FILE__ ) ).$global::AUTOMATED_SCRIPT_ROOT.$VersionMapping->populateScriptVersionsMapping($fileLocationAndName, $global::AUTOMATED_SCRIPT_FILENAME_PREFIX_POST_FOOTERCODE.'-'.$deviceName."-".$postVariationId), '');

        return true;

    }


    public function generatePosts($InPostConfigArray){
        global $wpdb;

        $global = new InDefineGlobals();
        $DEVICE_TYPE_ID_DESKTOP = $global::DEVICE_TYPE_ID_DESKTOP;
        $DEVICE_TYPE_ID_MOBILE = $global::DEVICE_TYPE_ID_MOBILE;

        foreach($InPostConfigArray as $postConfigObject) {
            $dbConfigColumnValues = [];
            $dbConfigColumnValues['postId'] = $postConfigObject->getPostId();
            $dbConfigColumnValues['postSlug'] = str_replace('\\', '', $postConfigObject->getPostSlug());
            $dbConfigColumnValues['advTemplateIds'] = $postConfigObject->getadvTemplateIds();
            $dbConfigColumnValues['postTitle'] = str_replace('\\', '', $postConfigObject->getPostTitle());
            $dbConfigColumnValues['head_scripts'] = $postConfigObject->getHeadScripts();
            $dbConfigColumnValues['footer_scripts'] = $postConfigObject->getFooterScripts();
            $dbConfigColumnValues['body_pages'] = $postConfigObject->getBodyPages();
            $dbConfigColumnValues['featuredImageId'] = $postConfigObject->getfeaturedImageId();
            $dbConfigColumnValues['body_page_template_id'] = $postConfigObject->getPageTemplateId();

            $postBodyDesktopGenerateResult = $this->constructPostBodyHtml($postConfigObject, $DEVICE_TYPE_ID_DESKTOP);
            $postBodyMobileGenerateResult = $this->constructPostBodyHtml($postConfigObject, $DEVICE_TYPE_ID_MOBILE);
            $headHtml = $this->constructPostHeadScriptsHtml($postConfigObject, $DEVICE_TYPE_ID_DESKTOP, $postConfigObject->getVariationId());
            //TODO: Add 'automated_headscripts' to "$headHtml"

            $dbDataArray = array(
                'head_html' => $headHtml,
                'footer_html' => $this->constructPostHeadScriptsHtml($postConfigObject, $DEVICE_TYPE_ID_MOBILE),
                'body_wp_html_code' => $postBodyDesktopGenerateResult['body_html'],
                'body_wp_html_code_mobile' => $postBodyMobileGenerateResult['body_html'],
                'post_configuration' => json_encode($dbConfigColumnValues),
                'post_id' => $dbConfigColumnValues['postId'],
                'variation_key' => $postConfigObject->generateKey(),
                'name' => $postConfigObject->getPostName()
            );

            if($postConfigObject->getVariationId() ===  ''){
                $wpdb->insert("in_post_variation", $dbDataArray);
                $ids[] = $wpdb->insert_id;
            }else{
                $id = $postConfigObject->getVariationId();
                $wpdb->update("in_post_variation", $dbDataArray,array('id'=>$id));
                $ids[] = $id;
            }
        }
        return $ids;
    }
}

/*
 * TESTING CODE - SHOULD BE REMOVED
 */

if (false) {

    $PostGenerator = new InPostGenerator();
    $InPostConfigArray = [];

    $postConfigObject = new InPostConfig();
    $postConfigObject->addHeadScripts([
        [InPostConfig::$PARAM_HEAD_SCRIPT_ID => 4, InPostConfig::$PARAM_HEAD_PARAMETERS => ['V101', 'V2', 3]],
        [InPostConfig::$PARAM_HEAD_SCRIPT_ID => 5, InPostConfig::$PARAM_HEAD_PARAMETERS => ['V102', 'V2', 23]]
    ]);
    $postConfigObject->addFooterScripts([
        [InPostConfig::$PARAM_HEAD_SCRIPT_ID => 4, InPostConfig::$PARAM_HEAD_PARAMETERS => ['V101', 'V2', 3]],
        [InPostConfig::$PARAM_HEAD_SCRIPT_ID => 5, InPostConfig::$PARAM_HEAD_PARAMETERS => ['V102', 'V2', 23]]
    ]);

    $postConfigObject->addBodyPages([272, 274, 281]);
    $postConfigObject->setPostSlug("Slug");
    $postConfigObject->setPostTitle("Post Title");
    $postConfigObject->setPageTemplateId(1);
    $postConfigObject->setPostId(80);
    $postConfigObject->setadvTemplateIds(["pre_image_ad_template_id"=>1,"page_end_ad_template_id"=>2]);

    $postConfigObject = new InPostConfig();
    $postConfigObject->addHeadScripts([
        [InPostConfig::$PARAM_HEAD_SCRIPT_ID => 6, InPostConfig::$PARAM_HEAD_PARAMETERS => ['V103', 2, 'V3.1']],
        [InPostConfig::$PARAM_HEAD_SCRIPT_ID => 7, InPostConfig::$PARAM_HEAD_PARAMETERS => ['V104', 'V2', 'V3234.1']]
    ]);
    $postConfigObject->addBodyPages([46, 47, 48]);
    $postConfigObject->setPostSlug("Slug");
    $postConfigObject->setPostTitle("Post Title");
    $postConfigObject->setPageTemplateId(2);
    $postConfigObject->setPostId(80);
    $postConfigObject->setadvTemplateIds(["pre_image_ad_template_id"=>1,"page_end_ad_template_id"=>2]);
    $PostGenerator->generatePosts($InPostConfigArray);
}
// END OF TESTING CODE
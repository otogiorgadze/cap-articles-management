<?php
include('InPostConfig.php');
include('InPostGenerator.php');
include('InPostRetriver.php');
include('InABTestingPost.php');

$inPostRetriever = new InPostRetriever();

$global = new InDefineGlobals();
$HTML_REMARK_HEAD_SCRIPT_START = $global::HTML_REMARK_HEAD_SCRIPT_START;
$HTML_REMARK_HEAD_SCRIPT_END = $global::HTML_REMARK_HEAD_SCRIPT_END;
$HTML_REMARK_Footer_SCRIPT_START = $global::HTML_REMARK_FOOTER_SCRIPT_START;
$HTML_REMARK_Footer_SCRIPT_END = $global::HTML_REMARK_FOOTER_SCRIPT_END;
$HTML_REMARK_BODY_CONTENT_START = $global::HTML_REMARK_BODY_CONTENT_START;
$HTML_REMARK_BODY_CONTENT_END = $global::HTML_REMARK_BODY_CONTENT_END;



function getInPostVariationHead($postId)
{
    global $inPostRetriever;
    return $inPostRetriever->getPostHead($postId);
}

function getInPostVariationFooter($postId)
{
    global $inPostRetriever;
    return $inPostRetriever->getPostFooter($postId);
}

function getInPostVariationBody($postId)
{
    global $inPostRetriever;
    return $inPostRetriever->getPostBody($postId);
}
function getInPostVariationTitle($postId)
{
    global $inPostRetriever;
    $postTitle = $inPostRetriever->getPostTitle($postId);
    if(empty($postTitle)){
        $postTitle = $inPostRetriever->getPostOpeningPage($postId)->post_title;
    }
    return $postTitle;
}
function getInPostVariationSlug($postId)
{
    global $inPostRetriever;
    return $inPostRetriever->getPostSlug($postId);
}

function in_content_filter($content) {
    global $post, $HTML_REMARK_BODY_CONTENT_START, $HTML_REMARK_BODY_CONTENT_END;

    if (is_single() and get_post_type() === 'dfncommpost') {
        $postId = $post->ID;// DEBUG - Use $post->ID;
        $content = $HTML_REMARK_BODY_CONTENT_START.stripslashes(getInPostVariationBody($postId)).$HTML_REMARK_BODY_CONTENT_END;
    }
    return do_blocks($content);
}
add_filter('the_content', 'in_content_filter', 9);


function in_title_filter($content) {
    global $post;
    $postId = $post->ID;// DEBUG - Use $post->ID;
    if (is_single() and get_post_type() ==='dfncommpost' and in_the_loop()) {
        $content = getInPostVariationTitle($postId);
    }
    return stripslashes($content);
}
add_filter('the_title', 'in_title_filter');


function in_wp_title_filter($content) {
    global $post;
    if (is_single() and get_post_type() ==='dfncommpost') {
        $postId = $post->ID;// DEBUG - Use $post->ID;
        $content = getInPostVariationTitle($postId);
    }
    return $content;
}
add_filter('wp_title', 'in_wp_title_filter', 99999);


add_filter( 'pre_get_document_title', function( $title ){
    global $post;

    if (is_single() and get_post_type() ==='dfncommpost') {
        $postId = $post->ID;// DEBUG - Use $post->ID;
        $title = getInPostVariationTitle($postId);
    }
    return $title;
}, 999, 1 );

//echo getInPostVariationSlug($postId);
function head_script_init(){
    global $post, $HTML_REMARK_HEAD_SCRIPT_START, $HTML_REMARK_HEAD_SCRIPT_END;

    echo $custom_css = "<style>aside#sidebar{display:none !important;}</style>";
    if (is_single() and get_post_type() === 'dfncommpost') {
        $postId = $post->ID;// DEBUG - Use $post->ID;

//        echo "<!-- INT-DEBUG: ".stripslashes(getInPostVariationHead($postId))." INT-DEBUG-END -->";
        echo $HTML_REMARK_HEAD_SCRIPT_START.stripslashes(getInPostVariationHead($postId)).$HTML_REMARK_HEAD_SCRIPT_END;
    }
}
add_action( 'wp_head', 'head_script_init' );


function add_overlay_ad_style() {
    wp_enqueue_style( 'dfn', plugin_dir_url(__FILE__) . '../css/dfn.css' );



}
add_action( 'wp_head', 'add_overlay_ad_style' );

function Footer_script_init(){
    global $post, $HTML_REMARK_Footer_SCRIPT_START, $HTML_REMARK_Footer_SCRIPT_END;

    if (is_single() and get_post_type() === 'dfncommpost') {
        $postId = $post->ID;// DEBUG - Use $post->ID;

        echo $HTML_REMARK_Footer_SCRIPT_START.stripslashes(getInPostVariationFooter($postId)).$HTML_REMARK_Footer_SCRIPT_END;
    }
}
add_action( 'wp_footer', 'Footer_script_init' );

//
//function remove_custom_post_type_slug( $post_link, $post ) {
//    $postId = $post->ID;// DEBUG - Use $post->ID;
//    $slug = getInPostVariationSlug($postId);
//    if ( 'dfncommpost' === $post->post_type && 'publish' === $post->post_status ) {
//        $post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );
//        $post_link = str_replace( $post->post_name, $slug, $post_link );
//    }
//    return $post_link;
//}
//add_filter( 'post_type_link', 'remove_custom_post_type_slug', 10, 2 );
//
//function remove_custom_post_type_slug( $post_link, $post ) {
//    if ( 'dfncommpost' === $post->post_type && 'publish' === $post->post_status ) {
//        $post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );
//    }
//    return $post_link;
//}
//add_filter( 'post_type_link', 'remove_custom_post_type_slug', 10, 99 );

//function add_custom_post_type_post_name_to_main_query( $query ) {
//    global $post;
//    $postId = $post->ID;// DEBUG - Use $post->ID;
//    $slug = getInPostVariationSlug($postId);
//
//
////    print_r($query->query['dfncommpost']);
//    // Return if this is not the main query.
//    if ( ! $query->is_main_query() ) {
//        return;
//    }
//    // Return if this query doesn't match our very specific rewrite rule.
//    if ( ! isset( $query->query['page'] ) || 2 !== count( $query->query ) ) {
//        return;
//    }
//    // Return if we're not querying based on the post name.
//    if ( empty( $query->query['name'] ) ) {
//        return;
//    }
////    print_r($query->query['name']);
//
////    $query->set( 'name', $slug );
//
////    print_r($query->query['name']);
//
//    // Add CPT to the list of post types WP will include when it queries based on the post name.
//    $query->set( 'post_type', array( 'post', 'page', 'dfncommpost' ) );
//}
//add_action( 'pre_get_posts', 'add_custom_post_type_post_name_to_main_query', 99 );
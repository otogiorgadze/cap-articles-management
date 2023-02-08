<?php
/*
Plugin Name: Intellecto CAP Admin console
Description: Plugin to administrate the CAP servers' content articles
Version: 1
Author: Intellecto
Textdomain: intellecto-cap-admin
License: PRIVATE!
*/
$table_name_post_page = 'in_page_content';
$table_name_opening_page = 'in_opening_page_content';
$table_name_template_ad = 'in_template_ad';
$table_name_post_variations = 'in_post_variation';
$table_name_post_variations_template = 'in_post_variation_template';
$table_name_adUnits = 'in_adunit';
$table_name_head_scripts = 'in_template_script';
$table_name_page_template = 'in_template_body_page';
$table_in_template_body_page = 'in_template_body_pagstage1e';
$table_in_template_script = 'in_template_script';
$table_in_template_ad = 'in_template_ad';
$table_in_ad_units = 'in_adunit';
$STRING_TAG = '"';
$EDITING_ROLE_TYPES_ARRAY = [ 'author', 'administrator', 'editor' ];
$pageParameter = 'page_id';

include plugin_dir_path(__FILE__) . "cap-lib/lib-file1.php";


function in_on_activate() {

    global $wpdb;
    WP_Filesystem();
    global $wp_filesystem;

    global $table_name_post_page;

    $charset_collate = $wpdb->get_charset_collate();


    $root = plugin_dir_path( __FILE__ );
    $dir = "/deploy/sql/v".get_plugin_data( __FILE__ )['Version'];
    $files = list_files($root.$dir);
    usort($files, 'version_compare');

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );


    foreach ($files as $file){
        if(!is_readable($file)) {
            echo 'File not found or not readable '.$file;
        }
        $sql_insert = $wp_filesystem->get_contents( $file );
        dbDelta($sql_insert);
    }
}
register_activation_hook( __FILE__, 'in_on_activate' );


function dfn_register_dfncommpost_post_type() {

    $labels = array(
        'name' => __( 'DfnCommposts', 'dfncommpost' ),
        'singular_name' => __( 'DfnCommpost', 'dfncommpost' ),
        'add_new' => __( 'New DfnCommpost', 'dfncommpost' ),
        'add_new_item' => __( 'Add New DfnCommpost', 'dfncommpost' ),
        'edit_item' => __( 'Edit DfnCommpost', 'dfncommpost' ),
        'new_item' => __( 'New DfnCommpost', 'dfncommpost' ),
        'view_item' => __( 'View DfnCommposts', 'dfncommpost' ),
        'all_items' => __('All dfncommposts', 'dfncommpost' ),
        'search_items' => __( 'Search DfnCommposts', 'dfncommpost' ),
        'not_found' =>  __( 'No DfnCommposts Found', 'dfncommpost' ),
        'not_found_in_trash' => __( 'No DfnCommposts found in Trash', 'dfncommpost' ),
    );


    $args = array(
        'labels' => $labels,
        'has_archive' => true,
        'public' => true,//enables the post type to be included in search results and in custom queries.
        'hierarchical' => false,//If you set this to true, then the post type will behave like pages, with a hierarchy possible and parent and child posts of any post of your post type. If you set it to false, it’ll behave like posts, without a hierarchy.
        // 'query_var' => true,// from old
        'editor',// from old
        'show_in_rest' => true,// from old
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            'custom-fields',
            'thumbnail',
            'page-attributes',

            'author', // post author
            'comments', // post comments
            'revisions', // post revisions
            'template', // post templateß
        ),

        'taxonomies' => array('category'),
    );

    register_post_type( 'dfncommpost', $args );
}
add_action( 'init', 'dfn_register_dfncommpost_post_type' );



add_action( 'admin_menu', 'in_plugin_menu' );

function in_plugin_menu() {
    add_menu_page(__('Intellecto','intellecto'), __('Intellecto','intellecto'), 'manage_options', 'Intellecto', 'in_toplevel_page', null, 9);
    add_submenu_page(
        'Intellecto', // Third party plugin Slug
        'Posts base content',
        'Posts base content',
        'manage_options',
        'Intellecto',
        'in_toplevel_page'
    );
    add_submenu_page(
        'Intellecto', // Third party plugin Slug
        'Post Variations',
        'Post Variations',
        'manage_options',
        'post_variations',
        'post_variations'
    );
    add_submenu_page(
        'Intellecto', // Third party plugin Slug
        'Edit scripts',
        'Edit scripts',
        'manage_options',
        'post_variations&edit=head_script_template&id=all',
        'post_variations&edit=head_script_template&id=all'
    );
    add_submenu_page(
        'Intellecto', // Third party plugin Slug
        'Edit Ad Templates',
        'Edit Ad Templates',
        'manage_options',
        'post_variations&edit=ad_template&id=all',
        'post_variations&edit=ad_template&id=all'
    );
    add_submenu_page(
        'Intellecto', // Third party plugin Slug
        'Edit Page Templates',
        'Edit Page Templates',
        'manage_options',
        'post_variations&edit=content_template&id=all',
        'post_variations&edit=content_template&id=all'
    );
}
function in_scripts( $hook ){


    if( $_GET["page"] === "Intellecto" || $_GET["page"] === "post_variations"){
        wp_register_style('custom_wp_admin_css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css');
        wp_enqueue_style('custom_wp_admin_css');

        wp_register_style('custom_wp_admin_cssb', plugins_url('css/intellecto.css', __FILE__));
        wp_enqueue_style('custom_wp_admin_cssb');
        wp_enqueue_script ( 'jquery_cdn', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js' );
    }

    if ( $_GET["page"] === "Intellecto") {
        wp_enqueue_script ( 'custom_jquery', plugins_url( 'js/jquery_js.js', __FILE__ ) );
    }

    if( $_GET['page'] === "post_variations"){
        wp_enqueue_script ( 'custom_jquery', plugins_url( 'js/variation.js', __FILE__ ) );
        wp_enqueue_script ( 'custom_jquery', plugins_url( 'sortable/sort-list.js', __FILE__ ) );
        wp_register_style('custom_wp_admin_cssb', plugins_url('css/sort-list.css', __FILE__));
        wp_enqueue_style('custom_wp_admin_cssb');
    }
}
add_action('admin_enqueue_scripts', 'in_scripts');
function post_variations(){
    global $wpdb, $table_name_post_variations, $table_name_template_ad, $table_name_opening_page, $table_name_page_template, $table_name_head_scripts, $table_in_ad_units;





    $result = $wpdb->get_results("SELECT * FROM $table_name_opening_page WHERE post_status='active' GROUP BY post_id ORDER BY post_id DESC");
    $result_template = $wpdb->get_results("SELECT * FROM $table_name_page_template");
    $result_scripts_header = $wpdb->get_results("SELECT * FROM $table_name_head_scripts WHERE type='1'");
    $result_scripts_footer = $wpdb->get_results("SELECT * FROM $table_name_head_scripts WHERE type='2'");

    $postId = ((!empty($_GET['varId']))) ? $_GET['varId'] : 0;

    $variationResult = $wpdb->get_results("SELECT * FROM $table_name_post_variations WHERE post_id=$postId");
    $ads = $wpdb->get_results("SELECT * FROM $table_name_template_ad ORDER BY id DESC");



    wp_enqueue_media();

    echo '
        <div class="container bg-white">
            <div class="row">
                <div class="col-12">
                <a href="#"><button class="btn pop_right_top" type="button">Variation Templates</button></a>
                    <form action="#" method="post" class="variations">
                    <div class="variation_pop_up" style="display: none">
                        <div class="card">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg close_pop" viewBox="0 0 16 16">
                              <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>
                            </svg>
                            <div class="spinner-box">
                              <div class="blue-orbit leo">
                              </div>
                              <div class="green-orbit leo">
                              </div>
                              <div class="red-orbit leo">
                              </div>
                              <div class="white-orbit w1 leo">
                              </div><div class="white-orbit w2 leo">
                              </div><div class="white-orbit w3 leo">
                              </div>
                            </div>
                           <!--- <button class="btn btn-info text-white font-weight-bold">TITLE</button> -->
                        </div>                
                    </div>
                    ';
    if(!$_GET['edit']) {
        echo '<div class="form-group shadow select">
                        <select class="form-control" name="post_id">
                          <option>Select Post ID</option>
                          ';
        foreach ($result as $option) {
            $select_title = $wpdb->get_results("SELECT * FROM $table_name_post_variations WHERE post_id=$option->post_id");
            $title = json_decode($select_title[0]->post_configuration, true);
            echo '<option value="' . $option->post_id . '">' . $option->post_indexing_name . '</option>';
        }
//        VARIATION TEMPLATE NEEDS TO BE GENERATED HERE
//        RESTRUCTURING NEEDED
        echo '
                        </select>
                      </div>
                    
                    <! -- Post Variation -->
                      <div class="post_variation d-none">
                        <input type="hidden" name="var_id">
                      <h4 class="float-left">Post Variation <span class="name"></span><span class="id"></span><span class="status"></span></h4>
                      <button class="btn btn-success save_variation_template" type="button">Save Template</button>
                      <button class="btn btn-danger remove_variation" type="button">Remove Variation</button>
                      <button class="btn btn-success saveVariation" disabled type="button">Save Variation</button>
                      <a href="#" class="d-inline-block relevant_post"><button class="btn btn-light" type="button">Edit Relevant Post Base</button></a>
                        <div class="heading_config mt-5">
                           <label for="exampleFormControlSelect">Post Config</label>
                           <div class="row">
                                <div class="col-12">
                                <label class="mt-3">Page Content Template</label>
                                <select name="in_template_body_page" class="w-100 shadow mt-3">
                                   <option>Select Page Content Template</option>
                          ';
        foreach ($result_template as $option) {

            echo '<option value="' . $option->id . '">' . $option->id . ' - ' . $option->name . '</option>';
        }
        echo '
                        </select>
                        <a href="?page=post_variations&edit=content_template" class="changeUrl"><button type="button" class="content_template d-block my-3 btn btn-success">Edit</button></a>
                                    <label class="mt-3">Name</label>
                                    <input required type="text" name="name">
                                    <label>Post Title</label>
                                    <input required type="text" name="post_title">
                                    <label class="d-none">Post slug</label>
                                   <!-- <input required type="text" class="d-none" name="post_slug"> -->
                                    <div class="w-100 py-5">
                                    <label class="d-block">Post Featured Image</label>
                                    <button class="upload_image btn btn-secondary mt-3" type="button">Select/Upload</button>
                                    <img class="imgPrev" ng-src="{{imagePreview}}" >
                                    <input type="hidden" name="image[]">
                                    </div>
                                    <div class="w-100 mt-2">
                                    <label class="w-100 font-weight-bold mb-2">Desktop</label>
                                    <label>Pre-image Ad template-ID</label>
                                    <select name="pre_image_ad_template_id">
                                    <option value="0">Select ads template</option>
            ';
        foreach ($ads as $ad) {
            echo '<option value="' . $ad->id . '">' . $ad->name . '</option>';
        }
        echo '                    </select>
                                        <a href="?page=post_variations&edit=ad_template" class="changeUrl"><button type="button" class="page_end_ad d-block btn btn-success">Edit Pre-image Ad Template</button></a>
                    </div>
                                    <div class="w-100 mt-2">
                                     <label>Page-end Ad template-ID</label>
                    <select name="page_end_ad_template_id">
              <option value="0">Select ads template</option>
            ';
        foreach ($ads as $ad) {
            echo '<option value="' . $ad->id . '">' . $ad->name . '</option>';
        }
        echo '                    </select>
                                        <a href="?page=post_variations&edit=ad_template" class="changeUrl"><button type="button" class="page_end_ad d-block btn btn-success">Edit Page-end Ad Template</button></a>
                                </div>
                                <div class="w-100 mt-2">
                                    <label class="w-100 font-weight-bold mb-2">Mobile</label>
                                    <label>Pre-image Ad template-ID</label>
                                    <select name="pre_image_ad_template_id_mobile">
                                    <option value="0">Select ads template</option>
            ';
        foreach ($ads as $ad) {
            echo '<option value="' . $ad->id . '">' . $ad->name . '</option>';
        }
        echo '                    </select>
                                        <a href="?page=post_variations&edit=ad_template" class="changeUrl"><button type="button" class="page_end_ad d-block btn btn-success">Edit Pre-image Ad Template</button></a>
                    </div>
                                    <div class="w-100 mt-2">
                                     <label>Page-end Ad template-ID</label>
                    <select name="page_end_ad_template_id_mobile">
              <option value="0">Select ads template</option>
            ';
        foreach ($ads as $ad) {
            echo '<option value="' . $ad->id . '">' . $ad->name . '</option>';
        }
        echo '                    </select>
                                        <a href="?page=post_variations&edit=ad_template" class="changeUrl"><button type="button" class="page_end_ad d-block btn btn-success">Edit Page-end Ad Template</button></a>
                                </div>
                                </div>
                            </div>
                        </div>
                        <! -- Head Scripts -->
                      <div class="heading inactive">
                       <label for="exampleFormControlSelect2">Post head scripts (by execution order)</label>
                       <! -- Select Head Script -->
                      <div class="form-group shadow my-3 dom_elm ">
                        <div class="row">
                          <div class="col-9">

                          <select class="form-control" name="head_script[]">
                              <option>Select head script</option>';

        foreach ($result_scripts_header as $script) {
            echo '<option value="' . $script->id . '">' . $script->id . ' - ' . $script->name . '</option>';
        }
        echo '
                            </select>
                          </div>
                          <div class="col-3">
                          <a href="?page=post_variations&edit=head_script_template" class="changeUrl"><button type="button" class="head_script_template btn btn-success float_smooth">Edit</button></a>
                          <button class="btn btn-danger remove_head" type="button">Remove Script</button>
                          </div>
                        </div>
                        <! -- Select Head Script -->
                        <div class="row block_input inactive mt-2">
                            <div class="col-6 flex-center">
                                <input type="text" name="input_value[]" placeholder="Enter input value">
                            </div>
                            <div class="col-4 st_select">
                                <label>Input data type</label>
                                <select class="form-control" name="data_type">
                                    <option value="0">String</option>
                                    <option value="1">Integer</option>
                                    <option value="2">JS Code</option>
                                </select>
                            </div>
                            <div class="col-2">
                               <button class="btn btn-danger remove mt-4" type="button">Remove Value</button>
                            </div>
                        </div>
                        <! -- Select Head Script -->
                          </div>
                          <button class="btn btn-primary mx-auto d-block add_block mt-4" type="button">Add Script</button>
                      </div>
                      <! -- Select Head Script -->
                      <! -- Footer script -->
                      <div class="heading_bottom inactive">
                       <label for="exampleFormControlSelect2">Post footer script (by execution order)</label>
                       <! -- Select Footer script -->
                      <div class="form-group shadow my-3 dom_elm ">
                        <div class="row">
                          <div class="col-9">

                          <select class="form-control" name="footer_script[]">
                              <option>Select footer script</option>';
        foreach ($result_scripts_footer as $script) {
            echo '<option value="' . $script->id . '">' . $script->id . ' - ' . $script->name . '</option>';
        }
        echo '
                            </select>
                          </div>
                          <div class="col-3">
                          <a href="?page=post_variations&edit=head_script_template" class="changeUrl"><button type="button" class="head_script_template btn btn-success float_smooth">Edit</button></a>
                          <button class="btn btn-danger remove_head" type="button">Remove Script</button>
                          </div>
                        </div>
                        <! -- Select Head Script -->
                        <div class="row block_input inactive mt-2">
                            <div class="col-6 flex-center">
                                <input type="text" name="input_value[]" placeholder="Enter input value">
                            </div>
                            <div class="col-4 st_select">
                                <label>Input data type</label>
                                <select class="form-control" name="data_type">
                                    <option value="0">String</option>
                                    <option value="1">Integer</option>
                                    <option value="2">JS Code</option>
                                </select>
                            </div>
                            <div class="col-2">
                               <button class="btn btn-danger remove mt-4" type="button">Remove Value</button>
                            </div>
                        </div>
                        <! -- Select Footer script -->
                      </div>
                      <! -- Select Footer script -->
                      <button class="btn btn-primary mx-auto d-block add_block mt-4" type="button">Add Script</button>
                    </div>
                    <! -- Head Scripts -->

                    <div class="heading_content">
                           <label for="exampleFormControlSelect2">Post pages (ordered)</label>
                         <div class="row selection mt-2">
                         <div class="col-8 d-flex">
                         <select name="post_content" multiple>
                            <option value="">Select Page</option>
                         </select>
                         </div>
                         <div class="col-4">
                         <button class="btn btn-primary mx-auto d-block add_selection w-100" type="button">Add Page</button>
                        </div>
                       </div>
                    </div>
                    <! -- Post Variation -->';
    }
    echo '</form>
                </div>';
    if(!$_GET['edit']) {
        echo '<button class="btn btn-primary mx-auto d-block add_variation" type="button">Add Post Variation</button>';
    }else{
        if($_GET['edit'] === 'ad_units'){
            if(isset($_GET['drop']) && !empty($_GET['drop'])){
                $wpdb->delete( $table_in_ad_units, array( 'id' => $_GET['drop'] ) );
            }
            if($_POST){
                foreach($_POST['id'] as $id => $dataId) {
                    $arrayQuery = [];
                    foreach ($_POST as $key=>$post){

//                        SET id, width, height AS GLOBAL VARIABLES

                        if($key !== 'id') {
                            $arrayQuery[] = [$key => $post[$id]];
                        }
                        if($key === 'width') {
                            $width = $post[$id];
                        }
                        if($key === 'height') {
                            $height = $post[$id];
                        }
                    }
                    $arrayQuery[] = ['ad_unit_name_dimensions' => json_encode([intval($width), intval($height)])];
                    $arrayQueryMerged = call_user_func_array('array_merge',$arrayQuery);
                    if(empty($id)){
                        $wpdb->insert($table_in_ad_units, $arrayQueryMerged);
                    }else{
                        $wpdb->update($table_in_ad_units, $arrayQueryMerged, array('id'=>$dataId));
                    }
                }
            }
            $queryIdentifier = "";
            if(isset($_GET['type']) && !empty($_GET['type'])){
                $queryIdentifier = "WHERE group_name='".$_GET['type']."'";
            }
            $adUnitsGroupedNamesArray = $wpdb->get_results("SELECT * FROM $table_in_ad_units GROUP BY group_name");
            $adUnitsArray = $wpdb->get_results("SELECT * FROM $table_in_ad_units $queryIdentifier ORDER BY id");
            echo "
                        <form action=' " .$_SERVER['PHP_SELF'] . "?page=post_variations&edit=ad_units".$_GET['type']."' method='post'>

                        <button type='submit' class='btn btn-success'>Save</button>
                        <button type='button' class='btn btn-success addCol'>Add New</button>";
            echo"    <select class='editGroupType w-100 my-2'>";
            if(!empty($_GET['type'])){
                $select = "selected";
            }else{
                $select = "";
            }
            echo "<option ".$select." value=''>All</option>";
            $select = "";
            foreach($adUnitsGroupedNamesArray as $adUnitsGroupedNames){
                if($_GET['type'] === $adUnitsGroupedNames->group_name){
                    $select = "selected";
                }
                echo "<option ".$select." value='".$adUnitsGroupedNames->group_name."'>".$adUnitsGroupedNames->group_name."</option>";
            }
            echo "</select>
                        <table>";
            foreach ($adUnitsArray as $adUnits){
                echo "<tr>";
                $id = $adUnits->id;
                foreach ($adUnits as $key=>$adUnit){
                    if($key !== 'id' && $key !== 'ad_unit_name_dimensions'){
                        echo "<td><input type='text' name='" . $key . "[]' value='" . $adUnit . "'></td>";
                    }elseif($key !== 'ad_unit_name_dimensions'){
                        echo "<td><input type='hidden' name='" . $key . "[]' value='" . $adUnit . "'></td>";
                    }
                }
                echo "<td><a href=' " .$_SERVER['PHP_SELF'] . "?page=post_variations&edit=ad_units&drop=".$id."'><button class='btn btn-danger'>DELETE</button></a></td>";
                echo "</tr>";
            }
            echo "
</table>
<button type='submit' class='btn btn-success'>Save</button>
<button type='button' class='btn btn-success addCol'>Add New</button>
</form>";
        }else {
            global $table_in_template_body_page, $table_in_template_script, $table_in_template_ad;
            if(!empty($_GET['id'])){
                $idIdent = "&id=" . $_GET['id'];
            }
            echo '<form method="post" class="editMode shadow p-3" action="' . $_SERVER['PHP_SELF'] . '?page=post_variations&edit=' . $_GET['edit'] . $idIdent . '&var=' . $_GET['var'] . '">';
            if ($_GET['edit'] === 'content_template') {
                $tableName = $table_in_template_body_page;
                $colName = "body_page_html";
            }
            if ($_GET['edit'] === 'ad_template') {
                $tableName = $table_in_template_ad;
                $colName = "ad_container_html";
            }
            if ($_GET['edit'] === 'head_script_template') {
                $tableName = $table_in_template_script;
                $colName = "head_script_html_template";
            }

            if (isset($_POST['type'])) {

                foreach ($_POST['name'] as $index => $data) {

                    if ($index == $_POST['temp'] || empty($_POST['temp'])) {
                        $name = $data;
                        $html = $_POST['html'][$index];
                        if(isset($_POST['scriptType'][$index])){

                            $insertOrUpdate = array(
                                'name' => $name,
                                $colName => $html,
                                'type' => $_POST['scriptType'][$index]
                            );
                        }
                        else {
                            $insertOrUpdate = array(
                                'name' => $name,
                                $colName => $html
                            );
                        }

                        if ($index === "x") {
                            $wpdb->insert($tableName, $insertOrUpdate,
                                array('%s', '%s', '%s')
                            );
                        } else {
                            $wpdb->update($tableName, $insertOrUpdate, array('id' => $index),
                                array('%s', '%s', '%s')
                            );
                        }
                        convertDbDataToPostConfigs();
                    }
                }
            }
            if (isset($_GET['edit'])) {
                if (isset($_GET['delete'])) {
                    $wpdb->delete($tableName, array('id' => $_GET['delete']));
                }
            }
            if (isset($_GET['id']) && $_GET['id'] !== 'all') {
                $result = $wpdb->get_results("SELECT * FROM $tableName WHERE id = $_GET[id] ORDER BY id DESC");
            } else {
                if(!empty($_GET['script_type'])){
                    $condition = "";
                    if($_GET['script_type'] === 'header'){
                        $condition = "WHERE type = '1'";
                    }elseif($_GET['script_type'] === 'footer'){
                        $condition = "WHERE type = '2'";
                    }
                }
                $result = $wpdb->get_results("SELECT * FROM $tableName $condition ORDER BY id DESC");
            }

            $selection = $wpdb->get_results("SELECT * FROM $tableName GROUP BY id ORDER BY id DESC");
            echo '<label>Template</label>';
            echo '<select class="choose_id w-100 mb-2">';
            if ($_GET['id'] === 'all') {
                echo '<option selected value="all">ALL</option>';
            } else {
                echo '<option value="all">ALL</option>';
            }
            foreach ($selection as $select) {
                $selected = "";
                if ($select->id === $_GET['id']) {
                    $selected = "selected";
                }
                echo '<option ' . $selected . ' value="' . $select->id . '">' . $select->id . ' - ' . $select->name . '</option>';
            }
            echo '</select>';
            if($_GET['edit'] === 'head_script_template' && (!isset($_GET['id']) || $_GET['id'] === 'all')){
                echo '<select class="w-100 script_type">';
                $all = "";
                $header = "";
                $footer = "";
                if($_GET['script_type'] === 'all') {
                    $all = "selected";
                }elseif($_GET['script_type'] === 'header'){
                    $header = "selected";
                }elseif($_GET['script_type'] === 'footer'){
                    $footer = "selected";
                }
                echo '<option '.$all.' value="all">All Scripts</option>';
                echo '<option '.$header.' value="header">Header Script</option>';
                echo '<option '.$footer.' value="footer">Footer Script</option>';
                echo '</select>';
            }

            if (isset($_GET['edit'])) {


                foreach ($result as $row) {

                    $colHtml = $row->{$colName};
                    echo '<div class="cont_template">';
                    echo '<label class="w-100">Template Name</label><input class="my-2" value="' . $row->name . '" name="name[' . $row->id . ']" placeholder="Name">';
                    echo '<label class="w-100">Page HTML Template</label><textarea class="my-2" name="html[' . $row->id . ']">' . stripslashes($colHtml) . '</textarea>';
                    if ($_GET['edit'] === 'head_script_template') {
                        $type1 = "";
                        $type2 = "";
                        if($row->type === "1"){ $type1 = "selected"; }
                        else{ $type2 = "selected"; }
                        echo '<select name="scriptType['.$row->id.']" class="w-100 mb-4">';
                        echo '<option '.$type1.' value="1">Header Script</option>';
                        echo '<option '.$type2.' value="2">Footer Script</option>';
                        echo '</select>';
                    }
                    echo '<button type="submit" class="btn btn-success mb-3" name="all">Save All</button>';
                    echo '<button type="submit" class="btn btn-success mb-3 ml-2" name="temp" value="' . $row->id . '">Save</button>';
                    echo '<a href="' . $_SERVER['PHP_SELF'] . '?page=post_variations&edit=' . $_GET['edit'] . '&var='.$_GET['var'].'&delete=' . $row->id . '"><button class="btn btn-danger" type="button">Delete</button></a>';
                    echo '</div>';
                }
            }

            echo '<input type="hidden" name="type" value="' . $_GET['edit'] . '">';
            echo '</form>';
            echo '<div class="back text-center mx-auto"><button type="button" class="btn add_list btn-success mr-4 d-inline-block">Add</button><span class="px-2"></span>';
            if(!empty($_GET['var'])) {
                echo '<a href="admin.php?page=post_variations&var=' . $_GET['var'] . '">Back to Variation</a></div>';
            }

        }
    }
    echo'</div>
        </div>
    ';

}
function strip_param_from_url( $url, $param )
{
    $base_url = strtok($url, '?');              // Get the base URL
    $parsed_url = parse_url($url);              // Parse it
    $query = $parsed_url['query'];              // Get the query string
    parse_str( $query, $parameters );           // Convert Parameters into array
    unset( $parameters[$param] );               // Delete the one you want
    $new_query = http_build_query($parameters); // Rebuilt query string
    return $base_url.'?'.$new_query;            // Finally URL is ready
}
function convertDbDataToPostConfigs($postID = null){
    global $wpdb, $table_name_post_variations, $table_name_opening_page;
    $condition = "";
    if($postID !== null){
        $condition = "WHERE post_id='$postID'";
    }
    $variationsResult = $wpdb->get_results("SELECT * FROM $table_name_post_variations $condition");
    $openingPage = $wpdb->get_results("SELECT * FROM $table_name_opening_page $condition");

    $PostGenerator = new InPostGenerator();
    $InPostConfigArray = [];

    foreach($variationsResult as $result){

        $data = json_decode($result->post_configuration);
        $page_end_ad_template_id = $data->advTemplateIds->page_end_ad_template_id;
        $pre_image_ad_template_id = $data->advTemplateIds->pre_image_ad_template_id;

        $page_end_ad_template_id_mobile = $data->advTemplateIds->page_end_ad_template_id_mobile;
        $pre_image_ad_template_id_mobile = $data->advTemplateIds->pre_image_ad_template_id_mobile;
        $name = $result->name;
        $image = $data->featuredImageId;
        if(empty($image)){
            $image = $openingPage[0]->post_title;
        }
        $post_slug = $data->postSlug;
        $post_title = $data->postTitle;
        if(empty($post_title)){
            $post_title = $openingPage[0]->post_title;
        }
        $array_head_script = $data->head_scripts;
        $array_footer_script = $data->footer_scripts;
        $BodyPages = $data->body_pages;
        $PageTemplateId = $data->body_page_template_id;
        $PostId = $data->postId;
        $varId = $result->id;

        $array_head_script = json_decode(json_encode($array_head_script), true);
        $array_footer_script = json_decode(json_encode($array_footer_script), true);

//        UPDATED IN VER 1.1
//        MOVED TO ARRAY OBJECT BEFORE PASSING TO postConfigObject DIRECTLY

        $readyInsertIntoPostConfigObject[] = [
            'addHeadScripts' => $array_head_script,
            'addFooterScripts' => $array_footer_script,
            'addBodyPages' => $BodyPages,
            'setPostSlug' => $post_slug,
            'setPostTitle' => $post_title,
            'setPageTemplateId' => $PageTemplateId,
            'setPostId' => $PostId,
            'setVariationId' => $varId,
            'setPostName' => $name,
            'setfeaturedImageId' => intval($image),
            'setadvTemplateIds' => ["pre_image_ad_template_id"=>intval($pre_image_ad_template_id),"page_end_ad_template_id"=>intval($page_end_ad_template_id), "pre_image_ad_template_id_mobile"=>intval($pre_image_ad_template_id_mobile),"page_end_ad_template_id_mobile"=>intval($page_end_ad_template_id_mobile)]
        ];

    }

    if(isset($readyInsertIntoPostConfigObject)) {
        generatePostVariations($readyInsertIntoPostConfigObject);
    }

}
function in_toplevel_page()
{
    global $wpdb;
    global $table_name_post_page, $table_name_opening_page, $table_name_template_ad, $table_name_post_variations;

    $result = $wpdb->get_results("SELECT * FROM $table_name_opening_page GROUP BY post_id ORDER BY post_id DESC");
    if(isset($_GET['page_id']) && !isset($_GET['post_id'])){ $_GET['post_id'] = $_GET['page_id'];}
    if(!isset($_GET['post_id'])){
        $class = "yo_dash";
        if(!$_GET['edit']){$class = "in_dash";}
        echo '<div class="container '.$class.'">
                    <a href="?page=Intellecto&post_id" class="d-block my-4 text-center"><button class="btn btn-success">New Post</button></a>

                    <div class="row">
                        <div class="col-12 mx-auto">
                            <h5 class="mt-4 text-center">Posts Under Constraction</h5>
                        ';
        foreach ($result as $render) {
            if(strlen($render->post_id) > 8){
                echo '<div class="card mx-auto"><a href="?page=Intellecto&post_id='.$render->post_id.'">'.stripslashes($render->post_indexing_name).'</a><a href="?page=Intellecto&post_id='.$render->post_id.'">Edit</a></div>';
            }
        }
        echo        '
                            <h5 class="mt-4 text-center">Active Posts</h5>';
        foreach ($result as $render) {
            if(strlen($render->post_id) <= 8){
                echo '<div class="card mx-auto"><a href="?page=Intellecto&post_id='.$render->post_id.'">'.stripslashes($render->post_indexing_name).'</a><div class="div"><a href="?page=Intellecto&post_id='.$render->post_id.'" class="mr-2">Edit</a><a target="_blank" href="'.site_url().'/post-slug-'.$render->post_id.'/">Preview</a></div></div>';
            }
        }
        echo '</div>
                    </div>
                    <a href="?page=Intellecto&post_id" class="d-block my-4 text-center"><button class="btn btn-success">New Post</button></a>
                   </div>';
    }
    else{
        if(!$_GET['edit']){
            $class = "in_dash"; $url = "#"; $method = "";
        }
        else{
            $class = "yo_dash";
            global $pageParameter;
            $url = $_SERVER['PHP_SELF'].'?page=Intellecto&edit=true&'.$pageParameter.'='.$_GET['post_id'].'&var='.$_GET['var'];

            if(isset($_GET['drop'])){
                $wpdb->delete( $table_name_post_page, array( 'id' => $_GET['drop'] ) );
                $string = get_site_url() . $url;
                $parsedUrl =  strip_param_from_url( $string, 'page_id' );
                $parsedUrl = $parsedUrl . '&' . $pageParameter . '=all';
                echo "<meta http-equiv='refresh' content='0;url=".$parsedUrl."'>";
            }

            $method = 'method="post"';

            if(isset($_POST)) {
                $res = "";
                $index = 0;
                foreach ($_POST['title'] as $data) {
                    $attr = [
                        'id' => $_POST['temp_id'][$index],
                        'title' => $_POST['title'][$index],
                        'post_id' => $_POST['save_id'],
                        'text1' => $_POST['text1'][$index],
                        'text2' => $_POST['text2'][$index],
                        'post_status' => 'active',
                        'wp_image_id' => $_POST['image'][$index]
                    ];

                    $res = $result = insert_or_update_page_content($attr);
                    $index++;
                }
                if($res) {

                    $string = get_site_url() . $url;
                    $parsedUrl =  strip_param_from_url( $string, 'page_id' );
                    if($index === 1) {
                        $parsedUrl = $parsedUrl . '&' . $pageParameter . '=' . $result;
                    }else{
                        $parsedUrl = $parsedUrl . '&' . $pageParameter . '=all';
                    }
                    echo "<meta http-equiv='refresh' content='0;url=" . $parsedUrl . "'>";
                    die;
                }
            }

        }
        echo '<div class="container '.$class.'">
                        <div class="row">
                            <div class="col-12 mx-auto">
                                <form action="'.$url.'" '.$method.'>
                                    <div class="form_row pt-5">';
        if(!$_GET['edit']) {

            $result_page = $wpdb->get_results("SELECT * FROM $table_name_opening_page WHERE post_id='$_GET[post_id]' ORDER BY id DESC LIMIT 1");
            $page = $result_page[0];

            $image_attributes = wp_get_attachment_url($page->wp_image_id);
            if ($image_attributes) {
            } else {
                $page->wp_image_id = "";
            }

//            if(empty($page->headline_text) || !isset($page->headline_text)){
//                $page->headline_text = "Default Headline";
//            }
            $imgFeatured = wp_get_attachment_url($page->img_default);
            if ($imgFeatured) {
                $imgFeatured = 'src="'.$imgFeatured.'"';
            } else {
                $imgFeatured = "";
            }
            echo '     <div class="opening_page">
                                    <h5 class="my-3">Opening Page Info</h5>
                                    <label>Post indexing name*</label>
                                    <input type="text" required name="post_indexing_name" value="' . stripslashes($page->post_indexing_name) . '">
                                    <label>Default Post Title*</label>
                                    <input type="text" required name="post_title" value="' . stripslashes($page->post_title) . '">
                                    <label>Default Featured Image</label>
                                    <button class="img_default btn btn-secondary mt-3" type="button">Upload Image</button>
                                    <img class="imgPrevDefault" ng-src="{{imagePreview}}" ' . $imgFeatured . '>
                                    <input type="hidden" name="img_default" value="'.$page->img_default.'">
                                    <label>Headline Text</label>
                                    <textarea name="headline_text">' . stripslashes($page->headline_text) . '</textarea>
                                    <label>Opening Text*</label>
                                    <textarea required name="text">' . stripslashes($page->text) . '</textarea>
                                    <label>Author*</label>
                                    <select name="author">
                                    <option value="0">Select Author</option>
                                    ';
            global $EDITING_ROLE_TYPES_ARRAY;
            $blogusers = get_users([ 'role__in' => $EDITING_ROLE_TYPES_ARRAY ]);

            foreach ($blogusers as $user) {
                $select = "";
                if ($user->ID == $page->author) {
                    $select = "selected";
                }
                echo '<option ' . $select . ' value="' . $user->ID . '">' . esc_html($user->display_name) . '</option>';
            }
            echo '
</select>
                                    <input type="hidden" name="post_edited">
                                    <input type="hidden" name="page_id" value="' . $page->id . '">
                            </div>';
        }
        wp_enqueue_media();

        if(!$_GET['edit']) {
            $result = $wpdb->get_results("SELECT * FROM $table_name_post_page WHERE post_id='$_GET[post_id]'");
            $class = 'active';
        }else{
            if($_GET[$pageParameter] === 'all'){
                $result = $wpdb->get_results("SELECT * FROM $table_name_post_page WHERE post_id='$_GET[var]'");
            }else {
                if($_GET['post_id'] === 'new'){
                    $result = $wpdb->get_results("SELECT * FROM $table_name_post_page WHERE post_id='$_GET[var]' LIMIT 0");
                }else {
                    $result = $wpdb->get_results("SELECT * FROM $table_name_post_page WHERE id='$_GET[page_id]'");

                }
            }
            $selection = $wpdb->get_results("SELECT * FROM $table_name_post_page WHERE post_id='$_GET[var]'");

            echo '<label class="w-100">Page</label>';
            echo '<select class="choose_id w-100 mb-2">';
            if($_GET['id'] === 'all') {
                echo '<option selected value="all">ALL</option>';
            }else{
                echo '<option value="all">ALL</option>';
            }
            foreach ($selection as $select){
                $selected = "";
                if($select->id === $_GET[$pageParameter]){
                    $selected = "selected";
                }
                echo '<option '.$selected.' value="'.$select->id.'">'.$select->id. ' - ' .$select->title.'</option>';
            }
            echo'</select>';
            $class = 'inactive my-5';
        }
        if(count($result) === 0){ $result[] = ""; }

        foreach ($result as $render) {
            $image_attributes = wp_get_attachment_url($render->wp_image_id);
            if ($image_attributes) {
                $img = 'src="' . $image_attributes . '"';
            } else {
                $img = "https://dev1.dailyfeednews.com/wp-content/uploads/2022/08/7.jpg";
                $render->wp_image_id = "7";
            }

            echo '      <div class="holder '.$class.'">';
            if(isset($_GET['drop'])){
                echo '<span class="text-danger">Deleting...</span>';
            }
            echo '    <h5 class="title">New page default input</h5>
                            <label>Title</label>
                            <input type="text" value="' . stripslashes($render->title) .'" name="title[]">
                            <label>Text1</label>
                            <textarea name="text1[]">' . stripslashes($render->text1) . '</textarea>
                            <button class="upload_image btn btn-secondary mt-3" type="button">Upload Image</button>
                            <img class="imgPrev" ng-src="{{imagePreview}}" ' . $img . '>
                            <input type="hidden" value="' . $render->wp_image_id . '" name="image[]">
                            <input type="hidden" name="temp_id[]" value="' . $render->id . '">
                            <input type="hidden" name="edited[]">
                            <label class="w-100">Text2</label>
                            <textarea name="text2[]">' . stripslashes($render->text2) . '</textarea>';
            if(!$_GET['edit']) {
                echo '<button class="del_record btn btn-danger">Delete this page</button>';
            }else{
                echo '<a href="'.$url.'&drop='.$render->id.'"><button type="button" class="btn btn-danger">Delete this page</button></a>';
            }
            echo '</div>';
        }
        echo '
                        </div>
                        ';
        if(!$_GET['edit']) {
            echo '  <div class="text-danger" style="display: none">Another request is being processed, please try later...</div>
                            <input type="hidden" name="save_id" value="' . $_GET['post_id'] . '">
                            <input type="hidden" name="action" value="save_form" name="save_form">
                            <div class="baseBar">
                            <button type="button" class="btn btn-secondary add">Add Page</button>
                            <button type="button" class="btn btn-secondary multiUpload">Multi Upload</button>
                            <button type="button" class="btn btn-danger cancel">Delete Post</button>
                            <button type="button" class="btn btn-success save" style="float: right; margin-left: 3px;">Publish</button>
                            <button type="button" class="btn btn-success temp_save" disabled style="float: right; margin-left: 3px;">Save</button>
                            <button type="button" class="btn btn-secondary expand-collapse-icon"></button>
                           ';
            if(strlen($_GET['post_id']) < 8){
                echo '<a href="'.$_SERVER['PHP_SELF'].'?page=post_variations&&var='.$_GET['post_id'].'" ><button type="button" class="btn btn-secondary">Post Variation</button></a>';
            }
            if(strlen($_GET['post_id']) < 8 && !empty($_GET['post_id'])){
                echo '<a target="_blank" class="btn btn-light" href="'.site_url().'/post-slug-'.$_GET['post_id'].'/">Preview</a>';
            }
            echo'</div>';

        }else{
            echo '<button class="btn btn-success mt-2" type="submit">Save</button>';
            global $pageParameter;
            $url = $_SERVER['PHP_SELF'].'?page=Intellecto&'.$pageParameter.'=new&edit=true&var='.$_GET['var'];
            echo '<a href="'.$url.'"><button class="btn ml-2 btn-success mt-2" type="button">Add New</button></a>';
            echo '<a href="admin.php?page=post_variations&var='.$_GET['var'].'"><button class="btn bt-light" type="button">Cancel</button></a>';
            if($_GET['post_id'] === 'new'){
                $render->post_id = $_GET['var'];
            }
            echo '<input type="hidden" name="save_id" value="' . $render->post_id . '">';
        }
        echo'
                        
                        </form>
                    </div>
                </div>
            </div>';
    }
}

function update_page_id($table, $post_id, $save_id){
    global $wpdb;
    $wpdb->update($table, array(
        'post_id' => $post_id,
        'post_status' => 'active',
    ), array('post_id' => $save_id),
        array('%s', '%s')
    );
}
function update_page_content_id($table, $post_id, $save_id){
    global $wpdb;
    $wpdb->update($table, array(
        'post_id' => $post_id,
        'post_status' => 'active',
    ), array('post_id' => $save_id),
        array('%s', '%s')
    );
}
function insert_or_update_page_content($attr){
    global $wpdb;

    global $table_name_post_page;
    $title = $attr['title'];
    $id = $attr['id'];
    $post_id = $attr['post_id'];
    $text1 = $attr['text1'];
    $text2 = $attr['text2'];
    $post_status = $attr['post_status'];
    $wp_image_id = $attr['wp_image_id'];

    if(empty($id)){
            $wpdb->insert($table_name_post_page, array(
                'title' => $title,
                'post_id' => $post_id,
                'text1' => $text1,
                'text2' => $text2,
                'post_status' => $post_status,
                'wp_image_id' => $wp_image_id,
            ));
            return $wpdb->insert_id;
    }else{
        $wpdb->update($table_name_post_page, array(
            'title' => $title,
            'post_id' => $post_id,
            'text1' => $text1,
            'text2' => $text2,
            'post_status' => $post_status,
            'wp_image_id' => $wp_image_id,
        ), array('id' => $id));

        return $id;
    }

}
function insert_or_update_opening_page($attr){
    global $wpdb;
    global $table_name_opening_page;

    $post_id = $attr['post_id'];
    $post_status = $attr['post_status'];
    $headline_text = $attr['headline_text'];
    $post_indexing_name = $attr['post_indexing_name'];
    $author = $attr['author'];
    $text = $attr['text'];
    $post_title = $attr['post_title'];
    $img_default = $attr['img_default'];


    if(empty($id)){
            $wpdb->insert($table_name_opening_page, array(
                'post_id' => $post_id,
                'post_status' => $post_status,
                'headline_text' => $headline_text,
                'post_indexing_name' => $post_indexing_name,
                'text' => $text,
                'author' => $author,
                'img_default' => $img_default,
                'post_title' => $post_title
            ));

//        RETURN IF NEEDED
            return $wpdb->insert_id;

    }else{
        $wpdb->update($table_name_opening_page, array(
            'post_status' => $post_status,
            'headline_text' => $headline_text,
            'post_indexing_name' => $post_indexing_name,
            'text' => $text,
            'author' => $author,
            'img_default' => $img_default,
            'post_title' => $post_title
        ), array('id' => $id));

//        RETURN IF NEEDED
        return $id;

    }



}
add_action( 'admin_enqueue_scripts', 'my_enqueue' );
function my_enqueue($hook) {
//    INIT JS PART

    wp_enqueue_script( 'ajax-script', plugins_url( '/js/ajax-handler.js', __FILE__ ), array('jquery') );
    wp_enqueue_script( 'ajax-script', plugins_url( '/js/variation.js', __FILE__ ), array('jquery') );


    // in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
    wp_localize_script( 'ajax-script', 'ajax_object',
        array( 'ajax_url' => admin_url( 'admin-ajax.php' )) );



}

add_action( 'wp_ajax_in_update', 'in_update' );
function in_update($table)
{
    global $wpdb;
    global $table_name_post_page;

    $data = array();

    parse_str($_POST['serialized'], $data);

    if(!empty($data['save_id'])){$save_id = $data['save_id'];}else{$save_id = $_POST['st'];}

    $title = $data['title'];
    $text1 = $data['text1'];
    $text2 = $data['text2'];
    $image = $data['image'];
    $temp_id = $data['temp_id'];
    $default_img = $data['img_default'];
    $post_title = $data['post_title'];
    $post_indexing_name = $data['post_indexing_name'];
    if(strlen($save_id) > 8){
        $status = "edit";
    }else{
        $status = "active";
    }
    for ($index = 0; $index < count($title); $index++) {

        if(!empty($title[$index])){

            $arg = [
                'id' => $temp_id[$index],
                'title' => $title[$index],
                'post_id' => $save_id,
                'text1' => $text1[$index],
                'text2' => $text2[$index],
                'post_status' => $status,
                'wp_image_id' => $image[$index]
            ];

            $to_save_with = insert_or_update_page_content($arg).';';
            echo $to_save_with;
        }
    }
    $arg = [
        'post_id' => $save_id,
        'post_status' => $status,
        'headline_text' => $data['headline_text'],
        'post_indexing_name' => $data['post_indexing_name'],
        'text' => $data['text'],
        'author' => $data['author'],
        'post_title' => $post_title,
        'img_default' => $default_img,

    ];

    echo insert_or_update_opening_page($arg);

    wp_die();
}
add_action( 'wp_ajax_in_cancel', 'in_cancel' );
function in_cancel(){
    global $wpdb;
    global $table_name_post_page, $table_name_opening_page;
    $wpdb->delete( $table_name_post_page, array( 'post_id' => $_POST['post_id'] ) );
    $wpdb->delete( $table_name_opening_page, array( 'post_id' => $_POST['post_id'] ) );
    wp_delete_post($_POST['post_id']);
    wp_die();
}
add_action( 'wp_ajax_in_drop', 'in_drop' );
function in_drop(){
    global $wpdb;


    global $table_name_post_page;
    $id = $_POST['id'];
    $post_id = $_POST['post_id'];
    foreach($id as $i){
        $wpdb->delete( $table_name_post_page, array( 'id' => $i ) );
    }
    $count = $wpdb->get_var("SELECT * FROM $table_name_post_page WHERE id='$post_id'");
    //    echo wp_delete_post( $post_id, true );
    if(count($count) < 1){

        wp_delete_post( $post_id, true );

    }

    wp_die();
}

add_action( 'wp_ajax_in_create', 'in_create' );
function in_create(){
    global $wpdb;
    global $user_ID;

    global $table_name_post_page;


    $data = array();
    parse_str($_POST['serialized'], $data);

    if(!empty($data['save_id'])){$save_id = $data['save_id'];}else{$save_id = $_POST['st'];}
    $pagesTitlesArray = $data['title'];
    $pagesText1Array = $data['text1'];
    $pagesText2Array = $data['text2'];
    $pagesImagesArray = $data['image'];
    $tempIdsArray = $data['temp_id'];
    $author = $data['author'];


    $post_data = array(
        'post_title' => $save_id,
        'post_date' => date('Y-m-d H:i:s'),
        'post_author' => $author,
        'post_name' => 'post-slug-'.$save_id,
        'post_type' => 'dfncommpost',
        'post_status' => 'publish'
    );

    if ( !get_post_status ( $save_id ) ) {
        $post_id = wp_insert_post($post_data);
        wp_update_post(
            array(
                'ID' => $post_id,
                'post_title' => $post_id." - ".$data['post_indexing_name'],
                'post_name' => 'post-slug-'.$post_id,
            )
        );
        update_post_meta( $post_id, '_thumbnail_id', $data['wp_image_id'] );
        $post_status = 'active';
    }
    else{
        $post_data["ID"]=$save_id;
        wp_update_post($post_data);
        update_post_meta( $save_id, '_thumbnail_id', $data['wp_image_id'] );
        $post_id = $save_id;
        $post_status = 'active';
    }
    global $table_name_opening_page;
    $arg = [
        'post_id' => $save_id,
        'post_status' => $post_status,
        'headline_text' => $data['headline_text'],
        'post_indexing_name' => $data['post_indexing_name'],
        'text' => $data['text'],
        'author' => $data['author'],
        'post_title' => $data['post_title'],
        'img_default' => $data['img_default']

    ];
    insert_or_update_opening_page($arg);

    for ($index = 0; $index < count($pagesTitlesArray); $index++) {
        if (!empty($pagesTitlesArray[$index])) {
                $arg = [
                    'id' => $tempIdsArray[$index],
                    'title' => $pagesTitlesArray[$index],
                    'post_id' => $save_id,
                    'text1' => $pagesText1Array[$index],
                    'text2' => $pagesText2Array[$index],
                    'post_status' => 'active',
                    'wp_image_id' => $pagesImagesArray[$index]
                ];
               $id_to_update = insert_or_update_page_content($arg).';';
        }
    }
    update_page_content_id($table_name_post_page, $post_id, $save_id);
    update_page_id($table_name_opening_page, $post_id, $save_id);
    convertDbDataToPostConfigs($post_id);

}
add_action( 'admin_footer', 'media_selector_print_scripts' );

function media_selector_print_scripts() {
    wp_enqueue_script ( 'media_js', plugins_url( 'js/wp_media_upload.js', __FILE__ ) );
}
add_action( 'wp_ajax_in_validate_img', 'in_validate_img' );
function in_validate_img()
{
    global $wpdb;
    $img = wp_get_attachment_url( $_POST['id'] );
    if($img){
        echo true;
    }else{
        echo false;
    }
    wp_die();
}
add_action( 'wp_ajax_return_pages', 'return_pages' );
function return_pages(){
    global $wpdb;
    global $table_name_post_page;
    $id = $_POST['id'];
    $results = $wpdb->get_results("SELECT * FROM $table_name_post_page WHERE post_id=$id");
    foreach($results as $result){
        $response[] = ['id'=>$result->id, 'image'=>wp_get_attachment_image_src($result->wp_image_id)[0], 'title'=>$result->title];
    }
//    return $response;
    echo json_encode($response);
//    INCLUDES WHOLE ROW
//    NEED IMG ATTRIBUTE TO BE TAKEN
//    GET IMAGE ID -> SELECT SRC -> PASS IT TO JSON (ADDITIONAL FIELD/COLUMN)
//    DON'T FORGET TO EDIT GETTING PART IN JS
//    VARIATIONS.JS

    wp_die();
}
add_action( 'wp_ajax_in_save_variation', 'in_save_variation' );
function in_save_variation(){
    global $wpdb;
    global $STRING_TAG;
    $WRAPPER_START = "$(";
    $WRAPPER_END = ")$";
    $variationIndex = 0;

    $PostGenerator = new InPostGenerator();
    $InPostConfigArray = [];

    foreach ($_POST['data'] as $each){
        parse_str($each, $data);
//        echo "<br>";

//        CHANGE "UNDEFINED" TO GLOBAL VARIABLE!!!

        if($data['var_id'] !== "undefined"){
            $varId = $data['var_id'];
        }else{
            $varId = "";
        }

        if(!is_numeric($data['in_template_body_page'])){
            $PageTemplateId = 0;
        }else{
            $PageTemplateId = intval($data['in_template_body_page']);
        }
        $page_end_ad_template_id = $data['page_end_ad_template_id'];
        $pre_image_ad_template_id = $data['pre_image_ad_template_id'];

        $page_end_ad_template_id_mobile = $data['page_end_ad_template_id_mobile'];
        $pre_image_ad_template_id_mobile = $data['pre_image_ad_template_id_mobile'];
        $name = stripslashes($data['name']);
        $image = $data['image'];
        $post_slug = stripslashes($data['post_slug']);
        $post_title = stripslashes($data['post_title']);

        if(!is_numeric($_POST['post_id'])){
            $PostId = 0;
        }else{
            $PostId = intval($_POST['post_id']);
        }



        $head_scripts = $_POST['head_script'];
        $footer_scripts = $_POST['footer_script'];
        $BodyPages = [];
        foreach ($data['page_id'] as $pages){
            $BodyPages[] = intval($pages);
        }
        $array_head_script = [];
        foreach ($head_scripts[$variationIndex] as $head_script){
            if($head_script['head'] !== "0") {
                $values = [];
                foreach ($head_script['h_array'] as $script_details) {
                    if ($script_details['input'] !== "") {
                        if ($script_details['select'] === "0") {
                            $values[] = $script_details['input'];
                        } elseif ($script_details['select'] === "2") {
                            $values[] = $WRAPPER_START . $script_details['input'] . $WRAPPER_END;
                        } else {
                            $values[] = intval($script_details['input']);
                        }
                    }
                }
                $headTempId = "";
                if (is_numeric($head_script['head'])) {
                    $headTempId = intval($head_script['head']);
                }
                if(intval($headTempId) !== 0) {
                    $array_head_script[] = [InPostConfig::$PARAM_HEAD_SCRIPT_ID => intval($headTempId), InPostConfig::$PARAM_HEAD_PARAMETERS => $values];
                }
            }
        }
        $array_footer_script = [];
        foreach ($footer_scripts[$variationIndex] as $footer_script){
            if($footer_script['head'] !== "0") {
                $values = [];
                foreach ($footer_script['h_array'] as $script_details) {
                    if ($script_details['input'] !== "") {
                        if ($script_details['select'] === "0") {
                            $values[] = $script_details['input'];
                        } elseif ($script_details['select'] === "2") {
                            $values[] = $WRAPPER_START . $script_details['input'] . $WRAPPER_END;
                        } else {
                            $values[] = intval($script_details['input']);
                        }
                    }
                }
                $headTempId = "";
                if (is_numeric($footer_script['head'])) {
                    $headTempId = intval($footer_script['head']);
                }
                if(intval($headTempId) !== 0) {
                    $array_footer_script[] = [InPostConfig::$PARAM_HEAD_SCRIPT_ID => intval($headTempId), InPostConfig::$PARAM_HEAD_PARAMETERS => $values];
                }
            }
        }


        $readyInsertIntoPostConfigObject[] = [
            'addHeadScripts' => $array_head_script,
            'addFooterScripts' => $array_footer_script,
            'addBodyPages' => $BodyPages,
            'setPostSlug' => $post_slug,
            'setPostTitle' => addslashes($post_title),
            'setPageTemplateId' => $PageTemplateId,
            'setPostId' => $PostId,
            'setVariationId' => $varId,
            'setPostName' => $name,
            'setfeaturedImageId' => intval($image),
            'setadvTemplateIds' => ["pre_image_ad_template_id"=>intval($pre_image_ad_template_id),"page_end_ad_template_id"=>intval($page_end_ad_template_id), "pre_image_ad_template_id_mobile"=>intval($pre_image_ad_template_id_mobile),"page_end_ad_template_id_mobile"=>intval($page_end_ad_template_id_mobile)]
        ];

        $variationIndex++;
    }


    if(isset($readyInsertIntoPostConfigObject)) {
        $response = generatePostVariations($readyInsertIntoPostConfigObject);
    }
    foreach ($response as $res){
        echo $res;
    }

    wp_die();
}
function generatePostVariations($objectConfigs){
    global $wpdb, $table_name_opening_page;



    $PostGenerator = new InPostGenerator();
    $InPostConfigArray = [];
    foreach ($objectConfigs as $objectConfig){

        $postId = $objectConfig['setPostId'];
        $opening_page_results = $wpdb->get_results("SELECT * FROM $table_name_opening_page WHERE post_id=$postId");

        if(empty($objectConfig['setfeaturedImageId']) || intval($objectConfig['setfeaturedImageId']) === 0) {
            $objectConfig['setfeaturedImageId'] = $opening_page_results[0]->img_default;
        }
        $postConfigObject = new InPostConfig();
        $postConfigObject->addHeadScripts($objectConfig['addHeadScripts']);
        $postConfigObject->addFooterScripts($objectConfig['addFooterScripts']);
        $postConfigObject->addBodyPages($objectConfig['addBodyPages']);
        $postConfigObject->setPostSlug($objectConfig['setPostSlug']);
        $postConfigObject->setPostTitle($objectConfig['setPostTitle']);
        $postConfigObject->setPageTemplateId($objectConfig['setPageTemplateId']);
        $postConfigObject->setPostId($objectConfig['setPostId']);
        $postConfigObject->setVariationId($objectConfig['setVariationId']);
        $postConfigObject->setPostName($objectConfig['setPostName']);
        $postConfigObject->setfeaturedImageId($objectConfig['setfeaturedImageId']);
        $postConfigObject->setadvTemplateIds($objectConfig['setadvTemplateIds']);
        array_push($InPostConfigArray, $postConfigObject);
    }
    return $PostGenerator->generatePosts($InPostConfigArray);
}
add_action( 'wp_ajax_delete_variation', 'delete_variation' );
function delete_variation(){
    global $wpdb, $table_name_post_variations;
    $wpdb->delete( $table_name_post_variations, array( 'id' => $_POST['id'] ) );
}
add_action('wp_ajax_saved_variation_configurations', 'saved_variation_configurations');
function saved_variation_configurations(){
    global $wpdb, $table_name_post_variations;
    $id = $_POST['id'];

    $WRAPPER_START = "$(";
    $WRAPPER_END = ")$";

    $result = $wpdb->get_results("SELECT id,post_configuration,name FROM $table_name_post_variations WHERE post_id=$id");
    $arr = [];
    foreach ($result as $res){
        $postConfigurationArray = json_decode($res->post_configuration, true);
        $index=0;
        foreach ($postConfigurationArray['head_scripts'] as $script){

            $paramsIndex = 0;
            foreach ($script['scriptParams'] as $params) {
                if (substr($params, 0, 2) === $WRAPPER_START && substr($params, -2) === $WRAPPER_END) {
                    $type = 2;
                    $params = str_replace($WRAPPER_START, "", $params);
                    $params = str_replace($WRAPPER_END, "", $params);
                    $params = stripslashes($params);
                }elseif(is_int($params)) {
                    $type = 1;
                }else{
                    $type = 0;
                }
                $postConfigurationArray['head_scripts'][$index]['scriptParams'][$paramsIndex] = [$params=>$type];
                $paramsIndex++;
            }
            $index++;

        }
        $index=0;
        foreach ($postConfigurationArray['footer_scripts'] as $scriptfooter){

            $paramsIndex = 0;
            foreach ($scriptfooter['scriptParams'] as $params) {
                if (substr($params, 0, 2) === $WRAPPER_START && substr($params, -2) === $WRAPPER_END) {
                    $type = 2;
                    $params = str_replace($WRAPPER_START, "", $params);
                    $params = str_replace($WRAPPER_END, "", $params);
                    $params = stripslashes($params);
                }elseif(is_int($params)) {
                    $type = 1;
                }else{
                    $type = 0;
                }
                $postConfigurationArray['footer_scripts'][$index]['scriptParams'][$paramsIndex] = [$params=>$type];
                $paramsIndex++;
            }
            $index++;

        }

        $arr[] = ['id'=>$res->id,'config'=>json_encode($postConfigurationArray, JSON_UNESCAPED_SLASHES), 'name'=>$res->name];
    }
    echo json_encode($arr, JSON_UNESCAPED_SLASHES);
}
add_action('wp_ajax_image_url', 'image_url');
function image_url(){

//    GETS IMG SRC OUT OF ID

//    SET BETTER EXPLANATION FOR THIS FUNCTION

    $image_attributes = wp_get_attachment_url($_POST['id']);
    echo $image_attributes;
    wp_die();
}
add_action( 'wp_ajax_get_variation_templates', 'get_variation_templates' );
function get_variation_templates(){
    global $wpdb, $table_name_post_variations_template;

    $condition = "";

    if(isset($_POST['id']) && !empty($_POST['id'])){
        $id = $_POST['id'];
        $condition = "WHERE id=$id";
    }

    $variationTemplates = $wpdb->get_results("SELECT * FROM $table_name_post_variations_template $condition");
    foreach ($variationTemplates as $variationTemplate) {
        $response[] = ['id' => $variationTemplate->id, 'config' => $variationTemplate->json_config, 'name' => $variationTemplate->title];
    }
    echo json_encode($response, JSON_UNESCAPED_SLASHES);
}
add_action( 'wp_ajax_save_variation_template', 'save_variation_template' );
function save_variation_template(){
    global $wpdb, $table_name_post_variations_template, $table_name_post_variations;

    $ID = $_POST['id'];

    $variationDB = $wpdb->get_results("SELECT * FROM $table_name_post_variations WHERE id=$ID");
    $variationConfig = $variationDB[0]->post_configuration;

    $wpdb->insert($table_name_post_variations_template, array(
        'title' => $_POST['name'],
        'json_config' => $variationConfig,
    ));

    return true;

}

add_action( 'wp_ajax_drop_variation_template', 'drop_variation_template' );
function drop_variation_template(){
    global $wpdb, $table_name_post_variations_template;

    $ID = $_POST['id'];
    $wpdb->delete( $table_name_post_variations_template, array( 'id' => $ID ) );

    return true;

}

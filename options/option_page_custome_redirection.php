<?php
global $wpdb,$table_prefix,$util;
$table_name = $table_prefix . 'WP_SEO_Redirection';
$table_name_404 = $table_prefix . 'WP_SEO_404_links';

if($util->post('redirect_from')!=''){

$redirect_from=urldecode($util->make_relative_url($util->post('redirect_from')));
$redirect_to=$util->make_relative_url($util->post('redirect_to'));
$redirect_type=$util->post('redirect_type');

$redirect_from_type=$util->post('redirect_from_type');
$redirect_from_folder_settings=$util->post('redirect_from_folder_settings');
$redirect_from_subfolders=$util->post('redirect_from_subfolders');

$redirect_to_type=$util->post('redirect_to_type');
$redirect_to_folder_settings=$util->post('redirect_to_folder_settings');

$enabled=$util->post('enabled');

$regex="";

if($redirect_from_type =='Folder')
{

	if(substr($redirect_from,-1)!='/')
		$redirect_from = $redirect_from . '/';

	if($redirect_from_folder_settings==2)
	{
		if($redirect_from_subfolders ==0)
		{
			$regex= '^'. $util->regex_prepare($redirect_from) . '.*'; ;
		}
		else
		{
			$regex= '^'. $util->regex_prepare($redirect_from) . '[^/]*$';
		}
	}
	else if($redirect_from_folder_settings==3)
	{
		if($redirect_from_subfolders ==0)
		{
			$regex= '^'. $util->regex_prepare($redirect_from) . '.+';
		}
		else
		{
			$regex= '^'. $util->regex_prepare($redirect_from) . '[^/]+$';			
		}
	}

}else if($redirect_from_type =='Regex')
{
	$regex= $redirect_from; 
}

if ($redirect_from_type=='Page' || $redirect_from_type=='Regex')
{
	$redirect_from_folder_settings="";
	$redirect_from_subfolders="";	
}

if ($redirect_to_type=='Page')
{
	$redirect_to_folder_settings="";
}

if($redirect_to_type =='Folder')
{
	if(substr($redirect_to,-1)!='/')
		$redirect_to= $redirect_to. '/';		
}

	

	if($util->post('add_new')!='')
	{
		
	$theurl = $wpdb->get_row($wpdb->prepare(
		"
		SELECT count(ID) as cnt 
		FROM $table_name 
		WHERE redirect_from = %s
		",
		$redirect_from
	));

	if($theurl->cnt >0)
	{
		$util->failure_option_msg("This URL <b>'$redirect_from'</b> is added previously!");
	}else
	{
		
		
		if($redirect_from=='' || $redirect_to=='' || $redirect_type=='' ){
			$util->failure_option_msg('Please input all required fields!');
		}else
		{
			$wpdb->query($wpdb->prepare(
				"
					INSERT INTO $table_name
					(redirect_from,redirect_to,redirect_type,url_type,redirect_from_type,redirect_from_folder_settings,redirect_from_subfolders,redirect_to_type,redirect_to_folder_settings,regex,enabled) 
					values
					(%s, %s, %s, 1, %s, %s, %s, %s, %s, %s, %s)		
				",
				$redirect_from, $redirect_to, $redirect_type, $redirect_from_type, $redirect_from_folder_settings, $redirect_from_subfolders, $redirect_to_type, $redirect_to_folder_settings, $regex, $enabled
			));
	
			$wpdb->query($wpdb->prepare(
				"
					DELETE FROM $table_name_404 WHERE link = %s
				", 
				$redirect_from
			));

			$SR_redirect_cache = new clogica_SR_redirect_cache();
			$SR_redirect_cache->free_cache();
		}
		
		
	
	}}else if($util->post('edit_exist')!='')
	{
		$edit=$util->post('edit');
			
		if($redirect_from=='' || $redirect_to=='' || $redirect_type=='' ){
			$util->failure_option_msg('Please input all required fields!');
		}else
		{
			$wpdb->query($wpdb->prepare(
				"
				UPDATE $table_name 
				SET redirect_from = %s, redirect_to = %s, redirect_type = %s, redirect_from_type = %s, redirect_from_folder_settings = %s, redirect_from_subfolders = %s, redirect_to_type = %s, redirect_to_folder_settings = %s, regex = %s, enabled = %s
				WHERE ID = %d
				",
				$redirect_from, $redirect_to, $redirect_type, $redirect_from_type, $redirect_from_folder_settings, $redirect_from_subfolders, $redirect_to_type, $redirect_to_folder_settings, $regex, $enabled, $edit
			));

			$SR_redirect_cache = new clogica_SR_redirect_cache();
			$SR_redirect_cache->free_cache();
		}
	
	
	}
	
if($util->there_is_cache()!='') 
	$util->info_option_msg("You have a cache plugin installed <b>'" . $util->there_is_cache() . "'</b>, you have to clear cache after any changes to get the changes reflected immediately! ");
	
}

  if($util->get('add')!='' || $util->get('edit')!='' )
  {
  	include "option_page_custome_redirection_add_update.php";
  }else
  {
  	include "option_page_custome_redirection_list.php";
  }
<?php


global $wpdb,$table_prefix,$util;
$table_name = $table_prefix . 'WP_SEO_Redirection';
$table_name_404 = $table_prefix . 'WP_SEO_404_links';

if(isset($_POST)){

$redirect_from=$_POST['redirect_from'];
$redirect_to=$_POST['redirect_to'];
$redirect_type=$_POST['redirect_type'];

$redirect_from_type=$_POST['redirect_from_type'];
$redirect_from_folder_settings=$_POST['redirect_from_folder_settings'];
$redirect_from_subfolders=$_POST['redirect_from_subfolders'];

$redirect_to_type=$_POST['redirect_to_type'];
$redirect_to_folder_settings=$_POST['redirect_to_folder_settings'];

$enabled=$_POST['enabled'];

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

	

	if(isset($_POST['add_new']))
	{
		
	$theurl = $wpdb->get_row(" select count(ID) as cnt from $table_name where redirect_from='$redirect_from' ");
	if($theurl->cnt >0)
	{
		$util->failure_option_msg("This URL <b>'$redirect_from'</b> is added previously!");
	}else
	{
		
		
		if($redirect_from=='' || $redirect_to=='' || $redirect_type=='' ){
			$util->failure_option_msg('Please input all required fields!');
		}else
		{
			$wpdb->query(" insert into $table_name(redirect_from,redirect_to,redirect_type,url_type,redirect_from_type,redirect_from_folder_settings,redirect_from_subfolders,redirect_to_type,redirect_to_folder_settings,regex,enabled) values('$redirect_from','$redirect_to','$redirect_type',1,'$redirect_from_type','$redirect_from_folder_settings','$redirect_from_subfolders','$redirect_to_type','$redirect_to_folder_settings','$regex','$enabled') ");
		
			$wpdb->query(" delete from $table_name_404 where link='$redirect_from' ");

		}
		
		
	
	}}else if(isset($_POST['edit_exist']))
	{
		$edit=$_POST['edit'];
			
		if($redirect_from=='' || $redirect_to=='' || $redirect_type=='' ){
			$util->failure_option_msg('Please input all required fields!');
		}else
		{
			$wpdb->query(" update $table_name set redirect_from='$redirect_from',redirect_to='$redirect_to',redirect_type='$redirect_type',redirect_from_type='$redirect_from_type' ,redirect_from_folder_settings='$redirect_from_folder_settings' ,redirect_from_subfolders='$redirect_from_subfolders' ,redirect_to_type='$redirect_to_type' ,redirect_to_folder_settings='$redirect_to_folder_settings' ,regex='$regex',enabled='$enabled'  where ID=" . $edit);
			
		}
	
	
	}
	
if($util->there_is_cache()!='') 
	$util->info_option_msg("You have a cache plugin installed <b>'" . $util->there_is_cache() . "'</b>, you have to clear cache after any changes to get the changes reflected immediately! ");
	
}


  if(isset($_GET['add']) || isset($_GET['edit']) )
  {
  	include "option_page_custome_redirection_add_update.php";
  }else
  {
  	include "option_page_custome_redirection_list.php";
  }



?>
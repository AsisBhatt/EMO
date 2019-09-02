<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.5.9
 */

/**
 * This hook gives a chance to prepend content or to replace the default view content with a custom content.
 * Please note that from inside the action callback you can access all the controller view
 * variables via {@CAttributeCollection $collection->controller->data}
 * In case the content is replaced, make sure to set {@CAttributeCollection $collection->renderContent} to false
 * in order to stop rendering the default content.
 * @since 1.3.3.1
 */
$hooks->doAction('before_view_file_content', $viewCollection = new CAttributeCollection(array(
    'controller'    => $this,
    'renderContent' => true,
)));

// and render if allowed
if ($viewCollection->renderContent) {
    /**
     * This hook gives a chance to prepend content before the active form or to replace the default active form entirely.
     * Please note that from inside the action callback you can access all the controller view variables
     * via {@CAttributeCollection $collection->controller->data}
     * In case the form is replaced, make sure to set {@CAttributeCollection $collection->renderForm} to false
     * in order to stop rendering the default content.
     * @since 1.3.3.1
     */
    $hooks->doAction('before_active_form', $collection = new CAttributeCollection(array(
        'controller'    => $this,
        'renderForm'    => true,
    )));

    // and render if allowed
    if ($collection->renderForm) {
        //$form = $this->beginWidget('CActiveForm');
		/*$form = $this->beginWidget('CActiveForm', array(
            'htmlOptions' => array('enctype' => 'multipart/form-data')
        ));
		*/
        ?>
		<script>
			$(document).ready(function(){
				var page_data = '<?php $_SESSION['pages']; ?>';
				$('#fb_pages').modal('show');
				
				
			});
		</script>
		<div class="portlet-title">
			<div class="caption">
				<span class="glyphicon glyphicon-envelope"></span>
				<span class="caption-subject font-dark sbold uppercase">
					<?php echo $pageHeading;?>
				</span>
			</div>
			<div class="actions">
				<div class="btn-group btn-group-devided">
					<?php if (!$message->isNewRecord) { ?>
					<?php //echo CHtml::link(Yii::t('app', 'Create new'), array('socialsetting/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Create new')));?>
					<?php } ?>
					<?php echo CHtml::link(Yii::t('app', 'Cancel'), array('socialsetting/connect'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Cancel')));?>
				</div>
			</div>
		</div>
		<div class="portlet-body">
			<?php
			/**
			 * This hook gives a chance to prepend content before the active form fields.
			 * Please note that from inside the action callback you can access all the controller view variables
			 * via {@CAttributeCollection $collection->controller->data}
			 * @since 1.3.3.1
			 */
			$hooks->doAction('before_active_form_fields', new CAttributeCollection(array(
				'controller'    => $this,
				//'form'          => $form
			)));
			?>	
			<div class="row">
				<div class="vik form-group col-lg-12">
					<div class="col-lg-4">
						<?php
							//$this->widget('customer.modules.hybridauth.widgets.renderProviders');
							/*$this->widget('ext.widgets.hybridAuth.SocialLoginButtonWidget', array(
								'enabled'	=>	Yii::app()->hybridAuth->enabled,
								'providers'	=>	Yii::app()->hybridAuth->getAllowedProviders(),
								'route'		=> 	'hybridauth/authenticate',
							));*/
						?>
						<!--<a href="<?php //echo 'https://www.facebook.com/dialog/oauth?client_id='.fApp_ID.'&redirect_uri='.fcallback_url.'&scope=email,user_likes'; ?>">
						 <img src="/myuploads/social/facebook.png" alt="Facebook"/>
						</a>-->
						<?php
							/*echo '<pre>';
							print_r($message->facebook_app_id);
							echo '</pre>';
							exit;*/
						?>
						<a href="<?php echo 'https://www.facebook.com/dialog/oauth?client_id='.$message->facebook_app_id.'&redirect_uri='.$loginUrl.'&scope=email, publish_actions, user_photos, manage_pages, publish_pages, pages_show_list'; ?>">
						 <img src="<?php echo AssetsUrl::img('facebook.png') ?>" alt="Facebook"/>
						</a>
					</div>
					<div class="col-lg-8">
						<?php if(@$_SESSION['facebook_access_token']){ ?>
						<span style="color:green; font-weight:bold"> Connected </span>
						<?php }else{ ?>
							<span style="color:red; font-weight:bold" >Not Connected <span/>
						<?php } ?>
					</div>
					
				</div>
				
				<div class="vik form-group col-lg-12">
					<div class="col-lg-4 ">
						<a href="http://socialstark.com/login/customer/index.php/socialsetting/twitterauthorise">
						<img src="/login/myuploads/social/twitter.png" alt="Twitter" />
						</a>
					</div>
					<div class="col-lg-8">
					<?php if(@$_SESSION['access_token']){ ?>
						<span style="color:green; font-weight:bold"> Connected </span>
					<?php }else{ ?>
						<span style="color:red; font-weight:bold" >Not Connected <span/>
					<?php } ?>
					</div>
					
					<?php 
					//print_r($_SESSION['t_profile_details']);	
					//die('ok');
					?>
					
					<div class="col-lg-12">
						<?php if(@$_SESSION['t_profile_details']){ ?>

							<b>Twitter Profile Info</b><br />
							<table class="table" border="0" cellspacing="3" cellpadding="3">
								<tr>
								<td colspan="2"><img style="height:80px; width:80px;" src="<?php echo @$_SESSION['t_profile_details']->profile_image_url; ?>" /></td>         
								</tr>
								<tr>
									<td>Name:</td>         
									<td><?php echo @$_SESSION['t_profile_details']->name; ?></a></td>
								</tr>
								<tr>
									<td>Sreen Name:</td>      
									<td><?php echo @$_SESSION['t_profile_details']->screen_name;?></td>
								</tr> 
								
							</table>
						<?php } ?>	
					</div>
					
					
				</div>
				
				<div class="vik form-group col-lg-12">
					<div class="col-lg-4">
						<a href="http://socialstark.com/login/customer/index.php/socialsetting/linkedinauthorise">
						 <img src="/login/myuploads/social/linkedin.png" alt="LinkedIn" />
						</a>
					</div>
					<div class="col-lg-3">
						<?php if(@$_SESSION['oauth_access_token']){ ?>
						<span style="color:green; font-weight:bold"> Connected </span>
						<?php }else{ ?>
							<span style="color:red; font-weight:bold" >Not Connected <span/>
						<?php } ?>	
					</div>
					
					<div class="col-lg-5">
						<?php //echo @$_SESSION['l_page_id']; ?>
						
						<?php if(@$_SESSION['l_all_companies']){ ?>
						<form action="http://socialstark.com/login/customer/index.php/socialsetting/linkedinpagesave">	
							<select name="page_id" id="" class="form-control">
							<option value="">--Company page--</option>
							<?php foreach ($_SESSION['l_all_companies']->values as $temp) { ?>
								<option  <?php if($_SESSION['l_page_id']== $temp->id ){ echo 'selected'; } ?> value="<?php echo $temp->id ;?>"><?php echo $temp->name; ?></option>
							<?php } ?>
							</select><br>
							<button name="save" type="submit" class="btn btn-warning">Save</button>
						</form>
						<?php } ?>	
					</div>
				
					<div class="col-lg-12">
						
						<?php if(@$_SESSION['l_profile_details']){ ?>

							<b>Linked Profile Info</b><br />
							<table class="table" border="0" cellspacing="3" cellpadding="3">
								<tr>
								<td colspan="2">
								<img style="height:80px; width:80px;" src="<?php echo @$_SESSION['l_profile_details']->pictureUrl ?>" /></td>         
								</tr>
								<tr>
									<td>Name:</td>         
									<td><?php echo @$_SESSION['l_profile_details']->firstName.' '.@$_SESSION['l_profile_details']->lastName?></a></td>
								</tr>
								<tr>
									<td>Email:</td>      
									<td><?php echo @$_SESSION['l_profile_details']->emailAddress;?></td>
								</tr> 
								<!--<tr>
									<td>Headline:</td>      
									<td><?php echo @$_SESSION['l_profile_details']->headline;?></td>
								</tr>-->
								
							</table>
							
							
						<?php } ?>	
					</div>
				</div>
				
			</div>
			
			<?php
				//print_r($_SESSION['l_profile_details']);
				/*if (isset($_SESSION['oauth_token']) && isset($_SESSION['oauth_token_secret'])){
					
					?>
					<b>Twitter Profile  Information</b><br />
					<table border="0" cellspacing="3" cellpadding="3">
						<tr><td>Name</td>         
						<td><a target="_blank" href="http://twitter.com/<?=$data->screen_name?>"><?=$data->name?></a></td>
						</tr>
						<tr><td>location</td>      
						<td><?=$data->location?></td>
						</tr>
						<tr><td>description</td>   
						<td><?=$data->description?></td>
						</tr>
						<tr><td>Profile Image</td> <td><img src="<?=$data->profile_image_url?>" alt="" /></td></tr>
					</table>
					
					<?php 
					//unset($_SESSION['oauth_token']);
					//unset($_SESSION['oauth_token_secret']);
					?>
			  <?php
				}
				*/
			  ?>
				
			<?php
			/**
			 * This hook gives a chance to append content after the active form fields.
			 * Please note that from inside the action callback you can access all the controller view variables
			 * via {@CAttributeCollection $collection->controller->data}
			 * @since 1.3.3.1
			 */
			$hooks->doAction('after_active_form_fields', new CAttributeCollection(array(
				'controller'    => $this,
				//'form'          => $form
			)));
			?>
			<div class="row">
				<!--
				<div class="col-md-12">
					<button type="submit" class="btn green btn-submit" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>"><?php echo Yii::t('app', 'Save changes');?></button>
				</div>
				-->
			</div>
		</div>
		<div class="modal fade" id="fb_pages" tabindex="-1" role="dialog" aria-labelledby="fb-page-modal" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h1 class="heading left">Choose Facebook pages to connect</h1><a class="right inline-link js-back lowercase" href="javascript:void(0);" data-pollinator-action="go_back" data-pollinator-trigger="click"> ‹ Go Back</a></div>
					<div class="modal-body ">
						<ul class="pages-list">
							<div>
								<li class="pages-item base-profile" title="Beelift Application">
									<img class="profile-img" alt="Beelift Application" src="https://scontent.xx.fbcdn.net/v/t1.0-1/p50x50/24131233_180427635875870_2112519962612031066_n.png?oh=fcde1a489b1ce8657802483a29ebf6f8&oe=5AD01CA8">
									<b class="profile-b">Beelift Application </b>
									<span class="profile-span">Connect</span>
									<span class="profile-checkbox">
										<span class="Checkbox">
											<input type="checkbox" class="Checkbox-input">
											<span class="Checkbox-indicator"></span>
										</span>
									</span>
								</li>
								<li class="pages-item base-profile" title="Beelift Media">
									<img class="profile-img" alt="Beelift Media" src="https://scontent.xx.fbcdn.net/v/t1.0-1/p50x50/24131233_180427635875870_2112519962612031066_n.png?oh=fcde1a489b1ce8657802483a29ebf6f8&oe=5AD01CA8">
										<b class="profile-b">Beelift Media </b>
										<span class="profile-span">Connect</span>
									<span class="profile-checkbox">
										<span class="Checkbox">
											<input type="checkbox" class="Checkbox-input">
											<span class="Checkbox-indicator"></span>
										</span>
									</span>
								</li>
								<li class="pages-item base-profile" title="Beelift Website">
									<img class="profile-img" alt="Beelift Website" src="https://scontent.xx.fbcdn.net/v/t1.0-1/p50x50/24131233_180427635875870_2112519962612031066_n.png?oh=fcde1a489b1ce8657802483a29ebf6f8&oe=5AD01CA8">
									<b class="profile-b">Beelift Website </b>
									<span class="profile-span">Connect</span>
										<span class="profile-checkbox">
											<span class="Checkbox">
												<input type="checkbox" class="Checkbox-input">
												<span class="Checkbox-indicator"></span>
											</span>
									</span>
								</li>
							</div>
						</ul>
					</div>
					<form method="POST" id="profile-select-facebook-form" action="/settings/networkconnect/saveFb/page" data-pollinator-action="connect_facebook_profiles" data-pollinator-trigger="submit">
						<input type="hidden" name="ci_csrf_token" value="3f4cda0e4554342fcb49e854fb5136ed">
						<input type="hidden" id="facebook-id-str" name="id_str">
						<input type="hidden" name="g_id" value="994060">
					</form>
					<div class="js-modal-footer">
						<div data-reactroot="" class="modal-footer">
							<div class="modal-footer-message">
								<p>You have <strong>8</strong> profiles remaining in your current plan.</p>
							</div>
							<div class="modal-actions">
								<button type="button" role="button" class="Button _primary _default _disabled" disabled="" aria-disabled="true" tabindex="-1"><span class="Button-text">Connect</span></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
        <?php
        //$this->endWidget();
    }
    /**
     * This hook gives a chance to append content after the active form.
     * Please note that from inside the action callback you can access all the controller view variables
     * via {@CAttributeCollection $collection->controller->data}
     * @since 1.3.3.1
     */
    $hooks->doAction('after_active_form', new CAttributeCollection(array(
        'controller'      => $this,
        'renderedForm'    => $collection->renderForm,
    )));
}
/**
 * This hook gives a chance to append content after the view file default content.
 * Please note that from inside the action callback you can access all the controller view
 * variables via {@CAttributeCollection $collection->controller->data}
 * @since 1.3.3.1
 */
$hooks->doAction('after_view_file_content', new CAttributeCollection(array(
    'controller'        => $this,
    'renderedContent'   => $viewCollection->renderContent,
)));

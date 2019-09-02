<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
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
if ($viewCollection->renderContent) { ?>
	<?php if (!$list->isNewRecord) { ?>
    <?php
    }
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
        $form = $this->beginWidget('CActiveForm');
        ?>
		<div class="portlet-title">
			<div class="caption">
				<span class="glyphicon glyphicon-list-alt"></span>
				<span class="caption-subject font-dark sbold uppercase">
					<?php echo Yii::t('lists', 'Please fill in your mail list details.');?>
				</span>
			</div>
			<div class="actions">
				<div class="btn-group btn-group-devided">
					<?php $this->widget('customer.components.web.widgets.MailListSubNavWidget', array(
						'list' => $list,
					))?>
					<?php 
						echo CHtml::link(Yii::t('app', 'Cancel'), array('lists/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Cancel')));
					?>
				</div>
			</div>
		</div>
		<div class="portlet-body">
			<?php
			/**
			 * This hook gives a chance to prepend content before the active form fields.
			 * Please note that from inside the action callback you can access all the controller view variables
			 * via {@CAttributeCollection $collection->controller->data}
			 *
			 * @since 1.3.3.1
			 */
			$hooks->doAction('before_active_form_fields', new CAttributeCollection(array(
				'controller'    => $this,
				'form'          => $form
			)));
			?>
			<div class="portlet light bordered margin-bottom-20 no-padding">
				<div class="portlet-title">
					<div class="caption margin">
						<span class="caption-subject font-dark sbold uppercase">
							<?php echo Yii::t('lists', 'General data');?>
						</span>
					</div>
				</div>
				<div class="portlet-body no-padding">
					<div class="form-group col-lg-6">
						<?php echo $form->hiddenField($list,'type',array('value' => (isset($_GET['type']) ? $_GET['type'] : ''))); ?>
						<?php echo $form->labelEx($list, 'name', array('class' => 'control-label'));?>
						<?php echo $form->textField($list, 'name', $list->getHtmlOptions('name')); ?>
						<?php echo $form->error($list, 'name');?>
					</div>
					<div class="form-group col-lg-6">
						<?php echo $form->labelEx($list, 'display_name', array('class' => 'control-label'));?>
						<?php echo $form->textField($list, 'display_name', $list->getHtmlOptions('display_name')); ?>
						<?php echo $form->error($list, 'display_name');?>
					</div>
					<div class="clearfix"><!-- --></div>
					<div class="form-group col-lg-12">
						<?php echo $form->labelEx($list, 'description', array('class' => 'control-label'));?>
						<?php echo $form->textArea($list, 'description', $list->getHtmlOptions('description', array('rows' => 2))); ?>
						<?php echo $form->error($list, 'description');?>
					</div>
					<div class="clearfix"><!-- --></div>
					<div class="form-group col-lg-6">
						<?php echo $form->labelEx($list, 'opt_in', array('class' => 'control-label'));?>
						<?php echo $form->dropDownList($list, 'opt_in', $list->getOptInArray(), $list->getHtmlOptions('opt_in')); ?>
						<?php echo $form->error($list, 'opt_in');?>
					</div>
					<div class="form-group col-lg-6">
						<?php echo $form->labelEx($list, 'opt_out', array('class' => 'control-label'));?>
						<?php echo $form->dropDownList($list, 'opt_out', $list->getOptOutArray(), $list->getHtmlOptions('opt_out')); ?>
						<?php echo $form->error($list, 'opt_out');?>
					</div>
					<div class="clearfix"><!-- --></div>
					<div class="form-group col-lg-6">
						<?php echo $form->labelEx($list, 'welcome_email', array('class' => 'control-label'));?>
						<?php echo $form->dropDownList($list, 'welcome_email', $list->getYesNoOptions(), $list->getHtmlOptions('welcome_email')); ?>
						<?php echo $form->error($list, 'welcome_email');?>
					</div>
					<div class="form-group col-lg-6">
						<?php echo $form->labelEx($list, 'subscriber_404_redirect', array('class' => 'control-label'));?>
						<?php echo $form->textField($list, 'subscriber_404_redirect', $list->getHtmlOptions('subscriber_404_redirect')); ?>
						<?php echo $form->error($list, 'subscriber_404_redirect');?>
					</div>
					<div class="clearfix"><!-- --></div>
					<div class="form-group col-lg-6">
						<?php echo $form->labelEx($list, 'subscriber_exists_redirect', array('class' => 'control-label'));?>
						<?php echo $form->textField($list, 'subscriber_exists_redirect', $list->getHtmlOptions('subscriber_exists_redirect')); ?>
						<?php echo $form->error($list, 'subscriber_exists_redirect');?>
					</div>
					<div class="form-group col-lg-6">
						<?php echo $form->labelEx($list, 'subscriber_require_approval', array('class' => 'control-label'));?>
						<?php echo $form->dropDownList($list, 'subscriber_require_approval', $list->getYesNoOptions(), $list->getHtmlOptions('subscriber_require_approval')); ?>
						<?php echo $form->error($list, 'subscriber_require_approval');?>
					</div>
					<div class="clearfix"><!-- --></div>
				</div>
			</div>
			
			<div class="portlet light bordered margin-bottom-20 no-padding">
				<div class="portlet-title">
					<div class="caption margin">
						<span class="caption-subject font-dark sbold uppercase">
							<?php echo Yii::t('lists', 'Defaults');?>
						</span>
					</div>
				</div>				
				<div class="portlet-body no-padding">
					<div class="form-group col-lg-6">
						<?php echo $form->labelEx($listDefault, 'from_name', array('class' => 'control-label'));?>
						<?php echo $form->textField($listDefault, 'from_name', $listDefault->getHtmlOptions('from_name')); ?>
						<?php echo $form->error($listDefault, 'from_name');?>
					</div>
					<div class="form-group col-lg-6">
						<?php echo $form->labelEx($listDefault, 'from_email', array('class' => 'control-label'));?>
						<?php echo $form->textField($listDefault, 'from_email', $listDefault->getHtmlOptions('from_email')); ?>
						<?php echo $form->error($listDefault, 'from_email');?>
					</div>
					<div class="clearfix"><!-- --></div>
					<div class="form-group col-lg-6">
						<?php echo $form->labelEx($listDefault, 'reply_to', array('class' => 'control-label'));?>
						<?php echo $form->textField($listDefault, 'reply_to', $listDefault->getHtmlOptions('reply_to')); ?>
						<?php echo $form->error($listDefault, 'reply_to');?>
					</div>
					<div class="form-group col-lg-6">
						<?php echo $form->labelEx($listDefault, 'subject', array('class' => 'control-label'));?>
						<?php echo $form->textField($listDefault, 'subject', $listDefault->getHtmlOptions('subject')); ?>
						<?php echo $form->error($listDefault, 'subject');?>
					</div>
					<div class="clearfix"><!-- --></div>
				</div>
			</div>
			
			<div class="portlet light bordered margin-bottom-20 no-padding">
				<div class="portlet-title">
					<div class="caption margin">
						<span class="caption-subject font-dark sbold uppercase">
							<?php echo Yii::t('lists', 'Notifications');?>
						</span>
					</div>
				</div>	
				<div class="portlet-body no-padding">
					<div class="form-group col-lg-6">
						<?php echo $form->labelEx($listCustomerNotification, 'subscribe', array('class' => 'control-label'));?>
						<?php echo $form->dropDownList($listCustomerNotification, 'subscribe', $listCustomerNotification->getYesNoDropdownOptions(),$listCustomerNotification->getHtmlOptions('subscribe')); ?>
						<?php echo $form->error($listCustomerNotification, 'subscribe');?>
					</div>
					<div class="form-group col-lg-6">
						<?php echo $form->labelEx($listCustomerNotification, 'unsubscribe', array('class' => 'control-label'));?>
						<?php echo $form->dropDownList($listCustomerNotification, 'unsubscribe', $listCustomerNotification->getYesNoDropdownOptions(),$listCustomerNotification->getHtmlOptions('unsubscribe')); ?>
						<?php echo $form->error($listCustomerNotification, 'unsubscribe');?>
					</div>
					<div class="clearfix"><!-- --></div>
					<div class="form-group col-lg-6">
						<?php echo $form->labelEx($listCustomerNotification, 'subscribe_to', array('class' => 'control-label'));?>
						<?php echo $form->textField($listCustomerNotification, 'subscribe_to', $listCustomerNotification->getHtmlOptions('subscribe_to')); ?>
						<?php echo $form->error($listCustomerNotification, 'subscribe_to');?>
					</div>
					<div class="form-group col-lg-6">
						<?php echo $form->labelEx($listCustomerNotification, 'unsubscribe_to', array('class' => 'control-label'));?>
						<?php echo $form->textField($listCustomerNotification, 'unsubscribe_to', $listCustomerNotification->getHtmlOptions('unsubscribe_to')); ?>
						<?php echo $form->error($listCustomerNotification, 'unsubscribe_to');?>
					</div>
					<div class="clearfix"><!-- --></div>
				</div>
			</div>
			
			<div class="portlet light bordered margin-bottom-20 no-padding">
				<div class="portlet-title">
					<div class="caption margin">
						<span class="caption-subject font-dark sbold uppercase">
							<?php echo Yii::t('lists', 'Subscriber actions');?>
						</span>
					</div>
				</div>	
				<div class="portlet-body no-padding margin-left-15 margin-right-15">
					<ul class="nav nav-tabs">
						<li class="active">
							<a href="#tab-subscriber-action-when-subscribe" data-toggle="tab">
								<?php echo Yii::t('lists', 'Actions when subscribe');?>
							</a>
						</li>
						<li>
							<a href="#tab-subscriber-action-when-unsubscribe" data-toggle="tab">
								<?php echo Yii::t('lists', 'Actions when unsubscribe');?>
							</a>
						</li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active" id="tab-subscriber-action-when-subscribe">
							<div class="alert alert-success margin-bottom-15">
								<?php echo Yii::t('lists', 'When a subscriber will subscribe into this list, if he exists in any of the lists below, unsubscribe him from them too. Please note that the unsubscribe from the lists below is silent, no email is sent to the subscriber.');?>
							</div>
							<div class="form-group">
								<div class="list-subscriber-actions-scrollbox">
									<ul class="list-group">
									<?php echo CHtml::checkBoxList($listSubscriberAction->modelName . '['. ListSubscriberAction::ACTION_SUBSCRIBE .'][]', $selectedSubscriberActions[ListSubscriberAction::ACTION_SUBSCRIBE], $subscriberActionLists, $listSubscriberAction->getHtmlOptions('target_list_id', array(
										'class'        => '',
										'template'     => '<li class="list-group-item">{beginLabel}{input} {labelTitle}<span></span> {endLabel}</li>',
										'container'    => '',
										'separator'    => '',
										'labelOptions' => array('class' => 'mt-checkbox mt-checkbox-outline no-margin')
									))); ?>
									</ul>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="tab-subscriber-action-when-unsubscribe">
							<div class="alert alert-success margin-bottom-15">
								<?php echo Yii::t('lists', 'When a subscriber will unsubscribe from this list, if he exists in any of the lists below, unsubscribe him from them too. Please note that the unsubscribe from the lists below is silent, no email is sent to the subscriber.');?>
							</div>
							<div class="form-group">
								<div class="list-subscriber-actions-scrollbox">
									<ul class="list-group">
									<?php echo CHtml::checkBoxList($listSubscriberAction->modelName . '['. ListSubscriberAction::ACTION_UNSUBSCRIBE .'][]', $selectedSubscriberActions[ListSubscriberAction::ACTION_UNSUBSCRIBE], $subscriberActionLists, $listSubscriberAction->getHtmlOptions('target_list_id', array(
										'class'        => '',
										'template'     => '<li class="list-group-item">{beginLabel}{input} {labelTitle}<span></span> {endLabel}</li>',
										'container'    => '',
										'separator'    => '',
										'labelOptions' => array('class' => 'mt-checkbox mt-checkbox-outline no-margin')
									))); ?>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="portlet light bordered margin-bottom-20 no-padding">
				<div class="portlet-title">
					<div class="caption margin">
						<span class="caption-subject font-dark sbold uppercase">
							<?php echo Yii::t('lists', 'Company details');?> <small>(<?php echo Yii::t('lists', 'defaults to <a href="{href}">account company</a>', array('{href}' => $this->createUrl('account/company')));?>)</small>
						</span>
					</div>
				</div>	
				<div class="portlet-body no-padding">
					<div class="form-group col-lg-4">
						<?php echo $form->labelEx($listCompany, 'name', array('class' => 'control-label'));?>
						<?php echo $form->textField($listCompany, 'name', $listCompany->getHtmlOptions('name')); ?>
						<?php echo $form->error($listCompany, 'name');?>
					</div>
					<div class="form-group col-lg-4">
						<?php echo $form->labelEx($listCompany, 'type_id', array('class' => 'control-label'));?>
						<?php echo $form->dropDownList($listCompany, 'type_id', CMap::mergeArray(array('' => Yii::t('app', 'Please select')), CompanyType::getListForDropDown()), $listCompany->getHtmlOptions('type_id')); ?>
						<?php echo $form->error($listCompany, 'type_id');?>
					</div>
					<div class="form-group col-lg-4">
						<?php echo $form->labelEx($listCompany, 'country_id', array('class' => 'control-label'));?>
						<?php echo $listCompany->getCountriesDropDown(); ?>
						<?php echo $form->error($listCompany, 'country_id');?>
					</div>
					<div class="clearfix"><!-- --></div>
					<div class="form-group col-lg-4">
						<?php echo $form->labelEx($listCompany, 'zone_id', array('class' => 'control-label'));?>
						<?php echo $listCompany->getZonesDropDown(); ?>
						<?php echo $form->error($listCompany, 'zone_id');?>
					</div>
					<div class="form-group col-lg-4">
						<?php echo $form->labelEx($listCompany, 'address_1', array('class' => 'control-label'));?>
						<?php echo $form->textField($listCompany, 'address_1', $listCompany->getHtmlOptions('address_1')); ?>
						<?php echo $form->error($listCompany, 'address_1');?>
					</div>
					<div class="form-group col-lg-4">
						<?php echo $form->labelEx($listCompany, 'address_2', array('class' => 'control-label'));?>
						<?php echo $form->textField($listCompany, 'address_2', $listCompany->getHtmlOptions('address_2')); ?>
						<?php echo $form->error($listCompany, 'address_2');?>
					</div>
					<div class="clearfix"><!-- --></div>
					<div class="zone-name-wrap">
						<div class="form-group col-lg-4">
							<?php echo $form->labelEx($listCompany, 'zone_name', array('class' => 'control-label'));?>
							<?php echo $form->textField($listCompany, 'zone_name', $listCompany->getHtmlOptions('zone_name')); ?>
							<?php echo $form->error($listCompany, 'zone_name');?>
						</div>
					</div>
					<div class="city-wrap">
						<div class="form-group col-lg-4">
							<?php echo $form->labelEx($listCompany, 'city', array('class' => 'control-label'));?>
							<?php echo $form->textField($listCompany, 'city', $listCompany->getHtmlOptions('city')); ?>
							<?php echo $form->error($listCompany, 'city');?>
						</div>
					</div>
					<div class="zip-wrap">
						<div class="form-group col-lg-4">
							<?php echo $form->labelEx($listCompany, 'zip_code', array('class' => 'control-label'));?>
							<?php echo $form->textField($listCompany, 'zip_code', $listCompany->getHtmlOptions('zip_code')); ?>
							<?php echo $form->error($listCompany, 'zip_code');?>
						</div>
					</div>
					<div class="clearfix"><!-- --></div>
					<div class="phone-wrap">
						<div class="form-group col-lg-4">
							<?php echo $form->labelEx($listCompany, 'phone', array('class' => 'control-label'));?>
							<?php echo $form->textField($listCompany, 'phone', $listCompany->getHtmlOptions('phone')); ?>
							<?php echo $form->error($listCompany, 'phone');?>
						</div>
					</div>
					<div class="form-group col-lg-4">
						<?php echo $form->labelEx($listCompany, 'website', array('class' => 'control-label'));?>
						<?php echo $form->textField($listCompany, 'website', $listCompany->getHtmlOptions('website')); ?>
						<?php echo $form->error($listCompany, 'website');?>
					</div>
					<div class="clearfix"><!-- --></div>
					<div class="form-group col-lg-12">
						<?php echo $form->labelEx($listCompany, 'address_format', array('class' => 'control-label'));?> [<a data-toggle="modal" href="#company-available-tags-modal"><?php echo Yii::t('lists', 'Available tags');?></a>]
						<?php echo $form->textArea($listCompany, 'address_format', $listCompany->getHtmlOptions('address_format', array('rows' => 4))); ?>
						<?php echo $form->error($listCompany, 'address_format');?>
					</div>
					<div class="clearfix"><!-- --></div>
				</div>
			</div>			
			<?php
			/**
			 * This hook gives a chance to append content after the active form fields.
			 * Please note that from inside the action callback you can access all the controller view variables
			 * via {@CAttributeCollection $collection->controller->data}
			 * @since 1.3.3.1
			 */
			$hooks->doAction('after_active_form_fields', new CAttributeCollection(array(
				'controller'    => $this,
				'form'          => $form
			)));
			?>
			<div class="row">
				<div class="col-md-12">
					<button type="submit" class="btn green btn-submit" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>"><?php echo Yii::t('app', 'Save changes');?></button>
				</div>
			</div>
		</div>
        <?php
        $this->endWidget();
    }
    /**
     * This hook gives a chance to append content after the active form fields.
     * Please note that from inside the action callback you can access all the controller view variables
     * via {@CAttributeCollection $collection->controller->data}
     * @since 1.3.3.1
     */
    $hooks->doAction('after_active_form', new CAttributeCollection(array(
        'controller'      => $this,
        'renderedForm'    => $collection->renderForm,
    )));
    ?>
    <div class="modal fade" id="company-available-tags-modal" tabindex="-1" role="dialog" aria-labelledby="company-available-tags-modal-label" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title"><?php echo Yii::t('lists', 'Available tags');?></h4>
            </div>
            <div class="modal-body" style="max-height: 300px; overflow-y:scroll;">
                <table class="table table-bordered table-hover table-striped">
                    <tr>
                        <td><?php echo Yii::t('lists', 'Tag');?></td>
                        <td><?php echo Yii::t('lists', 'Required');?></td>
                    </tr>
                    <?php foreach ($listCompany->getAvailableTags() as $tag) { ?>
                    <tr>
                        <td><?php echo CHtml::encode($tag['tag']);?></td>
                        <td><?php echo $tag['required'] ? strtoupper(Yii::t('app', ListCompany::TEXT_YES)) : strtoupper(Yii::t('app', ListCompany::TEXT_NO));?></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn dark btn-outline" data-dismiss="modal"><?php echo Yii::t('app', 'Close');?></button>
            </div>
          </div>
        </div>
    </div>
<?php
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

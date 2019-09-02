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
    <div class="tabs-container">
    <?php 
    //echo $this->renderTabs();
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
    
    // and render only if allowed
    if ($collection->renderForm) {
        $form = $this->beginWidget('CActiveForm', array(
            'htmlOptions' => array('enctype' => 'multipart/form-data')
        )); 
        ?>
        <div class="box box-primary no-top-border">
            <div class="box-body">
         
		         <h3><a href="http://email.unikainfocom.net/customer/index.php/account/ganalyticsreportgraph?pid=<?php echo trim($ga['profile_id']); ?> " >
				 <button type="button" class="btn btn-primary btn-submit">Switch To Graphic Mode</button></a></h3>		

                <div class="clearfix"><!-- --></div>
				<!--<br>-->
				<div class="">

				
				
		
<?php //print_r($ga['profile_id']); 
require 'gapi.class.php' ;
define('ga_profile_id',trim($ga['profile_id']));
//define('ga_profile_id','100600522');
$ga = new gapi("486869175061-compute@developer.gserviceaccount.com", "uploads/key.p12");	
?>


<?php
$ga->requestReportData(ga_profile_id,array('browser'),array('pageviews','visits'));
?>

<div class="col-md-12">
	<h4 style="color: red;">BROWSER </h4>
	<table class="table table-bordered table-hover">
	<tr>
	  <th>Browser </th>
	  <th>Pageviews</th>
	  <th>Visits</th>
	</tr>
	<?php
	foreach($ga->getResults() as $result):
	?>
	<tr>
	  <td><?php echo $result ?></td>
	  <td><?php echo $result->getPageviews() ?></td>
	  <td><?php echo $result->getVisits() ?></td>
	</tr>
	<?php
	endforeach
	?>
	</table>

	<table>
		<tr>
		  <th>Total Results</th>
		  <td><?php echo $ga->getTotalResults() ?></td>
		</tr>
		<tr>
		  <th>Total Pageviews</th>
		  <td><?php echo $ga->getPageviews() ?>
		</tr>
		<tr>
		  <th>Total Visits</th>
		  <td><?php echo $ga->getVisits() ?></td>
		</tr>
	</table>
</div>

<div class="col-md-12"> </br><hr><hr></br></div>

<?php
$ga->requestReportData(ga_profile_id,array('country'),array('pageviews','visits'));
?>

<div class="col-md-12">
	<h4 style="color: red;">COUNTRY </h4>
	<table class="table table-bordered table-hover">
	<tr>
	  <th>Country </th>
	  <th>Pageviews</th>
	  <th>Visits</th>
	</tr>
	<?php
	foreach($ga->getResults() as $result):
	?>
	<tr>
	  <td><?php echo $result ?></td>
	  <td><?php echo $result->getPageviews() ?></td>
	  <td><?php echo $result->getVisits() ?></td>
	</tr>
	<?php
	endforeach
	?>
	</table>

	<table>
	<tr>
	  <th>Total Results</th>
	  <td><?php echo $ga->getTotalResults() ?></td>
	</tr>
	<tr>
	  <th>Total Pageviews</th>
	  <td><?php echo $ga->getPageviews() ?>
	</tr>
	<tr>
	  <th>Total Visits</th>
	  <td><?php echo $ga->getVisits() ?></td>
	</tr>
	</table>
</div>

<div class="col-md-12"> </br><hr><hr></br></div>

<?php
$filter = 'country == India';

$ga->requestReportData(ga_profile_id,array('city'),array('pageviews','visits'),'-visits',$filter);

?>

<div class="col-md-12">
	<h4 style="color: red;">CITY </h4>
	<table class="table table-bordered table-hover">
	<tr>
	  <th>City </th>
	  <th>Pageviews</th>
	  <th>Visits</th>
	</tr>
	<?php
	foreach($ga->getResults() as $result):
	?>
	<tr>
	  <td><?php echo $result ?></td>
	  <td><?php echo $result->getPageviews() ?></td>
	  <td><?php echo $result->getVisits() ?></td>
	</tr>
	<?php
	endforeach
	?>
	</table>

	<table>
	<tr>
	  <th>Total Results</th>
	  <td><?php echo $ga->getTotalResults() ?></td>
	</tr>
	<tr>
	  <th>Total Pageviews</th>
	  <td><?php echo $ga->getPageviews() ?>
	</tr>
	<tr>
	  <th>Total Visits</th>
	  <td><?php echo $ga->getVisits() ?></td>
	</tr>
	</table>
</div>





				</div> 
				<br>
                <div class="clearfix"><!-- --></div>
     
            </div>
            <div class="box-footer">
                <div class="pull-right">
                    
					</div>
                <div class="clearfix"><!-- --></div>
            </div>
        </div>
        <?php 
        $this->endWidget(); 
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
    ?>
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
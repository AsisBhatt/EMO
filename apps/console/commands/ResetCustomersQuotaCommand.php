<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * ResetCustomersQuotaCommand
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.7.1
 */

class ResetCustomersQuotaCommand extends ConsoleCommand
{
    public function actionIndex()
    {
        $this->stdout('Looking for customers...');
        
        $criteria = new CDbCriteria();
        $criteria->select = 'customer_id';
        $customers = Customer::model()->findAll($criteria);
        
        if (empty($customers)) {
            $this->stdout('No customer found!');
            return 0;
        }

        $this->stdout('Start processing ' . count($customers) . ' customers...');
        
        foreach ($customers as $_customer) {
            $customer = Customer::model()->findByPk($_customer->customer_id);
            if (empty($customer)) {
                continue;
            }
            $this->stdout(sprintf('Processing %s (ID: %d)', $customer->fullName, $customer->customer_id));
            $customer->resetSendingQuota();
            $this->stdout(sprintf('Done processing %s (ID: %d)', $customer->fullName, $customer->customer_id));
        }

        $this->stdout('Done processing ' . count($customers) . ' customers.');
        
        return 0;
    }
}
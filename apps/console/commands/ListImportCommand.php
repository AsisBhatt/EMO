<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * ListImportCommand
 *
 * Handles the actions for list import related tasks.
 * Most of the logic is borrowed from the web interface importer.
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.5.9
 */

class ListImportCommand extends ConsoleCommand
{
    // the folder path from where we should load files
    public $folder_path;

    // max amount of files to process from the folder
    public $folder_process_files = 10;

    // the list where we want to import into
    public $list_uid;

    // the path where the import file is located
    public $file_path;

    // is verbose
    public $verbose = 0;

    // for external access maybe?
    public $lastMessage = array();

    public function actionFolder()
    {
        if (empty($this->folder_path)) {
            $this->folder_path = Yii::getPathOfAlias('common.runtime.list-import-queue');
        }

        if (!is_dir($this->folder_path) || !is_readable($this->folder_path)) {
            return $this->renderMessage(array(
                'result'  => 'error',
                'message' => Yii::t('list_import', 'Call this command with the --folder_path=XYZ param where XYZ is the full path to the folder you want to monitor.'),
                'return'  => 1,
            ));
        }

        $this->renderMessage(array(
            'result'  => 'info',
            'message' => 'The folder path is: '. $this->folder_path,
        ));

        $files  = FileSystemHelper::readDirectoryContents($this->folder_path, true);
        $pcntl  = CommonHelper::functionExists('pcntl_fork') && CommonHelper::functionExists('pcntl_waitpid');
        $childs = array();

        if ($pcntl) {
            Yii::app()->getDb()->setActive(false);
        }

        if (count($files) > (int)$this->folder_process_files) {
            $files = array_slice($files, (int)$this->folder_process_files);
        }

        $this->renderMessage(array(
            'result'  => 'info',
            'message' => 'Found '. count($files) . ' files (some of them might be already processing)',
        ));

        foreach ($files as $file) {
            if (!$pcntl) {
                $this->processFile($file);
                continue;
            }

            //
            $pid = pcntl_fork();
            if($pid == -1) {
                continue;
            }

            // Parent
            if ($pid) {
                $childs[] = $pid;
            }

            // Child
            if (!$pid) {
                $this->processFile($file);
                exit;
            }
        }

        if ($pcntl) {
            while (count($childs) > 0) {
                foreach ($childs as $key => $pid) {
                    $res = pcntl_waitpid($pid, $status, WNOHANG);
                    if($res == -1 || $res > 0) {
                        unset($childs[$key]);
                    }
                }
                sleep(1);
            }
        }

        return 0;
    }

    protected function processFile($file)
    {
        $this->renderMessage(array(
            'result'  => 'info',
            'message' => 'Processing: ' . $file,
        ));

        $lockName = sha1($file);
        if (!Yii::app()->mutex->acquire($lockName, 5)) {
            return $this->renderMessage(array(
                'result'  => 'info',
                'message' => 'Cannot acquire lock for processing: ' . $file,
                'return'  => 1,
            ));
        }

        if (!is_file($file)) {
            Yii::app()->mutex->release($lockName);
            return $this->renderMessage(array(
                'result'  => 'info',
                'message' => 'The file: "' . $file . '" was removed by another process!',
                'return'  => 1,
            ));
        }

        $fileName  = basename($file);
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $listName  = substr(trim(basename($fileName, $extension), '.'), 0, 13); // maybe uid-1.csv uid-2.txt

        Yii::app()->hooks->doAction('console_command_list_import_before_process', new CAttributeCollection(array(
            'command'    => $this,
            'importType' => $extension,
            'listUid'    => $listName,
            'filePath'   => $file,
        )));

        if ($extension == 'csv') {
            $this->processCsv(array(
                'list_uid'    => $listName,
                'file_path'   => $file,
            ));
        } elseif ($extension == 'txt') {
            $this->processText(array(
                'list_uid'    => $listName,
                'file_path'   => $file,
            ));
        }

        Yii::app()->hooks->doAction('console_command_list_import_after_process', new CAttributeCollection(array(
            'command'    => $this,
            'importType' => $extension,
            'listUid'    => $listName,
            'filePath'   => $file,
        )));

        if (in_array($extension, array('csv', 'txt')) && is_file($file)) {
            @unlink($file);
        }

        Yii::app()->mutex->release($lockName);

        $this->renderMessage(array(
            'result'  => 'info',
            'message' => 'The file: "' . $file . '" was processed!',
        ));
    }

    public function actionCsv()
    {
        Yii::app()->hooks->doAction('console_command_list_import_before_process', new CAttributeCollection(array(
            'command'    => $this,
            'importType' => 'csv',
            'listUid'    => $this->list_uid,
            'filePath'   => $this->file_path,
        )));

        $result = $this->processCsv(array(
            'list_uid'    => $this->list_uid,
            'file_path'   => $this->file_path,
        ));

        Yii::app()->hooks->doAction('console_command_list_import_after_process', new CAttributeCollection(array(
            'command'    => $this,
            'importType' => 'csv',
            'listUid'    => $this->list_uid,
            'filePath'   => $this->file_path,
        )));

        return $result;
    }

    protected function processCsv(array $params)
    {
        if (empty($params['list_uid'])) {
            return $this->renderMessage(array(
                'result'  => 'error',
                'message' => Yii::t('list_import', 'Call this command with the --list_uid=XYZ param where XYZ is the 13 chars unique list id.'),
                'return'  => 1,
            ));
        }

        $list = Lists::model()->findByUid($params['list_uid']);
        if (empty($list)) {
            return $this->renderMessage(array(
                'result'  => 'error',
                'message' => Yii::t('list_import', 'The list with the uid {uid} was not found in database.', array(
                    '{uid}' => $params['list_uid'],
                )),
                'return' => 1,
            ));
        }

        if (empty($params['file_path']) || !is_file($params['file_path'])) {
            return $this->renderMessage(array(
                'result'  => 'error',
                'message' => Yii::t('list_import', 'Call this command with the --file_path=/some/file.csv param where /some/file.csv is the full path to the csv file to be imported.'),
                'return'  => 1,
            ));
        }

        $options      = Yii::app()->options;
        $importAtOnce = (int)$options->get('system.importer.import_at_once', 50);

        ini_set('auto_detect_line_endings', true);

        $delimiter = StringHelper::detectCsvDelimiter($params['file_path']);
        $file      = new SplFileObject($params['file_path']);
        $file->setCsvControl($delimiter);
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE | SplFileObject::READ_AHEAD);
        $columns = $file->current(); // the header

        if (empty($columns)) {
            return $this->renderMessage(array(
                'result'  => 'error',
                'message' => Yii::t('list_import', 'Your file does not contain the header with the fields title!'),
                'return'  => 1,
            ));
        }

        $linesCount         = iterator_count($file);
        $totalFileRecords   = $linesCount - 1; // minus the header

        $file->seek(1);

        $customer              = $list->customer;
        $totalSubscribersCount = 0;
        $listSubscribersCount  = 0;
        $maxSubscribersPerList = (int)$customer->getGroupOption('lists.max_subscribers_per_list', -1);
        $maxSubscribers        = (int)$customer->getGroupOption('lists.max_subscribers', -1);

        if ($maxSubscribers > -1 || $maxSubscribersPerList > -1) {
            $criteria = new CDbCriteria();
            $criteria->select = 'COUNT(DISTINCT(t.email)) as counter';

            if ($maxSubscribers > -1 && ($listsIds = $customer->getAllListsIds())) {
                $criteria->addInCondition('t.list_id', $listsIds);
                $totalSubscribersCount = ListSubscriber::model()->count($criteria);
                if ($totalSubscribersCount >= $maxSubscribers) {
                    return $this->renderMessage(array(
                        'result'  => 'error',
                        'message' => Yii::t('list_import', 'You have reached the maximum number of allowed subscribers.'),
                        'return'  => 1,
                    ));
                }
            }

            if ($maxSubscribersPerList > -1) {
                $criteria->compare('t.list_id', (int)$list->list_id);
                $listSubscribersCount = ListSubscriber::model()->count($criteria);
                if ($listSubscribersCount >= $maxSubscribersPerList) {
                    return $this->renderMessage(array(
                        'result'  => 'error',
                        'message' => Yii::t('list_import', 'You have reached the maximum number of allowed subscribers into this list.'),
                        'return'  => 1,
                    ));
                }
            }
        }

        $criteria = new CDbCriteria();
        $criteria->select = 'field_id, label, tag';
        $criteria->compare('list_id', $list->list_id);
        $fields = ListField::model()->findAll($criteria);

        $foundTags = array();
        $searchReplaceTags = array(
            'E_MAIL'        => 'EMAIL',
            'EMAIL_ADDRESS' => 'EMAIL',
            'EMAILADDRESS'  => 'EMAIL',
        );
        foreach ($fields as $field) {
            if ($field->tag == 'FNAME') {
                $searchReplaceTags['F_NAME']     = 'FNAME';
                $searchReplaceTags['FIRST_NAME'] = 'FNAME';
                $searchReplaceTags['FIRSTNAME']  = 'FNAME';
                continue;
            }
            if ($field->tag == 'LNAME') {
                $searchReplaceTags['L_NAME']    = 'LNAME';
                $searchReplaceTags['LAST_NAME'] = 'LNAME';
                $searchReplaceTags['LASTNAME']  = 'LNAME';
                continue;
            }
        }

        $ioFilter = Yii::app()->ioFilter;
        $columns  = (array)$ioFilter->stripTags($ioFilter->xssClean($columns));
        $columns  = array_map('trim', $columns);

        foreach ($columns as $value) {
            $tagName     = StringHelper::getTagFromString($value);
            $tagName     = str_replace(array_keys($searchReplaceTags), array_values($searchReplaceTags), $tagName);
            $foundTags[] = $tagName;
        }

        $foundEmailTag = false;
        foreach ($foundTags as $tagName) {
            if ($tagName === 'EMAIL') {
                $foundEmailTag = true;
                break;
            }
        }

        if (!$foundEmailTag) {
            return $this->renderMessage(array(
                'result'  => 'error',
                'message' => Yii::t('list_import', 'Cannot find the "email" column in your file!'),
                'return'  => 1,
            ));
        }

        $foundReservedColumns = array();
        foreach ($columns as $columnName) {
            $columnName     = StringHelper::getTagFromString($columnName);
            $columnName     = str_replace(array_keys($searchReplaceTags), array_values($searchReplaceTags), $columnName);
            $tagIsReserved  = TagRegistry::model()->findByAttributes(array('tag' => '['.$columnName.']'));
            if (!empty($tagIsReserved)) {
                $foundReservedColumns[] = $columnName;
            }
        }

        if (!empty($foundReservedColumns)) {
            return $this->renderMessage(array(
                'result'  => 'error',
                'message' => Yii::t('list_import', 'Your list contains the columns: "{columns}" which are system reserved. Please update your file and change the column names!', array(
                    '{columns}' => implode(', ', $foundReservedColumns)
                )),
                'return'  => 1,
            ));
        }

        $rounds      = $totalFileRecords > $importAtOnce ? round($totalFileRecords / $importAtOnce) : 1;
        $mainCounter = 0;
        for ($rCount = 1; $rCount <= $rounds; $rCount++) {
            if ($rCount == 1) {
                $this->renderMessage(array(
                    'message' => Yii::t('list_import', 'Found the following column names: {columns}', array(
                        '{columns}' => implode(', ', $columns)
                    )),
                ));
            }

            $offset = $importAtOnce * ($rCount - 1);
            if ($offset >= $totalFileRecords) {
                return $this->renderMessage(array(
                    'result'  => 'success',
                    'message' => Yii::t('list_import', 'The import process has finished!'),
                    'return'  => 0,
                ));
            }
            $file->seek($offset);

            $csvData     = array();
            $columnCount = count($columns);
            $i           = 0;

            while (!$file->eof()) {

                $row = $file->fgetcsv();
                if (empty($row)) {
                    continue;
                }

                $row = (array)$ioFilter->stripTags($ioFilter->xssClean($row));
                $row = array_map('trim', $row);
                $rowCount = count($row);

                if ($rowCount == 0) {
                    continue;
                }

                $isEmpty = true;
                foreach ($row as $value) {
                    if (!empty($value)) {
                        $isEmpty = false;
                        break;
                    }
                }

                if ($isEmpty) {
                    continue;
                }

                if ($columnCount > $rowCount) {
                    $fill = array_fill($rowCount, $columnCount - $rowCount, '');
                    $row  = array_merge($row, $fill);
                } elseif ($rowCount > $columnCount) {
                    $row  = array_slice($row, 0, $columnCount);
                }

                $csvData[] = array_combine($columns, $row);

                ++$i;

                if ($i >= $importAtOnce) {
                    break;
                }
            }

            $fieldType = ListFieldType::model()->findByAttributes(array(
                'identifier' => 'text',
            ));

            $data = array();
            foreach ($csvData as $row) {
                $rowData = array();
                foreach ($row as $name => $value) {
                    $tagName = StringHelper::getTagFromString($name);
                    $tagName = str_replace(array_keys($searchReplaceTags), array_values($searchReplaceTags), $tagName);

                    $rowData[] = array(
                        'name'      => ucwords(str_replace('_', ' ', $name)),
                        'tagName'   => trim($tagName),
                        'tagValue'  => trim($value),
                    );
                }
                $data[] = $rowData;
            }

            if (empty($data) || count($data) < 1) {
                if ($rCount == 1) {
                    return $this->renderMessage(array(
                        'result'  => 'error',
                        'message' => Yii::t('list_import', 'Your file does not contain enough data to be imported!'),
                        'return'  => 1,
                    ));
                } else {

                    return $this->renderMessage(array(
                        'result'  => 'success',
                        'message' => Yii::t('list_import', 'The import process has finished!'),
                        'return'  => 0,
                    ));
                }
            }

            $tagToModel = array();
            foreach ($data[0] as $sample) {
                if ($rCount == 1) {
                    $this->renderMessage(array(
                        'type'    => 'info',
                        'message' => Yii::t('list_import', 'Checking to see if the tag "{tag}" is defined in your list fields...', array(
                            '{tag}' => CHtml::encode($sample['tagName'])
                        )),
                        'counter' => false,
                    ));
                }

                $model = ListField::model()->findByAttributes(array(
                    'list_id' => $list->list_id,
                    'tag'     => $sample['tagName']
                ));

                if (!empty($model)) {

                    if ($rCount == 1) {
                        $this->renderMessage(array(
                            'type'    => 'info',
                            'message' => Yii::t('list_import', 'The tag "{tag}" is already defined in your list fields.', array(
                                '{tag}' => CHtml::encode($sample['tagName'])
                            )),
                            'counter' => false,
                        ));
                    }

                    $tagToModel[$sample['tagName']] = $model;
                    continue;
                }

                if ($rCount == 1) {
                    $this->renderMessage(array(
                        'type'    => 'info',
                        'message' => Yii::t('list_import', 'The tag "{tag}" is not defined in your list fields, we will try to create it.', array(
                            '{tag}' => CHtml::encode($sample['tagName'])
                        )),
                        'counter' => false,
                    ));
                }

                $model = new ListField();
                $model->type_id = $fieldType->type_id;
                $model->list_id = $list->list_id;
                $model->label   = $sample['name'];
                $model->tag     = $sample['tagName'];

                if ($model->save(false)) {

                    if ($rCount == 1) {
                        $this->renderMessage(array(
                            'type'    => 'success',
                            'message' => Yii::t('list_import', 'The tag "{tag}" has been successfully created.', array(
                                '{tag}' => CHtml::encode($sample['tagName'])
                            )),
                            'counter' => false,
                        ));
                    }

                    $tagToModel[$sample['tagName']] = $model;

                } else {

                    if ($rCount == 1) {
                        $this->renderMessage(array(
                            'type'    => 'error',
                            'message' => Yii::t('list_import', 'The tag "{tag}" cannot be saved, reason: {reason}', array(
                                '{tag}'    => CHtml::encode($sample['tagName']),
                                '{reason}' => '<br />'.$model->shortErrors->getAllAsString()
                            )),
                            'counter' => false,
                        ));
                    }
                }
            }

            // since 1.3.5.9
            $bulkEmails = array();
            foreach ($data as $index => $fields) {
                foreach ($fields as $detail) {
                    if ($detail['tagName'] == 'EMAIL' && !empty($detail['tagValue'])) {
                        $email = $detail['tagValue'];
                        if (!EmailBlacklist::getFromStore($email)) {
                            $bulkEmails[$email] = false;
                        }
                        break;
                    }
                }
            }
            $failures = (array)Yii::app()->hooks->applyFilters('list_import_data_bulk_check_failures', array(), (array)$bulkEmails);
            foreach ($failures as $email => $message) {
                EmailBlacklist::addToBlacklist($email, $message);
            }
            // end 1.3.5.9

            $finished    = false;
            $importCount = 0;
            $importLog   = array();

            // since 1.3.5.9
            Yii::app()->hooks->doAction('list_import_before_processing_data', $collection = new CAttributeCollection(array(
                'data'        => $data,
                'list'        => $list,
                'importLog'   => $importLog,
                'finished'    => $finished,
                'importCount' => $importCount,
                'failures'    => $failures,
                'importType'  => 'csv'
            )));

            $data        = $collection->data;
            $importLog   = $collection->importLog;
            $importCount = $collection->importCount;
            $finished    = $collection->finished;
            $failures    = $collection->failures;
            //

            $transaction = Yii::app()->getDb()->beginTransaction();
            $mustCommitTransaction = true;

            try {

                foreach ($data as $index => $fields) {

                    $email = null;
                    foreach ($fields as $detail) {
                        if ($detail['tagName'] == 'EMAIL' && !empty($detail['tagValue'])) {
                            $email = $detail['tagValue'];
                            break;
                        }
                    }

                    if (empty($email)) {
                        continue;
                    }

                    $mainCounter++;
                    $percent = round(($mainCounter / $totalFileRecords) * 100);

                    $this->renderMessage(array(
                        'type'    => 'info',
                        'message' => '['.$percent.'%] - ' . Yii::t('list_import', 'Checking the list for the email: "{email}"', array(
                            '{email}' => CHtml::encode($email),
                        )),
                        'counter' => false,
                    ));

                    if (!empty($failures[$email])) {
                        $this->renderMessage(array(
                            'type'    => 'error',
                            'message' => '['.$percent.'%] - ' . Yii::t('list_import', 'Failed to save the email "{email}", reason: {reason}', array(
                                '{email}'  => CHtml::encode($email),
                                '{reason}' => '<br />'.$failures[$email],
                            )),
                            'counter' => true,
                        ));
                        continue;
                    }

                    $subscriber = null;
                    if (!empty($email)) {
                        $subscriber = ListSubscriber::model()->findByAttributes(array(
                            'list_id' => $list->list_id,
                            'email'   => $email,
                        ));
                    }

                    if (empty($subscriber)) {

                        $this->renderMessage(array(
                            'type'    => 'info',
                            'message' => '['.$percent.'%] - ' . Yii::t('list_import', 'The email "{email}" was not found, we will try to create it...', array(
                                '{email}' => CHtml::encode($email),
                            )),
                            'counter' => false,
                        ));

                        $subscriber = new ListSubscriber();
                        $subscriber->list_id = $list->list_id;
                        $subscriber->email   = $email;
                        $subscriber->source  = ListSubscriber::SOURCE_IMPORT;
                        $subscriber->status  = ListSubscriber::STATUS_CONFIRMED;

                        $validator = new CEmailValidator();
                        $validator->allowEmpty  = false;
                        $validator->validateIDN = true;
                        if (Yii::app()->options->get('system.common.dns_email_check', false)) {
                            $validator->checkMX   = CommonHelper::functionExists('checkdnsrr');
                            $validator->checkPort = CommonHelper::functionExists('dns_get_record') && CommonHelper::functionExists('fsockopen');
                        }
                        $validEmail = !empty($email) && $validator->validateValue($email);

                        if (!$validEmail) {
                            $subscriber->addError('email', Yii::t('list_import', 'Invalid email address!'));
                        } else {
                            $blacklisted = $subscriber->getIsBlacklisted();
                            if (!empty($blacklisted)) {
                                $subscriber->addError('email', Yii::t('list_import', 'This email address is blacklisted!'));
                            }
                        }

                        if (!$validEmail || $subscriber->hasErrors() || !$subscriber->save()) {
                            $this->renderMessage(array(
                                'type'    => 'error',
                                'message' => '['.$percent.'%] - ' . Yii::t('list_import', 'Failed to save the email "{email}", reason: {reason}', array(
                                    '{email}'  => CHtml::encode($email),
                                    '{reason}' => '<br />'.$subscriber->shortErrors->getAllAsString()
                                )),
                                'counter' => true,
                            ));
                            continue;
                        }

                        $listSubscribersCount++;
                        $totalSubscribersCount++;

                        if ($maxSubscribersPerList > -1 && $listSubscribersCount >= $maxSubscribersPerList) {
                            $finished = Yii::t('lists', 'You have reached the maximum number of allowed subscribers into this list.');
                            break;
                        }

                        if ($maxSubscribers > -1 && $totalSubscribersCount >= $maxSubscribers) {
                            $finished = Yii::t('lists', 'You have reached the maximum number of allowed subscribers.');
                            break;
                        }

                        $this->renderMessage(array(
                            'type'    => 'success',
                            'message' => '['.$percent.'%] - ' . Yii::t('list_import', 'The email "{email}" has been successfully saved.', array(
                                '{email}' => CHtml::encode($email),
                            )),
                            'counter' => true,
                        ));

                    } else {

                        $this->renderMessage(array(
                            'type'    => 'info',
                            'message' => '['.$percent.'%] - ' . Yii::t('list_import', 'The email "{email}" has been found, we will update it.', array(
                                '{email}' => CHtml::encode($email),
                            )),
                            'counter' => true,
                        ));
                    }

                    foreach ($fields as $detail) {
                        if (!isset($tagToModel[$detail['tagName']])) {
                            continue;
                        }
                        $fieldModel = $tagToModel[$detail['tagName']];
                        $valueModel = ListFieldValue::model()->findByAttributes(array(
                            'field_id'      => $fieldModel->field_id,
                            'subscriber_id' => $subscriber->subscriber_id,
                        ));
                        if (empty($valueModel)) {
                            $valueModel = new ListFieldValue();
                            $valueModel->field_id      = $fieldModel->field_id;
                            $valueModel->subscriber_id = $subscriber->subscriber_id;
                        }
                        $valueModel->value = $detail['tagValue'];
                        $valueModel->save();
                    }

                    ++$importCount;

                    if ($finished) {
                        break;
                    }
                }

                $transaction->commit();
                $mustCommitTransaction = false;

            } catch(Exception $e) {

                $transaction->rollback();
                $mustCommitTransaction = false;

                return $this->renderMessage(array(
                    'result'  => 'error',
                    'message' => $e->getMessage(),
                    'return'  => 1,
                ));
            }

            if ($mustCommitTransaction) {
                $transaction->commit();
            }

            if ($finished) {
                return $this->renderMessage(array(
                    'result'  => 'error',
                    'message' => $finished,
                    'return'  => 0,
                ));
            }
        }

        return $this->renderMessage(array(
            'result'  => 'success',
            'message' => Yii::t('list_import', 'The import process has finished!'),
            'return'  => 0,
        ));
    }

    public function actionText()
    {
        Yii::app()->hooks->doAction('console_command_list_import_before_process', new CAttributeCollection(array(
            'command'    => $this,
            'importType' => 'text',
            'listUid'    => $this->list_uid,
            'filePath'   => $this->file_path,
        )));

        $result = $this->processText(array(
            'list_uid'    => $this->list_uid,
            'file_path'   => $this->file_path,
        ));

        Yii::app()->hooks->doAction('console_command_list_import_after_process', new CAttributeCollection(array(
            'command'    => $this,
            'importType' => 'text',
            'listUid'    => $this->list_uid,
            'filePath'   => $this->file_path,
        )));

        return $result;
    }

    protected function processText(array $params)
    {
        if (empty($params['list_uid'])) {
            return $this->renderMessage(array(
                'result'  => 'error',
                'message' => Yii::t('list_import', 'Call this command with the --list_uid=XYZ param where XYZ is the 13 chars unique list id.'),
                'return'  => 1,
            ));
        }

        $list = Lists::model()->findByUid($params['list_uid']);
        if (empty($list)) {
            return $this->renderMessage(array(
                'result'  => 'error',
                'message' => Yii::t('list_import', 'The list with the uid {uid} was not found in database.', array(
                    '{uid}' => $params['list_uid'],
                )),
                'return' => 1,
            ));
        }

        if (empty($params['file_path'])) {
            return $this->renderMessage(array(
                'result'  => 'error',
                'message' => Yii::t('list_import', 'Call this command with the --file_path=/some/file.txt param where /some/file.txt is the full path to the csv file to be imported.'),
                'return'  => 1,
            ));
        }

        $options      = Yii::app()->options;
        $importAtOnce = (int)$options->get('system.importer.import_at_once', 50);
        $pause        = (int)$options->get('system.importer.pause', 1);

        $file = new SplFileObject($params['file_path']);
        // $file->setFlags(SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE | SplFileObject::READ_AHEAD);

        $file->seek($file->getSize());
        $totalFileRecords = $file->key() + 1;
        $file->seek(0);

        $customer              = $list->customer;
        $totalSubscribersCount = 0;
        $listSubscribersCount  = 0;
        $maxSubscribersPerList = (int)$customer->getGroupOption('lists.max_subscribers_per_list', -1);
        $maxSubscribers        = (int)$customer->getGroupOption('lists.max_subscribers', -1);

        if ($maxSubscribers > -1 || $maxSubscribersPerList > -1) {
            $criteria = new CDbCriteria();
            $criteria->select = 'COUNT(DISTINCT(t.email)) as counter';

            if ($maxSubscribers > -1 && ($listsIds = $customer->getAllListsIds())) {
                $criteria->addInCondition('t.list_id', $listsIds);
                $totalSubscribersCount = ListSubscriber::model()->count($criteria);
                if ($totalSubscribersCount >= $maxSubscribers) {
                    return $this->renderMessage(array(
                        'result'  => 'error',
                        'message' => Yii::t('list_import', 'You have reached the maximum number of allowed subscribers.'),
                        'return'  => 1,
                    ));
                }
            }

            if ($maxSubscribersPerList > -1) {
                $criteria->compare('t.list_id', (int)$list->list_id);
                $listSubscribersCount = ListSubscriber::model()->count($criteria);
                if ($listSubscribersCount >= $maxSubscribersPerList) {
                    return $this->renderMessage(array(
                        'result'  => 'error',
                        'message' => Yii::t('list_import', 'You have reached the maximum number of allowed subscribers into this list.'),
                        'return'  => 1,
                    ));
                }
            }
        }

        $rounds = round($totalFileRecords / $importAtOnce);
        for ($rCount = 1; $rCount <= $rounds; $rCount++) {

            $offset = $importAtOnce * ($rCount - 1);
            if ($offset >= $totalFileRecords) {
                return $this->renderMessage(array(
                    'result'  => 'success',
                    'message' => Yii::t('list_import', 'The import process has finished!'),
                    'return'  => 0,
                ));
            }
            $file->seek($offset > 0 ? $offset - 1 : 0);

            $ioFilter = Yii::app()->ioFilter;
            $emails   = array();
            $i        = 0;

            while (!$file->eof()) {
                $emails[] = $ioFilter->xssClean(trim($file->fgets()));
                ++$i;
                if ($i >= $importAtOnce) {
                    break;
                }
            }

            if (empty($emails)) {
                if ($rCount == 1) {
                    return $this->renderMessage(array(
                        'result'  => 'error',
                        'message' => Yii::t('list_import', 'Your file does not contain enough data to be imported!'),
                        'return'  => 1,
                    ));
                } else {
                    return $this->renderMessage(array(
                        'result'  => 'success',
                        'message' => Yii::t('list_import', 'The import process has finished!'),
                        'return'  => 0,
                    ));
                }
            }

            // trim them
            $emails = array_map('trim', $emails);

            // since 1.3.5.9
            $bulkEmails = array();
            foreach ($emails as $email) {
                if (!EmailBlacklist::getFromStore($email)) {
                    $bulkEmails[$email] = false;
                }
            }
            $failures = (array)Yii::app()->hooks->applyFilters('list_import_data_bulk_check_failures', array(), (array)$bulkEmails);
            foreach ($failures as $email => $message) {
                EmailBlacklist::addToBlacklist($email, $message);
            }
            // end 1.3.5.9

            $fieldModel = ListField::model()->findByAttributes(array(
                'list_id' => $list->list_id,
                'tag'     => 'EMAIL',
            ));

            $finished    = false;
            $importCount = 0;
            $importLog   = array();

            // since 1.3.5.9
            Yii::app()->hooks->doAction('list_import_before_processing_data', $collection = new CAttributeCollection(array(
                'data'        => $emails,
                'list'        => $list,
                'importLog'   => $importLog,
                'finished'    => $finished,
                'importCount' => $importCount,
                'failures'    => $failures,
                'importType'  => 'text'
            )));

            $emails      = $collection->data;
            $importLog   = $collection->importLog;
            $importCount = $collection->importCount;
            $finished    = $collection->finished;
            $failures    = $collection->failures;
            //

            $transaction = Yii::app()->getDb()->beginTransaction();
            $mustCommitTransaction = true;

            try {

                foreach ($emails as $email) {

                    $this->renderMessage(array(
                        'type'    => 'info',
                        'message' => Yii::t('list_import', 'Checking the list for the email: "{email}"', array(
                            '{email}' => CHtml::encode($email),
                        )),
                        'counter' => false,
                    ));

                    if (!empty($failures[$email])) {
                        $this->renderMessage(array(
                            'type'    => 'error',
                            'message' => Yii::t('list_import', 'Failed to save the email "{email}", reason: {reason}', array(
                                '{email}'  => CHtml::encode($email),
                                '{reason}' => '<br />'.$failures[$email],
                            )),
                            'counter' => true,
                        ));
                        continue;
                    }

                    $subscriber = null;
                    if (!empty($email)) {
                        $subscriber = ListSubscriber::model()->findByAttributes(array(
                            'list_id' => $list->list_id,
                            'email'   => $email,
                        ));
                    }

                    if (empty($subscriber)) {

                        $this->renderMessage(array(
                            'type'    => 'info',
                            'message' => Yii::t('list_import', 'The email "{email}" was not found, we will try to create it...', array(
                                '{email}' => CHtml::encode($email),
                            )),
                            'counter' => false,
                        ));

                        $subscriber = new ListSubscriber();
                        $subscriber->list_id = $list->list_id;
                        $subscriber->email   = $email;
                        $subscriber->source  = ListSubscriber::SOURCE_IMPORT;
                        $subscriber->status  = ListSubscriber::STATUS_CONFIRMED;

                        $validator = new CEmailValidator();
                        $validator->allowEmpty  = false;
                        $validator->validateIDN = true;
                        if (Yii::app()->options->get('system.common.dns_email_check', false)) {
                            $validator->checkMX   = CommonHelper::functionExists('checkdnsrr');
                            $validator->checkPort = CommonHelper::functionExists('dns_get_record') && CommonHelper::functionExists('fsockopen');
                        }
                        $validEmail = !empty($email) && $validator->validateValue($email);

                        if (!$validEmail) {
                            $subscriber->addError('email', Yii::t('list_import', 'Invalid email address!'));
                        } else {
                            $blacklisted = $subscriber->getIsBlacklisted();
                            if (!empty($blacklisted)) {
                                $subscriber->addError('email', Yii::t('list_import', 'This email address is blacklisted!'));
                            }
                        }

                        if (!$validEmail || $subscriber->hasErrors() || !$subscriber->save()) {
                            $this->renderMessage(array(
                                'type'    => 'error',
                                'message' => Yii::t('list_import', 'Failed to save the email "{email}", reason: {reason}', array(
                                    '{email}'  => CHtml::encode($email),
                                    '{reason}' => '<br />'.$subscriber->shortErrors->getAllAsString()
                                )),
                                'counter' => true,
                            ));
                            continue;
                        }

                        $listSubscribersCount++;
                        $totalSubscribersCount++;

                        if ($maxSubscribersPerList > -1 && $listSubscribersCount >= $maxSubscribersPerList) {
                            $finished = Yii::t('lists', 'You have reached the maximum number of allowed subscribers into this list.');
                            break;
                        }

                        if ($maxSubscribers > -1 && $totalSubscribersCount >= $maxSubscribers) {
                            $finished = Yii::t('lists', 'You have reached the maximum number of allowed subscribers.');
                            break;
                        }

                        $this->renderMessage(array(
                            'type'    => 'success',
                            'message' => Yii::t('list_import', 'The email "{email}" has been successfully saved.', array(
                                '{email}' => CHtml::encode($email),
                            )),
                            'counter' => true,
                        ));

                    } else {

                        $this->renderMessage(array(
                            'type'    => 'info',
                            'message' => Yii::t('list_import', 'The email "{email}" has been found, we will update it.', array(
                                '{email}' => CHtml::encode($email),
                            )),
                            'counter' => true,
                        ));
                    }

                    $valueModel = ListFieldValue::model()->findByAttributes(array(
                        'field_id'      => $fieldModel->field_id,
                        'subscriber_id' => $subscriber->subscriber_id,
                    ));
                    if (empty($valueModel)) {
                        $valueModel = new ListFieldValue();
                        $valueModel->field_id      = $fieldModel->field_id;
                        $valueModel->subscriber_id = $subscriber->subscriber_id;
                    }
                    $valueModel->value = $email;
                    $valueModel->save();

                    ++$importCount;

                    if ($finished) {
                        break;
                    }
                }

                $transaction->commit();
                $mustCommitTransaction = false;

            } catch(Exception $e) {

                $transaction->rollback();
                $mustCommitTransaction = false;

                return $this->renderMessage(array(
                    'result'  => 'error',
                    'message' => $e->getMessage(),
                    'return'  => 1,
                ));
            }

            if ($mustCommitTransaction) {
                $transaction->commit();
            }

            if ($finished) {
                return $this->renderMessage(array(
                    'result'  => 'error',
                    'message' => $finished,
                    'return'  => 1,
                ));
            }
        }

        return $this->renderMessage(array(
            'result'  => 'success',
            'message' => Yii::t('list_import', 'The import process has finished!'),
            'return'  => 0,
        ));
    }

    protected function renderMessage($data = array())
    {
        if (isset($data['type']) && in_array($data['type'], array('success', 'error'))) {
            $this->lastMessage = $data;
        }

        if (isset($data['message']) && $this->verbose) {
            $out = '['.date('Y-m-d H:i:s').'] - ';
            if (isset($data['type'])) {
                $out .= '[' . strtoupper($data['type']) . '] - ';
            }
            $out .= strip_tags(str_replace(array('<br />', '<br/>', '<br>'), PHP_EOL, $data['message'])) . PHP_EOL;
            echo $out;
        }

        if (isset($data['return']) || array_key_exists('return', $data)) {
            return (int)$data['return'];
        }
    }
}

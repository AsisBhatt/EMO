--
-- Install sql for MailWizz EMA
--

-- --------------------------------------------------------

--
-- Table structure for table `article`
--

DROP TABLE IF EXISTS `article`;
CREATE TABLE IF NOT EXISTS `article` (
  `article_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `status` char(15) NOT NULL DEFAULT 'published',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`article_id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `article_category`
--

DROP TABLE IF EXISTS `article_category`;
CREATE TABLE IF NOT EXISTS `article_category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(250) NOT NULL,
  `description` text NULL,
  `status` char(15) NOT NULL DEFAULT 'active',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `fk_article_category_article_category1_idx` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `article_to_category`
--

DROP TABLE IF EXISTS `article_to_category`;
CREATE TABLE IF NOT EXISTS `article_to_category` (
  `article_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`article_id`,`category_id`),
  KEY `fk_article_to_category_article_category1_idx` (`category_id`),
  KEY `fk_article_to_category_article1_idx` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bounce_server`
--

DROP TABLE IF EXISTS `bounce_server`;
CREATE TABLE IF NOT EXISTS `bounce_server` (
  `server_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` INT(11) NULL DEFAULT NULL,
  `hostname` varchar(150) NOT NULL,
  `username` varchar(150) NOT NULL,
  `password` varchar(150) NOT NULL,
  `email` varchar(100) NULL,
  `service` enum('imap','pop3') NOT NULL DEFAULT 'imap',
  `port` int(5) NOT NULL DEFAULT '143',
  `protocol` enum('ssl','tls','notls') NOT NULL DEFAULT 'notls',
  `validate_ssl` enum('yes','no') NOT NULL DEFAULT 'no',
  `locked` enum('yes', 'no') NOT NULL DEFAULT 'no',
  `disable_authenticator` VARCHAR(50) NULL,
  `search_charset` VARCHAR(50) NOT NULL DEFAULT 'UTF-8',
  `delete_all_messages` ENUM('yes','no') NOT NULL DEFAULT 'no',
  `status` char(15) NOT NULL DEFAULT 'active',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`server_id`),
  KEY `fk_bounce_server_customer1_idx` (`customer_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `campaign`
--

DROP TABLE IF EXISTS `campaign`;
CREATE TABLE IF NOT EXISTS `campaign` (
  `campaign_id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_uid` char(13) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `list_id` int(11) NOT NULL,
  `segment_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `type` CHAR(15) NOT NULL DEFAULT 'regular',
  `name` varchar(255) NOT NULL,
  `from_name` varchar(100) NULL,
  `from_email` varchar(100) NOT NULL,
  `to_name` varchar(255) NOT NULL DEFAULT '[EMAIL]',
  `reply_to` varchar(100) NULL,
  `subject` varchar(255) NULL,
  `send_at` datetime NULL,
  `started_at` datetime NULL,
  `finished_at` datetime NULL,
  `delivery_logs_archived` ENUM('yes','no') NOT NULL DEFAULT 'no',
  `status` char(15) NOT NULL DEFAULT 'draft',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`campaign_id`),
  UNIQUE KEY `campaign_uid_UNIQUE` (`campaign_uid`),
  KEY `fk_campaign_list1_idx` (`list_id`),
  KEY `fk_campaign_list_segment1_idx` (`segment_id`),
  KEY `fk_campaign_customer1_idx` (`customer_id`),
  KEY `fk_campaign_campaign_group1_idx` (`group_id`),
  KEY `type` (`type`),
  KEY `status_delivery_logs_archived_campaign_id` (`status`, `delivery_logs_archived`, `campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_abuse_report`
--

DROP TABLE IF EXISTS `campaign_abuse_report`;
CREATE TABLE IF NOT EXISTS `campaign_abuse_report` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `campaign_id` int(11) DEFAULT NULL,
  `list_id` int(11) DEFAULT NULL,
  `subscriber_id` int(11) DEFAULT NULL,
  `customer_info` varchar(255) NOT NULL,
  `campaign_info` varchar(255) NOT NULL,
  `list_info` varchar(255) NOT NULL,
  `subscriber_info` varchar(255) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `log` VARCHAR(255) NULL DEFAULT NULL,
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`report_id`),
  INDEX `fk_campaign_abuse_report_campaign1_idx` (`campaign_id`),
  INDEX `fk_campaign_abuse_report_customer1_idx` (`customer_id`),
  INDEX `fk_campaign_abuse_report_list1_idx` (`list_id`),
  INDEX `fk_campaign_abuse_report_list_subscriber1_idx` (`subscriber_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_attachment`
--

DROP TABLE IF EXISTS `campaign_attachment`;
CREATE TABLE IF NOT EXISTS `campaign_attachment` (
  `attachment_id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `file` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `size` int(11) NOT NULL DEFAULT '0',
  `extension` CHAR(10) NOT NULL,
  `mime_type` varchar(50) NOT NULL,
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`attachment_id`),
  KEY `fk_campaign_attachment_campaign1_idx` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_bounce_log`
--

DROP TABLE IF EXISTS `campaign_bounce_log`;
CREATE TABLE IF NOT EXISTS `campaign_bounce_log` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `message` text NULL,
  `bounce_type` enum('hard','soft','internal') NOT NULL DEFAULT 'hard',
  `processed` enum('yes','no') NOT NULL DEFAULT 'no',
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`log_id`),
  UNIQUE KEY `cid_sid` (`campaign_id`, `subscriber_id`),
  KEY `fk_campaign_bounce_log_campaign1_idx` (`campaign_id`),
  KEY `fk_campaign_bounce_log_list_subscriber1_idx` (`subscriber_id`),
  KEY `sub_proc_bt` (`subscriber_id`,`processed`,`bounce_type`),
  KEY `proc_bt` (`processed`,`bounce_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_delivery_log`
--

DROP TABLE IF EXISTS `campaign_delivery_log`;
CREATE TABLE IF NOT EXISTS `campaign_delivery_log` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `server_id` int(11) NULL DEFAULT NULL,
  `message` text NULL,
  `processed` enum('yes','no') NOT NULL DEFAULT 'no',
  `retries` int(1) NOT NULL DEFAULT '0',
  `max_retries` int(1) NOT NULL DEFAULT '3',
  `email_message_id` varchar(255) NULL,
  `delivery_confirmed` ENUM('yes','no') NOT NULL DEFAULT 'yes',
  `status` char(15) NOT NULL DEFAULT 'success',
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `fk_campaign_delivery_log_list_subscriber1_idx` (`subscriber_id`),
  KEY `fk_campaign_delivery_log_campaign1_idx` (`campaign_id`),
  KEY `fk_campaign_delivery_log_delivery_server1_idx` (`server_id`),
  KEY `sub_proc_status` (`subscriber_id`,`processed`,`status`),
  KEY `proc_status` (`processed`,`status`),
  KEY `email_message_id` (`email_message_id`),
  KEY `cid_status`(`campaign_id`, `status`),
  KEY `cid_date_added`(`campaign_id`, `date_added`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_delivery_log_archive`
--

DROP TABLE IF EXISTS `campaign_delivery_log_archive`;
CREATE TABLE IF NOT EXISTS `campaign_delivery_log_archive` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `server_id` int(11) NULL DEFAULT NULL,
  `message` text NULL,
  `processed` enum('yes','no') NOT NULL DEFAULT 'no',
  `retries` int(1) NOT NULL DEFAULT '0',
  `max_retries` int(1) NOT NULL DEFAULT '3',
  `email_message_id` varchar(255) NULL,
  `delivery_confirmed` enum('yes','no') NOT NULL DEFAULT 'yes',
  `status` char(15) NOT NULL DEFAULT 'success',
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `fk_campaign_delivery_log_archive_list_subscriber1_idx` (`subscriber_id`),
  KEY `fk_campaign_delivery_log_archive_campaign1_idx` (`campaign_id`),
  KEY `fk_campaign_delivery_log_archive_delivery_server1_idx` (`server_id`),
  KEY `sub_proc_status` (`subscriber_id`,`processed`,`status`),
  KEY `proc_status` (`processed`,`status`),
  KEY `email_message_id` (`email_message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_forward_friend`
--

DROP TABLE IF EXISTS `campaign_forward_friend`;
CREATE TABLE IF NOT EXISTS `campaign_forward_friend` (
  `forward_id` INT NOT NULL AUTO_INCREMENT,
  `campaign_id` INT(11) NOT NULL,
  `subscriber_id` INT(11) NULL,
  `to_email` VARCHAR(150) NOT NULL,
  `to_name` VARCHAR(150) NOT NULL,
  `from_email` VARCHAR(150) NOT NULL,
  `from_name` VARCHAR(150) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `ip_address` CHAR(15) NOT NULL,
  `user_agent` VARCHAR(255) NOT NULL,
  `date_added` DATETIME NOT NULL,
  `last_updated` DATETIME NOT NULL,
  PRIMARY KEY (`forward_id`),
  KEY `fk_campaign_forward_friend_campaign1_idx` (`campaign_id` ASC),
  KEY `fk_campaign_forward_friend_list_subscriber1_idx` (`subscriber_id` ASC))
ENGINE = InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_group`
--

DROP TABLE IF EXISTS `campaign_group`;
CREATE TABLE IF NOT EXISTS `campaign_group` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_uid` char(13) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`group_id`),
  UNIQUE KEY `group_uid` (`group_uid`),
  KEY `fk_campaign_group_customer1_idx` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_open_action_list_field`
--

DROP TABLE IF EXISTS `campaign_open_action_list_field`;
CREATE TABLE IF NOT EXISTS `campaign_open_action_list_field` (
  `action_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `list_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `field_value` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`action_id`),
  KEY `fk_campaign_open_action_list_field_list1_idx` (`list_id`),
  KEY `fk_campaign_open_action_list_field_campaign1_idx` (`campaign_id`),
  KEY `fk_campaign_open_action_list_field_list_field1_idx` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_open_action_subscriber`
--

DROP TABLE IF EXISTS `campaign_open_action_subscriber`;
CREATE TABLE IF NOT EXISTS `campaign_open_action_subscriber` (
  `action_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `list_id` int(11) NOT NULL,
  `action` char(5) NOT NULL DEFAULT 'copy',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`action_id`),
  KEY `fk_campaign_open_action_subscriber_campaign1_idx` (`campaign_id`),
  KEY `fk_campaign_open_action_subscriber_list1_idx` (`list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_option`
--

DROP TABLE IF EXISTS `campaign_option`;
CREATE TABLE IF NOT EXISTS `campaign_option` (
  `campaign_id` int(11) NOT NULL,
  `open_tracking` enum('yes','no') NOT NULL DEFAULT 'yes',
  `url_tracking` enum('yes','no') NOT NULL DEFAULT 'no',
  `json_feed` enum('yes','no') NOT NULL DEFAULT 'no',
  `xml_feed` enum('yes','no') NOT NULL DEFAULT 'no',
  `embed_images` enum('yes','no') NOT NULL DEFAULT 'no',
  `plain_text_email` enum('yes','no') NOT NULL DEFAULT 'yes',
  `autoresponder_event` char(20) NOT NULL DEFAULT 'AFTER-SUBSCRIBE',
  `autoresponder_time_unit` varchar(6) NOT NULL DEFAULT 'day',
  `autoresponder_time_value` int(11) NOT NULL DEFAULT '0',
  `autoresponder_open_campaign_id` INT(11) NULL,
  `autoresponder_include_imported` enum('yes','no') NOT NULL DEFAULT 'no',
  `email_stats` varchar(255) NOT NULL,
  `regular_open_unopen_action` char(10) NULL,
  `regular_open_unopen_campaign_id` int(11) NULL,
  `cronjob` VARCHAR(255) NULL,
  `cronjob_enabled` TINYINT(1) NOT NULL DEFAULT '0',
  `blocked_reason` VARCHAR(255) NULL DEFAULT NULL,
  `giveup_counter` INT(11) NOT NULL DEFAULT '0',
  `max_send_count` INT(11) NOT NULL DEFAULT '0',
  `max_send_count_random` enum('yes','no') NOT NULL DEFAULT 'no',
  `tracking_domain_id` INT(11) NULL,
  `preheader` VARCHAR(255) NULL,
  PRIMARY KEY (`campaign_id`),
  KEY `fk_campaign_option_campaign1_idx` (`campaign_id`),
  KEY `fk_campaign_option_campaign2_idx` (`autoresponder_open_campaign_id`),
  KEY `fk_campaign_option_campaign3_idx` (`regular_open_unopen_campaign_id`),
  KEY `fk_campaign_option_campaign4_idx` (`tracking_domain_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_template`
--

DROP TABLE IF EXISTS `campaign_template`;
CREATE TABLE IF NOT EXISTS `campaign_template` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `customer_template_id` int(11) NULL,
  `content` longtext NOT NULL,
  `inline_css` enum('yes','no') NOT NULL DEFAULT 'no',
  `minify` enum('yes','no') NOT NULL DEFAULT 'no',
  `plain_text` TEXT NULL DEFAULT NULL,
  `only_plain_text` ENUM('yes','no') NOT NULL DEFAULT 'no',
  `auto_plain_text` ENUM('yes','no') NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`template_id`),
  KEY `fk_customer_email_template1_idx` (`customer_template_id`),
  KEY `fk_campaign_template_campaign1_idx` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_template_url_action_list_field`
--

DROP TABLE IF EXISTS `campaign_template_url_action_list_field`;
CREATE TABLE IF NOT EXISTS `campaign_template_url_action_list_field` (
  `url_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `list_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `field_value` varchar(255) NOT NULL,
  `url` text NOT NULL,
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`url_id`),
  KEY `fk_campaign_template_url_action_list_field_campaign1_idx` (`campaign_id`),
  KEY `fk_campaign_template_url_action_list_field_list1_idx` (`list_id`),
  KEY `fk_campaign_template_url_action_list_field_campaign_temp_idx` (`template_id`),
  KEY `fk_campaign_template_url_action_list_field_list_field1_idx` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_template_url_action_subscriber`
--

DROP TABLE IF EXISTS `campaign_template_url_action_subscriber`;
CREATE TABLE IF NOT EXISTS `campaign_template_url_action_subscriber` (
  `url_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `list_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `url` text NOT NULL,
  `action` char(5) NOT NULL DEFAULT 'copy',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`url_id`),
  KEY `fk_campaign_template_url_action_subscriber_campaign_t_idx` (`template_id`),
  KEY `fk_campaign_template_url_action_subscriber_list1_idx` (`list_id`),
  KEY `fk_campaign_template_url_action_subscriber_campaign1_idx` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_temporary_source`
--

DROP TABLE IF EXISTS `campaign_temporary_source`;
CREATE TABLE IF NOT EXISTS `campaign_temporary_source` (
  `source_id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `list_id` int(11) NOT NULL,
  `segment_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`source_id`),
  KEY `fk_campaign_temporary_source_campaign1_idx` (`campaign_id`),
  KEY `fk_campaign_temporary_source_list1_idx` (`list_id`),
  KEY `fk_campaign_temporary_source_list_segment1_idx` (`segment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_to_delivery_server`
--

DROP TABLE IF EXISTS `campaign_to_delivery_server`;
CREATE TABLE IF NOT EXISTS `campaign_to_delivery_server` (
  `campaign_id` int(11) NOT NULL,
  `server_id` int(11) NOT NULL,
  PRIMARY KEY (`campaign_id`,`server_id`),
  KEY `fk_campaign_to_delivery_server_delivery_server1_idx` (`server_id`),
  KEY `fk_campaign_to_delivery_server_campaign1_idx` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ip_location`
--

DROP TABLE IF EXISTS `ip_location`;
CREATE TABLE IF NOT EXISTS `ip_location` (
  `location_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ip_address` char(15) NOT NULL,
  `country_code` char(3) NOT NULL,
  `country_name` varchar(150) NOT NULL,
  `zone_name` varchar(150) NULL,
  `city_name` varchar(150) NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`location_id`),
  UNIQUE KEY `ip_address_UNIQUE` (`ip_address`))
ENGINE = InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_track_open`
--

DROP TABLE IF EXISTS `campaign_track_open`;
CREATE TABLE IF NOT EXISTS `campaign_track_open` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `location_id` bigint(20) NULL,
  `ip_address` char(15) NULL,
  `user_agent` varchar(255) NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_campaign_track_open_campaign1_idx` (`campaign_id`),
  KEY `fk_campaign_track_open_list_subscriber1_idx` (`subscriber_id`),
  KEY `fk_campaign_track_open_ip_location1_idx` (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_track_unsubscribe`
--

DROP TABLE IF EXISTS `campaign_track_unsubscribe`;
CREATE TABLE IF NOT EXISTS `campaign_track_unsubscribe` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `location_id` bigint(20) NULL,
  `ip_address` char(15) NULL,
  `user_agent` varchar(255) NULL,
  `reason` varchar(255) NULL,
  `note` varchar(255) NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_campaign_track_unsubscribe_campaign1_idx` (`campaign_id`),
  KEY `fk_campaign_track_unsubscribe_list_subscriber1_idx` (`subscriber_id`),
  KEY `fk_campaign_track_unsubscribe_ip_location1_idx` (`location_id`),
  KEY `date_added` (`date_added`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_track_url`
--

DROP TABLE IF EXISTS `campaign_track_url`;
CREATE TABLE IF NOT EXISTS `campaign_track_url` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `url_id` bigint(20) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `location_id` bigint(20) NULL,
  `ip_address` char(15) NULL,
  `user_agent` varchar(255) NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_campaign_track_url_list_subscriber1_idx` (`subscriber_id`),
  KEY `fk_campaign_track_url_ip_location1_idx` (`location_id`),
  KEY `fk_campaign_track_url_campaign_url1_idx` (`url_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_url`
--

DROP TABLE IF EXISTS `campaign_url`;
CREATE TABLE IF NOT EXISTS `campaign_url` (
  `url_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `hash` char(40) NOT NULL,
  `destination` text NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`url_id`),
  KEY `campaign_hash` (`campaign_id`,`hash`),
  KEY `fk_campaign_url_campaign1_idx` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
CREATE TABLE IF NOT EXISTS `country` (
  `country_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `code` char(3) NOT NULL,
  `status` char(10) NOT NULL DEFAULT 'active',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=240 ;

-- --------------------------------------------------------

--
-- Table structure for table `currency`
--

DROP TABLE IF EXISTS `currency`;
CREATE TABLE IF NOT EXISTS `currency` (
  `currency_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `code` char(3) NOT NULL,
  `value` decimal(15,8) NOT NULL DEFAULT '0.00000000',
  `is_default` enum('yes','no') NOT NULL DEFAULT 'no',
  `status` char(15) NOT NULL DEFAULT 'active',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`currency_id`),
  UNIQUE KEY `code_UNIQUE` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2;

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS `customer`;
CREATE TABLE IF NOT EXISTS `customer` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_uid` char(13) NOT NULL,
  `group_id` int(11) NULL,
  `language_id` int(11) NULL,
  `first_name` varchar(100) NULL,
  `last_name` varchar(100) NULL,
  `email` varchar(100) NOT NULL,
  `password` char(34) NOT NULL,
  `timezone` varchar(50) NOT NULL,
  `avatar` VARCHAR(255) NULL,
  `hourly_quota` INT NOT NULL DEFAULT '0',
  `removable` enum('yes','no') NOT NULL DEFAULT 'yes',
  `confirmation_key` char(40) NULL,
  `oauth_uid` bigint(20) NULL,
  `oauth_provider` char(10) NULL,
  `status` char(15) NOT NULL DEFAULT 'inactive',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `customer_uid_UNIQUE` (`customer_uid`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  KEY `fk_customer_language1_idx` (`language_id`),
  KEY `fk_customer_customer_group1_idx` (`group_id`),
  KEY `oauth` (`oauth_uid`, `oauth_provider`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `customer_action_log`
--

DROP TABLE IF EXISTS `customer_action_log`;
CREATE TABLE IF NOT EXISTS `customer_action_log` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL DEFAULT 'info',
  `reference_id` int(11) NOT NULL DEFAULT '0',
  `reference_relation_id` int(11) NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `fk_customer_notification_log_customer1_idx` (`customer_id`),
  KEY `customer_category_reference` (`customer_id`,`category`,`reference_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `customer_api_key`
--

DROP TABLE IF EXISTS `customer_api_key`;
CREATE TABLE IF NOT EXISTS `customer_api_key` (
  `key_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `public` char(40) NOT NULL,
  `private` char(40) NOT NULL,
  `ip_whitelist` varchar(255) DEFAULT NULL,
  `ip_blacklist` varchar(255) DEFAULT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`key_id`),
  UNIQUE KEY `public_UNIQUE` (`public`),
  UNIQUE KEY `private_UNIQUE` (`private`),
  KEY `fk_customer_api_key_customer1_idx` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `customer_auto_login_token`
--

DROP TABLE IF EXISTS `customer_auto_login_token`;
CREATE TABLE IF NOT EXISTS `customer_auto_login_token` (
  `token_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `token` char(40) NOT NULL,
  PRIMARY KEY (`token_id`),
  KEY `fk_customer_auto_login_token_customer1_idx` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `customer_company`
--

DROP TABLE IF EXISTS `customer_company`;
CREATE TABLE IF NOT EXISTS `customer_company` (
  `company_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `type_id` int(11) DEFAULT NULL,
  `country_id` int(11) NOT NULL,
  `zone_id` int(11) NULL,
  `name` varchar(100) NOT NULL,
  `website` varchar(255) NULL,
  `address_1` varchar(255) NOT NULL,
  `address_2` varchar(255) NULL,
  `zone_name` varchar(150) NULL,
  `city` varchar(150) NOT NULL,
  `zip_code` char(10) NOT NULL,
  `phone` varchar(32) NULL,
  `fax` varchar(32) NULL,
  `vat_number` varchar(100) NULL,
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`company_id`),
  KEY `fk_customer_company_country1_idx` (`country_id`),
  KEY `fk_customer_company_zone1_idx` (`zone_id`),
  KEY `fk_customer_company_customer1_idx` (`customer_id`),
  KEY `fk_customer_company_company_type1_idx` (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `company_type`
--

DROP TABLE IF EXISTS `company_type`;
CREATE TABLE IF NOT EXISTS `company_type` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`type_id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `customer_email_template`
--

DROP TABLE IF EXISTS `customer_email_template`;
CREATE TABLE IF NOT EXISTS `customer_email_template` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_uid` char(13) NOT NULL,
  `customer_id` int(11) NULL,
  `name` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `content_hash` char(40) NOT NULL,
  `create_screenshot` enum('yes','no') NOT NULL DEFAULT 'yes',
  `screenshot` varchar(255) NULL,
  `inline_css` enum('yes','no') NOT NULL DEFAULT 'no',
  `minify` enum('yes','no') NOT NULL DEFAULT 'no',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`template_id`),
  KEY `fk_customer_email_template_customer1_idx` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `customer_group`
--

DROP TABLE IF EXISTS `customer_group`;
CREATE TABLE IF NOT EXISTS `customer_group` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `is_default` enum('yes','no') NOT NULL DEFAULT 'no',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `customer_group_option`
--

DROP TABLE IF EXISTS `customer_group_option`;
CREATE TABLE IF NOT EXISTS `customer_group_option` (
  `option_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `is_serialized` tinyint(1) NOT NULL DEFAULT '0',
  `value` longblob,
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`option_id`),
  KEY `fk_customer_group_option_customer_group1_idx` (`group_id`),
  KEY `group_code` (`group_id`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `customer_password_reset`
--

DROP TABLE IF EXISTS `customer_password_reset`;
CREATE TABLE IF NOT EXISTS `customer_password_reset` (
  `request_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `reset_key` char(40) NOT NULL,
  `ip_address` char(15) NULL,
  `status` char(15) NOT NULL DEFAULT 'active',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`request_id`),
  KEY `fk_customer_password_reset_customer1` (`customer_id`),
  KEY `key_status` (`reset_key`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `customer_quota_mark`
--

DROP TABLE IF EXISTS `customer_quota_mark`;
CREATE TABLE IF NOT EXISTS `customer_quota_mark` (
  `mark_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`mark_id`),
  KEY `fk_customer_quota_mark_customer1_idx` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_server`
--

DROP TABLE IF EXISTS `delivery_server`;
CREATE TABLE IF NOT EXISTS `delivery_server` (
  `server_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` INT(11) NULL DEFAULT NULL,
  `bounce_server_id` int(11) NULL,
  `tracking_domain_id` int(11) NULL,
  `type` char(20) NOT NULL,
  `name` varchar(150) NULL,
  `hostname` varchar(150) NOT NULL,
  `username` varchar(150) NOT NULL,
  `password` varchar(255) NULL,
  `port` int(5) NULL DEFAULT '25',
  `protocol` char(10) NULL,
  `timeout` int(3) NULL DEFAULT '30',
  `from_email` varchar(150) NOT NULL,
  `from_name` varchar(150) NULL,
  `reply_to_email` VARCHAR(150) NULL DEFAULT NULL,
  `probability` int(3) NOT NULL DEFAULT '100',
  `hourly_quota` int(11) NOT NULL DEFAULT '0',
  `monthly_quota` int(11) NOT NULL DEFAULT '0',
  `meta_data` blob,
  `confirmation_key` char(40) NULL,
  `locked` enum('yes', 'no') NOT NULL DEFAULT 'no',
  `use_for` CHAR(15) NOT NULL DEFAULT 'all',
  `use_queue` enum('yes', 'no') NOT NULL DEFAULT 'no',
  `signing_enabled` enum('yes', 'no') NOT NULL DEFAULT 'yes',
  `force_from` VARCHAR(50) NOT NULL DEFAULT 'never',
  `force_reply_to` VARCHAR(50) NOT NULL DEFAULT 'never',
  `force_sender` enum('yes', 'no') NOT NULL DEFAULT 'no',
  `must_confirm_delivery` ENUM('yes','no') NOT NULL DEFAULT 'no',
  `max_connection_messages` INT(11) NOT NULL DEFAULT '1',
  `status` char(15) NOT NULL DEFAULT 'inactive',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`server_id`),
  KEY `fk_delivery_server_bounce_server1_idx` (`bounce_server_id`),
  KEY `idx_gen0` (`status`, `hourly_quota`, `probability`),
  KEY `fk_delivery_server_customer1_idx` (`customer_id`),
  KEY `fk_delivery_server_tracking_domain1_idx` (`tracking_domain_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_server_domain_policy`
--

DROP TABLE IF EXISTS `delivery_server_domain_policy`;
CREATE TABLE IF NOT EXISTS `delivery_server_domain_policy` (
  `domain_id` int(11) NOT NULL AUTO_INCREMENT,
  `server_id` int(11) NOT NULL,
  `domain` varchar(64) NOT NULL,
  `policy` char(15) NOT NULL DEFAULT 'allow',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`domain_id`),
  KEY `fk_delivery_server_domain_policy_delivery_server1_idx` (`server_id`),
  KEY `server_domain_policy` (`server_id`, `domain`, `policy`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_server_to_customer_group`
--

CREATE TABLE IF NOT EXISTS `delivery_server_to_customer_group` (
  `server_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`server_id`,`group_id`),
  KEY `fk_delivery_server_to_customer_group_customer_group1_idx` (`group_id`),
  KEY `fk_delivery_server_to_customer_group_delivery_server1_idx` (`server_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_server_usage_log`
--

DROP TABLE IF EXISTS `delivery_server_usage_log`;
CREATE TABLE IF NOT EXISTS `delivery_server_usage_log` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `server_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `delivery_for` char(15) NOT NULL DEFAULT 'system',
  `customer_countable` enum('yes','no') NOT NULL DEFAULT 'yes',
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `fk_delivery_server_usage_log_delivery_server1_idx` (`server_id`),
  KEY `fk_delivery_server_usage_log_customer1_idx` (`customer_id`),
  KEY `server_date` (`server_id`,`date_added`),
  KEY `customer_countable_date` (`customer_id`,`customer_countable`,`date_added`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `email_blacklist`
--

DROP TABLE IF EXISTS `email_blacklist`;
CREATE TABLE IF NOT EXISTS `email_blacklist` (
  `email_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `subscriber_id` int(11) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `reason` text NOT NULL,
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`email_id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_email_blacklist_list_subscriber1_idx` (`subscriber_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `email_blacklist_monitor`
--
DROP TABLE IF EXISTS `email_blacklist_monitor`;
CREATE TABLE IF NOT EXISTS `email_blacklist_monitor` (
  `monitor_id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email_condition` CHAR(15) NULL,
  `email` VARCHAR(255) NULL,
  `reason_condition` CHAR(15) NULL,
  `reason` VARCHAR(255) NULL,
  `condition_operator` ENUM('and', 'or') NOT NULL DEFAULT 'and',
  `notifications_to` VARCHAR(255) NULL,
  `status` CHAR(15) NOT NULL DEFAULT 'active',
  `date_added` DATETIME NOT NULL,
  `last_updated` DATETIME NOT NULL,
  PRIMARY KEY (`monitor_id`))
  ENGINE = InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `feedback_loop_server`
--

DROP TABLE IF EXISTS `feedback_loop_server`;
CREATE TABLE IF NOT EXISTS `feedback_loop_server` (
  `server_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `hostname` varchar(150) NOT NULL,
  `username` varchar(150) NOT NULL,
  `password` varchar(150) NOT NULL,
  `email` varchar(100) NULL,
  `service` enum('imap','pop3') NOT NULL DEFAULT 'imap',
  `port` int(5) NOT NULL DEFAULT '143',
  `protocol` enum('ssl','tls','notls') NOT NULL DEFAULT 'notls',
  `validate_ssl` enum('yes','no') NOT NULL DEFAULT 'no',
  `locked` enum('yes', 'no') NOT NULL DEFAULT 'no',
  `disable_authenticator` VARCHAR(50) NULL,
  `search_charset` VARCHAR(50) NOT NULL DEFAULT 'UTF-8',
  `delete_all_messages` ENUM('yes','no') NOT NULL DEFAULT 'no',
  `status` char(15) NOT NULL DEFAULT 'active',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`server_id`),
  KEY `fk_feedback_loop_server_customer1_idx` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `guest_fail_attempt`
--

DROP TABLE IF EXISTS `guest_fail_attempt`;
CREATE TABLE IF NOT EXISTS `guest_fail_attempt` (
  `attempt_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `ip_address_hash` char(32) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `place` varchar(255) NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`attempt_id`),
  KEY `ip_hash_date` (`ip_address_hash`, `date_added`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

DROP TABLE IF EXISTS `language`;
CREATE TABLE IF NOT EXISTS `language` (
  `language_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `language_code` char(2) NOT NULL,
  `region_code` char(2) NULL,
  `is_default` enum('yes','no') NOT NULL DEFAULT 'no',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`language_id`),
  KEY `is_default` (`is_default`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- --------------------------------------------------------

--
-- Table structure for table `list`
--

DROP TABLE IF EXISTS `list`;
CREATE TABLE IF NOT EXISTS `list` (
  `list_id` int(11) NOT NULL AUTO_INCREMENT,
  `list_uid` char(13) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `visibility` char(15) NOT NULL DEFAULT 'public',
  `opt_in` enum('double','single') NOT NULL DEFAULT 'double',
  `opt_out` enum('double','single') NOT NULL DEFAULT 'single',
  `merged` enum('yes','no') NOT NULL DEFAULT 'no',
  `welcome_email` enum('yes','no') NOT NULL DEFAULT 'no',
  `removable` enum('yes','no') NOT NULL DEFAULT 'yes',
  `subscriber_require_approval` enum('yes','no') NOT NULL DEFAULT 'no',
  `subscriber_404_redirect` VARCHAR(255) NULL,
  `subscriber_exists_redirect` VARCHAR(255) NULL,
  `meta_data` BLOB NULL DEFAULT NULL,
  `status` char(15) NOT NULL DEFAULT 'active',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`list_id`),
  UNIQUE KEY `unique_id_UNIQUE` (`list_uid`),
  KEY `fk_list_customer1_idx` (`customer_id`),
  KEY `status_visibility` (`status`,`visibility`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `list_company`
--

DROP TABLE IF EXISTS `list_company`;
CREATE TABLE IF NOT EXISTS `list_company` (
  `list_id` int(11) NOT NULL,
  `type_id` int(11) DEFAULT NULL,
  `country_id` int(11) NOT NULL,
  `zone_id` int(11) NULL,
  `name` varchar(100) NOT NULL,
  `website` VARCHAR(255) NULL,
  `address_1` varchar(255) NOT NULL,
  `address_2` varchar(255) NULL,
  `zone_name` varchar(150) NULL,
  `city` varchar(150) NOT NULL,
  `zip_code` char(10) NOT NULL,
  `phone` varchar(32) NULL,
  `address_format` varchar(255) NOT NULL,
  PRIMARY KEY (`list_id`),
  KEY `fk_customer_company_country1_idx` (`country_id`),
  KEY `fk_customer_company_zone1_idx` (`zone_id`),
  KEY `fk_list_company_company_type1_idx`(`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `list_customer_notification`
--

DROP TABLE IF EXISTS `list_customer_notification`;
CREATE TABLE IF NOT EXISTS `list_customer_notification` (
  `list_id` int(11) NOT NULL,
  `daily` enum('yes','no') NOT NULL DEFAULT 'no',
  `subscribe` enum('yes','no') NOT NULL DEFAULT 'no',
  `unsubscribe` enum('yes','no') NOT NULL DEFAULT 'no',
  `daily_to` varchar(255) NULL,
  `subscribe_to` varchar(255) NULL,
  `unsubscribe_to` varchar(255) NULL,
  PRIMARY KEY (`list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `list_default`
--

DROP TABLE IF EXISTS `list_default`;
CREATE TABLE IF NOT EXISTS `list_default` (
  `list_id` int(11) NOT NULL,
  `from_name` varchar(100) NOT NULL,
  `from_email` varchar(100) NOT NULL,
  `reply_to` varchar(100) NOT NULL,
  `subject` varchar(255) NULL,
  PRIMARY KEY (`list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `list_field`
--

DROP TABLE IF EXISTS `list_field`;
CREATE TABLE IF NOT EXISTS `list_field` (
  `field_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `list_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `tag` varchar(50) NOT NULL,
  `default_value` varchar(255) NULL,
  `help_text` varchar(255) NULL,
  `required` enum('yes','no') NOT NULL DEFAULT 'no',
  `visibility` enum('visible','hidden') NOT NULL DEFAULT 'visible',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`field_id`),
  KEY `fk_list_field_list1_idx` (`list_id`),
  KEY `fk_list_field_list_field_type1_idx` (`type_id`),
  KEY `list_tag` (`list_id`,`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `list_field_option`
--

DROP TABLE IF EXISTS `list_field_option`;
CREATE TABLE IF NOT EXISTS `list_field_option` (
  `option_id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `value` varchar(255) NOT NULL,
  `is_default` enum('yes','no') NOT NULL DEFAULT 'no',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`option_id`),
  KEY `fk_list_field_option_list_field1_idx` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `list_field_type`
--

DROP TABLE IF EXISTS `list_field_type`;
CREATE TABLE IF NOT EXISTS `list_field_type` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `identifier` varchar(50) NOT NULL,
  `class_alias` varchar(255) NOT NULL,
  `description` varchar(255) NULL,
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `list_field_value`
--

DROP TABLE IF EXISTS `list_field_value`;
CREATE TABLE IF NOT EXISTS `list_field_value` (
  `value_id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `value` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`value_id`),
  KEY `fk_list_field_value_list_field1_idx` (`field_id`),
  KEY `fk_list_field_value_list_subscriber1_idx` (`subscriber_id`),
  KEY `field_subscriber` (`field_id`,`subscriber_id`),
  KEY `field_id_value` (`field_id`, `value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `list_page`
--

DROP TABLE IF EXISTS `list_page`;
CREATE TABLE IF NOT EXISTS `list_page` (
  `list_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `meta_data` longblob,
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`list_id`,`type_id`),
  KEY `fk_list_page_list_page_type1_idx` (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `list_page_type`
--

DROP TABLE IF EXISTS `list_page_type`;
CREATE TABLE IF NOT EXISTS `list_page_type` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `content` longtext NOT NULL,
  `full_html` enum('yes','no') NOT NULL DEFAULT 'no',
  `meta_data` longblob,
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `list_segment`
--

DROP TABLE IF EXISTS `list_segment`;
CREATE TABLE IF NOT EXISTS `list_segment` (
  `segment_id` int(11) NOT NULL AUTO_INCREMENT,
  `segment_uid` char(13) NOT NULL,
  `list_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `operator_match` enum('any','all') NOT NULL DEFAULT 'any',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`segment_id`),
  UNIQUE KEY `segment_uid` (`segment_uid`),
  KEY `fk_list_segment_list1_idx` (`list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `list_segment_condition`
--

DROP TABLE IF EXISTS `list_segment_condition`;
CREATE TABLE IF NOT EXISTS `list_segment_condition` (
  `condition_id` int(11) NOT NULL AUTO_INCREMENT,
  `segment_id` int(11) NOT NULL,
  `operator_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `value` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`condition_id`),
  KEY `fk_list_segment_condition_list_segment_operator1_idx` (`operator_id`),
  KEY `fk_list_segment_condition_list_segment1_idx` (`segment_id`),
  KEY `fk_list_segment_condition_list_field1_idx` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `list_segment_operator`
--

DROP TABLE IF EXISTS `list_segment_operator`;
CREATE TABLE IF NOT EXISTS `list_segment_operator` (
  `operator_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`operator_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `list_subscriber`
--

DROP TABLE IF EXISTS `list_subscriber`;
CREATE TABLE IF NOT EXISTS `list_subscriber` (
  `subscriber_id` int(11) NOT NULL AUTO_INCREMENT,
  `subscriber_uid` char(13) NOT NULL,
  `list_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `ip_address` char(15) NULL,
  `source` enum('web','api','import') NOT NULL DEFAULT 'web',
  `status` char(15) NOT NULL DEFAULT 'unconfirmed',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`subscriber_id`),
  UNIQUE KEY `unique_id_UNIQUE` (`subscriber_uid`),
  KEY `fk_list_subscriber_list1_idx` (`list_id`),
  KEY `list_email` (`list_id`,`email`),
  KEY `status_last_updated` (`status`,`last_updated`),
  KEY `list_id_status`(`list_id`,`status`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `list_subscriber_action`
--

DROP TABLE IF EXISTS `list_subscriber_action`;
CREATE TABLE IF NOT EXISTS `list_subscriber_action` (
  `action_id` int(11) NOT NULL AUTO_INCREMENT,
  `source_list_id` int(11) NOT NULL,
  `source_action` char(15) NOT NULL DEFAULT 'subscribe',
  `target_list_id` int(11) NOT NULL,
  `target_action` char(15) NOT NULL DEFAULT 'unsubscribe',
  PRIMARY KEY (`action_id`),
  KEY `fk_list_subscriber_action_list1_idx` (`source_list_id`),
  KEY `fk_list_subscriber_action_list2_idx` (`target_list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `option`
--

DROP TABLE IF EXISTS `option`;
CREATE TABLE IF NOT EXISTS `option` (
  `category` varchar(150) NOT NULL,
  `key` varchar(150) NOT NULL,
  `value` longblob NOT NULL,
  `is_serialized` tinyint(1) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`category`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `price_plan`
--

DROP TABLE IF EXISTS `price_plan`;
CREATE TABLE IF NOT EXISTS `price_plan` (
  `plan_id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_uid` char(13) NOT NULL,
  `group_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `description` text NOT NULL,
  `recommended` enum('yes','no') NOT NULL DEFAULT 'no',
  `visible` enum('yes','no') NOT NULL DEFAULT 'yes',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `status` char(15) NOT NULL DEFAULT 'active',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`plan_id`),
  UNIQUE KEY `plan_uid_UNIQUE` (`plan_uid`),
  KEY `fk_price_plan_customer_group1_idx` (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `price_plan_order`
--

DROP TABLE IF EXISTS `price_plan_order`;
CREATE TABLE IF NOT EXISTS `price_plan_order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_uid` char(13) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `promo_code_id` int(11) DEFAULT NULL,
  `tax_id` int(11) DEFAULT NULL,
  `currency_id` int(11) NOT NULL,
  `subtotal` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `tax_percent` decimal(4,2) NOT NULL DEFAULT 0.00,
  `tax_value` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `discount` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `total` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `status` char(15) NOT NULL DEFAULT 'incomplete',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `order_uid_UNIQUE` (`order_uid`),
  KEY `fk_price_plan_order_price_plan1_idx` (`plan_id`),
  KEY `fk_price_plan_order_customer1_idx` (`customer_id`),
  KEY `fk_price_plan_order_price_plan_promo_code1_idx` (`promo_code_id`),
  KEY `fk_price_plan_order_currency1_idx` (`currency_id`),
  KEY `fk_price_plan_order_price_plan_tax1_idx` (`tax_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `price_plan_order_note`
--

DROP TABLE IF EXISTS `price_plan_order_note`;
CREATE TABLE IF NOT EXISTS `price_plan_order_note` (
  `note_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NULL,
  `user_id` int(11) NULL,
  `note` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`note_id`),
  KEY `fk_price_plan_order_note_price_plan_order1_idx` (`order_id`),
  KEY `fk_price_plan_order_note_customer1_idx` (`customer_id`),
  KEY `fk_price_plan_order_note_user1_idx` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `price_plan_order_transaction`
--

DROP TABLE IF EXISTS `price_plan_order_transaction`;
CREATE TABLE IF NOT EXISTS `price_plan_order_transaction` (
  `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_uid` char(13) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_gateway_name` varchar(50) NOT NULL,
  `payment_gateway_transaction_id` varchar(100) NOT NULL,
  `payment_gateway_response` text NOT NULL,
  `status` char(15) NOT NULL DEFAULT 'failed',
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`transaction_id`),
  UNIQUE KEY `transaction_uid_UNIQUE` (`transaction_uid`),
  KEY `fk_price_plan_order_transaction_price_plan_order1_idx` (`order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `price_plan_promo_code`
--

DROP TABLE IF EXISTS `price_plan_promo_code`;
CREATE TABLE IF NOT EXISTS `price_plan_promo_code` (
  `promo_code_id` int(11) NOT NULL AUTO_INCREMENT,
  `code` char(15) NOT NULL,
  `type` enum('percentage','fixed amount') NOT NULL DEFAULT 'fixed amount',
  `discount` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `total_amount` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `total_usage` tinyint(4) NOT NULL DEFAULT '0',
  `customer_usage` tinyint(4) NOT NULL DEFAULT '0',
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `status` char(15) NOT NULL DEFAULT 'active',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`promo_code_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `sending_domain`
--

DROP TABLE IF EXISTS `sending_domain`;
CREATE TABLE IF NOT EXISTS `sending_domain` (
  `domain_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `name` varchar(64) NOT NULL,
  `dkim_private_key` text NOT NULL,
  `dkim_public_key` text NOT NULL,
  `locked` enum('yes','no') NOT NULL DEFAULT 'no',
  `verified` enum('yes','no') NOT NULL DEFAULT 'no',
  `signing_enabled` enum('yes','no') NOT NULL DEFAULT 'yes',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`domain_id`),
  KEY `fk_sending_domain_customer1_idx` (`customer_id`),
  KEY `name_verified_customer` (`name`, `verified`, `customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tax`
--

DROP TABLE IF EXISTS `tax`;
CREATE TABLE IF NOT EXISTS `tax` (
  `tax_id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) DEFAULT NULL,
  `zone_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `percent` decimal(4,2) NOT NULL DEFAULT '0.00',
  `is_global` enum('yes','no') NOT NULL DEFAULT 'no',
  `status` char(15) NOT NULL,
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`tax_id`),
  KEY `fk_tax_zone1_idx` (`zone_id`),
  KEY `fk_tax_country1_idx` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tracking_domain`
--

DROP TABLE IF EXISTS `tracking_domain`;
CREATE TABLE IF NOT EXISTS `tracking_domain` (
  `domain_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`domain_id`),
  KEY `fk_tracking_domain_customer1_idx` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `transactional_email`
--

DROP TABLE IF EXISTS `transactional_email`;
CREATE TABLE IF NOT EXISTS `transactional_email` (
  `email_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email_uid` char(13) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `to_email` varchar(150) NOT NULL,
  `to_name` varchar(150) NOT NULL,
  `from_email` varchar(150) NOT NULL,
  `from_name` varchar(150) NOT NULL,
  `reply_to_email` varchar(150) NULL,
  `reply_to_name` varchar(150) NULL,
  `subject` varchar(255) NOT NULL,
  `body` longblob NOT NULL,
  `plain_text` longblob NOT NULL,
  `priority` tinyint(1) NOT NULL DEFAULT '5',
  `retries` tinyint(1) NOT NULL DEFAULT '0',
  `max_retries` tinyint(1) NOT NULL DEFAULT '3',
  `send_at` datetime NOT NULL,
  `status` char(15) NOT NULL DEFAULT 'unsent',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`email_id`),
  UNIQUE KEY `email_uid_UNIQUE` (`email_uid`),
  KEY `fk_transactional_email_customer1_idx` (`customer_id`),
  KEY `status_send_at_retries_max_retries` (`status`, `send_at`, `retries`, `max_retries`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `transactional_email_log`
--

DROP TABLE IF EXISTS `transactional_email_log`;
CREATE TABLE IF NOT EXISTS `transactional_email_log` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email_id` bigint(20) NOT NULL,
  `message` text NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `fk_transactional_email_log_transactional_email1_idx` (`email_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
CREATE TABLE IF NOT EXISTS `session` (
  `id` char(32) NOT NULL,
  `expire` int(11) NULL,
  `data` longblob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tag_registry`
--

DROP TABLE IF EXISTS `tag_registry`;
CREATE TABLE IF NOT EXISTS `tag_registry` (
  `tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(50) NOT NULL,
  `description` varchar(255) NULL,
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`tag_id`),
  UNIQUE KEY `tag_UNIQUE` (`tag`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=45 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_uid` char(13) NOT NULL,
  `group_id` INT NULL DEFAULT NULL,
  `language_id` int(11) NULL,
  `first_name` varchar(100) NULL,
  `last_name` varchar(100) NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(34) NOT NULL,
  `timezone` varchar(50) NOT NULL,
  `avatar` VARCHAR(255) NULL,
  `removable` enum('yes','no') NOT NULL DEFAULT 'yes',
  `status` char(15) NOT NULL DEFAULT 'inactive',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_uid_UNIQUE` (`user_uid`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  KEY `fk_user_language1_idx` (`language_id`),
  KEY `fk_user_user_group1_idx` (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_auto_login_token`
--

DROP TABLE IF EXISTS `user_auto_login_token`;
CREATE TABLE IF NOT EXISTS `user_auto_login_token` (
  `token_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` char(40) NOT NULL,
  PRIMARY KEY (`token_id`),
  KEY `fk_user_auto_login_token_user1_idx` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_group`
--

CREATE TABLE IF NOT EXISTS `user_group` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`group_id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_group_route_access`
--

CREATE TABLE IF NOT EXISTS `user_group_route_access` (
  `route_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `route`  VARCHAR(255) NOT NULL,
  `access` enum('allow','deny') NOT NULL DEFAULT 'allow',
  `date_added` datetime DEFAULT NULL,
  PRIMARY KEY (`route_id`),
  KEY `fk_user_group_route_access_user_group1_idx` (`group_id`),
  KEY `group_route_access` (`group_id`, `route`, `access`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------


--
-- Table structure for table `user_password_reset`
--

DROP TABLE IF EXISTS `user_password_reset`;
CREATE TABLE IF NOT EXISTS `user_password_reset` (
  `request_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `reset_key` char(40) NOT NULL,
  `ip_address` char(15) NULL,
  `status` char(15) NOT NULL DEFAULT 'active',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`request_id`),
  KEY `fk_user_password_reset_user1_idx` (`user_id`),
  KEY `key_status` (`reset_key`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------


--
-- Table structure for table `customer_campaign_tag`
--

DROP TABLE IF EXISTS `customer_campaign_tag`;
CREATE TABLE IF NOT EXISTS `customer_campaign_tag` (
    `tag_id` INT NOT NULL AUTO_INCREMENT,
    `tag_uid` CHAR(13) NOT NULL,
    `customer_id` INT(11) NOT NULL,
    `tag` VARCHAR(50) NOT NULL,
    `content` TEXT NOT NULL,
    `random` ENUM('yes','no') NOT NULL DEFAULT 'no',
    `date_added` DATETIME NOT NULL,
    `last_updated` DATETIME NOT NULL,
    PRIMARY KEY (`tag_id`),
    KEY `fk_customer_campaign_tag_customer1_idx` (`customer_id`),
    UNIQUE KEY `customer_campaign_tag_uid` (`tag_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `customer_campaign_tag`
--
DROP TABLE IF EXISTS `customer_message`;
CREATE TABLE IF NOT EXISTS `customer_message` (
  `message_id` INT NOT NULL AUTO_INCREMENT,
  `message_uid` CHAR(13) NOT NULL,
  `customer_id` INT(11) NOT NULL,
  `title` VARCHAR(255) NULL,
  `message` TEXT NOT NULL,
  `params` TEXT NULL,
  `status` CHAR(15) NOT NULL DEFAULT 'unseen',
  `date_added` DATETIME NOT NULL,
  `last_updated` DATETIME NOT NULL,
  PRIMARY KEY (`message_id`),
  KEY `fk_customer_message_customer1_idx` (`customer_id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table customer_email_blacklist
--
DROP TABLE IF EXISTS `customer_email_blacklist`;
CREATE TABLE IF NOT EXISTS `customer_email_blacklist` (
  `email_id` INT NOT NULL AUTO_INCREMENT,
  `email_uid` CHAR(13) NOT NULL,
  `customer_id` INT(11) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `reason` VARCHAR(255) NULL DEFAULT NULL,
  `date_added` DATETIME NOT NULL,
  `last_updated` DATETIME NOT NULL,
  PRIMARY KEY (`email_id`),
  UNIQUE KEY `unique_id_UNIQUE` (`email_uid`),
  UNIQUE KEY `customer_id_email_UNIQUE` (`customer_id`, `email`),
  KEY `fk_customer_email_blacklist_customer1_idx` (`customer_id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `list_subscriber_field_cache`
--
CREATE TABLE IF NOT EXISTS `list_subscriber_field_cache` (
  `subscriber_id` INT(11) NOT NULL,
  `data` LONGBLOB NOT NULL,
  INDEX `fk_list_subscriber_field_cache_list_subscriber1_idx` (`subscriber_id` ASC),
  PRIMARY KEY (`subscriber_id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `customer_login_log`
--
CREATE TABLE IF NOT EXISTS `customer_login_log` (
  `log_id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `customer_id` INT(11) NOT NULL,
  `location_id` BIGINT(20) NULL,
  `ip_address` CHAR(15) NOT NULL,
  `user_agent` VARCHAR(255) NOT NULL,
  `date_added` DATETIME NOT NULL,
  PRIMARY KEY (`log_id`),
  INDEX `fk_customer_login_log_customer1_idx` (`customer_id` ASC),
  INDEX `fk_customer_login_log_ip_location1_idx` (`location_id` ASC)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `list_subscriber_list_move`
--
CREATE TABLE IF NOT EXISTS `list_subscriber_list_move` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `source_subscriber_id` INT(11) NOT NULL,
  `source_list_id` INT(11) NOT NULL,
  `destination_subscriber_id` INT(11) NOT NULL,
  `destination_list_id` INT(11) NOT NULL,
  `date_added` DATETIME NOT NULL,
  `last_updated` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_list_subscriber_list_move_list_subscriber1_idx` (`source_subscriber_id`),
  INDEX `fk_list_subscriber_list_move_list1_idx` (`source_list_id`),
  INDEX `fk_list_subscriber_list_move_list_subscriber2_idx` (`destination_subscriber_id`),
  INDEX `fk_list_subscriber_list_move_list2_idx` (`destination_list_id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `zone`
--
DROP TABLE IF EXISTS `zone`;
CREATE TABLE IF NOT EXISTS `zone` (
  `zone_id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `code` varchar(50) NOT NULL,
  `status` char(10) NOT NULL DEFAULT 'active',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`zone_id`),
  KEY `fk_zone_country1_idx` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=3970 ;

-- --------------------------------------------------------

--
-- Constraints for dumped tables
--

--
-- Constraints for table `article_category`
--
ALTER TABLE `article_category`
  ADD CONSTRAINT `fk_article_category_article_category1` FOREIGN KEY (`parent_id`) REFERENCES `article_category` (`category_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `article_to_category`
--
ALTER TABLE `article_to_category`
  ADD CONSTRAINT `fk_article_to_category_article1` FOREIGN KEY (`article_id`) REFERENCES `article` (`article_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_article_to_category_article_category1` FOREIGN KEY (`category_id`) REFERENCES `article_category` (`category_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `bounce_server`
--
ALTER TABLE `bounce_server`
  ADD CONSTRAINT `fk_bounce_server_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `campaign`
--
ALTER TABLE `campaign`
  ADD CONSTRAINT `fk_campaign_campaign_group1` FOREIGN KEY (`group_id`) REFERENCES `campaign_group` (`group_id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_list1` FOREIGN KEY (`list_id`) REFERENCES `list` (`list_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_list_segment1` FOREIGN KEY (`segment_id`) REFERENCES `list_segment` (`segment_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `campaign_abuse_report`
--
ALTER TABLE `campaign_abuse_report`
    ADD CONSTRAINT `fk_campaign_abuse_report_campaign1` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`campaign_id`) ON DELETE SET NULL ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_campaign_abuse_report_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE SET NULL ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_campaign_abuse_report_list1` FOREIGN KEY (`list_id`) REFERENCES `list` (`list_id`) ON DELETE SET NULL ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_campaign_abuse_report_list_subscriber1` FOREIGN KEY (`subscriber_id`) REFERENCES `list_subscriber` (`subscriber_id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `campaign_attachment`
--
ALTER TABLE `campaign_attachment`
  ADD CONSTRAINT `fk_campaign_attachment_campaign1` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`campaign_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `campaign_bounce_log`
--
ALTER TABLE `campaign_bounce_log`
  ADD CONSTRAINT `fk_campaign_bounce_log_campaign1` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`campaign_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_bounce_log_list_subscriber1` FOREIGN KEY (`subscriber_id`) REFERENCES `list_subscriber` (`subscriber_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `campaign_delivery_log`
--
ALTER TABLE `campaign_delivery_log`
  ADD CONSTRAINT `fk_campaign_delivery_log_campaign1` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`campaign_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_delivery_log_list_subscriber1` FOREIGN KEY (`subscriber_id`) REFERENCES `list_subscriber` (`subscriber_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_delivery_log_delivery_server1` FOREIGN KEY (`server_id`) REFERENCES `delivery_server` (`server_id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `campaign_delivery_log_archive`
--
ALTER TABLE `campaign_delivery_log_archive`
  ADD CONSTRAINT `fk_campaign_delivery_log_archive_campaign1` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`campaign_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_delivery_log_archive_list_subscriber1` FOREIGN KEY (`subscriber_id`) REFERENCES `list_subscriber` (`subscriber_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_delivery_log_archive_delivery_server1` FOREIGN KEY (`server_id`) REFERENCES `delivery_server` (`server_id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `campaign_forward_friend`
--
ALTER TABLE `campaign_forward_friend`
  ADD CONSTRAINT `fk_campaign_forward_friend_campaign1` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`campaign_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_forward_friend_list_subscriber1` FOREIGN KEY (`subscriber_id`) REFERENCES `list_subscriber` (`subscriber_id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `campaign_group`
--
ALTER TABLE `campaign_group`
  ADD CONSTRAINT `fk_campaign_group_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `campaign_open_action_list_field`
--
ALTER TABLE `campaign_open_action_list_field`
  ADD CONSTRAINT `fk_campaign_open_action_list_field_list1` FOREIGN KEY (`list_id`) REFERENCES `list` (`list_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_open_action_list_field_campaign1` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`campaign_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_open_action_list_field_list_field1` FOREIGN KEY (`field_id`) REFERENCES `list_field` (`field_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `campaign_open_action_subscriber`
--
ALTER TABLE `campaign_open_action_subscriber`
  ADD CONSTRAINT `fk_campaign_open_action_subscriber_campaign1` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`campaign_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_open_action_subscriber_list1` FOREIGN KEY (`list_id`) REFERENCES `list` (`list_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `campaign_option`
--
ALTER TABLE `campaign_option`
  ADD CONSTRAINT `fk_campaign_option_campaign1` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`campaign_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_option_campaign2` FOREIGN KEY (`autoresponder_open_campaign_id`) REFERENCES `campaign` (`campaign_id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_option_campaign3` FOREIGN KEY (`regular_open_unopen_campaign_id`) REFERENCES `campaign` (`campaign_id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_option_campaign4` FOREIGN KEY (`tracking_domain_id`) REFERENCES `tracking_domain` (`domain_id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `campaign_template`
--
ALTER TABLE `campaign_template`
  ADD CONSTRAINT `fk_campaign_template_campaign1` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`campaign_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_customer_email_template1` FOREIGN KEY (`customer_template_id`) REFERENCES `customer_email_template` (`template_id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `campaign_template_url_action_list_field`
--
ALTER TABLE `campaign_template_url_action_list_field`
  ADD CONSTRAINT `fk_campaign_template_url_action_list_field_campaign1` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`campaign_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_template_url_action_list_field_list1` FOREIGN KEY (`list_id`) REFERENCES `list` (`list_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_template_url_action_list_field_campaign_templa1` FOREIGN KEY (`template_id`) REFERENCES `campaign_template` (`template_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_template_url_action_list_field_list_field1` FOREIGN KEY (`field_id`) REFERENCES `list_field` (`field_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `campaign_template_url_action_subscriber`
--
ALTER TABLE `campaign_template_url_action_subscriber`
  ADD CONSTRAINT `fk_campaign_template_url_action_subscriber_campaign_tem1` FOREIGN KEY (`template_id`) REFERENCES `campaign_template` (`template_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_template_url_action_subscriber_list1` FOREIGN KEY (`list_id`) REFERENCES `list` (`list_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_template_url_action_subscriber_campaign1` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`campaign_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `campaign_temporary_source`
--
ALTER TABLE `campaign_temporary_source`
  ADD CONSTRAINT `fk_campaign_temporary_source_campaign1` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`campaign_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_temporary_source_list1` FOREIGN KEY (`list_id`) REFERENCES `list` (`list_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_temporary_source_list_segment1` FOREIGN KEY (`segment_id`) REFERENCES `list_segment` (`segment_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `campaign_to_delivery_server`
--
ALTER TABLE `campaign_to_delivery_server`
  ADD CONSTRAINT `fk_campaign_to_delivery_server_campaign1` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`campaign_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_to_delivery_server_delivery_server1` FOREIGN KEY (`server_id`) REFERENCES `delivery_server` (`server_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `campaign_track_open`
--
ALTER TABLE `campaign_track_open`
  ADD CONSTRAINT `fk_campaign_track_open_campaign1` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`campaign_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_track_open_list_subscriber1` FOREIGN KEY (`subscriber_id`) REFERENCES `list_subscriber` (`subscriber_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_track_open_ip_location1` FOREIGN KEY (`location_id`) REFERENCES `ip_location` (`location_id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `campaign_track_unsubscribe`
--
ALTER TABLE `campaign_track_unsubscribe`
  ADD CONSTRAINT `fk_campaign_track_unsubscribe_campaign1` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`campaign_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_track_unsubscribe_list_subscriber1` FOREIGN KEY (`subscriber_id`) REFERENCES `list_subscriber` (`subscriber_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_track_unsubscribe_ip_location1` FOREIGN KEY (`location_id`) REFERENCES `ip_location` (`location_id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `campaign_track_url`
--
ALTER TABLE `campaign_track_url`
  ADD CONSTRAINT `fk_campaign_track_url_campaign_url1` FOREIGN KEY (`url_id`) REFERENCES `campaign_url` (`url_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_track_url_list_subscriber1` FOREIGN KEY (`subscriber_id`) REFERENCES `list_subscriber` (`subscriber_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_campaign_track_url_ip_location1` FOREIGN KEY (`location_id`) REFERENCES `ip_location` (`location_id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `campaign_url`
--
ALTER TABLE `campaign_url`
  ADD CONSTRAINT `fk_campaign_url_campaign1` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`campaign_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `fk_customer_customer_group1` FOREIGN KEY (`group_id`) REFERENCES `customer_group` (`group_id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_customer_language1` FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `customer_action_log`
--
ALTER TABLE `customer_action_log`
  ADD CONSTRAINT `fk_customer_notification_log_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `customer_api_key`
--
ALTER TABLE `customer_api_key`
  ADD CONSTRAINT `fk_customer_api_key_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `customer_auto_login_token`
--
ALTER TABLE `customer_auto_login_token`
  ADD CONSTRAINT `fk_customer_auto_login_token_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `customer_company`
--
ALTER TABLE `customer_company`
  ADD CONSTRAINT `fk_customer_company_country10` FOREIGN KEY (`country_id`) REFERENCES `country` (`country_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_customer_company_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_customer_company_zone10` FOREIGN KEY (`zone_id`) REFERENCES `zone` (`zone_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_customer_company_company_type1` FOREIGN KEY (`type_id`) REFERENCES `company_type` (`type_id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `customer_email_template`
--
ALTER TABLE `customer_email_template`
  ADD CONSTRAINT `fk_customer_email_template_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `customer_group_option`
--
ALTER TABLE `customer_group_option`
  ADD CONSTRAINT `fk_customer_group_option_customer_group1` FOREIGN KEY (`group_id`) REFERENCES `customer_group` (`group_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `customer_password_reset`
--
ALTER TABLE `customer_password_reset`
  ADD CONSTRAINT `fk_customer_password_reset_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `customer_quota_mark`
--
ALTER TABLE `customer_quota_mark`
  ADD CONSTRAINT `fk_customer_quota_mark_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `delivery_server`
--
ALTER TABLE `delivery_server`
  ADD CONSTRAINT `fk_delivery_server_tracking_domain1` FOREIGN KEY (`tracking_domain_id`) REFERENCES `tracking_domain` (`domain_id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_delivery_server1` FOREIGN KEY (`bounce_server_id`) REFERENCES `bounce_server` (`server_id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_delivery_server_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `delivery_server_domain_policy`
--
ALTER TABLE `delivery_server_domain_policy`
  ADD CONSTRAINT `fk_delivery_server_domain_policy_delivery_server1` FOREIGN KEY (`server_id`) REFERENCES `delivery_server` (`server_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `delivery_server_to_customer_group`
--
ALTER TABLE `delivery_server_to_customer_group`
    ADD CONSTRAINT `fk_delivery_server_to_customer_group_delivery_server1` FOREIGN KEY (`server_id`) REFERENCES `delivery_server` (`server_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_delivery_server_to_customer_group_customer_group1` FOREIGN KEY (`group_id`) REFERENCES `customer_group` (`group_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `delivery_server_usage_log`
--
ALTER TABLE `delivery_server_usage_log`
  ADD CONSTRAINT `fk_delivery_server_usage_log_delivery_server1` FOREIGN KEY (`server_id`) REFERENCES `delivery_server` (`server_id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_delivery_server_usage_log_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `email_blacklist`
--
ALTER TABLE `email_blacklist`
  ADD CONSTRAINT `fk_email_blacklist1` FOREIGN KEY (`subscriber_id`) REFERENCES `list_subscriber` (`subscriber_id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `feedback_loop_server`
--
ALTER TABLE `feedback_loop_server`
  ADD CONSTRAINT `fk_feedback_loop_server_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `list`
--
ALTER TABLE `list`
  ADD CONSTRAINT `fk_list_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `list_company`
--
ALTER TABLE `list_company`
  ADD CONSTRAINT `fk_customer_company_country100` FOREIGN KEY (`country_id`) REFERENCES `country` (`country_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_customer_company_zone100` FOREIGN KEY (`zone_id`) REFERENCES `zone` (`zone_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_list_company_list1` FOREIGN KEY (`list_id`) REFERENCES `list` (`list_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_list_company_company_type1` FOREIGN KEY (`type_id`) REFERENCES `company_type` (`type_id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `list_customer_notification`
--
ALTER TABLE `list_customer_notification`
  ADD CONSTRAINT `fk_list_notification_list1` FOREIGN KEY (`list_id`) REFERENCES `list` (`list_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `list_default`
--
ALTER TABLE `list_default`
  ADD CONSTRAINT `fk_list_default_list1` FOREIGN KEY (`list_id`) REFERENCES `list` (`list_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `list_field`
--
ALTER TABLE `list_field`
  ADD CONSTRAINT `fk_list_field_list1` FOREIGN KEY (`list_id`) REFERENCES `list` (`list_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_list_field_list_field_type1` FOREIGN KEY (`type_id`) REFERENCES `list_field_type` (`type_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `list_field_option`
--
ALTER TABLE `list_field_option`
  ADD CONSTRAINT `fk_list_field_option_list_field1` FOREIGN KEY (`field_id`) REFERENCES `list_field` (`field_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `list_field_value`
--
ALTER TABLE `list_field_value`
  ADD CONSTRAINT `fk_list_field_value_list_field1` FOREIGN KEY (`field_id`) REFERENCES `list_field` (`field_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_list_field_value_list_subscriber1` FOREIGN KEY (`subscriber_id`) REFERENCES `list_subscriber` (`subscriber_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `list_page`
--
ALTER TABLE `list_page`
  ADD CONSTRAINT `fk_list_page_list1` FOREIGN KEY (`list_id`) REFERENCES `list` (`list_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_list_page_list_page_type1` FOREIGN KEY (`type_id`) REFERENCES `list_page_type` (`type_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `list_segment`
--
ALTER TABLE `list_segment`
  ADD CONSTRAINT `fk_list_segment_list1` FOREIGN KEY (`list_id`) REFERENCES `list` (`list_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `list_segment_condition`
--
ALTER TABLE `list_segment_condition`
  ADD CONSTRAINT `fk_list_segment_condition_list_field1` FOREIGN KEY (`field_id`) REFERENCES `list_field` (`field_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_list_segment_condition_list_segment1` FOREIGN KEY (`segment_id`) REFERENCES `list_segment` (`segment_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_list_segment_condition_list_segment_operator1` FOREIGN KEY (`operator_id`) REFERENCES `list_segment_operator` (`operator_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `list_subscriber`
--
ALTER TABLE `list_subscriber`
  ADD CONSTRAINT `fk_subscriber_list1` FOREIGN KEY (`list_id`) REFERENCES `list` (`list_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `list_subscriber_action`
--
ALTER TABLE `list_subscriber_action`
    ADD CONSTRAINT `fk_list_subscriber_action_list1` FOREIGN KEY (`source_list_id`) REFERENCES `list` (`list_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_list_subscriber_action_list2` FOREIGN KEY (`target_list_id`) REFERENCES `list` (`list_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `price_plan`
--
ALTER TABLE `price_plan`
  ADD CONSTRAINT `fk_price_plan_customer_group1` FOREIGN KEY (`group_id`) REFERENCES `customer_group` (`group_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `price_plan_order`
--
ALTER TABLE `price_plan_order`
  ADD CONSTRAINT `fk_price_plan_order_price_plan1` FOREIGN KEY (`plan_id`) REFERENCES `price_plan` (`plan_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_price_plan_order_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_price_plan_order_price_plan_promo_code1` FOREIGN KEY (`promo_code_id`) REFERENCES `price_plan_promo_code` (`promo_code_id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_price_plan_order_currency1` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`currency_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_price_plan_order_tax1` FOREIGN KEY (`tax_id`) REFERENCES `tax` (`tax_id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `price_plan_order_note`
--
ALTER TABLE `price_plan_order_note`
  ADD CONSTRAINT `fk_price_plan_order_note_price_plan_order1` FOREIGN KEY (`order_id`) REFERENCES `price_plan_order` (`order_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_price_plan_order_note_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_price_plan_order_note_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `price_plan_order_transaction`
--
ALTER TABLE `price_plan_order_transaction`
  ADD CONSTRAINT `fk_price_plan_order_transaction_price_plan_order1` FOREIGN KEY (`order_id`) REFERENCES `price_plan_order` (`order_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `sending_domain`
--
ALTER TABLE `sending_domain`
    ADD CONSTRAINT `fk_sending_domain_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `tax`
--
ALTER TABLE `tax`
  ADD CONSTRAINT `fk_tax_country1` FOREIGN KEY (`country_id`) REFERENCES `country` (`country_id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_tax_zone1` FOREIGN KEY (`zone_id`) REFERENCES `zone` (`zone_id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `tracking_domain`
--
ALTER TABLE `tracking_domain`
  ADD CONSTRAINT `fk_tracking_domain_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `transactional_email`
--
ALTER TABLE `transactional_email`
  ADD CONSTRAINT `fk_transactional_email_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `transactional_email_log`
--
ALTER TABLE `transactional_email_log`
  ADD CONSTRAINT `fk_transactional_email_log_transactional_email1` FOREIGN KEY (`email_id`) REFERENCES `transactional_email` (`email_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_user_language1` FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_user_group1` FOREIGN KEY (`group_id`) REFERENCES `user_group`(`group_id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `user_auto_login_token`
--
ALTER TABLE `user_auto_login_token`
  ADD CONSTRAINT `fk_user_auto_login_token_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `user_group_route_access`
--
ALTER TABLE `user_group_route_access`
    ADD CONSTRAINT `fk_user_group_route_access_user_group1` FOREIGN KEY (`group_id`) REFERENCES `user_group` (`group_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `user_password_reset`
--
ALTER TABLE `user_password_reset`
  ADD CONSTRAINT `fk_user_password_reset_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `customer_campaign_tag`
--
ALTER TABLE `customer_campaign_tag`
    ADD CONSTRAINT `fk_customer_campaign_tag_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `customer_campaign_tag`
--
ALTER TABLE `customer_message`
    ADD CONSTRAINT `fk_customer_message_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `customer_email_blacklist`
--
ALTER TABLE `customer_email_blacklist`
    ADD CONSTRAINT `fk_customer_email_blacklist_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `list_subscriber_field_cache`
--
ALTER TABLE `list_subscriber_field_cache`
    ADD CONSTRAINT `fk_list_subscriber_field_cache_list_subscriber1` FOREIGN KEY (`subscriber_id`) REFERENCES `list_subscriber` (`subscriber_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `customer_login_log`
--
ALTER TABLE `customer_login_log` 
  ADD CONSTRAINT `fk_customer_login_log_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_customer_login_log_ip_location1` FOREIGN KEY (`location_id`) REFERENCES `ip_location` (`location_id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `list_subscriber_list_move`
--
ALTER TABLE `list_subscriber_list_move` 
  ADD CONSTRAINT `fk_list_subscriber_list_move_list_subscriber1` FOREIGN KEY (`source_subscriber_id`) REFERENCES `list_subscriber` (`subscriber_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_list_subscriber_list_move_list1` FOREIGN KEY (`source_list_id`) REFERENCES `list` (`list_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_list_subscriber_list_move_list_subscriber2` FOREIGN KEY (`destination_subscriber_id`) REFERENCES `list_subscriber` (`subscriber_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_list_subscriber_list_move_list2` FOREIGN KEY (`destination_list_id`) REFERENCES `list` (`list_id`) ON DELETE CASCADE ON UPDATE NO ACTION;
  
--
-- Constraints for table `zone`
--
ALTER TABLE `zone`
  ADD CONSTRAINT `fk_zone_country1` FOREIGN KEY (`country_id`) REFERENCES `country` (`country_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

-- --------------------------------------------------------

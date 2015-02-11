<?php

$installer = $this;

$installer->startSetup();

$installer->run("
-- DROP TABLE IF EXISTS `westpac_quickgateway`;
CREATE TABLE `westpac_quickgateway` (
  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `order_id` INT( 11 ) UNSIGNED NOT NULL ,
  `increment_id` VARCHAR( 50 ) NULL ,
  `orderNumber` VARCHAR( 50 ) NULL ,
  `summaryCode` TINYINT( 1 ) UNSIGNED NULL ,
  `responseCode` VARCHAR( 2 ) NULL ,
  `text` VARCHAR( 200 ) NULL ,
  `settlementDate` VARCHAR( 8 ) NULL ,
  `receiptNo` VARCHAR( 32 ) NULL ,
  `cardSchemeName` VARCHAR( 20 ) NULL ,
  `creditGroup` VARCHAR( 20 ) NULL ,
  `cvnResponse` VARCHAR( 1 ) NULL ,
  `transactionDate` VARCHAR( 25 ) NULL ,
  `authId` VARCHAR( 6 ) NULL ,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  INDEX ( `order_id` ),
  INDEX ( `orderNumber` )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci 
");

$installer->endSetup(); 

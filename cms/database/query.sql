ALTER TABLE `client_settings` CHANGE `max_visit_radius_with_client` `max_visit_radius_with_client` BIGINT(20) UNSIGNED NOT NULL DEFAULT '100', CHANGE `auto_finish_visit_radius` `auto_finish_visit_radius` BIGINT(20) UNSIGNED NOT NULL DEFAULT '200', CHANGE `salesman_to_party_radius` `salesman_to_party_radius` BIGINT(20) UNSIGNED NOT NULL DEFAULT '100';

ALTER TABLE `client_settings` CHANGE `loc_fetch_interval` `loc_fetch_interval` INT(11) NULL DEFAULT '30';

ALTER TABLE `client_settings` CHANGE `order_above_credit_limit` `order_above_credit_limit` TINYINT(4) NOT NULL DEFAULT '1';

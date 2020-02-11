CREATE TABLE IF NOT EXISTS `wp_voters` (
  `voter_id` int(11) NOT NULL,
  `first_name` text,
  `middle_name` text,
  `last_name` text,
  `street_num` int(11) DEFAULT NULL,
  `street` text,
  `type` text,
  `apt_num` text,
  `zip` text,
  `birth_date` text,
  PRIMARY KEY (`voter_id`),
  UNIQUE KEY `voter_id` (`voter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1	

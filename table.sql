CREATE TABLE `zerosum` (
`id` int(11) NOT NULL auto_increment,
`filename` varchar(255) NOT NULL default '',
`permhash` varchar(40) NOT NULL default '',
`filehash` varchar(40) NOT NULL default '',
PRIMARY KEY (`id`)

) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
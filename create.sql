CREATE TABLE IF NOT EXISTS `comments` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`parent_id` int(11) NOT NULL,
`post_id` int(11) NOT NULL,
`author` varchar(255) NOT NULL,
`comment` text NOT NULL,
`addtime` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

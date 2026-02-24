<?php
namespace SAMSPlugin\Base\SAMSHostConfig;

class Initializer
{
	public static function init()
	{
		new CPT();
        new Metaboxes();
        new REST();
	}
}
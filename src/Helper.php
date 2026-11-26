<?php
namespace MBCPT;

class Helper {
	public static function has_builder(): bool {
		return defined( 'MBB_VER' ) || defined( 'META_BOX_AIO_DIR' );
	}
}
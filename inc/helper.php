<?php
function mb_cpt_get_prop( $object, $prop, $default = '' ) {
	return property_exists( $object, $prop ) ? $object->$prop : $default;
}
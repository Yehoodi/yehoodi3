<?php
/**
 * Static class for common array functions
 * This class assumes some sort of autoloader is in use such as Zend_Loader
 *
 * @package    Sol
 * @subpackage Array
 * @category   
 *
 * @copyright  Copyright (c) CBS Interactive
 *
 * @link http://wiki.cne.cnet.com/display/GNG/Sol_Array Sol_Array Documentation
 *
 * @uses Sol_Array_Exception
 *
 * @see gne_common_array
 * 
 * @see Zend_Loader
 *
 */


class Sol_Array
{

    /**
     * returns an array of data indexed by $column.
     * this is handle for making primary key's index the row they point to.
     * @see gne_index_array_by_column
     *
     * @param array $array
     * @param string $column
     */
    public static function indexByColumn( array $array, $column )
    {

        $returnArray = array();

        if ( is_array($array) && !empty($column) ) {

            foreach ( $array as $val ) {
                if ( is_array($val[$column]) || is_object($val[$column]) ) {
                    throw new Sol_Array_Exception('Cannot index array by an object or array.');
                }
                $returnArray[$val[$column]] = $val;
            }

        }

        return $returnArray;

    }
    
}

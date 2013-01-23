<?php
/**
 * Static class for array sorting functions.
 * Basically took GNE_Array_Sorting and made it into a static method.
 * Also included some array sort functions from gne_common_array
 *
 * This class assumes some sort of autoloader is in use such as Zend_Loader
 *
 * @package    Sol
 * @subpackage Array
 * @category   Sort
 *
 * @copyright  Copyright (c) CBS Interactive
 *
 * @link http://wiki.cne.cnet.com/display/GNG/Sol_Array Sol_Array Documentation
 *
 * @uses Sol_Array_Exception
 *
 * @see gne_common_array
 * @see GNE_Array_Sorting
 *
 * @see Zend_Exception
 * @see Zend_Loader
 *
 */
class Sol_Array_Sort
{

    // Each field to be sorted on.
    private static $_fields             = array();

    // The directions each field in $this->_fields should be sorted in.
    // Each direction corresponds to the index of the array
    private static $_fieldDirections    = array();

    // Count of fields in the array..
    // stored to save from calling count() over and over.
    private static $_fieldCount         = 0;

    /**
     * SAMPLE USAGE:
     *      $list = array(
     *          '1' => array(
     *              'first_name' => 'john',
     *              'last_name' => 'doe',
     *          ),
     *          '2' => array(
     *              'first_name' => 'john',
     *              'last_name' => 'smith',
     *          ),
     *          '3' => array(
     *              'first_name' => 'adam',
     *              'last_name' => 'smith',
     *          ),
     *      );
     *      $sortDef = array(
     *          'first_name' => SORT_ASC,
     *          'last_name' => SORT_DESC,
     *      );
     *      $sorted = self::multiSort($list, $sortDef);
     *
     *      RESULTING ORDER:
     *          Adam Smith
     *          John Smith
     *          John Doe
     *
     * @param array $array
     * @param array $sortFields
     * @return array
     */
    public static function multiSort( array $array, array $sortFields )
    {
        if ( !is_array($sortFields) || count($sortFields) == 0 ) {
            throw new Sol_Array_Exception('sortFields is not an array');
        }

        if ( empty($array) || count($array) == 1 ) {
            return $array;
        }

        $firstRow = current($array);
        reset($array);
        foreach ( $sortFields as $fieldName => $sortDirection ) {
            if ( !isset($firstRow[$fieldName])
                || is_array($firstRow[$fieldName])
                || is_object($firstRow[$fieldName]) ) {
                throw new Sol_Array_Exception(
                    'sortField: ' . $fieldName . ' is not a valid sortField.'
                );
            }
            array_push(self::$_fields, $fieldName);
            switch ( $sortDirection ) {
                case SORT_DESC:
                    $sortDirection = -1;
                    break;
                case SORT_ASC:
                default:
                    $sortDirection = 1;
                    break;
            }
            array_push(self::$_fieldDirections, $sortDirection);
        }
        
        self::$_fieldCount = count(self::$_fields);
        if ( self::$_fieldCount == 0 ) {
            throw new Sol_Array_Exception('_fieldCount is zero');
        }

        
        // Make sure we're sorting at least one field and that the count of
        // the fields and fielddirections are correct.
        if ( self::$_fieldCount != 0 &&
             count(self::$_fields) == count(self::$_fieldDirections) ) {
            if ( is_array($array) ) {
                uasort($array, array('Sol_Array_Sort', "_compare"));
            }
        }


        // reinitialize back to defaults
        self::$_fieldDirections = array();
        self::$_fields = array();
        self::$_fieldCount = 0;

        return $array;
    }

    /**
     * The sort comparison function used by uasort
     * in the multiSort method
     *
     * @param string $a
     * @param string $b
     * @param int $idx
     * @return int
     */
    private static function _compare( $a, $b, $idx = 0 )
    {
        // do a natural order compare * sort order
        // to find the position in the array.
        $ret = self::$_fieldDirections[$idx] *
                       strnatcasecmp(
                           $a[self::$_fields[$idx]],
                           $b[self::$_fields[$idx]]
                       );

        if ( $ret == 0 && $idx < (self::$_fieldCount - 1) ) {
            return self::_compare($a, $b, $idx + 1);
        }

        return $ret;
    }


    /**
     *
     * @param array $array
     * @param string $sortByField
     * @param int $sortOrder
     * @param boolean $caseSensitive
     * @return array
     */
    public static function sortByField(
        array $array,
        $sortByField,
        $sortOrder = SORT_ASC,
        $caseSensitive = true)
    {

        if ( !is_array($array) ) {
            throw new Sol_Array_Exception('Not an array');
        }

        if ( empty($array) || count($array) == 1 ) {
            return $array;
        }

        $firstRow = reset($array);
        
        // If we have an array field, we need to parse out the indexes
        // With this, either single or double quotes are allowed
        $indices = array();
        if ( preg_match("/(.+)[\"']\]\[[\"'](.+)/", $sortByField, $indices) ) {
            if ( !isset($firstRow[$indices[1]][$indices[2]]) 
                || is_object($firstRow[$indices[1]][$indices[2]]) ) {
                throw new Sol_Array_Exception(
                    'sortField: ' . $sortByField . ' is not a valid sortField.'
                );
            }
            $checkMe = $firstRow[$indices[1]][$indices[2]];
            $sortByField = $indices[1].'"]["'.$indices[2];
        } else {
            if ( !isset($firstRow[$sortByField])
                || is_object($firstRow[$sortByField]) ) {
                throw new Sol_Array_Exception(
                    'sortField: ' . $sortByField . ' is not a valid sortField.'
                );
            }
            $checkMe = $firstRow[$sortByField];
        }

        if ( is_string($checkMe) && !$caseSensitive ) {
            if ($sortOrder == SORT_ASC) {
                $compare = create_function(
                    '$a,$b',
                    'if (strtolower($a["' .
                    $sortByField . '"]) == strtolower($b["' .
                    $sortByField . '"])) {return 0;} else { ' .
                    'return (strtolower($a["' .
                    $sortByField . '"]) > strtolower($b["' .
                    $sortByField . '"])) ? 1 : -1;}'
                );
            } else {
                $compare = create_function(
                    '$a,$b',
                    'if (strtolower($a["' .
                    $sortByField . '"]) == strtolower($b["' .
                    $sortByField . '"])) {return 0;}else { ' .
                    'return (strtolower($a["' .
                    $sortByField . '"]) > strtolower($b["' .
                    $sortByField . '"])) ? -1 : 1;}'
                );
            }
        } else {
            if ($sortOrder == SORT_ASC) {
                $compare = create_function(
                    '$a,$b',
                    'if ($a["' .
                    $sortByField . '"] == $b["' .
                    $sortByField . '"]) {return 0;} else {return ($a["' .
                    $sortByField . '"] > $b["' .
                    $sortByField .'"]) ? 1 : -1;}'
                );
            } else {
                $compare = create_function(
                    '$a,$b',
                    'if ($a["' .
                    $sortByField . '"] == $b["' .
                    $sortByField . '"]) {return 0;} else {return ($a["' .
                    $sortByField.'"] > $b["' .
                    $sortByField.'"]) ? -1 : 1;}'
                );
            }
        }

        if (uasort($array, $compare)) {
            return $array;
        }
        
        throw new Sol_Array_Exception('usort failed');

    }
}

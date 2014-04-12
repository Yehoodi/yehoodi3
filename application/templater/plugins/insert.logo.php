<?php
/**
 * Simple insert that adds the Yehoodi logo to the masthead
 * swapping out our special holiday logos on certain dates.
 *
 * @return image path
 */
function smarty_insert_logo()
{
	$date = date('m-d');
	//$date = '02-14';
	
    switch($date) {
        case '02-14':
            // Valentine's Day
            $logo = "/images/graphics/logos/logo-valentines.png";
            break;
            
        case '04-01':
            // April Fool's Day
            $logo = "/images/graphics/logos/logo-aprilfools.png";
            break;
            
        case '04-20':
            // Easter
            $logo = "/images/graphics/logos/logo-easter.png";
            break;
            
        // multiple dates with same image
        case '10-29':
        case '10-30':
        case '10-31':
            // Halloween
            $logo = "/images/graphics/logos/logo-halloween.png";
            break;
            
        case '11-26':
        case '11-27':
        case '11-28':
            // Thanksgiving
            $logo = "/images/graphics/logos/logo-thanksgiving.png";
            break;
            
        case '12-23':
        case '12-24':
        case '12-25':
        case '12-26':
        case '12-27':
        case '12-28':
        case '12-29':
        case '12-30':
        case '12-31':
            // Christmas
            $logo = "/images/graphics/logos/logo-xmas.png";
            break;
            
        default:
            $logo = "/images/graphics/logos/logo-yehoodi.png";
    }
    
    return $logo;
}
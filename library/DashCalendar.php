<?php


class DashCalendar
{
    public $yr_id;
    public $mn_id;
    public $month;
    public $events;
    public $identity;
    public $filter;
    public $lon;
    public $lat;

    public function __construct($month, $year, $nav, $filter = "All", $identity = null, $lon = null, $lat = null){
        //--- FETCH posted year and month use current if none given
        if (!$year) {
        	$year = date('Y');
        }
        
        if (!$month) {
        	$month = date('m');
        }

        //--- Validate month and year
        if(!(preg_match('/^20[0-9]{1,2}$/',$year))) $year = date('Y');
        if(!(preg_match('/^(0*)([1-9]|10|11|12)$/',$month))) $month = date('m');

        //--- Process action if any given
        switch($nav){
            case '+1':
                true;
                $time   = mktime(0,0,0,($month+1),1,$year);
                $year   = date('Y',$time);
                $month  = date('m',$time);
                break;
            case '-1':
                $time   = mktime(0,0,0,($month-1),1,$year);
                $year   = date('Y',$time);
                $month  = date('m',$time);
                break;
            default:
                break;
        }
        $this->set_year($year);
        $this->set_month($month);
        $this->set_identity($identity);
        $this->set_filter($filter);
        $this->set_lon($lon);
        $this->set_lat($lat);
        $this->init();
    }
    
    public function set_year($yr){
        $this->yr_id = $yr;
    }
    
    public function set_month($mn){
        $this->mn_id = ($mn - 1);
    }
    
    public function set_identity($i){
        $this->identity = ($i);
    }
    
    public function set_filter($f){
        $this->filter = $f;
    }
    
    public function set_lon($lo){
        $this->lon = $lo;
    }
    
    public function set_lat($la){
        $this->lat = $la;
    }
    
    public function init(){
        $this->month = new DashCalendar_DashCalMonth($this->yr_id,$this->mn_id, $this->filter, $this->identity, $this->lon, $this->lat);
    }
    
    public function get_title(){
        $title = sprintf("PTO Calendar(%s)",date('F Y',mktime(0,0,0,($this->mn_id+1),1,$this->yr_id)));
        return $title;
    }

    public function get_month(){
        return $this->month;
    }
    
    public function get_month_names() {
    	//--- CREATE an array of month names to use
		for($i=1; $i<=12; $i++){
		    $dashMonths['cal_months'][] = array('id'=>$i,'name'=>date('F',mktime(0,0,0,$i,1,2005)));
		}

		return $dashMonths['cal_months'];
    }
}
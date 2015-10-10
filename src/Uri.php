<?php
namespace Lyra;

class Uri{
    protected $segments = NULL;

    public function __construct()
    {
        if(isset($_GET['q']))
            $this->segments = explode("/", $_GET['q']);
    }

    public function segment($number, $noExist=false)
    {
        $number = intval($number);
        if (isset($this->segments[$number])){
            return $this->segments[$number];
        }else{
            return $noExist;
        }
    }

    public function slash_segment($number, $slash='trailing')
    {
        if ($this->segment($number))
        {
            $return = "";
            if($slash == 'leading' || $slash == 'both')
                $return .= '/';
            return $return;
        } else {
            return false;
        }
    }
}
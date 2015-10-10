<?php

namespace Lyra\Hook;
use Lyra\Hook;
use Lyra\Container;

/**
 * PlayerStats
 * @see \Lyra\Hook
 */
class PlayerStats extends Hook
{
	public function actionAfter()
	{
        if(\App::getModel('Session')->isLoggedin()) {
            // When we have db
            $data = \App::registerHook('playerStats');
            $attrModel = \App::getModel('PlayerAttributes');
            $attrs = $this->formatAttributes($attrModel->getAll());
            if (is_array($data)) {
                array_push($data, $attrs);
                $res = $this->combineAttributes($data);
            }else {
                $res = $attrs;
            }
            \View::set('PlayerStats', $res);
        }
	}

    private function combineAttributes($data)
    {
        $res = array();
        foreach ($data as $key => $arr)
        {
            foreach($arr as $attr => $val)
            {
                if(array_key_exists($attr, $res))
                {
                    $res[$attr] = $res[$attr] + $val;
                }else{
                    $res[$attr] = $val;
                }
            }
        }
        return $res;
    }

    private function formatAttributes($attrs)
    {
        $res = array();
        foreach($attrs as $key => $arr)
        {
            $res = array_merge($res, array($arr['title']=>$arr['value']));
        }
        return $res;
    }
}
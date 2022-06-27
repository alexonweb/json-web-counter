<?php
/**
 * SmartCounter View
 * Version: 0.1 Alpha
 * 
 * Alexander Dalle dalle@criptext.com 
 * 
 */

namespace FriendlyWeb;

class SmartCounterView extends SmartCounter
{

    public function __construct()
    {

        parent::__construct();

    }

    // Method for debugging
    // retrun JSON current statistics data
    public function rawStats()
    {

        return json_encode($this->statistics);

    }

    // Return views (hits) of all pages total or 
    // current page if $thispage is true
    public function views($thispage = false)
    {

        if ($thispage) {

            return array_sum($this->statistics->pages[$this->pagekey]->hits);

        } else {

            return $this->total(false);

        }

    }

    // Return total visits (unique)
    public function visits($thispage = false)
    {

        if ($thispage) {

            return $this->statistics->pages[$this->pagekey]->unique;

        } else {

            return $this->total(true);

        }

    }

    // Method for counting the total number of views or visits
    // $unique = true - for visits 
    // $unique = false - for views
    private function total($unique) {

        $total = 0;

        foreach ($this->statistics->pages as $page) {
    
            if ($unique) {

                $total += $page->unique;

            } else {
    
                $total += array_sum($page->hits);
    
            }

        }

        return $total;

    }

}
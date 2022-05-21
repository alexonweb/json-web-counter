<?php

namespace FriendlyWeb;

use DateTime;

class SmartCounter
{

    private $statisticsFilePath = 'user/smartcounter.json';
    private $statistics = null;
    private $datenow = null;


    public function __construct()
    {

        $this->setDatenow();

        $this->dataInitial();

    }

    private function setDatenow()
    {

        $this->datenow = new DateTime('NOW');

    }

    private function dataInitial()
    {

        if ( !file_exists( $this->statisticsFilePath ) ) {

            $this->statistics = (object) $this->createSkeleton();

        } else {

            $this->statistics = json_decode( file_get_contents($this->statisticsFilePath) );

        }

    }

    /**
     * Understanding Skeleton
     * 
     * date - date of last editing data file
     * common
     *  hits -  array as a sequence of days
     *  hosts - array as a sequence of days
     */
    private function createSkeleton()
    {
    
        $sq = array(0);

        $common = array( 
            'hits'      => $sq,
            'hosts'     => $sq);

        $skeleton = array(
            'date'      => $this->datenow->format('Y-m-d'),
            'common'    => (object)$common);

        return $skeleton;

    }

    /**
     * Fill 0 values of the days before the current date
     * 
     * $sq - array
     * $startdate - DateTime object
     * 
     * return Array 
     */
    private function subsequence ( $sq, $startdate )
    {

        // $days = $this->datenow->diff($startdate)->format("%a");
        $days = $this->daysBefore($startdate);

        for ($i=0; $i<$days; $i++) {

            $sq[] = 0;

        }

        return $sq;

    }

    /**
     * Days before the current date
     * $lastdate - DateTime object
     */ 
    private function daysBefore($lastdate)
    {

        $days = $this->datenow->diff($lastdate)->format("%a");

        return $days;

    }

    public function addTolastOne($sq) 
    {

        end($sq);

        $key = key($sq);

        $sq[$key]++;

        return $sq;

    }


    /**
     * 
     */
    public function count()
    {

        $lastdate = new DateTime($this->statistics->date);

        // HITS

        $sq = $this->statistics->common->hits;

        $sq = $this->subsequence($sq, $lastdate);

        $sq = $this->addTolastOne($sq);

        $this->statistics->common->hits = $sq;

        // HOSTS

        $sq = $this->statistics->common->hosts;

        $sq = $this->subsequence($sq, $lastdate);

        if ( isset($_COOKIE["smartcounter"]) ) {

            $datecookie = $_COOKIE["smartcounter"];

            $datecookie = new DateTime($datecookie); // @todo check data

            $days = $this->daysBefore($datecookie);

            if ($days != 0) {
                
                $sq = $this->addTolastOne($sq);
            }

        } else {

            $sq = $this->addTolastOne($sq);

        }

        $this->statistics->common->hosts = $sq;


        // 
        $this->statistics->date = $this->datenow->format('Y-m-d');

        $domainname = $_SERVER['SERVER_NAME']; // bug with 'localhost'

        setcookie ('smartcounter', $this->statistics->date, time()+87091200, '/', ".$domainname");

        $jsondata = json_encode($this->statistics);

        file_put_contents($this->statisticsFilePath, $jsondata);

    }

    // Raw statistics
    public function rawStats()
    {

        return $this->statistics;

    }

}



?>
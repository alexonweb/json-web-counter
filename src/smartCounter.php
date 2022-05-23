<?php

namespace FriendlyWeb;

use DateTime;

class SmartCounter
{

    private $statisticsFilePath = 'user/smartcounter.json';

    private $statistics = null;
    private $datenow = null;
    private $cookiedate = null;


    public function __construct()
    {

        $this->setDatenow();

        $this->dataInitial();

        $this->upToDateStatistics();

    }

    private function setDatenow()
    {

        $this->datenow = new DateTime('NOW');

    }

    // Return Object
    private function dataInitial()
    {

        if ( file_exists( $this->statisticsFilePath ) ) {

            $this->statistics = json_decode( file_get_contents($this->statisticsFilePath), false );

        } else {

            $this->statistics = (object) $this->createSkeleton();

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
    private function subsequence ( $sq )
    {

        $startdate = new DateTime($this->statistics->date);

        $days = $this->daysBefore($startdate);

        for ($i=0; $i<$days; $i++) {

            $sq[] = 0;

        }

        return $sq;

    }

    /**
     * Days before the CURRENT date
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

    private function upToDateStatistics()
    {

        $sq = $this->statistics->common->hits;
        
        $sq = $this->subsequence($sq);

        $this->statistics->common->hits = $sq;

        $sq = $this->statistics->common->hosts;

        $sq = $this->subsequence($sq);

        $this->statistics->common->hosts = $sq;

        $this->statistics->date = $this->datenow->format('Y-m-d');

    }


    /**
     * 
     */
    public function count()
    {

        $this->GetCookie();

        $this->statistics->common->hits = $this->addTolastOne( $this->statistics->common->hits );

        // hosts
        
        $sq = $this->statistics->common->hosts;

        if ($this->cookiedate !== null) {

//            $days = $this->daysBefore($this->cookiedate);
            
            if ( $this->daysBefore($this->cookiedate) != 0) {
                
                $sq = $this->addTolastOne($sq);
            }

        } else {

            $sq = $this->addTolastOne($sq);

        }

        $this->statistics->common->hosts = $sq;

        // new date

        $this->statistics->date = $this->datenow->format('Y-m-d');

        $this->SetCookie();

        $this->putJSONtoFile();

    }


    //
    private function SetCookie()
    {

        $domainname = $_SERVER['SERVER_NAME']; // bug with 'localhost'

        setcookie ('smartcounter', $this->statistics->date, time()+87091200, '/', ".$domainname");

    }

    //
    private function GetCookie()
    {

        if ( isset($_COOKIE["smartcounter"]) ) {

            $datecookie = $_COOKIE["smartcounter"];

            if ($this->verifyDate($datecookie)) {

                $this->cookiedate = new DateTime($datecookie);

            } else {
                // wrong cookie date format 
            }

        }

    }

    private function verifyDate($date)
    {

        return (DateTime::createFromFormat('Y-m-d', $date) !== false);

    }

    private function putJSONtoFile()
    {

        file_put_contents( $this->statisticsFilePath, json_encode($this->statistics) );

    }



    // Raw statistics
    public function rawStats()
    {

        return $this->statistics;

    }

}



?>

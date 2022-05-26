<?php
/**
 * SmartCounter 0.2.5.1 alpha
 * 
 * Alexander Dalle dalle@criptext.com 
 * 
 * Simple JSON based webcounter
 * 
 */

namespace FriendlyWeb;

use DateTime;

class SmartCounter
{

    private $statisticsFilePath = 'user/smartcounter.json';

    public function __construct()
    {

        $this->setCurrentDate();

        $this->getStatsData();

        $this->updateStatsData();

    }

    private function setCurrentDate()
    {

        $this->datenow = new DateTime('NOW');

    }

    private function getStatsData()
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
    private function daysBefore($startdate)
    {

        $days = $this->datenow->diff($startdate)->format("%a");

        return $days;

    }

    public function addTolastOne($sq) 
    {

        end($sq);

        $key = key($sq);

        $sq[$key]++;

        return $sq;

    }

    private function updateStatsData()
    {

        $hh = array("hits", "hosts");

        foreach ($hh as $h) {

            $this->statistics->common->$h = 
                $this->subsequence( $this->statistics->common->$h );

        }

        $this->statistics->date = $this->datenow->format('Y-m-d');

    }

    public function count()
    {

        $this->statistics->common->hits = $this->addTolastOne( $this->statistics->common->hits );
        
        if ( $this->isNewVisitor() ) {

            $this->statistics->common->hosts = $this->addTolastOne( $this->statistics->common->hosts );

        }

        $this->setCookie();

        $this->putJSONtoFile();

    }

    private function isNewVisitor()
    {

        $this->getCookie();

        if ($this->cookiedate !== null) {
            
            if ( $this->daysBefore($this->cookiedate) != 0) {

                return true;

            } else {

                return false;
            }

        } else {

            return true;

        }

    }

    private function setCookie()
    {

        $domainname = $_SERVER['SERVER_NAME']; // bug with 'localhost'

        setcookie ('smartcounter', $this->statistics->date, time()+87091200, '/', ".$domainname");

    }

    private function getCookie()
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

    // Return JSON
    public function rawStats()
    {

        return json_encode( $this->statistics );

    }

}

?>
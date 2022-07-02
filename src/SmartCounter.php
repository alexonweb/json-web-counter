<?php
/**
 * SmartCounter Controller
 * Version: 0.4.6 Alpha
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

    private $statisticsFilePath = 'user/smartcounter/pages.json';
    var $statistics = null;
    var $pagekey = null;
    private $cookiedate = null;
    private $uri = null;

    public function __construct()
    {

        $this->setCurrentDate();

        $this->getStatsData();

        $this->getCookie();

        $this->setURI();

        $this->setPageKey();

        $this->updateStatsData();

    }

    private function setCurrentDate()
    {

        $this->datenow = new DateTime('NOW');

    }

    private function getStatsData()
    {

        if (file_exists($this->statisticsFilePath)) {

            $this->statistics = json_decode(file_get_contents($this->statisticsFilePath), false);

        } else {

            $this->statistics = (object)$this->createSkeleton();

        }

    }

    /**
     * Understanding Skeleton
     * 
     * date - date of last editing data file
     * pages - array of objects (statistics by URI pages)
     *  uri  -  resource identifier
     *  hits -  array as a sequence of days
     *  hosts - array as a sequence of days
     *  unique - number of visitors without cookies
     */
    private function createSkeleton()
    {

        $pages[] = $this->smallSkeleton();

        $skeleton = array(
            'date'      => $this->datenow->format('Y-m-d'),
            'pages'    => $pages);

        return $skeleton;

    }

    private function smallSkeleton($uri = 'index')
    {

        $sq = array(0);

        $empty = array(
            'uri' => $uri,
            'hits'=>$sq,
            'hosts'=>$sq,
            'unique'=>0
        );

        return (object)$empty;

    }

    // Set current key of pages array in $this->statistics by current URI
    private function setPageKey()
    {

        $this->pagekey = $this->findPageKey($this->uri);

    }

    private function findPageKey($uri)
    {

        foreach ($this->statistics->pages as $key => $page) {

            if ($page->uri == $uri) {

               return $key;

            }

        }

    }

    /**
     * Fill 0 values of the days before the current date
     * 
     * $sq - array
     * $startdate - what date to start counting
     * 
     * return Array 
     */
    private function sequences($sq, $startdate)
    {

        $startdate = new DateTime($startdate);

        $days = $this->daysBefore($startdate);

        for ($i=0; $i<$days; $i++) {

            $sq[] = 0;

        }

        return $sq;

    }

    /**
     * Days before the CURRENT date
     * $startdate - DateTime object
     */ 
    private function daysBefore($startdate)
    {

        return $this->datenow->diff($startdate)->format("%a");

    }

    private function addTolastOne($sq) 
    {

        end($sq);

        $key = key($sq);

        $sq[$key]++;

        return $sq;

    }

    private function updateSequences($key, $date)
    {

        $hh = array("hits", "hosts");

        foreach ($hh as $h) {

            $this->statistics->pages[$key]->$h = 
                $this->sequences($this->statistics->pages[$key]->$h, $date);

        }

    }

    // Retrun the date when the statistics began to be taken
    private function absoluteDate()
    {

        $pagekey = $this->findPageKey("index");

        $dayspast = count($this->statistics->pages[$pagekey]->hits);

        return date('Y-m-d', strtotime('-' . --$dayspast . ' days'));

    }

    private function addPage()
    {

        $this->statistics->pages[] = $this->smallSkeleton($this->uri);

        $this->setPageKey();

        $this->updateSequences($this->pagekey, $this->absoluteDate());

    }

    private function updateSequencesForAllPages()
    {

        foreach ($this->statistics->pages as $key => $page) {

            $this->updateSequences($key, $this->statistics->date);

        }

    }

    // Actualize stats
    private function updateStatsData()
    {

        ($this->pagekey === null) ? $this->addPage() : false;

        $this->updateSequencesForAllPages();

        $this->statistics->date = $this->datenow->format('Y-m-d');

    }

    private function issetCookie()
    {

        return (isset($this->cookiedate)) ? true : false;

    }

    private function isNewVisitor()
    {

        if ($this->issetCookie()) {

            if ($this->daysBefore($this->cookiedate) == 0) {

                return false;

            } else {

                return true;
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

        if (isset($_COOKIE["smartcounter"])) {

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

        file_put_contents($this->statisticsFilePath, json_encode($this->statistics));

    }

    private function getURI()
    {

        $uri = urldecode($_SERVER['REQUEST_URI']);

        $uri = substr($uri, 1); // Remove slash at the start of the line

        if ($uri == '') {

            return 'index';

        } else {

            return $uri;

        }

    }

    private function setURI()
    {

        $this->uri = $this->getURI();

    }

    // Adds hits and hosts to statistics JSON file and sets cookies
    public function count()
    {

        $this->statistics->pages[$this->pagekey]->hits = 
            $this->addTolastOne($this->statistics->pages[$this->pagekey]->hits);

        if ($this->isNewVisitor()) {

            $this->statistics->pages[$this->pagekey]->hosts = 
                $this->addTolastOne($this->statistics->pages[$this->pagekey]->hosts);

        }

        if (!$this->issetCookie()) {

            $this->statistics->pages[$this->pagekey]->unique++;

        }

        $this->setCookie();

        $this->putJSONtoFile();

    }

}

?>
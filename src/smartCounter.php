<?php

namespace FriendlyWeb;

class SmartCounter
{

    private $statisticsFilePath = 'user/smartcounter.json';
    private $statistics = null;
    private $datenow = null;
    private $domainname = null; 
    private $options = array(
        'sq' => 360
    );


    public function __construct()
    {

        $this->dataInitial();

    }

    // 
    private function setDatenow()
    {

        $this->datenow = mktime(0, 0, 0, date("m"), date("d"), date("Y"));

    }

    // 
    private function dataInitial()
    {

        if ( !file_exists( $this->statisticsFilePath ) ) {

            $this->statistics = $this->createSkeleton();

        } else {

            $this->statistics = file_get_contents($this->statisticsFilePath);

        }

    }

    // 
    private function createSkeleton()
    {

        $this->setDatenow();

        $ymdall = array('Y' => null, 'm' => null, 'd' => null, 'all' => null);

        $sq = '';

        for ($i=0; $i < $this->options['sq']; $i++) {

            $sq .= '0;';

        }

        $common = array( 
            'hits'      => $ymdall,
            'hosts'     => $ymdall,
            'sq'        => $sq);

        $skeleton = array(
            'date'      => $this->datenow,
            'common'    => $common);

        return json_encode($skeleton);

    }

    // hook for updating
    private function resourceIdentifier()
    {

        $uri = urldecode($_SERVER['REQUEST_URI']);

        $uri = substr($uri, 1); // Remove slash at the start of the line

        if ($uri == '') {

            return 'common';

        } else {

            return $uri;

        }

    }

    // 
    public function count()
    {

        $this->dataInitial();

        $statistics = json_decode($this->statistics);

        $lastdate = (int)$statistics->date;

        if (isset($_COOKIE["smartcounter"])) {

            $datecookie = (int)$_COOKIE["smartcounter"];

        }

        $dstrfrmt = array ("Y", "m", "d");

        foreach ($dstrfrmt as $datestr) {

            if (date("$datestr", $this->datenow) == date("$datestr", $lastdate)) {

                $statistics->common->hits->$datestr++;

                // hook for updating

            } else {

                foreach ($dstrfrmt as $datestrsec ) {

                    if ($datestrsec == $datestr) {

                        $mark = true;

                    }

                    if ($mark) {

                        $statistics->common->hosts->$datestrsec = 1;
                        
                        $statistics->common->hits->$datestrsec = 1;

                        // hook for updating

                    }

                }

                break;

            }

            if ( isset($datecookie) ) {

                if ( (date("$datestr", $this->datenow) != date("$datestr", $datecookie)) ) {

                    $statistics->common->hosts->$datestr++;

                    // hook for updating

                }

            } else {

                $statistics->common->hosts->$datestr++;

                // hook for updating

            }

        }

        $statistics->common->hits->all++;

        // hook for updating

    	if ( !isset($datecookie) ) {

            $statistics->common->hosts->all++;

        }


        // Subsequence 

        $sq_days = $this->options['sq']; // hook for updating

        $sq_arr = explode(";", $statistics->common->sq);

        reset($sq_arr);

        $mark = false;

        if ($this->datenow == $lastdate) {

            if (isset($datecookie)) {

                if ($this->datenow != $datecookie) {

                    $mark = true;

                }

    		} else {

                $mark = true;

    		}

        } else {

            $zerodays = (int)( ($this->datenow - $lastdate) /(3600*24) );

            if ($zerodays >= $sq_days) {

                foreach ($sq_arr as $sq_key => $sq_value) {

                    $sq_arr[$sq_key] = 0; 

                };

            } else {

                for ($i=0; $i < $zerodays; $i++) {

                    $sq_arr[] = 0;

                }

                $sq_arr = array_slice($sq_arr, $zerodays);

            }

            $mark = true;

        }

        end($sq_arr); 

        $sq_lastkey = key($sq_arr);

        if ($mark) {

            $sq_arr[$sq_lastkey] = $statistics->common->hosts->d;

            // hook for updating

        }

        $statistics->common->sq = "";

        foreach ($sq_arr as $sq_key => $sq_value ) {

          $statistics->common->sq .= $sq_value;

            if ($sq_key != $sq_lastkey) {

                $statistics->common->sq .= ";";

            }

        }

        // Subsequences end

        $this->setDatenow();

        $statistics->date = $this->datenow;

        $domainname = $_SERVER['SERVER_NAME']; // bug with 'localhost'

        setcookie ('smartcounter', $this->datenow, time()+87091200, '/', ".$domainname");

        $jsondata = json_encode($statistics);

        file_put_contents($this->statisticsFilePath, $jsondata);

    }

    // 
    public function getStats($page = 'common', $type = 'hits', $period = 'all')
    {

        $this->dataInitial();

        $statistics = json_decode($this->statistics);

        return $statistics->$page->$type->$period;

    }

}



?>
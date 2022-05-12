<?php
/*
 * JSON Web Counter
 * 
 */

class SmartCounter 
{

    private $statisticsFilePath = 'smartstat.json';
    private $statistics = null;
    private $datenow = null;
    private $domainname = null;
    private $options = array(
        'sq' => 50
    );

    public function __construct()
    {

        $this->dataInitial();

    }

    private function setDatenow()
    {

        $this->datenow = mktime(0, 0, 0, date("m"), date("d"), date("Y"));

    }

    private function dataInitial() 
    {

        if ( !file_exists( $this->statisticsFilePath ) ) {

            $this->statistics = $this->createSkeleton();

        } else {

            $this->statistics = file_get_contents($this->statisticsFilePath);

        }

    }

    private function createSkeleton()
    {

        $this->setDatenow();

        $ymdall = array ('Y' => null, 'm' => null, 'd' => null, 'all' => null);

        $sq = '';

        for ($i=0; $i < $this->options['sq']; $i++) {

            $sq .= '0;';

        }

        $skeleton = array(
            'date'      => $this->datenow,
            'hits'      => $ymdall,
            'hosts'     => $ymdall,
            'sq'        => $sq);

        return json_encode( $skeleton );

    }

    public function getStatistics($req)
    {

        $this->dataInitial();

    }

    public function counter()
    {

        $this->dataInitial();

        $statistics = json_decode($this->statistics);

        $sm_date = (int)$statistics->date;

        if (isset($_COOKIE["visit"])) {

            $datecookie = (int)$_COOKIE["visit"];

        }

        $dstrfrmt = array ("Y", "m", "d");

        foreach ($dstrfrmt as $datestr) {

            if (date("$datestr", $this->datenow) == date("$datestr", $sm_date)) {

                $statistics->hits->$datestr++;

            } else {

                foreach ($dstrfrmt as $datestrsec ) {

                    if ($datestrsec == $datestr) {

                        $mark = true;

                    }

                    if ($mark) {

                        $statistics->hosts->$datestrsec = 1;

                        $statistics->hits->$datestrsec = 1;

                    }

                }

                break;

            }

            if ( isset($datecookie) ) {

                if ( (date("$datestr", $this->datenow) != date("$datestr", $datecookie)) ) {

                    $statistics->hosts->$datestr++;

                }

            } else {

                $statistics->hosts->$datestr++;

            }

        }

        $statistics->hits->all++;

    	if ( !isset($datecookie) ) {

            $statistics->hosts->all++;

        }

        $sq_days = 50;

        $sq_arr = explode(";", $statistics->sq);

        reset($sq_arr);

        $mark = false;

        if ($this->datenow == $sm_date) {

            if (isset($datecookie)) {

                if ($this->datenow != $datecookie) {

                    $mark = true;

                }

    		} else {

                $mark = true;

    		}

        } else {

            $zerodays = (int)( ($this->datenow - $sm_date) /(3600*24) );

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

            $sq_arr[$sq_lastkey] = $statistics->hosts->d;

        }

        $statistics->sq = "";

        foreach ($sq_arr as $sq_key => $sq_value ) {

          $statistics->sq .= $sq_value;

            if ($sq_key != $sq_lastkey) {

                $statistics->sq .= ";";

            }

        }

        $this->setDatenow();

        $statistics->date = $this->datenow;

        $domainname = $_SERVER['SERVER_NAME'];

        setcookie ('visit', $this->datenow, time()+87091200, '/', ".$domainname");

        $jsondata = json_encode($statistics);

        file_put_contents($this->statisticsFilePath, $jsondata);

    }

}




?>

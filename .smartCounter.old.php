<?php

    function smartCounter() {

        $domainname = ""; // 

        $statisticsfile = "data/statistics.xml"; //


        $datenow = mktime(0, 0, 0, date("m"), date("d"), date("Y")); // @bugfix 

        if ( file_exists( $statisticsfile ) ) 
        {
            $statistics = simplexml_load_file($statisticsfile);
        } else 
        {
            
            $xmlstr = <<<XML
            <?xml version='1.0' standalone='yes'?>
            <smartcounter>
                <date/>
                <hits>
                <Y>1</Y>
                <m>1</m>
                <d>1</d>
                </hits>
            </smartcounter>
            XML;

            /* $statistics = new SimpleXMLElement($xmlstr);

            $statistics->date =  $datenow;

            $statistics->hits->all = 1;
            $statistics->hits->Y = 1;
            $statistics->hits->m = 1;
            $statistics->hits->d = 1;
            // $statistics->hosts->Y = 0;
            */

        }




        $datexml = (int)$statistics->date;

        if (isset($_COOKIE["visit"])) {

            $datecookie = (int)$_COOKIE["visit"];

        }

        $dstrfrmt = array ("Y", "m", "d");

        foreach ($dstrfrmt as $datestr) {

            if (date("$datestr", $datenow) == date("$datestr", $datexml)) {

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

                if ( (date("$datestr", $datenow) != date("$datestr", $datecookie)) ) {

                    $statistics->hosts->$datestr++;

                }

            } else {

                $statistics->hosts->$datestr++;

            }

        }

        $statistics->hits->all++; // Hit

    	if ( !isset($datecookie) ) {

            $statistics->hosts->all++; // Host

        }


        $sq_days = 50;

        $sq_arr = explode(";", $statistics->sq);

        reset($sq_arr);

        $mark = false;

        if ($datenow == $datexml) {

            if (isset($datecookie)) {

                if ($datenow != $datecookie) {

                    $mark = true;

                }

    		} else {

                $mark = true;

    		}

        } else {

            $zerodays = (int)( ($datenow-$datexml) /(3600*24) );

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


        $statistics->date = $datenow; // Update date


        setcookie ("visit", $datenow, time()+87091200, "/", ".$domainname");

        $statistics->saveXML($statisticsfile);

    }

    

/* Конец метода smartCounter */

// smartCounter();


?>
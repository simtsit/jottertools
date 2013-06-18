<?php

        // This function searches a URL for photos

        function checkforphotos($request_url){

                global $makelinks;
                global $showpreview;
                global $count;

                $count=0;

                if($shownotes) { echo "<br><font color=green>Crawling " . $request_url. "...</font><br>"; }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $request_url);    // The url to get links from
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // We want to get the respone
                $result = curl_exec($ch);

                $regex='|<img.*?src="(.*?)"|';
                preg_match_all($regex,$result,$parts);
                $photos=$parts[1];

                foreach($photos as $photo){

                        if((strpos($photo, $request_url) !== false)||($photo[0] != '/')) $image_url = $photo;
                                else $image_url = $request_url . $photo;

                        $count++;
#                       echo $count . ". ";

                        if($makelinks) echo '<a href=' . $image_url . ' target="_blank">';

                        echo $image_url;

                        if($showpreview) echo "<br><img width=150 src=" . $image_url . '><br><br>';

                        if($makelinks) echo "</a>";

                        echo "<br>";
                }
        }



        if(substr($target_url, -1, 1)=='/')  $link = substr_replace($link ,"",-1);
        checkforphotos($target_url);
        echo "<br><br>There were totaly " . $count . " images found.";

?>

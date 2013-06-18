<?php

        echo "<h2 align=center>Slideshows of " . $url . "</h2><br>";


        // This fucntion is counting photos.

        function countphotos($slideshow_id) {
                $photos_url ='http://www.schooljotter.com/slideshowswfs/getxml.php?pfid=' . $slideshow_id;

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $photos_url);    // The url to get links from
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // We want to get the respone
                $result = curl_exec($ch);

                $regex='|<photo.*?url="(.*?)"|';
                preg_match_all($regex,$result,$parts);
                $links=$parts[1];
                $photos = count($links);

                curl_close($ch);
                return($photos);

        }


        // This function is looking for internal links.

        function checkthis($request_url){

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $request_url);    // The url to get links from
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // We want to get the respone
                $result = curl_exec($ch);

                $regex='|<iframe.*?src="(.*?)"|';
                preg_match_all($regex,$result,$parts);
                $links=$parts[1];

                if(empty($links)) {
                        echo "No slideshows found! <br>";
                } else {
                echo "<table><th>Slideshow ID</th><th>File Name</th><th>No of Images</th><th></th>";
                foreach($links as $link){
                        $slideshowid = substr($link, -13);
                        echo '<form method="get" action="slideshowdownload.php">';
                        echo '<tr><td><input size=7 type="text" name="id" value=' . $slideshowid . '></td>';
                        echo '<td><input size=12 type="text" name="zipname" value=' . $slideshowid . '></td>';
                        echo '<td>'. countphotos($slideshowid) . ' photos </td>';
                        echo '<td><input type="submit" value="Download"></td></tr></form>';
                }
                echo '</table>';
                }
                curl_close($ch);
        }


        checkthis($url);

        echo "<br>";
?>

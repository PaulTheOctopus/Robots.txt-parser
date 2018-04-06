<?php
session_start();
function outputCSV($data,$file_name = 'file.csv') {
       # output headers so that the file is downloaded rather than displayed
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=$file_name");
        # Disable caching - HTTP 1.1
        header("Cache-Control: no-cache, no-store, must-revalidate");
        # Disable caching - HTTP 1.0
        header("Pragma: no-cache");
        # Disable caching - Proxies
        header("Expires: 0");
    
        # Start the ouput
        $output = fopen("php://output", "w");
        stream_filter_append($output, 'convert.iconv.utf-8/windows-1251', STREAM_FILTER_WRITE);
         # Then loop through the rows
        //fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM
       /* function toWindow($ii){
            return iconv( "utf-8", "windows-1251",$ii);
        }

        /* кодируем данные массива в windows-1251 */
       /* foreach($data as $p=>$item){
            $data[$p] = toWindow($item);
        }*/
 
        
        foreach ($data as $row) {
            # Add the rows to the body
            fputcsv($output, $row); // here you can change delimiter/enclosure
        }
        # Close the stream off
        fclose($output);
    }

    outputCSV($_SESSION['array'],'download.csv');
?>
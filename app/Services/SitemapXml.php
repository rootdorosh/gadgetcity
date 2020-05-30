<?php
namespace App\Services;

class SitemapXml
{
    /*
     * @param string $file
     * @return array
     */
    public function getLocsByGzFile(string $file): array
    {
        $content = '';
        $gz = gzopen($file,'r') or die("can't open: $php_errormsg"); 
        while ($line = gzgets($gz,1024)) { 
            $content.= $line;
        }
        gzclose($gz);
        
        $xml = simplexml_load_string($content);
        $items = [];
        foreach ($xml as $item) {
            $items[] = (string)$item->loc;
        }   
        
        return $items;
    }

}

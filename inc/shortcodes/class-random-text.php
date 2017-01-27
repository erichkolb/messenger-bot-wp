<?php

namespace GigaAI\Shortcode;

class Random_Text
{
    public function __construct()
    {
        add_shortcode('random-text', [$this, 'random_text']);
    }
    
    public function random_text($atts = [], $content = '')
    {
        $rows = preg_split('/\r\n|[\r\n]/', $content);
        
        foreach ($rows as $n => $row) {
            
            if (empty($row)) {
                unset($rows[$n]);
                continue;
            }
            
            preg_match_all('/\((.*?)\)/', $row, $matches);
            
            if ( ! empty($matches[1])) {
                foreach ($matches[1] as $index => $patterns) {
                    $patterns = explode('|', $patterns);
                    
                    $pick = $patterns[array_rand($patterns)];
                    
                    $row = str_replace($matches[0][$index], $pick, $row);
                }
            }
            
            $rows[$n] = $row;
        }
        
        // Pick a random string from source
        return $rows[array_rand($rows)];
    }
}

new Random_Text;
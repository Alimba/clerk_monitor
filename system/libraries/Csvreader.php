<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* CSVReader Class
*
* $Id: csvreader.php 147 2007-07-09 23:12:45Z Pierre-Jean $
*
* Allows to retrieve a CSV file content as a two dimensional array.
* The first text line shall contains the column names.
*
* @author        Pierre-Jean Turpeau
* @link        http://www.codeigniter.com/wiki/CSVReader
*/
class CI_CSVReader 
{
    
    var $fields;        /** columns names retrieved after parsing */
    var $separator = ',';    /** separator used to explode each line */
    
    /**
     * Parse a text containing CSV formatted data.
     *
     * @access    public
     * @param    string
     * @return    array
     */
    function parse_text($p_Text) 
    {
        $lines = explode("\n", $p_Text);
        return $this->parse_lines($lines);
    }
    
    /**
     * Parse a file containing CSV formatted data.
     *
     * @access    public
     * @param    string
     * @return    array
     */
    function parse_file($p_Filepath) 
    {
        $lines = file($p_Filepath);

        return $this->parse_lines($lines);
    }
    /**
     * Parse an array of text lines containing CSV formatted data.
     *
     * @access    public
     * @param    array
     * @return    array
     */
    function parse_lines($p_CSVLines) 
    {    
        if ($p_CSVLines != '')
        {
            foreach ($p_CSVLines as $line => $id)
            {
                $lines[$line] = explode(',',$id);
            }
            foreach ($lines as $line => $id) 
                {
                    if (isset($lines[$line+1]))
                    {
                        if (count($lines[$line]) < count($lines[$line+1])) 
                        {
                            unset($lines[$line]);
                        }  
                    }                            
                }      
            return $lines; 
        }
        return 'File is Empty! :O';   
    }

    function get_file_date($p_Filepath)
    {
        $file_date = date('Y-d-m', filemtime($p_Filepath));
        
        return $file_date;
    }
}

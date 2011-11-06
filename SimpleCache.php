<?php
/**
 * SimpleCache for PHP
 * 
 * A simple cache file system using array
 * 
 * @author Enrico Zimuel (enrico@zimuel.it)
 * @copyright Copyright (C) 2011 Enrico Zimuel - http://www.zimuel.it
 * @license GNU General Public License - http://www.gnu.org/licenses/gpl.html
 */
class SimpleCache {
    
    const DEFAULT_LIFETIME = 60;
    
    protected $values = array();
    protected $cacheFile = '.simplecache';
    
    /**
     * Constructor
     * 
     * @param string $file 
     */
    public function __construct($path=null) 
    {
        if (!empty($path)) {
            $this->cacheFile = $path . '/' . $this->cacheFile;
        }
    }
    /**
     * Load the key value from the cache
     * 
     * @param  string $key
     * @return mixed|boolean 
     */
    public function load($key) 
    {
        if (isset($this->values[$key])) {
            if ($this->values[$key]['l']>time()) {
                return $this->values[$key]['v'];
            } else {
                $this->remove($key);
                return false;
            }
        } 
        $this->values[$key]= @include($this->cacheFile.'_'.md5($key).'.php');
        if (empty($this->values[$key])) {
            unset($this->values[$key]);
            return false;
        }
        if ($this->values[$key]['l']>time()) {
            $this->values[$key]['v']= unserialize($this->values[$key]['v']);
            return $this->values[$key]['v'];
        } else {
            $this->remove($key);
            return false;
        }
    }
    /**
     * Save a value in the cache
     * 
     * @param  mixed   $value
     * @param  string  $key
     * @param  integer $lifetime
     * @return boolean 
     */
    public function save($value, $key, $lifetime=self::DEFAULT_LIFETIME) 
    {
        if (empty($key)) {
            return false;
        }
        $lifetime = time() + (int) $lifetime;
        $this->values[$key] = array (
            'v' => $value,
            'l' => $lifetime
        );
        return $this->writeToFile($key, $this->values[$key]); 
    }
    /**
     * Remove a key from the cache
     * 
     * @param  string $key
     * @return boolean 
     */
    public function remove($key)
    {
        if (empty($key)) {
            return false;
        }
        unset($this->values[$key]);
        return @unlink($this->cacheFile.'_'.md5($key).'.php');
    }
    /**
     * Clean the cache repository
     * 
     * @return void
     */
    public function clean()
    {
        $this->values= array();
        array_map("unlink", glob($this->cacheFile.'_*.php'));
    }
    /**
     * Write the cache values to file
     * 
     * @param  string $key
     * @param  mixed  $value
     * @return boolean 
     */
    protected function writeToFile($key, $value)
    {
        $value['v'] = serialize($value['v']);
        $array = var_export($value,true);
        return (file_put_contents($this->cacheFile.'_'.md5($key).'.php', "<?php return $array;",LOCK_EX)!==false); 
    }
}